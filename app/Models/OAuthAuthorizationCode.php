<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OAuthAuthorizationCode extends Model
{
    protected $table = 'oauth_authorization_codes';

    public $timestamps = false;

    protected $fillable = [
        'code',
        'client_id',
        'user_id',
        'scopes',
        'redirect_uri',
        'expires_at',
        'code_challenge',
        'code_challenge_method',
        'state',
        'nonce',
        'is_used',
        'created_at',
    ];

    protected $casts = [
        'scopes'     => 'array',
        'is_used'    => 'boolean',
        'expires_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    // ── Relationships ─────────────────────────────────────────────────────────

    public function client(): BelongsTo
    {
        return $this->belongsTo(OAuthClient::class, 'client_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(UcmUser::class, 'user_id');
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function isValid(): bool
    {
        return ! $this->is_used && ! $this->isExpired();
    }
}
