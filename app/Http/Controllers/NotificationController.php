<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreNotificationChannelRequest;
use App\Models\NotificationChannel;
use App\Models\UcmUser;
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
        abort_unless($this->authUser()?->isSuperAdmin(), 403, 'เฉพาะ Admin ระดับ 2 เท่านั้นที่สามารถเข้าถึง Notification Channels ได้');

        $channels = NotificationChannel::latest()->get();

        return view('notifications.index', compact('channels'));
    }

    public function store(StoreNotificationChannelRequest $request): RedirectResponse
    {
        abort_unless($this->authUser()?->isSuperAdmin(), 403, 'เฉพาะ Admin ระดับ 2 เท่านั้นที่สามารถเพิ่ม Notification Channel ได้');

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

        NotificationChannel::create($data);

        return redirect()->route('notifications.index')->with('success', 'เพิ่ม Notification Channel สำเร็จ');
    }

    public function update(StoreNotificationChannelRequest $request, NotificationChannel $notificationChannel): RedirectResponse
    {
        abort_unless($this->authUser()?->isSuperAdmin(), 403, 'เฉพาะ Admin ระดับ 2 เท่านั้นที่สามารถแก้ไข Notification Channel ได้');

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

        return redirect()->route('notifications.index')->with('success', 'แก้ไข Notification Channel สำเร็จ');
    }

    public function destroy(NotificationChannel $notificationChannel): RedirectResponse
    {
        abort_unless($this->authUser()?->isSuperAdmin(), 403, 'เฉพาะ Admin ระดับ 2 เท่านั้นที่สามารถลบ Notification Channel ได้');

        $notificationChannel->delete();

        return redirect()->route('notifications.index')->with('success', 'ลบ Notification Channel สำเร็จ');
    }
}
