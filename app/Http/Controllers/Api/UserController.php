<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdateProfileRequest;
use App\Models\Ad;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function show(User $user): JsonResponse
    {
        if (!$user->is_active) {
            return $this->notFound('User not found');
        }

        return $this->success([
            'id' => $user->id,
            'name' => $user->name,
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
        ]);
    }

    public function updateProfile(UpdateProfileRequest $request): JsonResponse
    {
        $user = $request->user();

        $user->update($request->only(['name', 'phone', 'bio', 'location']));

        return $this->success([
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
        ], 'Profile updated successfully');
    }

    public function updateAvatar(Request $request): JsonResponse
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $user = $request->user();

        $path = $request->file('avatar')->store('avatars', 'public');
        $avatarUrl = Storage::url($path);

        $user->update(['avatar' => $avatarUrl]);

        return $this->success(['avatar' => $avatarUrl], 'Avatar updated successfully');
    }

    public function changePassword(Request $request): JsonResponse
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:6',
        ]);

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return $this->error('Current password is incorrect', 400);
        }

        $user->update(['password' => Hash::make($request->new_password)]);

        return $this->success(null, 'Password changed successfully');
    }

    public function deleteAccount(Request $request): JsonResponse
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        $user = $request->user();

        if (!Hash::check($request->password, $user->password)) {
            return $this->error('Password is incorrect', 400);
        }

        $user->update(['is_active' => false]);
        $user->tokens()->delete();

        return $this->success(null, 'Account deleted successfully');
    }

    public function favorites(Request $request): JsonResponse
    {
        $user = $request->user();

        $favorites = Ad::query()
            ->active()
            ->whereHas('favorites', fn($q) => $q->where('user_id', $user->id))
            ->with(['category:id,name', 'primaryImage'])
            ->orderByDesc('created_at')
            ->get()
            ->map(fn($ad) => [
                'id' => $ad->id,
                'title' => $ad->title,
                'price' => $ad->price,
                'location' => $ad->location,
                'category_name' => $ad->category?->name,
                'primary_image' => $ad->primaryImage?->image_url,
                'created_at' => $ad->created_at,
            ]);

        return $this->success($favorites);
    }

    public function ads(User $user): JsonResponse
    {
        $status = request('status', 'active');

        $ads = $user->ads()
            ->where('status', $status)
            ->with(['category:id,name', 'primaryImage'])
            ->orderByDesc('created_at')
            ->get()
            ->map(fn($ad) => [
                'id' => $ad->id,
                'title' => $ad->title,
                'price' => $ad->price,
                'location' => $ad->location,
                'condition' => $ad->condition,
                'views' => $ad->views,
                'category_name' => $ad->category?->name,
                'primary_image' => $ad->primaryImage?->image_url,
                'created_at' => $ad->created_at,
            ]);

        return $this->success($ads);
    }
}
