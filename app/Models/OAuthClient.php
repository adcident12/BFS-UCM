<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class OAuthClient extends Model
{
    use SoftDeletes;

    protected $table = 'oauth_clients';

    protected $fillable = [
        'name',
        'slug',
        'client_id',
        'client_secret_hash',
        'redirect_uris',
        'allowed_scopes',
        'grant_types',
        'is_confidential',
        'auto_approve',
        'is_active',
        'description',
        'homepage_url',
        'registered_by',
    ];

    protected $casts = [
        'redirect_uris'  => 'array',
        'allowed_scopes' => 'array',
        'grant_types'    => 'array',
        'is_confidential' => 'boolean',
        'auto_approve'   => 'boolean',
        'is_active'      => 'boolean',
    ];

    protected $hidden = ['client_secret_hash'];

    protected $attributes = [
        'allowed_scopes' => '["openid","profile","email","permissions"]',
        'grant_types'    => '["authorization_code"]',
    ];

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeActive($query): void
    {
        $query->where('is_active', true);
    }

    // ── Relationships ─────────────────────────────────────────────────────────

    public function registeredBy(): BelongsTo
    {
        return $this->belongsTo(UcmUser::class, 'registered_by');
    }

    public function authorizationCodes(): HasMany
    {
        return $this->hasMany(OAuthAuthorizationCode::class, 'client_id');
    }

    public function accessTokens(): HasMany
    {
        return $this->hasMany(OAuthAccessToken::class, 'client_id');
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    public static function generateClientId(): string
    {
        return Str::random(32);
    }

    public static function generateClientSecret(): string
    {
        return Str::random(64);
    }

    public function verifySecret(string $secret): bool
    {
        if (! $this->is_confidential || ! $this->client_secret_hash) {
            return false;
        }

        return Hash::check($secret, $this->client_secret_hash);
    }

    public function allowsRedirectUri(string $uri): bool
    {
        return in_array($uri, $this->redirect_uris ?? [], true);
    }

    public function allowsScope(string $scope): bool
    {
        return in_array($scope, $this->allowed_scopes ?? [], true);
    }

    public function allowsGrantType(string $grantType): bool
    {
        return in_array($grantType, $this->grant_types ?? [], true);
    }
}
