<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\System;
use App\Models\UcmUser;
use App\Models\UserSystemPermission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserExportController extends Controller
{
    /**
     * GET /api/users/export
     *
     * ส่งออกข้อมูลผู้ใช้ + permissions ทุกระบบ ในรูปแบบ JSON
     *
     * Query params (optional):
     *   user_ids[]  — กรอง user ด้วย ID (ถ้าไม่ส่งจะดึงทั้งหมด)
     *   usernames[] — กรอง user ด้วย username แทน ID ก็ได้
     */
    public function export(Request $request): JsonResponse
    {
        $request->validate([
            'user_ids'    => 'array',
            'user_ids.*'  => 'integer|min:1',
            'usernames'   => 'array',
            'usernames.*' => 'string|max:100',
        ]);

        $query = UcmUser::where('is_active', true)->orderBy('name');

        if ($request->filled('user_ids')) {
            $query->whereIn('id', $request->input('user_ids'));
        } elseif ($request->filled('usernames')) {
            $query->whereIn('username', $request->input('usernames'));
        }

        $users = $query->get();

        // โหลด permissions ทุก user ในคำสั่งเดียว (ไม่ N+1)
        $permRows = UserSystemPermission::whereIn('user_id', $users->pluck('id'))
            ->join('systems', function ($j) {
                $j->on('user_system_permissions.system_id', '=', 'systems.id')
                  ->where('systems.is_active', true)
                  ->whereNull('systems.deleted_at');
            })
            ->select(
                'user_system_permissions.user_id',
                'systems.slug',
                'user_system_permissions.permission_key'
            )
            ->get();

        // group: user_id → slug → [keys]
        $permsMap = [];
        foreach ($permRows as $row) {
            $permsMap[$row->user_id][$row->slug][] = $row->permission_key;
        }

        $data = $users->map(fn ($user) => [
            'username'        => $user->username,
            'employee_number' => $user->employee_number,
            'name'            => $user->name,
            'email'           => $user->email,
            'department'      => $user->department,
            'title'           => $user->title,
            'systems'         => $permsMap[$user->id] ?? (object) [],
        ]);

        return response()->json([
            'count'     => $data->count(),
            'exported'  => now()->toIso8601String(),
            'users'     => $data,
        ]);
    }
}
