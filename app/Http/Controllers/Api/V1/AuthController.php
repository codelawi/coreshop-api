<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\OnboardingRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials',
            ], 401);
        }

        if (! $user->isActive()) {
            return response()->json([
                'success' => false,
                'message' => 'Your account has been suspended',
            ], 403);
        }

        if (! $user->hasVerifiedEmail()) {
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => false,
                'code' => 'email_unverified',
                'message' => 'Please verify your email before signing in.',
                'data' => [
                    'token' => $token,
                    'user' => $this->userPayload($user),
                ],
            ], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'user' => $this->userPayload($user),
                'token' => $token,
            ],
        ]);
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create([
            'name' => $request->email,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'client',
            'status' => 'active',
            'onboarding_completed' => false,
        ]);

        $user->sendEmailVerificationNotification();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Account created. Please verify your email.',
            'data' => [
                'user' => $this->userPayload($user),
                'token' => $token,
            ],
        ], 201);
    }

    public function verifyEmail(Request $request, int $id, string $hash): Response
    {
        $user = User::findOrFail($id);

        abort_unless(
            hash_equals(sha1($user->getEmailForVerification()), $hash),
            403,
            'Invalid verification link.'
        );

        if (! $user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        return response(
            '<html><body style="font-family:sans-serif;text-align:center;padding:60px">
                <h2>✓ Email verified!</h2>
                <p>You can return to the CoreShop app now.</p>
            </body></html>',
            200,
            ['Content-Type' => 'text/html']
        );
    }

    public function resendVerification(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'success' => false,
                'message' => 'Email is already verified.',
            ], 422);
        }

        $user->sendEmailVerificationNotification();

        return response()->json([
            'success' => true,
            'message' => 'Verification email resent.',
        ]);
    }

    public function onboarding(OnboardingRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $user->update([
            'name' => $request->name,
            'role' => $request->role,
            'avatar' => $request->avatar,
            'city' => $request->city,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'interests' => $request->interests,
            'onboarding_completed' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Onboarding completed',
            'data' => $this->userPayload($user->fresh()),
        ]);
    }

    public function me(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->userPayload(Auth::user()),
        ]);
    }

    public function google(Request $request): JsonResponse
    {
        $request->validate([
            'access_token' => ['required', 'string'],
        ]);

        $googleResponse = Http::get('https://www.googleapis.com/oauth2/v2/userinfo', [
            'access_token' => $request->access_token,
        ]);

        if (! $googleResponse->ok()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid Google token.',
            ], 401);
        }

        $googleUser = $googleResponse->json();

        if (empty($googleUser['email'])) {
            return response()->json([
                'success' => false,
                'message' => 'Google account has no email address.',
            ], 422);
        }

        $user = User::firstOrCreate(
            ['email' => $googleUser['email']],
            [
                'name' => $googleUser['name'] ?? $googleUser['email'],
                'avatar' => $googleUser['picture'] ?? null,
                'password' => Hash::make(Str::random(32)),
                'role' => 'client',
                'status' => 'active',
                'onboarding_completed' => false,
                'email_verified_at' => now(),
            ]
        );

        if (! $user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        if ($user->wasRecentlyCreated === false && $googleUser['picture'] && $user->avatar !== $googleUser['picture']) {
            $user->update(['avatar' => $googleUser['picture']]);
        }

        if (! $user->isActive()) {
            return response()->json([
                'success' => false,
                'message' => 'Your account has been suspended.',
            ], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Signed in with Google',
            'data' => [
                'user' => $this->userPayload($user->fresh()),
                'token' => $token,
            ],
        ]);
    }

    public function savePushToken(Request $request): JsonResponse
    {
        $request->validate(['token' => ['required', 'string']]);

        Auth::user()->update(['expo_push_token' => $request->token]);

        return response()->json(['success' => true]);
    }

    public function forgotPassword(Request $request): JsonResponse
    {
        $request->validate(['email' => ['required', 'email']]);

        Password::sendResetLink($request->only('email'));

        return response()->json([
            'success' => true,
            'message' => 'If an account with that email exists, a reset link has been sent.',
        ]);
    }

    public function showResetForm(Request $request): Response
    {
        $token = $request->query('token', '');
        $email = $request->query('email', '');

        $html = <<<HTML
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8" />
            <meta name="viewport" content="width=device-width, initial-scale=1.0" />
            <title>Reset Password — CoreShop</title>
            <style>
                * { box-sizing: border-box; margin: 0; padding: 0; }
                body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #FAFAFA; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 24px; }
                .card { background: #fff; border-radius: 16px; padding: 40px 32px; width: 100%; max-width: 420px; box-shadow: 0 4px 24px rgba(0,0,0,0.08); }
                h1 { font-size: 22px; font-weight: 700; color: #0A0A0A; margin-bottom: 8px; }
                p { font-size: 14px; color: #6B7280; margin-bottom: 28px; }
                label { display: block; font-size: 13px; font-weight: 600; color: #0A0A0A; margin-bottom: 6px; }
                input { width: 100%; border: 1.5px solid #E5E7EB; border-radius: 10px; padding: 12px 14px; font-size: 15px; color: #0A0A0A; outline: none; transition: border-color 0.15s; margin-bottom: 16px; }
                input:focus { border-color: #0A0A0A; }
                button { width: 100%; background: #0A0A0A; color: #fff; border: none; border-radius: 10px; padding: 14px; font-size: 16px; font-weight: 600; cursor: pointer; margin-top: 4px; }
                button:hover { opacity: 0.88; }
                .error { background: #FEF2F2; border: 1px solid #FECACA; border-radius: 8px; padding: 12px 14px; font-size: 13px; color: #DC2626; margin-bottom: 16px; }
            </style>
        </head>
        <body>
            <div class="card">
                <h1>Reset your password</h1>
                <p>Enter a new password for your CoreShop account.</p>
                <form method="POST" action="/api/v1/auth/reset-password">
                    <input type="hidden" name="token" value="{$token}" />
                    <input type="hidden" name="email" value="{$email}" />
                    <label>New password</label>
                    <input type="password" name="password" placeholder="At least 8 characters" required minlength="8" />
                    <label>Confirm password</label>
                    <input type="password" name="password_confirmation" placeholder="Repeat your password" required minlength="8" />
                    <button type="submit">Set new password</button>
                </form>
            </div>
        </body>
        </html>
        HTML;

        return response($html, 200, ['Content-Type' => 'text/html']);
    }

    public function resetPassword(Request $request): Response
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'min:8', 'confirmed'],
            'password_confirmation' => ['required'],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill(['password' => Hash::make($password)])->save();
                $user->tokens()->delete();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            $html = <<<'HTML'
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8" />
                <meta name="viewport" content="width=device-width, initial-scale=1.0" />
                <title>Password Reset — CoreShop</title>
                <style>
                    * { box-sizing: border-box; margin: 0; padding: 0; }
                    body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #FAFAFA; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 24px; }
                    .card { background: #fff; border-radius: 16px; padding: 40px 32px; width: 100%; max-width: 420px; box-shadow: 0 4px 24px rgba(0,0,0,0.08); text-align: center; }
                    .icon { font-size: 48px; margin-bottom: 16px; }
                    h1 { font-size: 22px; font-weight: 700; color: #0A0A0A; margin-bottom: 8px; }
                    p { font-size: 14px; color: #6B7280; line-height: 1.6; }
                </style>
            </head>
            <body>
                <div class="card">
                    <div class="icon">✓</div>
                    <h1>Password updated!</h1>
                    <p>Your password has been reset successfully.<br/>You can now sign in to the CoreShop app with your new password.</p>
                </div>
            </body>
            </html>
            HTML;

            return response($html, 200, ['Content-Type' => 'text/html']);
        }

        $token = $request->input('token', '');
        $email = $request->input('email', '');
        $errorMsg = $status === Password::INVALID_TOKEN ? 'This reset link has expired or is invalid. Please request a new one.' : 'No account found with that email address.';

        $html = <<<HTML
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8" />
            <meta name="viewport" content="width=device-width, initial-scale=1.0" />
            <title>Reset Password — CoreShop</title>
            <style>
                * { box-sizing: border-box; margin: 0; padding: 0; }
                body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #FAFAFA; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 24px; }
                .card { background: #fff; border-radius: 16px; padding: 40px 32px; width: 100%; max-width: 420px; box-shadow: 0 4px 24px rgba(0,0,0,0.08); }
                h1 { font-size: 22px; font-weight: 700; color: #0A0A0A; margin-bottom: 8px; }
                p { font-size: 14px; color: #6B7280; margin-bottom: 28px; }
                label { display: block; font-size: 13px; font-weight: 600; color: #0A0A0A; margin-bottom: 6px; }
                input { width: 100%; border: 1.5px solid #E5E7EB; border-radius: 10px; padding: 12px 14px; font-size: 15px; color: #0A0A0A; outline: none; transition: border-color 0.15s; margin-bottom: 16px; }
                input:focus { border-color: #0A0A0A; }
                button { width: 100%; background: #0A0A0A; color: #fff; border: none; border-radius: 10px; padding: 14px; font-size: 16px; font-weight: 600; cursor: pointer; margin-top: 4px; }
                button:hover { opacity: 0.88; }
                .error { background: #FEF2F2; border: 1px solid #FECACA; border-radius: 8px; padding: 12px 14px; font-size: 13px; color: #DC2626; margin-bottom: 16px; }
            </style>
        </head>
        <body>
            <div class="card">
                <h1>Reset your password</h1>
                <p>Enter a new password for your CoreShop account.</p>
                <div class="error">{$errorMsg}</div>
                <form method="POST" action="/api/v1/auth/reset-password">
                    <input type="hidden" name="token" value="{$token}" />
                    <input type="hidden" name="email" value="{$email}" />
                    <label>New password</label>
                    <input type="password" name="password" placeholder="At least 8 characters" required minlength="8" />
                    <label>Confirm password</label>
                    <input type="password" name="password_confirmation" placeholder="Repeat your password" required minlength="8" />
                    <button type="submit">Set new password</button>
                </form>
            </div>
        </body>
        </html>
        HTML;

        return response($html, 422, ['Content-Type' => 'text/html']);
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $user = Auth::user();

        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email', 'max:255', 'unique:users,email,'.$user->id],
        ]);

        $user->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Profile updated.',
            'data' => $this->userPayload($user->fresh()),
        ]);
    }

    public function changePassword(Request $request): JsonResponse
    {
        $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = Auth::user();

        if (! Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect.',
            ], 422);
        }

        $user->update(['password' => Hash::make($request->password)]);

        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully.',
        ]);
    }

    public function logout(): JsonResponse
    {
        Auth::user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully',
        ]);
    }

    private function userPayload(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'status' => $user->status,
            'avatar' => $user->avatar,
            'city' => $user->city,
            'latitude' => $user->latitude,
            'longitude' => $user->longitude,
            'interests' => $user->interests,
            'email_verified_at' => $user->email_verified_at,
            'onboarding_completed' => (bool) $user->onboarding_completed,
        ];
    }
}
