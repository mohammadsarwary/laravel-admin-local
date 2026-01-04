<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next, ?string $role = null): Response
    {
        $user = $request->user();

        if (!$user) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Authentication required',
                ], 401);
            }
            return redirect()->route('login');
        }

        if (!$user->is_admin) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Admin access required',
                ], 403);
            }
            return redirect()->route('login')->with('error', 'Admin access required');
        }

        if (!$user->is_active) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Account is deactivated',
                ], 403);
            }
            return redirect()->route('login')->with('error', 'Account is deactivated');
        }

        if ($role && !$user->hasAdminRole($role)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => "Insufficient permissions. {$role} role required.",
                ], 403);
            }
            return redirect()->route('login')->with('error', "Insufficient permissions. {$role} role required.");
        }

        return $next($request);
    }
}
