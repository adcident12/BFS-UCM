<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MatrixShareLink extends Model
{
    protected $fillable = [
        'token',
        'label',
        'filter_system_ids',
        'filter_departments',
        'filter_usernames',
        'is_active',
        'expires_at',
        'last_viewed_at',
        'view_count',
        'created_by',
        'revoked_by',
        'revoked_at',
    ];

    protected $casts = [
        'filter_system_ids'  => 'array',
        'filter_departments' => 'array',
        'filter_usernames'   => 'array',
        'is_active'          => 'boolean',
        'expires_at'         => 'datetime',
        'last_viewed_at'     => 'datetime',
        'revoked_at'         => 'datetime',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(UcmUser::class, 'created_by');
    }

    public function revoker(): BelongsTo
    {
        return $this->belongsTo(UcmUser::class, 'revoked_by');
    }

    public function isAccessible(): bool
    {
        return $this->is_active && $this->expires_at->isFuture();
    }

    public function recordView(): void
    {
        $this->increment('view_count');
        $this->update(['last_viewed_at' => now()]);
    }

    public function hasFilters(): bool
    {
        return ! empty($this->filter_system_ids)
            || ! empty($this->filter_departments)
            || ! empty($this->filter_usernames);
    }

    public static function generateToken(): string
    {
        return bin2hex(random_bytes(32));
    }
}
