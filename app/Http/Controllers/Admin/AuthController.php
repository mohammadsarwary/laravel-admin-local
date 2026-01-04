<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', strtolower($request->email))->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->error('Invalid email or password', 401);
        }

        if (!$user->is_admin) {
            return $this->error('Access denied. Admin privileges required.', 403);
        }

        if (!$user->is_active) {
            return $this->error('Account is deactivated', 403);
        }

        $user->update(['last_login' => now()]);

        $token = $user->createToken('admin_access_token', ['admin'], now()->addHour());
        $refreshToken = $user->createToken('admin_refresh_token', ['admin', 'refresh'], now()->addDays(30));

        return $this->success([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'avatar' => $user->avatar,
                'is_admin' => $user->is_admin,
                'admin_role' => $user->admin_role,
            ],
            'access_token' => $token->plainTextToken,
            'refresh_token' => $refreshToken->plainTextToken,
            'token' => $token->plainTextToken,
            'token_type' => 'Bearer',
            'expires_in' => 3600,
        ], 'Admin login successful');
    }

    public function verify(Request $request): JsonResponse
    {
        $user = $request->user();

        return $this->success([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'avatar' => $user->avatar,
                'is_admin' => $user->is_admin,
                'admin_role' => $user->admin_role,
            ],
            'admin_role' => $user->admin_role,
        ], 'Admin session valid');
    }
}
