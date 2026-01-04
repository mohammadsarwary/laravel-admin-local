<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ad;
use App\Models\AdminLog;
use App\Models\Message;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $page = max(1, (int) $request->get('page', 1));
        $limit = min(100, max(1, (int) $request->get('limit', 20)));
        $search = $request->get('search');
        $status = $request->get('status');

        $query = User::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($status === 'active') {
            $query->where('is_active', true);
        } elseif ($status === 'inactive') {
            $query->where('is_active', false);
        } elseif ($status === 'verified') {
            $query->where('is_verified', true);
        } elseif ($status === 'admin') {
            $query->where('is_admin', true);
        }

        $total = $query->count();

        $users = $query->select([
            'id', 'name', 'email', 'phone', 'location', 'rating',
            'active_listings', 'is_verified', 'is_active', 'is_admin',
            'admin_role', 'created_at', 'last_login'
        ])
        ->orderByDesc('created_at')
        ->offset(($page - 1) * $limit)
        ->limit($limit)
        ->get();

        $stats = [
            'total' => User::count(),
            'active' => User::where('is_active', true)->count(),
            'verified' => User::where('is_verified', true)->count(),
            'inactive' => User::where('is_active', false)->count(),
        ];

        return $this->success([
            'users' => $users,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $limit,
                'total' => $total,
                'total_pages' => ceil($total / $limit),
                'from' => ($page - 1) * $limit + 1,
                'to' => min($page * $limit, $total),
            ],
            'stats' => $stats,
        ]);
    }

    public function show(User $user): JsonResponse
    {
        $user->total_ads = Ad::where('user_id', $user->id)->count();
        $user->active_ads_count = Ad::where('user_id', $user->id)->where('status', 'active')->count();
        $user->recent_ads = Ad::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return $this->success($user);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'phone' => 'nullable|string|max:20',
            'location' => 'nullable|string|max:100',
            'bio' => 'nullable|string|max:500',
            'is_verified' => 'nullable|boolean',
            'status' => 'nullable|in:active,inactive',
            'role' => 'nullable|string',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => strtolower($request->email),
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'location' => $request->location,
            'bio' => $request->bio,
            'is_verified' => $request->boolean('is_verified'),
            'is_active' => $request->get('status', 'active') === 'active',
        ]);

        AdminLog::log($request->user(), 'create_user', $user->id, 'user', 'User created by admin');

        return $this->success(['user_id' => $user->id], 'User created successfully', 201);
    }

    public function suspend(Request $request, User $user): JsonResponse
    {
        $reason = $request->get('reason', 'Suspended by admin');

        $user->update(['is_active' => false]);

        AdminLog::log($request->user(), 'suspend_user', $user->id, 'user', $reason);

        return $this->success(null, 'User suspended successfully');
    }

    public function activate(Request $request, User $user): JsonResponse
    {
        $user->update(['is_active' => true]);

        AdminLog::log($request->user(), 'activate_user', $user->id, 'user', 'User activated');

        return $this->success(null, 'User activated successfully');
    }

    public function ban(Request $request, User $user): JsonResponse
    {
        $reason = $request->get('reason', 'Banned by admin');

        $user->update(['is_active' => false]);

        AdminLog::log($request->user(), 'ban_user', $user->id, 'user', $reason);

        return $this->success(null, 'User banned successfully');
    }

    public function verifyUser(Request $request, User $user): JsonResponse
    {
        $user->update(['is_verified' => true]);

        AdminLog::log($request->user(), 'verify_user', $user->id, 'user', 'User verified');

        return $this->success(null, 'User verified successfully');
    }

    public function destroy(Request $request, User $user): JsonResponse
    {
        $admin = $request->user();

        if ($admin->admin_role !== 'super_admin') {
            return $this->error('Only super admins can delete users', 403);
        }

        $reason = $request->get('reason', 'Deleted by admin');

        AdminLog::log($admin, 'delete_user', $user->id, 'user', $reason);

        $user->delete();

        return $this->success(null, 'User deleted successfully');
    }

    public function bulkAction(Request $request): JsonResponse
    {
        $request->validate([
            'action' => 'required|in:activate,suspend,ban',
            'user_ids' => 'required|array',
            'user_ids.*' => 'integer|exists:users,id',
        ]);

        $action = $request->action;
        $userIds = $request->user_ids;

        $updateData = match ($action) {
            'activate' => ['is_active' => true],
            'suspend', 'ban' => ['is_active' => false],
        };

        User::whereIn('id', $userIds)->update($updateData);

        AdminLog::log(
            $request->user(),
            "bulk_{$action}",
            null,
            'user',
            "Bulk action on " . count($userIds) . " users"
        );

        return $this->success(null, count($userIds) . ' users updated successfully');
    }

    public function activity(User $user): JsonResponse
    {
        $ads = Ad::where('user_id', $user->id)
            ->select(DB::raw("'ad_created' as type"), 'id', 'title as description', 'created_at')
            ->orderByDesc('created_at')
            ->limit(50);

        $messages = Message::where('sender_id', $user->id)
            ->select(DB::raw("'message_sent' as type"), 'id', 'message as description', 'created_at')
            ->orderByDesc('created_at')
            ->limit(50);

        $reviews = Review::where('reviewer_id', $user->id)
            ->select(DB::raw("'review_given' as type"), 'id', 'comment as description', 'created_at')
            ->orderByDesc('created_at')
            ->limit(50);

        $activity = $ads->union($messages)->union($reviews)
            ->orderByDesc('created_at')
            ->limit(50)
            ->get();

        return $this->success($activity);
    }

    public function export(): JsonResponse
    {
        $users = User::select([
            'id', 'name', 'email', 'phone', 'location', 'rating',
            'active_listings', 'is_verified', 'is_active', 'created_at'
        ])
        ->orderByDesc('created_at')
        ->get();

        return response()->json($users)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="users_export_' . date('Y-m-d') . '.csv"');
    }
}
