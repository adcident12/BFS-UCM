<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class OAuthAccessToken extends Model
{
    protected $table = 'oauth_access_tokens';

    protected $fillable = [
        'jti',
        'client_id',
        'user_id',
        'scopes',
        'expires_at',
        'revoked_at',
    ];

    protected $casts = [
        'scopes'     => 'array',
        'expires_at' => 'datetime',
        'revoked_at' => 'datetime',
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

    public function refreshToken(): HasOne
    {
        return $this->hasOne(OAuthRefreshToken::class, 'access_token_id');
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function isRevoked(): bool
    {
        return $this->revoked_at !== null;
    }

    public function isValid(): bool
    {
        return ! $this->isRevoked() && ! $this->isExpired();
    }

    public function hasScope(string $scope): bool
    {
        return in_array($scope, $this->scopes ?? [], true);
    }

    public function revoke(): bool
    {
        $this->refreshToken?->revoke();

        return $this->update(['revoked_at' => now()]);
    }
}
