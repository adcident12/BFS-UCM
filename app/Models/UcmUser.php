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
        'ldap_dn', 'ldap_guid', 'is_active', 'is_admin',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_admin'  => 'boolean',
    ];

    public function systemPermissions(): HasMany
    {
        return $this->hasMany(UserSystemPermission::class, 'user_id');
    }

    public function syncLogs(): HasMany
    {
        return $this->hasMany(SyncLog::class, 'user_id');
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
