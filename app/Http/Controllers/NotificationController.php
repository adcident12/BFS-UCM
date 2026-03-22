<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreNotificationChannelRequest;
use App\Models\NotificationChannel;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(): View
    {
        $channels = NotificationChannel::latest()->get();

        return view('notifications.index', compact('channels'));
    }

    public function store(StoreNotificationChannelRequest $request): RedirectResponse
    {
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
        $notificationChannel->delete();

        return redirect()->route('notifications.index')->with('success', 'ลบ Notification Channel สำเร็จ');
    }
}
