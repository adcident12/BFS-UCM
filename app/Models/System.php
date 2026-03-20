<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class System extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'slug', 'description', 'adapter_class',
        'db_host', 'db_port', 'db_name', 'db_user', 'db_password',
        'api_url', 'api_token', 'color', 'icon', 'is_active', 'two_way_permissions',
    ];

    protected $casts = [
        'is_active'          => 'boolean',
        'two_way_permissions' => 'boolean',
    ];

    protected $hidden = ['db_password', 'api_token'];

    public function permissions()
    {
        return $this->hasMany(SystemPermission::class);
    }

    public function userPermissions()
    {
        return $this->hasMany(UserSystemPermission::class);
    }

    public function syncLogs()
    {
        return $this->hasMany(SyncLog::class);
    }
}
