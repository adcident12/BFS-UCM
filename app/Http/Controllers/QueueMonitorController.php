<?php

namespace App\Http\Controllers;

use App\Models\SyncLog;
use App\Models\UcmUser;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class QueueMonitorController extends Controller
{
    private function authUser(): ?UcmUser
    {
        /** @var UcmUser|null */
        return Auth::user();
    }

    public function index()
    {
        abort_unless($this->authUser()?->isAdmin(), 403, 'เฉพาะ Admin เท่านั้น');

        $pendingJobs    = DB::table('jobs')->whereNull('reserved_at')->count();
        $processingJobs = DB::table('jobs')->whereNotNull('reserved_at')->count();
        $failedCount    = DB::table('failed_jobs')->count();
        $syncSuccess    = SyncLog::where('status', 'success')->whereDate('created_at', today())->count();
        $syncFailed     = SyncLog::where('status', 'failed')->whereDate('created_at', today())->count();

        // Failed jobs paginated — decode displayName from payload
        $failedJobs = DB::table('failed_jobs')
            ->orderByDesc('failed_at')
            ->paginate(20);

        // Recent sync activity
        $recentSyncs = SyncLog::with(['user', 'system', 'performedBy'])
            ->latest()
            ->limit(40)
            ->get();

        return view('queue.monitor', compact(
            'pendingJobs', 'processingJobs', 'failedCount',
            'syncSuccess', 'syncFailed',
            'failedJobs', 'recentSyncs',
        ));
    }

    public function retryFailed(string $uuid)
    {
        abort_unless($this->authUser()?->isSuperAdmin(), 403, 'เฉพาะ Admin ระดับ 2 เท่านั้น');

        Artisan::call('queue:retry', ['id' => [$uuid]]);

        return back()->with('success', "ส่ง retry job สำเร็จ");
    }

    public function retryAll()
    {
        abort_unless($this->authUser()?->isSuperAdmin(), 403, 'เฉพาะ Admin ระดับ 2 เท่านั้น');

        $count = DB::table('failed_jobs')->count();
        Artisan::call('queue:retry', ['id' => ['all']]);

        return back()->with('success', "ส่ง retry {$count} jobs สำเร็จ");
    }

    public function destroyFailed(string $uuid)
    {
        abort_unless($this->authUser()?->isSuperAdmin(), 403, 'เฉพาะ Admin ระดับ 2 เท่านั้น');

        Artisan::call('queue:forget', ['id' => $uuid]);

        return back()->with('success', "ลบ failed job เรียบร้อย");
    }

    public function flushFailed()
    {
        abort_unless($this->authUser()?->isSuperAdmin(), 403, 'เฉพาะ Admin ระดับ 2 เท่านั้น');

        Artisan::call('queue:flush');

        return back()->with('success', "ล้าง failed jobs ทั้งหมดเรียบร้อย");
    }
}
