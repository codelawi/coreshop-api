<?php

namespace App\Http\Middleware;

use App\Models\SecurityEvent;
use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): mixed
    {
        if (! $request->user() || ! in_array($request->user()->role, $roles)) {
            SecurityEvent::log('unauthorized', $request, [
                'role' => $request->user()?->role,
                'required' => $roles,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Unauthorized — insufficient permissions',
            ], 403);
        }

        return $next($request);
    }
}
