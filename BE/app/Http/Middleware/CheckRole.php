<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!auth('sanctum')->check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $user = auth('sanctum')->user();

        if (!$user->is_active) {
            return response()->json(['message' => 'Account is inactive'], 403);
        }

        if (!in_array($user->role, $roles)) {
            return response()->json(['message' => 'Forbidden - Insufficient permissions'], 403);
        }

        return $next($request);
    }
}
