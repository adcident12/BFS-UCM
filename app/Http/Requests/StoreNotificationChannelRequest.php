<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreNotificationChannelRequest extends FormRequest
{
    /** Known event keys — must be kept in sync with notifications/index.blade.php $availableEvents */
    public const KNOWN_EVENTS = [
        '*',
        // Users
        'permissions_updated',
        'permissions_discovered',
        'account_status_changed',
        'user_imported',
        'user_bulk_imported',
        'user_removed',
        'admin_level_updated',
        // Systems
        'system_created',
        'system_updated',
        'system_deleted',
        'system_2way_toggled',
        // Permission Definitions
        'perm_def_created',
        'perm_def_updated',
        'perm_def_deleted',
        'perm_def_discovered',
        // Reference Data (Group Records)
        'group_record_created',
        'group_record_updated',
        'group_record_deleted',
        'group_records_discovered',
        // Connectors
        'connector_created',
        'connector_updated',
        'connector_deleted',
        // UCM Access Control
        'feature_level_updated',
        'feature_grant_created',
        'feature_grant_deleted',
        // Notifications
        'notification_channel_created',
        'notification_channel_updated',
        'notification_channel_deleted',
        // Queue
        'queue_job_retried',
        'queue_all_retried',
        'queue_job_deleted',
        'queue_flushed',
        // Security / API
        'login_failed',
        'api_token_issued',
        // Share Links
        'share_link_created',
        'share_link_revoked',
    ];

    public function authorize(): bool
    {
        return auth()->check() && auth()->user()?->canAccess('notifications');
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $knownEvents = implode(',', self::KNOWN_EVENTS);

        return [
            'name'         => ['required', 'string', 'max:100'],
            'type'         => ['required', 'in:email,webhook'],
            'events'       => ['required', 'array', 'min:1'],
            'events.*'     => ['string', 'in:' . $knownEvents],
            'is_active'    => ['boolean'],
            'config.url'   => ['required_if:type,webhook', 'nullable', 'url', 'max:500'],
            'config.secret' => ['nullable', 'string', 'max:200'],
            'config.to'    => ['required_if:type,email', 'nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'config.url.required_if'  => 'กรุณาระบุ Webhook URL',
            'config.url.url'          => 'Webhook URL ไม่ถูกต้อง',
            'config.to.required_if'   => 'กรุณาระบุอีเมลผู้รับ',
            'events.required'         => 'กรุณาเลือกอย่างน้อย 1 event',
            'events.*.in'             => 'Event ":input" ไม่ถูกต้อง',
        ];
    }
}
