<?php

namespace App\Jobs;

use App\Adapters\AdapterFactory;
use App\Models\SyncLog;
use App\Models\System;
use App\Services\NotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Queue Job: sync permissions ของ user ไปยัง legacy system
 *
 * วิธีใช้:
 *   SyncPermissionsJob::dispatch($syncLog);
 *
 * Job จะ:
 *   1. สร้าง Adapter จาก system
 *   2. เรียก adapter->syncPermissions()
 *   3. อัปเดต sync_logs.status → success / failed
 */
class SyncPermissionsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;       // retry สูงสุด 3 ครั้ง

    public int $timeout = 30;      // timeout 30 วินาที / ครั้ง

    public int $backoff = 10;      // รอ 10 วินาทีก่อน retry

    public function __construct(protected SyncLog $syncLog) {}

    public function handle(): void
    {
        $log = $this->syncLog->fresh(['user', 'system']);
        $user = $log->user;
        $system = $log->system;

        if (! $user || ! $system) {
            $this->markFailed($log, 'ไม่พบ user หรือ system');

            return;
        }

        if (! AdapterFactory::hasAdapter($system)) {
            $this->markFailed($log, "ไม่มี Adapter สำหรับ {$system->slug}");

            return;
        }

        try {
            $adapter = AdapterFactory::make($system);
            $permissions = $log->payload['permissions'] ?? [];
            $success = $adapter->syncPermissions($user, $permissions);

            if ($success) {
                $log->update([
                    'status' => 'success',
                    'synced_at' => now(),
                ]);
                Log::info("[UCM Sync] OK — {$user->username} → {$system->slug}");
            } else {
                $this->markFailed($log, 'Adapter sync คืนค่า false');
            }

        } catch (\Throwable $e) {
            Log::error("[UCM Sync] Exception — {$user->username} → {$system->slug}: ".$e->getMessage());
            $this->markFailed($log, $e->getMessage());

            if ($this->attempts() < $this->tries) {
                $this->release($this->backoff);
            }
        }
    }

    /**
     * เรียกเมื่อ job หมด retry ทั้งหมด — แจ้งเตือน admin ผ่าน NotificationService
     */
    public function failed(\Throwable $e): void
    {
        $log = $this->syncLog->fresh(['user', 'system']);
        $user = $log?->user;
        $system = $log?->system;

        $username = $user?->username ?? '(unknown user)';
        $systemSlug = $system?->name ?? $log?->system_id ?? '(unknown system)';
        $errMsg = $e->getMessage();

        Log::error("[UCM Sync] Job failed permanently after all retries — {$username} → {$systemSlug}: {$errMsg}");

        // อัปเดต sync_log ให้ชัดเจนว่า failed ถาวร
        if ($log) {
            $log->update([
                'status' => 'failed',
                'error_message' => "หมด retry แล้ว: {$errMsg}",
                'synced_at' => now(),
            ]);
        }

        // แจ้งเตือน admin ผ่าน Notification channels
        try {
            app(NotificationService::class)->dispatch('sync_failed', [
                'username' => $username,
                'system' => $systemSlug,
                'error' => $errMsg,
                'description' => "Sync สิทธิ์ล้มเหลวถาวร: {$username} → {$systemSlug} — {$errMsg}",
            ]);
        } catch (\Throwable $notifyError) {
            Log::warning('[UCM Sync] ส่ง notification ไม่ได้หลัง job failed: '.$notifyError->getMessage());
        }
    }

    protected function markFailed(SyncLog $log, string $message): void
    {
        $log->update([
            'status' => 'failed',
            'error_message' => $message,
            'synced_at' => now(),
        ]);
    }
}
