<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSystemPermission extends Model
{
    protected $fillable = [
        'user_id', 'system_id', 'permission_key', 'granted_by', 'granted_at',
    ];

    protected $casts = [
        'granted_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(UcmUser::class, 'user_id');
    }

    public function system()
    {
        return $this->belongsTo(System::class);
    }

    public function grantedBy()
    {
        return $this->belongsTo(UcmUser::class, 'granted_by');
    }
}
