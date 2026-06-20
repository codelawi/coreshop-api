<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\OnboardingRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
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

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Account created successfully',
            'data' => [
                'user' => $this->userPayload($user),
                'token' => $token,
            ],
        ], 201);
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
            ]
        );

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
