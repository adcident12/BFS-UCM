<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\SyncLog;
use App\Models\System;
use App\Models\UcmUser;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'systems' => System::where('is_active', true)->count(),
            'users' => UcmUser::where('is_active', true)->count(),
            'sync_success' => SyncLog::where('status', 'success')->whereDate('created_at', today())->count(),
            'sync_failed' => SyncLog::where('status', 'failed')->whereDate('created_at', today())->count(),
        ];

        $recentLogs = SyncLog::with(['user', 'system', 'performedBy'])
            ->latest()
            ->limit(10)
            ->get();

        $systems = System::where('is_active', true)->withCount('userPermissions')->orderBy('name')->get();

        $chartData = $this->buildChartData();

        return view('dashboard', compact('stats', 'recentLogs', 'systems', 'chartData'));
    }

    private function buildChartData(): array
    {
        $days = collect(range(6, 0))->map(fn ($i) => now()->subDays($i)->format('Y-m-d'));
        $labels = $days->map(fn ($d) => now()->parse($d)->locale('th')->isoFormat('D MMM'))->values()->all();

        // Audit activity by category (last 7 days)
        $auditRaw = AuditLog::query()
            ->selectRaw('DATE(created_at) as date, event_category, COUNT(*) as total')
            ->whereBetween('created_at', [now()->subDays(6)->startOfDay(), now()->endOfDay()])
            ->groupBy('date', 'event_category')
            ->get()
            ->groupBy('date');

        $auditCategories = ['auth', 'users', 'permissions', 'systems', 'connectors', 'api'];
        $auditColors = [
            'auth' => '#8b5cf6',
            'users' => '#3b82f6',
            'permissions' => '#6366f1',
            'systems' => '#f97316',
            'connectors' => '#14b8a6',
            'api' => '#64748b',
        ];

        $auditDatasets = [];
        foreach ($auditCategories as $cat) {
            $data = $days->map(fn ($d) => (int) ($auditRaw->get($d)?->firstWhere('event_category', $cat)?->total ?? 0))->values()->all();
            if (array_sum($data) > 0) {
                $auditDatasets[] = [
                    'label' => $cat,
                    'data' => $data,
                    'backgroundColor' => $auditColors[$cat].'cc',
                    'borderColor' => $auditColors[$cat],
                    'borderWidth' => 1,
                    'borderRadius' => 4,
                ];
            }
        }

        // Sync success/failed (last 7 days)
        $syncRaw = SyncLog::query()
            ->selectRaw('DATE(created_at) as date, status, COUNT(*) as total')
            ->whereBetween('created_at', [now()->subDays(6)->startOfDay(), now()->endOfDay()])
            ->groupBy('date', 'status')
            ->get()
            ->groupBy('date');

        $syncDatasets = [
            [
                'label' => 'สำเร็จ',
                'data' => $days->map(fn ($d) => (int) ($syncRaw->get($d)?->firstWhere('status', 'success')?->total ?? 0))->values()->all(),
                'backgroundColor' => '#10b981cc',
                'borderColor' => '#10b981',
                'borderWidth' => 1,
                'borderRadius' => 4,
            ],
            [
                'label' => 'ล้มเหลว',
                'data' => $days->map(fn ($d) => (int) ($syncRaw->get($d)?->firstWhere('status', 'failed')?->total ?? 0))->values()->all(),
                'backgroundColor' => '#f43f5ecc',
                'borderColor' => '#f43f5e',
                'borderWidth' => 1,
                'borderRadius' => 4,
            ],
        ];

        // Permissions per system
        $permSystems = System::withCount('userPermissions')->orderByDesc('user_permissions_count')->limit(8)->get();

        return [
            'labels' => $labels,
            'auditDatasets' => $auditDatasets,
            'syncDatasets' => $syncDatasets,
            'permLabels' => $permSystems->pluck('name')->values()->all(),
            'permData' => $permSystems->pluck('user_permissions_count')->values()->all(),
            'permColors' => $permSystems->pluck('color')->values()->all(),
        ];
    }
}
