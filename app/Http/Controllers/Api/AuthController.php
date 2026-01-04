<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create([
            'name' => trim($request->name),
            'email' => strtolower(trim($request->email)),
            'phone' => $request->phone,
            'password' => $request->password,
            'location' => $request->location,
        ]);

        $token = $user->createToken('access_token', ['*'], now()->addHour());
        $refreshToken = $user->createToken('refresh_token', ['refresh'], now()->addDays(30));

        return $this->success([
            'user' => $this->formatUser($user),
            'access_token' => $token->plainTextToken,
            'refresh_token' => $refreshToken->plainTextToken,
            'token_type' => 'Bearer',
            'expires_in' => 3600,
        ], 'Registration successful', 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', strtolower($request->email))->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->error('Invalid email or password', 401);
        }

        if (!$user->is_active) {
            return $this->error('Account is deactivated', 403);
        }

        $user->update(['last_login' => now()]);

        $token = $user->createToken('access_token', ['*'], now()->addHour());
        $refreshToken = $user->createToken('refresh_token', ['refresh'], now()->addDays(30));

        return $this->success([
            'user' => $this->formatUser($user),
            'access_token' => $token->plainTextToken,
            'refresh_token' => $refreshToken->plainTextToken,
            'token_type' => 'Bearer',
            'expires_in' => 3600,
        ], 'Login successful');
    }

    public function refresh(Request $request): JsonResponse
    {
        $request->validate([
            'refresh_token' => 'required|string',
        ]);

        $user = $request->user();

        if (!$user) {
            return $this->error('Invalid or expired refresh token', 401);
        }

        $token = $user->createToken('access_token', ['*'], now()->addHour());

        return $this->success([
            'access_token' => $token->plainTextToken,
            'token_type' => 'Bearer',
            'expires_in' => 3600,
        ], 'Token refreshed successfully');
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return $this->success(null, 'Logout successful');
    }

    public function me(Request $request): JsonResponse
    {
        return $this->success($this->formatUser($request->user()));
    }

    private function formatUser(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'avatar' => $user->avatar,
            'bio' => $user->bio,
            'location' => $user->location,
            'rating' => $user->rating,
            'review_count' => $user->review_count,
            'active_listings' => $user->active_listings,
            'sold_items' => $user->sold_items,
            'followers' => $user->followers,
            'is_verified' => $user->is_verified,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
        ];
    }
}
