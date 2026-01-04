<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ad;
use App\Models\Report;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function stats(): JsonResponse
    {
        $stats = [
            'total_users' => User::count(),
            'active_users' => User::where('is_active', true)->count(),
            'total_ads' => Ad::count(),
            'active_ads' => Ad::where('status', 'active')->count(),
            'pending_ads' => Ad::where('status', 'pending')->count(),
            'total_reports' => Report::count(),
            'pending_reports' => Report::where('status', 'pending')->count(),
            'new_users_today' => User::whereDate('created_at', today())->count(),
            'new_ads_today' => Ad::whereDate('created_at', today())->count(),
            'new_users_week' => User::where('created_at', '>=', now()->subDays(7))->count(),
            'new_ads_week' => Ad::where('created_at', '>=', now()->subDays(7))->count(),
        ];

        return $this->success($stats);
    }

    public function activity(Request $request): JsonResponse
    {
        $limit = min(50, max(1, (int) $request->get('limit', 20)));

        $users = User::select(
            DB::raw("'user' as type"),
            'id',
            'name as title',
            'email as subtitle',
            'created_at'
        )->orderByDesc('created_at')->limit($limit);

        $ads = Ad::select(
            DB::raw("'ad' as type"),
            'id',
            'title',
            DB::raw("CONCAT('Price: $', price) as subtitle"),
            'created_at'
        )->orderByDesc('created_at')->limit($limit);

        $reports = Report::select(
            DB::raw("'report' as type"),
            'id',
            'reason as title',
            'reported_type as subtitle',
            'created_at'
        )->orderByDesc('created_at')->limit($limit);

        $activity = $users->union($ads)->union($reports)
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();

        return $this->success($activity);
    }
}
