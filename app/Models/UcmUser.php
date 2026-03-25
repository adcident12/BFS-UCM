<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class UcmUser extends Authenticatable
{
    use HasApiTokens, SoftDeletes;

    protected $table = 'ucm_users';

    protected $fillable = [
        'username', 'employee_number', 'name', 'email', 'department', 'title',
        'ldap_dn', 'ldap_guid', 'is_active', 'is_admin', 'last_login_at',
    ];

    protected $casts = [
        'is_active'     => 'boolean',
        'is_admin'      => 'integer',   // 0=ทั่วไป | 1=Admin ระดับ 1 | 2=Admin ระดับ 2
        'last_login_at' => 'datetime',
    ];

    /** Level 1+ (เพิ่ม Reference Data ได้ / ใช้ API ได้) */
    public function isAdmin(): bool
    {
        return $this->is_admin >= 1;
    }

    /** Level 2 (ทำได้ทุกอย่าง: toggle 2-way, แก้ไข/ลบ Reference, จัดการ admin) */
    public function isSuperAdmin(): bool
    {
        return $this->is_admin >= 2;
    }

    public function systemPermissions(): HasMany
    {
        return $this->hasMany(UserSystemPermission::class, 'user_id');
    }

    public function syncLogs(): HasMany
    {
        return $this->hasMany(SyncLog::class, 'user_id');
    }

    public function featureGrants(): HasMany
    {
        return $this->hasMany(UcmUserFeatureGrant::class, 'user_id');
    }

    /**
     * ตรวจสอบว่าผู้ใช้นี้สามารถเข้าถึงฟีเจอร์ UCM ได้หรือไม่
     * ลำดับการตรวจ:
     *   1. ดึง effective min_level (config default → DB override)
     *   2. ถ้า is_admin >= min_level → ผ่าน
     *   3. ถ้ามี individual grant สำหรับ feature นี้ → ผ่าน
     */
    public function canAccess(string $feature): bool
    {
        $minLevel = UcmFeatureOverride::getEffectiveLevel($feature);

        if ($this->is_admin >= $minLevel) {
            return true;
        }

        return $this->featureGrants()->where('feature_key', $feature)->exists();
    }

    public function hasPermission(string $systemSlug, string $permissionKey): bool
    {
        return $this->systemPermissions()
            ->whereHas('system', fn ($q) => $q->where('slug', $systemSlug))
            ->where('permission_key', $permissionKey)
            ->exists();
    }

    public function getPermissionsForSystem(int $systemId): array
    {
        return $this->systemPermissions()
            ->where('system_id', $systemId)
            ->pluck('permission_key')
            ->toArray();
    }
}
