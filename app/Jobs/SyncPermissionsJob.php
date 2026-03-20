<?php

namespace App\Jobs;

use App\Adapters\AdapterFactory;
use App\Models\SyncLog;
use App\Models\System;
use App\Models\UcmUser;
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

    public int $tries   = 3;       // retry สูงสุด 3 ครั้ง
    public int $timeout = 30;      // timeout 30 วินาที / ครั้ง
    public int $backoff = 10;      // รอ 10 วินาทีก่อน retry

    public function __construct(protected SyncLog $syncLog) {}

    public function handle(): void
    {
        $log    = $this->syncLog->fresh(['user', 'system']);
        $user   = $log->user;
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
            $adapter     = AdapterFactory::make($system);
            $permissions = $log->payload['permissions'] ?? [];
            $success     = $adapter->syncPermissions($user, $permissions);

            if ($success) {
                $log->update([
                    'status'    => 'success',
                    'synced_at' => now(),
                ]);
                Log::info("[UCM Sync] OK — {$user->username} → {$system->slug}");
            } else {
                $this->markFailed($log, 'Adapter sync คืนค่า false');
            }

        } catch (\Throwable $e) {
            Log::error("[UCM Sync] Exception — {$user->username} → {$system->slug}: " . $e->getMessage());
            $this->markFailed($log, $e->getMessage());

            if ($this->attempts() < $this->tries) {
                $this->release($this->backoff);
            }
        }
    }

    protected function markFailed(SyncLog $log, string $message): void
    {
        $log->update([
            'status'        => 'failed',
            'error_message' => $message,
            'synced_at'     => now(),
        ]);
    }
}
