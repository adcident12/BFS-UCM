<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SyncLog extends Model
{
    protected $fillable = [
        'user_id', 'system_id', 'performed_by', 'action',
        'payload', 'status', 'error_message', 'synced_at',
    ];

    protected $casts = [
        'payload'   => 'array',
        'synced_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(UcmUser::class, 'user_id');
    }

    public function system()
    {
        return $this->belongsTo(System::class);
    }

    public function performedBy()
    {
        return $this->belongsTo(UcmUser::class, 'performed_by');
    }
}
