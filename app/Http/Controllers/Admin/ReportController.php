<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ad;
use App\Models\AdminLog;
use App\Models\Report;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $page = max(1, (int) $request->get('page', 1));
        $limit = min(100, max(1, (int) $request->get('limit', 20)));
        $status = $request->get('status');
        $type = $request->get('type');

        $query = Report::query()
            ->with(['reporter:id,name,email']);

        if ($status) {
            $query->where('status', $status);
        }

        if ($type) {
            $query->where('reported_type', $type);
        }

        $total = $query->count();

        $reports = $query->orderByDesc('created_at')
            ->offset(($page - 1) * $limit)
            ->limit($limit)
            ->get()
            ->map(fn($report) => [
                'id' => $report->id,
                'reported_type' => $report->reported_type,
                'reported_id' => $report->reported_id,
                'reason' => $report->reason,
                'description' => $report->description,
                'status' => $report->status,
                'created_at' => $report->created_at,
                'reporter_id' => $report->reporter_id,
                'reporter_name' => $report->reporter?->name,
                'reporter_email' => $report->reporter?->email,
            ]);

        return $this->success([
            'reports' => $reports,
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'total' => $total,
                'pages' => ceil($total / $limit),
            ],
        ]);
    }

    public function show(Report $report): JsonResponse
    {
        $report->load(['reporter:id,name,email']);

        $reportedContent = null;
        if ($report->reported_type === 'ad') {
            $reportedContent = Ad::find($report->reported_id);
        } elseif ($report->reported_type === 'user') {
            $reportedContent = User::select('id', 'name', 'email', 'phone')
                ->find($report->reported_id);
        }

        return $this->success([
            'id' => $report->id,
            'reported_type' => $report->reported_type,
            'reported_id' => $report->reported_id,
            'reason' => $report->reason,
            'description' => $report->description,
            'status' => $report->status,
            'created_at' => $report->created_at,
            'updated_at' => $report->updated_at,
            'reporter_id' => $report->reporter_id,
            'reporter_name' => $report->reporter?->name,
            'reporter_email' => $report->reporter?->email,
            'reported_content' => $reportedContent,
        ]);
    }

    public function stats(): JsonResponse
    {
        $stats = [
            'total_reports' => Report::count(),
            'pending_reports' => Report::where('status', 'pending')->count(),
            'resolved_reports' => Report::where('status', 'resolved')->count(),
            'dismissed_reports' => Report::where('status', 'dismissed')->count(),
            'reports_by_type' => Report::selectRaw('reported_type, COUNT(*) as count')
                ->groupBy('reported_type')
                ->get(),
        ];

        return $this->success($stats);
    }

    public function resolve(Request $request, Report $report): JsonResponse
    {
        $action = $request->get('action', 'resolved');

        $report->resolve();

        AdminLog::log($request->user(), 'resolve_report', $report->id, 'report', "Report resolved: {$action}");

        return $this->success(null, 'Report resolved successfully');
    }

    public function dismiss(Request $request, Report $report): JsonResponse
    {
        $reason = $request->get('reason', 'Dismissed by admin');

        $report->dismiss();

        AdminLog::log($request->user(), 'dismiss_report', $report->id, 'report', $reason);

        return $this->success(null, 'Report dismissed successfully');
    }

    public function takeAction(Request $request, Report $report): JsonResponse
    {
        $action = $request->get('action', '');

        switch ($action) {
            case 'delete_content':
                if ($report->reported_type === 'ad') {
                    Ad::find($report->reported_id)?->delete();
                }
                break;
            case 'suspend_user':
                if ($report->reported_type === 'user') {
                    User::find($report->reported_id)?->update(['is_active' => false]);
                }
                break;
            case 'warn_user':
                break;
        }

        $report->resolve();

        AdminLog::log($request->user(), 'report_action', $report->id, 'report', "Action taken: {$action}");

        return $this->success(null, 'Action taken successfully');
    }
}
