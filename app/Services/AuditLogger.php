<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\UcmUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuditLogger
{
    public static function log(
        string $category,
        string $eventType,
        string $description,
        array $metadata = [],
        ?UcmUser $actor = null,
        ?string $subjectType = null,
        ?int $subjectId = null,
        ?string $subjectLabel = null,
        ?Request $request = null,
    ): void {
        $actor ??= Auth::user();
        $req = $request ?? request();

        AuditLog::create([
            'event_category' => $category,
            'event_type' => $eventType,
            'actor_id' => $actor?->id,
            'actor_username' => $actor?->username,
            'subject_type' => $subjectType,
            'subject_id' => $subjectId,
            'subject_label' => $subjectLabel,
            'description' => $description,
            'metadata' => empty($metadata) ? null : $metadata,
            'ip_address' => $req->ip(),
            'user_agent' => $req->userAgent(),
            'created_at' => now(),
        ]);
    }
}
