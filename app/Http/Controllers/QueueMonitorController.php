<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\SyncLog;
use App\Models\UcmUser;
use App\Services\AuditLogger;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class QueueMonitorController extends Controller
{
    private function authUser(): ?UcmUser
    {
        /** @var UcmUser|null */
        return Auth::user();
    }

    public function index(): View
    {
        abort_unless($this->authUser()?->canAccess('queue_monitor'), 403, 'เฉพาะ Admin เท่านั้น');

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

    public function poll(): JsonResponse
    {
        abort_unless($this->authUser()?->canAccess('queue_monitor'), 403);

        $pendingJobs    = DB::table('jobs')->whereNull('reserved_at')->count();
        $processingJobs = DB::table('jobs')->whereNotNull('reserved_at')->count();
        $failedCount    = DB::table('failed_jobs')->count();
        $syncSuccess    = SyncLog::where('status', 'success')->whereDate('created_at', today())->count();
        $syncFailed     = SyncLog::where('status', 'failed')->whereDate('created_at', today())->count();

        $failedJobs = DB::table('failed_jobs')
            ->orderByDesc('failed_at')
            ->limit(20)
            ->get()
            ->map(function ($job) {
                $payload   = json_decode($job->payload, true);
                $shortName = class_basename($payload['displayName'] ?? $payload['job'] ?? 'Unknown');
                $failedAt  = \Carbon\Carbon::parse($job->failed_at);

                return [
                    'uuid'       => $job->uuid,
                    'name'       => $shortName,
                    'queue'      => $job->queue,
                    'failed_at'  => $failedAt->diffForHumans(),
                    'failed_at_full' => $failedAt->format('d/m/Y H:i:s'),
                    'exception'  => Str::limit($job->exception ?? '', 120),
                ];
            });

        $recentSyncs = SyncLog::with(['user', 'system', 'performedBy'])
            ->latest()
            ->limit(40)
            ->get()
            ->map(fn ($log) => [
                'status'        => $log->status,
                'action'        => $log->action,
                'user_name'     => $log->user?->name ?? $log->user?->username ?? '—',
                'system_name'   => $log->system?->name,
                'error_message' => $log->error_message ? Str::limit($log->error_message, 60) : null,
                'performed_by'  => ($log->performed_by && $log->performed_by !== $log->user_id)
                    ? $log->performedBy?->username : null,
                'diff'          => $log->created_at->diffForHumans(),
                'full_time'     => $log->created_at->format('d/m/Y H:i:s'),
            ]);

        return response()->json([
            'pendingJobs'    => $pendingJobs,
            'processingJobs' => $processingJobs,
            'failedCount'    => $failedCount,
            'syncSuccess'    => $syncSuccess,
            'syncFailed'     => $syncFailed,
            'failedJobs'     => $failedJobs,
            'recentSyncs'    => $recentSyncs,
        ]);
    }

    public function retryFailed(string $uuid): RedirectResponse
    {
        abort_unless($this->authUser()?->canAccess('queue_monitor'), 403, 'เฉพาะ Admin ระดับ 2 เท่านั้น');
        abort_unless(Str::isUuid($uuid), 422, 'Job ID ไม่ถูกต้อง');

        Artisan::call('queue:retry', ['id' => [$uuid]]);

        AuditLogger::log(
            AuditLog::CATEGORY_QUEUE,
            AuditLog::EVENT_QUEUE_JOB_RETRIED,
            "Retry failed job: {$uuid}",
            ['uuid' => $uuid],
            $this->authUser(),
        );

        app(NotificationService::class)->dispatch('queue_job_retried', [
            'description' => "Retry failed job: {$uuid}",
            'uuid'        => $uuid,
            'actor'       => $this->authUser()?->name,
        ]);

        return back()->with('success', "ส่ง retry job สำเร็จ");
    }

    public function retryAll(): RedirectResponse
    {
        abort_unless($this->authUser()?->canAccess('queue_monitor'), 403, 'เฉพาะ Admin ระดับ 2 เท่านั้น');

        $count = DB::table('failed_jobs')->count();
        Artisan::call('queue:retry', ['id' => ['all']]);

        AuditLogger::log(
            AuditLog::CATEGORY_QUEUE,
            AuditLog::EVENT_QUEUE_ALL_RETRIED,
            "Retry failed jobs ทั้งหมด {$count} jobs",
            ['count' => $count],
            $this->authUser(),
        );

        app(NotificationService::class)->dispatch('queue_all_retried', [
            'description' => "Retry failed jobs ทั้งหมด {$count} jobs",
            'count'       => $count,
            'actor'       => $this->authUser()?->name,
        ]);

        return back()->with('success', "ส่ง retry {$count} jobs สำเร็จ");
    }

    public function destroyFailed(string $uuid): RedirectResponse
    {
        abort_unless($this->authUser()?->canAccess('queue_monitor'), 403, 'เฉพาะ Admin ระดับ 2 เท่านั้น');
        abort_unless(Str::isUuid($uuid), 422, 'Job ID ไม่ถูกต้อง');

        Artisan::call('queue:forget', ['id' => $uuid]);

        AuditLogger::log(
            AuditLog::CATEGORY_QUEUE,
            AuditLog::EVENT_QUEUE_JOB_DELETED,
            "ลบ failed job: {$uuid}",
            ['uuid' => $uuid],
            $this->authUser(),
        );

        app(NotificationService::class)->dispatch('queue_job_deleted', [
            'description' => "ลบ failed job: {$uuid}",
            'uuid'        => $uuid,
            'actor'       => $this->authUser()?->name,
        ]);

        return back()->with('success', "ลบ failed job เรียบร้อย");
    }

    public function flushFailed(): RedirectResponse
    {
        abort_unless($this->authUser()?->canAccess('queue_monitor'), 403, 'เฉพาะ Admin ระดับ 2 เท่านั้น');

        $count = DB::table('failed_jobs')->count();
        Artisan::call('queue:flush');

        AuditLogger::log(
            AuditLog::CATEGORY_QUEUE,
            AuditLog::EVENT_QUEUE_FLUSHED,
            "ล้าง failed jobs ทั้งหมด {$count} jobs",
            ['count' => $count],
            $this->authUser(),
        );

        app(NotificationService::class)->dispatch('queue_flushed', [
            'description' => "ล้าง failed jobs ทั้งหมด {$count} jobs",
            'count'       => $count,
            'actor'       => $this->authUser()?->name,
        ]);

        return back()->with('success', "ล้าง failed jobs ทั้งหมดเรียบร้อย");
    }
}
