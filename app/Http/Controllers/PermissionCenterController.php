<?php

namespace App\Http\Controllers;

use App\Models\System;
use App\Models\UcmUser;
use App\Models\UserSystemPermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PermissionCenterController extends Controller
{
    private function authUser(): ?UcmUser
    {
        /** @var UcmUser|null */
        return Auth::user();
    }

    public function index(Request $request)
    {
        abort_unless($this->authUser()?->canAccess('permission_center'), 403, 'เฉพาะ Admin ระดับ 1 ขึ้นไปเท่านั้นที่สามารถเข้าหน้าจัดการ Permissions ได้');

        $systems = System::withCount(['permissions', 'userPermissions'])
            ->orderBy('name')
            ->get();

        $selectedSystem = null;
        $permissionsByGroup = collect();
        $userAssignments = collect();

        $systemId = (int) $request->get('system', $systems->first()?->id ?? 0);

        if ($systemId > 0) {
            $selectedSystem = System::with([
                'permissions' => fn ($q) => $q->orderBy('group')->orderBy('sort_order')->orderBy('label'),
            ])->find($systemId);
        }

        if ($selectedSystem) {
            $permissionsByGroup = $selectedSystem->permissions->groupBy(
                fn ($p) => filled($p->group) ? $p->group : '__none__'
            );

            $userAssignments = UserSystemPermission::where('system_id', $selectedSystem->id)
                ->with(['user:id,username,name,department,title'])
                ->get()
                ->groupBy('user_id')
                ->map(fn ($rows) => [
                    'user' => $rows->first()->user,
                    'keys' => $rows->pluck('permission_key')->all(),
                ])
                ->values()
                ->sortBy(fn ($a) => $a['user']?->name ?? '')
                ->values();
        }

        return view('permissions.index', compact(
            'systems',
            'selectedSystem',
            'permissionsByGroup',
            'userAssignments',
        ));
    }
}
