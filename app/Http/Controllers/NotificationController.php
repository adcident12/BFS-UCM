<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreNotificationChannelRequest;
use App\Models\AuditLog;
use App\Models\NotificationChannel;
use App\Models\UcmUser;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NotificationController extends Controller
{
    private function authUser(): ?UcmUser
    {
        return Auth::user();
    }

    public function index(): View
    {
        abort_unless($this->authUser()?->canAccess('notifications'), 403, 'เฉพาะ Admin ระดับ 2 เท่านั้นที่สามารถเข้าถึง Notification Channels ได้');

        $channels = NotificationChannel::latest()->get();

        return view('notifications.index', compact('channels'));
    }

    public function store(StoreNotificationChannelRequest $request): RedirectResponse
    {
        abort_unless($this->authUser()?->canAccess('notifications'), 403, 'เฉพาะ Admin ระดับ 2 เท่านั้นที่สามารถเพิ่ม Notification Channel ได้');

        $data = $request->validated();

        // Normalize config
        if ($data['type'] === 'email') {
            $to = array_filter(array_map('trim', explode(',', $data['config']['to'] ?? '')));
            $data['config'] = ['to' => array_values($to)];
        } else {
            $data['config'] = [
                'url' => $data['config']['url'],
                'secret' => $data['config']['secret'] ?? null,
            ];
        }

        $data['is_active'] = $request->boolean('is_active', true);

        $channel = NotificationChannel::create($data);

        AuditLogger::log(
            AuditLog::CATEGORY_NOTIFICATIONS,
            AuditLog::EVENT_NOTIFICATION_CHANNEL_CREATED,
            "เพิ่ม Notification Channel '{$channel->name}' ประเภท {$channel->type}",
            ['channel_id' => $channel->id, 'name' => $channel->name, 'type' => $channel->type, 'events' => $channel->events],
            $this->authUser(),
            'notification_channel', $channel->id, $channel->name,
        );

        return redirect()->route('notifications.index')->with('success', 'เพิ่ม Notification Channel สำเร็จ');
    }

    public function update(StoreNotificationChannelRequest $request, NotificationChannel $notificationChannel): RedirectResponse
    {
        abort_unless($this->authUser()?->canAccess('notifications'), 403, 'เฉพาะ Admin ระดับ 2 เท่านั้นที่สามารถแก้ไข Notification Channel ได้');

        $data = $request->validated();

        if ($data['type'] === 'email') {
            $to = array_filter(array_map('trim', explode(',', $data['config']['to'] ?? '')));
            $data['config'] = ['to' => array_values($to)];
        } else {
            $data['config'] = [
                'url' => $data['config']['url'],
                'secret' => $data['config']['secret'] ?? null,
            ];
        }

        $data['is_active'] = $request->boolean('is_active', true);

        $notificationChannel->update($data);

        AuditLogger::log(
            AuditLog::CATEGORY_NOTIFICATIONS,
            AuditLog::EVENT_NOTIFICATION_CHANNEL_UPDATED,
            "แก้ไข Notification Channel '{$notificationChannel->name}' ประเภท {$notificationChannel->type}",
            ['channel_id' => $notificationChannel->id, 'name' => $notificationChannel->name, 'type' => $notificationChannel->type, 'events' => $notificationChannel->events],
            $this->authUser(),
            'notification_channel', $notificationChannel->id, $notificationChannel->name,
        );

        return redirect()->route('notifications.index')->with('success', 'แก้ไข Notification Channel สำเร็จ');
    }

    public function destroy(NotificationChannel $notificationChannel): RedirectResponse
    {
        abort_unless($this->authUser()?->canAccess('notifications'), 403, 'เฉพาะ Admin ระดับ 2 เท่านั้นที่สามารถลบ Notification Channel ได้');

        $channelId   = $notificationChannel->id;
        $channelName = $notificationChannel->name;
        $channelType = $notificationChannel->type;

        $notificationChannel->delete();

        AuditLogger::log(
            AuditLog::CATEGORY_NOTIFICATIONS,
            AuditLog::EVENT_NOTIFICATION_CHANNEL_DELETED,
            "ลบ Notification Channel '{$channelName}' ประเภท {$channelType}",
            ['channel_id' => $channelId, 'name' => $channelName, 'type' => $channelType],
            $this->authUser(),
            'notification_channel', $channelId, $channelName,
        );

        return redirect()->route('notifications.index')->with('success', 'ลบ Notification Channel สำเร็จ');
    }
}
