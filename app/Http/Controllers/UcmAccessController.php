<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\UcmFeatureOverride;
use App\Models\UcmUser;
use App\Models\UcmUserFeatureGrant;
use App\Services\AuditLogger;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UcmAccessController extends Controller
{
    private function authUser(): ?UcmUser
    {
        /** @var UcmUser|null */
        return Auth::user();
    }

    public function index()
    {
        abort_unless($this->authUser()?->canAccess('ucm_access'), 403, 'เฉพาะ Admin ระดับ 2 เท่านั้นที่สามารถจัดการสิทธิ์ระบบ UCM ได้');

        /** @var array<string,array{name:string,description:string,group:string,default_level:int,lockable:bool}> $allFeatures */
        $allFeatures = config('ucm_features', []);

        // โหลด overrides และ grants ทั้งหมดในครั้งเดียว
        $overrides = UcmFeatureOverride::with('updatedBy:id,username,name')
            ->get()
            ->keyBy('feature_key');

        $grants = UcmUserFeatureGrant::with(['user:id,username,name,department', 'grantedBy:id,username,name'])
            ->get()
            ->groupBy('feature_key');

        // จัดกลุ่ม features
        $grouped = collect($allFeatures)
            ->map(fn ($cfg, $key) => array_merge($cfg, [
                'key'            => $key,
                'override'       => $overrides->get($key),
                'effective_level' => UcmFeatureOverride::getEffectiveLevel($key),
                'grants'         => $grants->get($key, collect()),
            ]))
            ->groupBy('group');

        return view('ucm-access.index', compact('grouped'));
    }

    public function updateLevel(Request $request, string $featureKey)
    {
        abort_unless($this->authUser()?->canAccess('ucm_access'), 403);

        $feature = config("ucm_features.{$featureKey}");
        abort_if($feature === null, 404, "Feature '{$featureKey}' ไม่พบในระบบ");

        // Feature ที่ lockable=false ห้ามเปลี่ยน min_level
        abort_if(
            ($feature['lockable'] ?? true) === false,
            422,
            "Feature '{$featureKey}' ไม่อนุญาตให้เปลี่ยน minimum level"
        );

        $data = $request->validate([
            'min_level' => 'required|integer|in:0,1,2',
        ]);

        $oldLevel = UcmFeatureOverride::getEffectiveLevel($featureKey);
        $newLevel = (int) $data['min_level'];

        DB::transaction(function () use ($featureKey, $newLevel) {
            UcmFeatureOverride::updateOrCreate(
                ['feature_key' => $featureKey],
                ['min_level' => $newLevel, 'updated_by' => $this->authUser()?->id],
            );
        });

        UcmFeatureOverride::clearCache($featureKey);

        $levelLabel = ['0' => 'ทุกคน (L0)', '1' => 'Admin L1+', '2' => 'Admin L2 เท่านั้น'];

        AuditLogger::log(
            AuditLog::CATEGORY_ACCESS,
            AuditLog::EVENT_FEATURE_LEVEL_UPDATED,
            "เปลี่ยน min_level ของ '{$feature['name']}' จาก {$levelLabel[$oldLevel]} → {$levelLabel[$newLevel]}",
            [
                'feature_key' => $featureKey,
                'feature_name' => $feature['name'],
                'old_level' => $oldLevel,
                'new_level' => $newLevel,
            ],
            $this->authUser(),
            'feature', null, $feature['name'],
        );

        app(NotificationService::class)->dispatch('feature_level_updated', [
            'description'  => "เปลี่ยนระดับสิทธิ์ '{$feature['name']}' จาก {$levelLabel[$oldLevel]} เป็น {$levelLabel[$newLevel]}",
            'feature_key'  => $featureKey,
            'feature_name' => $feature['name'],
            'old_level'    => $levelLabel[$oldLevel],
            'new_level'    => $levelLabel[$newLevel],
            'actor'        => $this->authUser()?->name,
        ]);

        return back()->with('success', "อัปเดตสิทธิ์ '{$feature['name']}' เป็น {$levelLabel[$newLevel]} เรียบร้อย");
    }

    public function storeGrant(Request $request, string $featureKey)
    {
        abort_unless($this->authUser()?->canAccess('ucm_access'), 403);

        $feature = config("ucm_features.{$featureKey}");
        abort_if($feature === null, 404);

        $data = $request->validate([
            'user_id' => 'required|integer|exists:ucm_users,id',
        ]);

        $user = UcmUser::findOrFail($data['user_id']);

        // ตรวจว่า user นั้นมีสิทธิ์จาก level อยู่แล้ว
        $effectiveLevel = UcmFeatureOverride::getEffectiveLevel($featureKey);
        if ($user->is_admin >= $effectiveLevel) {
            return back()->withErrors([
                "{$user->name} มีสิทธิ์เข้าถึง '{$feature['name']}' อยู่แล้วจาก Admin Level {$user->is_admin}",
            ]);
        }

        UcmUserFeatureGrant::firstOrCreate(
            ['user_id' => $user->id, 'feature_key' => $featureKey],
            ['granted_by' => $this->authUser()?->id, 'granted_at' => now()],
        );

        AuditLogger::log(
            AuditLog::CATEGORY_ACCESS,
            AuditLog::EVENT_FEATURE_GRANT_CREATED,
            "ให้สิทธิ์ '{$feature['name']}' แก่ {$user->name} ({$user->username}) เป็นกรณีพิเศษ",
            [
                'feature_key'  => $featureKey,
                'feature_name' => $feature['name'],
                'user_id'      => $user->id,
                'username'     => $user->username,
            ],
            $this->authUser(),
            'user', $user->id, $user->name,
        );

        app(NotificationService::class)->dispatch('feature_grant_created', [
            'description'  => "ให้สิทธิ์พิเศษ '{$feature['name']}' แก่ {$user->name} ({$user->username})",
            'feature_key'  => $featureKey,
            'feature_name' => $feature['name'],
            'username'     => $user->username,
            'user_name'    => $user->name,
            'actor'        => $this->authUser()?->name,
        ]);

        return back()->with('success', "ให้สิทธิ์ '{$feature['name']}' แก่ {$user->name} เรียบร้อย");
    }

    public function destroyGrant(string $featureKey, UcmUserFeatureGrant $grant)
    {
        abort_unless($this->authUser()?->canAccess('ucm_access'), 403);

        abort_if($grant->feature_key !== $featureKey, 404);

        $feature = config("ucm_features.{$featureKey}");
        $user = $grant->user;

        $grant->delete();

        AuditLogger::log(
            AuditLog::CATEGORY_ACCESS,
            AuditLog::EVENT_FEATURE_GRANT_DELETED,
            "ถอนสิทธิ์พิเศษ '{$feature['name']}' ของ {$user?->name} ({$user?->username})",
            [
                'feature_key'  => $featureKey,
                'feature_name' => $feature['name'] ?? $featureKey,
                'user_id'      => $user?->id,
                'username'     => $user?->username,
            ],
            $this->authUser(),
            'user', $user?->id, $user?->name,
        );

        app(NotificationService::class)->dispatch('feature_grant_deleted', [
            'description'  => "ถอนสิทธิ์พิเศษ '{$feature['name']}' ของ {$user?->name} ({$user?->username})",
            'feature_key'  => $featureKey,
            'feature_name' => $feature['name'] ?? $featureKey,
            'username'     => $user?->username,
            'user_name'    => $user?->name,
            'actor'        => $this->authUser()?->name,
        ]);

        return back()->with('success', "ถอนสิทธิ์พิเศษ '{$feature['name']}' ของ {$user?->name} เรียบร้อย");
    }

    /** AJAX: ค้นหาผู้ใช้สำหรับ grant form */
    public function searchUsers(Request $request)
    {
        abort_unless($this->authUser()?->canAccess('ucm_access'), 403);

        $q = $request->string('q')->trim();

        $users = UcmUser::where('is_active', true)
            ->where(function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                    ->orWhere('username', 'like', "%{$q}%")
                    ->orWhere('department', 'like', "%{$q}%");
            })
            ->orderBy('name')
            ->limit(15)
            ->get(['id', 'username', 'name', 'department', 'is_admin']);

        return response()->json($users->map(fn ($u) => [
            'id'         => $u->id,
            'username'   => $u->username,
            'name'       => $u->name,
            'department' => $u->department,
            'is_admin'   => $u->is_admin,
        ]));
    }
}
