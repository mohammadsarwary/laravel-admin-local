<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

class AdminSessionMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Check if token is in session
        $token = $request->session()->get('admin_token');
        
        if ($token) {
            // Verify token and authenticate user
            $personalAccessToken = PersonalAccessToken::findToken($token);
            
            if ($personalAccessToken && $personalAccessToken->tokenable) {
                $user = $personalAccessToken->tokenable;
                
                // Check if user is admin and active
                if ($user->is_admin && $user->is_active) {
                    // Set the user on the request
                    $request->setUserResolver(function () use ($user) {
                        return $user;
                    });
                    
                    return $next($request);
                }
            }
        }
        
        // No valid session, redirect to login
        return redirect()->route('login');
    }
}
