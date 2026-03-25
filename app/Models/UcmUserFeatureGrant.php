<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UcmUserFeatureGrant extends Model
{
    protected $fillable = [
        'user_id',
        'feature_key',
        'granted_by',
        'granted_at',
    ];

    protected $casts = [
        'granted_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(UcmUser::class, 'user_id');
    }

    public function grantedBy(): BelongsTo
    {
        return $this->belongsTo(UcmUser::class, 'granted_by');
    }
}
