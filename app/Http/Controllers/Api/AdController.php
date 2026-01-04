<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Ad\StoreAdRequest;
use App\Http\Requests\Ad\UpdateAdRequest;
use App\Models\Ad;
use App\Models\Favorite;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Ad::query()->active();

        if ($request->filled('category_id')) {
            $query->byCategory($request->category_id);
        }

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('min_price') || $request->filled('max_price')) {
            $query->priceRange($request->min_price, $request->max_price);
        }

        if ($request->filled('location')) {
            $query->byLocation($request->location);
        }

        if ($request->filled('condition')) {
            $query->where('condition', $request->condition);
        }

        $sortMap = [
            'newest' => ['created_at', 'desc'],
            'oldest' => ['created_at', 'asc'],
            'price_asc' => ['price', 'asc'],
            'price_desc' => ['price', 'desc'],
        ];

        $sort = $sortMap[$request->get('sort', 'newest')] ?? $sortMap['newest'];
        $query->orderBy($sort[0], $sort[1]);

        $page = max(1, (int) $request->get('page', 1));
        $limit = min(100, max(1, (int) $request->get('limit', 20)));

        $ads = $query->with(['user:id,name,avatar,rating', 'category:id,name', 'primaryImage'])
            ->paginate($limit, ['*'], 'page', $page);

        return $this->success([
            'ads' => $ads->items(),
            'page' => $ads->currentPage(),
            'limit' => $ads->perPage(),
            'total' => $ads->total(),
        ]);
    }

    public function show(Ad $ad, Request $request): JsonResponse
    {
        if ($ad->status !== 'active') {
            return $this->notFound('Ad not found');
        }

        $ad->incrementViews();

        $ad->load(['user:id,name,avatar,rating,review_count,created_at', 'category:id,name', 'images']);

        $user = $request->user();
        $isFavorited = $user ? $ad->isFavoritedBy($user) : false;

        return $this->success([
            'id' => $ad->id,
            'title' => $ad->title,
            'description' => $ad->description,
            'price' => $ad->price,
            'condition' => $ad->condition,
            'location' => $ad->location,
            'latitude' => $ad->latitude,
            'longitude' => $ad->longitude,
            'views' => $ad->views,
            'favorites' => $ad->favorites,
            'is_promoted' => $ad->is_promoted,
            'is_featured' => $ad->is_featured,
            'created_at' => $ad->created_at,
            'user_id' => $ad->user_id,
            'user_name' => $ad->user?->name,
            'user_avatar' => $ad->user?->avatar,
            'user_rating' => $ad->user?->rating,
            'user_review_count' => $ad->user?->review_count,
            'user_member_since' => $ad->user?->created_at,
            'category_name' => $ad->category?->name,
            'images' => $ad->images->map(fn($img) => [
                'image_url' => $img->image_url,
                'is_primary' => $img->is_primary,
            ]),
            'is_favorited' => $isFavorited,
        ]);
    }

    public function store(StoreAdRequest $request): JsonResponse
    {
        $user = $request->user();

        $ad = Ad::create([
            'user_id' => $user->id,
            'category_id' => $request->category_id,
            'title' => trim($request->title),
            'description' => trim($request->description),
            'price' => $request->price,
            'condition' => $request->condition ?? 'good',
            'location' => trim($request->location),
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        $user->incrementStat('active_listings');

        $ad->load(['category:id,name']);

        return $this->success([
            'id' => $ad->id,
            'title' => $ad->title,
            'description' => $ad->description,
            'price' => $ad->price,
            'condition' => $ad->condition,
            'location' => $ad->location,
            'category_name' => $ad->category?->name,
            'created_at' => $ad->created_at,
        ], 'Ad created successfully', 201);
    }

    public function update(UpdateAdRequest $request, Ad $ad): JsonResponse
    {
        $ad->update($request->only([
            'title', 'description', 'price', 'condition', 'location', 'category_id'
        ]));

        $ad->load(['category:id,name', 'images']);

        return $this->success([
            'id' => $ad->id,
            'title' => $ad->title,
            'description' => $ad->description,
            'price' => $ad->price,
            'condition' => $ad->condition,
            'location' => $ad->location,
            'category_name' => $ad->category?->name,
            'images' => $ad->images,
            'updated_at' => $ad->updated_at,
        ], 'Ad updated successfully');
    }

    public function destroy(Request $request, Ad $ad): JsonResponse
    {
        $user = $request->user();

        if ($ad->user_id !== $user->id) {
            return $this->forbidden("You don't have permission to delete this ad");
        }

        $ad->update(['status' => 'deleted']);
        $user->incrementStat('active_listings', -1);

        return $this->success(null, 'Ad deleted successfully');
    }

    public function markAsSold(Request $request, Ad $ad): JsonResponse
    {
        $user = $request->user();

        if ($ad->user_id !== $user->id) {
            return $this->forbidden("You don't have permission to update this ad");
        }

        $ad->markAsSold();
        $user->incrementStat('active_listings', -1);
        $user->incrementStat('sold_items');

        return $this->success(null, 'Ad marked as sold');
    }

    public function uploadImages(Request $request, Ad $ad): JsonResponse
    {
        $user = $request->user();

        if ($ad->user_id !== $user->id) {
            return $this->forbidden("You don't have permission to upload images for this ad");
        }

        $request->validate([
            'images' => 'required|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $uploadedImages = [];
        $existingImages = $ad->images()->count();

        foreach ($request->file('images') as $index => $file) {
            $path = $file->store('ads', 'public');
            $imageUrl = Storage::url($path);

            $ad->images()->create([
                'image_url' => $imageUrl,
                'display_order' => $existingImages + $index,
                'is_primary' => $existingImages === 0 && $index === 0,
            ]);

            $uploadedImages[] = $imageUrl;
        }

        return $this->success(['images' => $uploadedImages], 'Images uploaded successfully');
    }

    public function toggleFavorite(Request $request, Ad $ad): JsonResponse
    {
        $user = $request->user();

        $favorite = Favorite::where('user_id', $user->id)
            ->where('ad_id', $ad->id)
            ->first();

        if ($favorite) {
            $favorite->delete();
            return $this->success(['is_favorited' => false], 'Removed from favorites');
        }

        Favorite::create([
            'user_id' => $user->id,
            'ad_id' => $ad->id,
        ]);

        return $this->success(['is_favorited' => true], 'Added to favorites');
    }
}
