<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ad;
use App\Models\AdminLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $page = max(1, (int) $request->get('page', 1));
        $limit = min(100, max(1, (int) $request->get('limit', 20)));
        $status = $request->get('status');
        $category = $request->get('category');
        $search = $request->get('search');

        $query = Ad::query()
            ->with(['user:id,name,email', 'category:id,name']);

        if ($status) {
            $query->where('status', $status);
        }

        if ($category) {
            $query->where('category_id', $category);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $total = $query->count();

        $ads = $query->orderByDesc('created_at')
            ->offset(($page - 1) * $limit)
            ->limit($limit)
            ->get()
            ->map(fn($ad) => [
                'id' => $ad->id,
                'title' => $ad->title,
                'description' => $ad->description,
                'price' => $ad->price,
                'condition' => $ad->condition,
                'location' => $ad->location,
                'status' => $ad->status,
                'views' => $ad->views,
                'is_featured' => $ad->is_featured,
                'is_promoted' => $ad->is_promoted,
                'created_at' => $ad->created_at,
                'user_id' => $ad->user_id,
                'user_name' => $ad->user?->name,
                'user_email' => $ad->user?->email,
                'category_id' => $ad->category_id,
                'category_name' => $ad->category?->name,
            ]);

        return $this->success([
            'ads' => $ads,
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'total' => $total,
                'pages' => ceil($total / $limit),
            ],
        ]);
    }

    public function show(Ad $ad): JsonResponse
    {
        $ad->load(['user:id,name,email,phone', 'category:id,name', 'images']);

        return $this->success([
            'id' => $ad->id,
            'title' => $ad->title,
            'description' => $ad->description,
            'price' => $ad->price,
            'condition' => $ad->condition,
            'location' => $ad->location,
            'latitude' => $ad->latitude,
            'longitude' => $ad->longitude,
            'status' => $ad->status,
            'views' => $ad->views,
            'favorites' => $ad->favorites,
            'is_featured' => $ad->is_featured,
            'is_promoted' => $ad->is_promoted,
            'promoted_until' => $ad->promoted_until,
            'created_at' => $ad->created_at,
            'updated_at' => $ad->updated_at,
            'user_id' => $ad->user_id,
            'user_name' => $ad->user?->name,
            'user_email' => $ad->user?->email,
            'user_phone' => $ad->user?->phone,
            'category_id' => $ad->category_id,
            'category_name' => $ad->category?->name,
            'images' => $ad->images,
        ]);
    }

    public function update(Request $request, Ad $ad): JsonResponse
    {
        // Delete specified images
        if ($request->has('images_to_delete') && is_array($request->images_to_delete)) {
            $ad->images()->whereIn('id', $request->images_to_delete)->delete();
        }

        // Update ad fields
        $ad->update($request->only([
            'title', 'description', 'price', 'category_id', 'status'
        ]));

        AdminLog::log($request->user(), 'update_ad', $ad->id, 'ad', 'Ad updated');

        return $this->success(null, 'Ad updated successfully');
    }

    public function approve(Request $request, Ad $ad): JsonResponse
    {
        // Accept status parameter to support toggle between active/inactive
        $status = $request->get('status', 'active');

        if ($status === 'active') {
            $ad->approve();
            AdminLog::log($request->user(), 'approve_ad', $ad->id, 'ad', 'Ad approved');
        } else {
            $ad->update(['status' => $status]);
            AdminLog::log($request->user(), 'update_ad_status', $ad->id, 'ad', "Ad status changed to {$status}");
        }

        return $this->success(null, "Ad status updated to {$status}");
    }

    public function reject(Request $request, Ad $ad): JsonResponse
    {
        $reason = $request->get('reason', 'Rejected by admin');

        $ad->reject($reason);

        AdminLog::log($request->user(), 'reject_ad', $ad->id, 'ad', $reason);

        return $this->success(null, 'Ad rejected successfully');
    }

    public function feature(Request $request, Ad $ad): JsonResponse
    {
        $featured = $request->boolean('featured', true);

        $ad->feature($featured);

        $action = $featured ? 'Featured' : 'Unfeatured';
        AdminLog::log($request->user(), 'feature_ad', $ad->id, 'ad', "{$action} ad");

        return $this->success(null, "Ad {$action} successfully");
    }

    public function promote(Request $request, Ad $ad): JsonResponse
    {
        $days = max(1, (int) $request->get('days', 7));

        $ad->promote($days);

        AdminLog::log($request->user(), 'promote_ad', $ad->id, 'ad', "Promoted for {$days} days");

        return $this->success(null, 'Ad promoted successfully');
    }

    public function destroy(Request $request, Ad $ad): JsonResponse
    {
        $reason = $request->get('reason', 'Deleted by admin');

        AdminLog::log($request->user(), 'delete_ad', $ad->id, 'ad', $reason);

        $ad->delete();

        return $this->success(null, 'Ad deleted successfully');
    }

    public function bulkAction(Request $request): JsonResponse
    {
        $request->validate([
            'action' => 'required|in:approve,reject,delete',
            'ad_ids' => 'required|array',
            'ad_ids.*' => 'integer|exists:ads,id',
        ]);

        $action = $request->action;
        $adIds = $request->ad_ids;

        switch ($action) {
            case 'approve':
                Ad::whereIn('id', $adIds)->update([
                    'status' => 'active',
                    'approved_at' => now(),
                ]);
                break;
            case 'reject':
                Ad::whereIn('id', $adIds)->update(['status' => 'rejected']);
                break;
            case 'delete':
                Ad::whereIn('id', $adIds)->delete();
                break;
        }

        AdminLog::log(
            $request->user(),
            "bulk_{$action}",
            null,
            'ad',
            "Bulk action on " . count($adIds) . " ads"
        );

        return $this->success(null, count($adIds) . ' ads updated successfully');
    }

    public function export()
    {
        $ads = Ad::with(['user:id,name', 'category:id,name'])
            ->select([
                'id', 'title', 'price', 'condition', 'location', 'status',
                'views', 'user_id', 'category_id', 'created_at'
            ])
            ->orderByDesc('created_at')
            ->get();

        $headers = ['ID', 'Title', 'Price', 'Condition', 'Location', 'Status', 'Views', 'User Name', 'Category Name', 'Created At'];
        
        $callback = function() use ($ads, $headers) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, $headers);
            
            foreach ($ads as $ad) {
                fputcsv($file, [
                    $ad->id,
                    $ad->title,
                    $ad->price,
                    $ad->condition,
                    $ad->location,
                    $ad->status,
                    $ad->views,
                    $ad->user?->name,
                    $ad->category?->name,
                    $ad->created_at,
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="ads_export_' . date('Y-m-d') . '.csv"',
        ]);
    }
}
