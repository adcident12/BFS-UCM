<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemPermission extends Model
{
    protected $fillable = [
        'system_id', 'key', 'label', 'remote_value', 'group', 'description', 'sort_order', 'is_exclusive',
    ];

    protected $casts = [
        'is_exclusive' => 'boolean',
    ];

    public function system()
    {
        return $this->belongsTo(System::class);
    }
}
