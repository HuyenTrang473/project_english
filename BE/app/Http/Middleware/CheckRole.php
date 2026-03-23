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

        // Map role numbers to role names for backward compatibility
        $roleMap = [
            1 => 'giao_vien',
            2 => 'admin',
            3 => 'hoc_sinh',
        ];

        // Get user's role - could be string or integer
        $userRole = $user->role;
        if (is_numeric($userRole) && isset($roleMap[$userRole])) {
            $userRole = $roleMap[$userRole];
        }

        // Check if user's role is in the required roles
        if (!in_array($userRole, $roles)) {
            return response()->json([
                'message' => 'Forbidden - Insufficient permissions',
                'debug' => [
                    'user_role' => $user->role,
                    'required_roles' => $roles,
                ]
            ], 403);
        }

        return $next($request);
    }
}
