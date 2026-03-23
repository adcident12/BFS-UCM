<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\System;
use App\Models\UcmUser;
use App\Models\UserSystemPermission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    /**
     * GET /api/users/{username}/permissions?system=repair-system
     *
     * Returns all permission keys for a user in a given system.
     */
    public function forUser(Request $request, string $username): JsonResponse
    {
        $request->validate([
            'system' => 'required|string|exists:systems,slug',
        ]);

        /** @var UcmUser $caller */
        $caller = $request->user();

        if (! $caller->isAdmin() && $caller->username !== $username) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $user = UcmUser::where('username', $username)
            ->where('is_active', true)
            ->first();

        if (! $user) {
            return response()->json(['message' => "User '{$username}' not found"], 404);
        }

        $system = System::where('slug', $request->system)
            ->where('is_active', true)
            ->firstOrFail();

        $permissions = $user->getPermissionsForSystem($system->id);

        return response()->json([
            'username' => $user->username,
            'system' => $system->slug,
            'permissions' => $permissions,
        ]);
    }

    /**
     * POST /api/permissions/check
     *
     * Check if a user has a specific permission in a system.
     */
    public function check(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'username' => 'required|string|max:100',
            'system' => 'required|string|max:100',
            'permission' => 'required|string|max:100',
        ]);

        /** @var UcmUser $caller */
        $caller = $request->user();

        if (! $caller->isAdmin() && $caller->username !== $validated['username']) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $user = UcmUser::where('username', $validated['username'])
            ->where('is_active', true)
            ->first();

        if (! $user) {
            return response()->json(['allowed' => false, 'reason' => 'user_not_found']);
        }

        $allowed = $user->hasPermission($validated['system'], $validated['permission']);

        return response()->json(['allowed' => $allowed]);
    }

    /**
     * GET /api/users/{username}/permissions/all
     *
     * Returns permissions for a user across ALL active systems.
     * Uses a single query + grouping to avoid N+1.
     */
    public function allSystems(Request $request, string $username): JsonResponse
    {
        /** @var UcmUser $caller */
        $caller = $request->user();

        if (! $caller->isAdmin() && $caller->username !== $username) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $user = UcmUser::where('username', $username)
            ->where('is_active', true)
            ->first();

        if (! $user) {
            return response()->json(['message' => "User '{$username}' not found"], 404);
        }

        // โหลด permissions ทั้งหมดของ user พร้อม system slug ในคำสั่งเดียว
        $permissions = UserSystemPermission::where('user_id', $user->id)
            ->join('systems', function ($join) {
                $join->on('user_system_permissions.system_id', '=', 'systems.id')
                    ->where('systems.is_active', true)
                    ->whereNull('systems.deleted_at');
            })
            ->select('systems.slug', 'user_system_permissions.permission_key')
            ->get();

        // จัด group by slug ใน PHP (O(n) ไม่ต้อง query ซ้ำ)
        $result = [];
        foreach ($permissions as $row) {
            $result[$row->slug][] = $row->permission_key;
        }

        return response()->json([
            'username' => $user->username,
            'systems' => $result,
        ]);
    }
}
