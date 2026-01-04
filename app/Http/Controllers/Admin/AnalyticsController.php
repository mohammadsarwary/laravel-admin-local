<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ad;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function users(Request $request): JsonResponse
    {
        $period = $request->get('period', '30days');
        $days = $this->parsePeriod($period);

        $data = User::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays($days))
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get();

        return $this->success($data);
    }

    public function ads(Request $request): JsonResponse
    {
        $period = $request->get('period', '7days');
        $days = $this->parsePeriod($period);

        $data = Ad::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays($days))
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get();

        return $this->success($data);
    }

    public function categories(): JsonResponse
    {
        $data = Category::leftJoin('ads', 'categories.id', '=', 'ads.category_id')
            ->selectRaw('categories.name, COUNT(ads.id) as count')
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('count')
            ->get();

        return $this->success($data);
    }

    public function locations(): JsonResponse
    {
        $data = Ad::selectRaw('location, COUNT(*) as count')
            ->whereNotNull('location')
            ->groupBy('location')
            ->orderByDesc('count')
            ->limit(20)
            ->get();

        return $this->success($data);
    }

    private function parsePeriod(string $period): int
    {
        if (preg_match('/(\d+)days?/', $period, $matches)) {
            return (int) $matches[1];
        }
        return 30;
    }
}
