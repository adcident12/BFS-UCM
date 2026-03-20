<?php

namespace App\Http\Controllers;

use App\Models\SyncLog;
use App\Models\System;
use App\Models\UcmUser;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'systems'      => System::where('is_active', true)->count(),
            'users'        => UcmUser::where('is_active', true)->count(),
            'sync_success' => SyncLog::where('status', 'success')->whereDate('created_at', today())->count(),
            'sync_failed'  => SyncLog::where('status', 'failed')->whereDate('created_at', today())->count(),
        ];

        $recentLogs = SyncLog::with(['user', 'system', 'performedBy'])
            ->latest()
            ->limit(10)
            ->get();

        $systems = System::where('is_active', true)->withCount('userPermissions')->get();

        return view('dashboard', compact('stats', 'recentLogs', 'systems'));
    }
}
