<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\OAuthClient;
use App\Services\AuditLogger;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class OAuthClientController extends Controller
{
    // ── GET /admin/oauth-clients ───────────────────────────────────────────────

    public function index(): View
    {
        abort_unless(auth()->user()?->canAccess('oauth_clients'), 403, 'สิทธิ์ไม่เพียงพอ');

        $clients = OAuthClient::with('registeredBy')
            ->latest()
            ->paginate(20);

        return view('admin.oauth-clients.index', compact('clients'));
    }

    // ── GET /admin/oauth-clients/create ────────────────────────────────────────

    public function create(): View
    {
        abort_unless(auth()->user()?->canAccess('oauth_clients'), 403, 'สิทธิ์ไม่เพียงพอ');

        return view('admin.oauth-clients.create');
    }

    // ── POST /admin/oauth-clients ──────────────────────────────────────────────

    public function store(Request $request): RedirectResponse
    {
        abort_unless(auth()->user()?->canAccess('oauth_clients'), 403, 'สิทธิ์ไม่เพียงพอ');

        $data = $request->validate([
            'name'             => ['required', 'string', 'max:255'],
            'slug'             => ['required', 'string', 'max:80', 'unique:oauth_clients,slug', 'regex:/^[a-z0-9\-]+$/'],
            'description'      => ['nullable', 'string', 'max:1000'],
            'homepage_url'     => ['nullable', 'url', 'max:255'],
            'redirect_uris'    => ['required', 'string'],
            'allowed_scopes'   => ['required', 'array', 'min:1'],
            'allowed_scopes.*' => ['string', 'in:openid,profile,email,permissions,users:read'],
            'grant_types'      => ['required', 'array', 'min:1'],
            'grant_types.*'    => ['string', 'in:authorization_code,refresh_token,client_credentials'],
            'is_confidential'  => ['boolean'],
            'auto_approve'     => ['boolean'],
        ]);

        // Parse and sanitize redirect URIs (one per line)
        $redirectUris = array_values(array_filter(
            array_map('trim', explode("\n", $data['redirect_uris']))
        ));

        // Generate credentials
        $clientId   = 'ucm_'.Str::random(32);
        $rawSecret  = Str::random(64);
        $secretHash = bcrypt($rawSecret);

        $client = OAuthClient::create([
            'name'               => $data['name'],
            'slug'               => $data['slug'],
            'client_id'          => $clientId,
            'client_secret_hash' => $data['is_confidential'] ?? true ? $secretHash : null,
            'redirect_uris'      => $redirectUris,
            'allowed_scopes'     => $data['allowed_scopes'],
            'grant_types'        => $data['grant_types'],
            'is_confidential'    => $data['is_confidential'] ?? true,
            'auto_approve'       => $data['auto_approve'] ?? false,
            'description'        => $data['description'] ?? null,
            'homepage_url'       => $data['homepage_url'] ?? null,
            'registered_by'      => auth()->id(),
        ]);

        AuditLogger::log(
            AuditLog::CATEGORY_OAUTH,
            AuditLog::EVENT_OAUTH_CLIENT_CREATED,
            "สร้าง OAuth Client '{$client->name}' (slug: {$client->slug})",
            [
                'slug'            => $client->slug,
                'client_id'       => $client->client_id,
                'allowed_scopes'  => $client->allowed_scopes,
                'grant_types'     => $client->grant_types,
                'is_confidential' => $client->is_confidential,
                'auto_approve'    => $client->auto_approve,
            ],
            null,
            'oauth_client',
            $client->id,
            $client->name,
            $request,
        );

        app(NotificationService::class)->dispatch('oauth_client_created', [
            'client_name'  => $client->name,
            'client_id'    => $client->client_id,
            'performed_by' => auth()->user()?->username,
        ]);

        // Flash plain secret once — never stored in plain text
        session()->flash('client_secret', $rawSecret);

        return redirect()->route('admin.oauth-clients.show', $client)
            ->with('success', 'OAuth client created. Copy the client secret now — it will not be shown again.');
    }

    // ── GET /admin/oauth-clients/{client} ──────────────────────────────────────

    public function show(OAuthClient $oauthClient): View
    {
        abort_unless(auth()->user()?->canAccess('oauth_clients'), 403, 'สิทธิ์ไม่เพียงพอ');
        $oauthClient->load('registeredBy');

        $recentTokens = $oauthClient->accessTokens()
            ->with('user')
            ->latest()
            ->limit(20)
            ->get();

        $flashedSecret = session('client_secret');

        return view('admin.oauth-clients.show', compact('oauthClient', 'recentTokens', 'flashedSecret'));
    }

    // ── GET /admin/oauth-clients/{client}/edit ─────────────────────────────────

    public function edit(OAuthClient $oauthClient): View
    {
        abort_unless(auth()->user()?->canAccess('oauth_clients'), 403, 'สิทธิ์ไม่เพียงพอ');

        return view('admin.oauth-clients.edit', compact('oauthClient'));
    }

    // ── PUT /admin/oauth-clients/{client} ──────────────────────────────────────

    public function update(Request $request, OAuthClient $oauthClient): RedirectResponse
    {
        abort_unless(auth()->user()?->canAccess('oauth_clients'), 403, 'สิทธิ์ไม่เพียงพอ');

        $data = $request->validate([
            'name'             => ['required', 'string', 'max:255'],
            'description'      => ['nullable', 'string', 'max:1000'],
            'homepage_url'     => ['nullable', 'url', 'max:255'],
            'redirect_uris'    => ['required', 'string'],
            'allowed_scopes'   => ['required', 'array', 'min:1'],
            'allowed_scopes.*' => ['string', 'in:openid,profile,email,permissions,users:read'],
            'grant_types'      => ['required', 'array', 'min:1'],
            'grant_types.*'    => ['string', 'in:authorization_code,refresh_token,client_credentials'],
            'auto_approve'     => ['boolean'],
            'is_active'        => ['boolean'],
        ]);

        $redirectUris = array_values(array_filter(
            array_map('trim', explode("\n", $data['redirect_uris']))
        ));

        // Capture changed fields for audit
        $before = $oauthClient->only(['name', 'allowed_scopes', 'grant_types', 'auto_approve', 'is_active', 'redirect_uris']);

        $oauthClient->update([
            'name'           => $data['name'],
            'description'    => $data['description'] ?? null,
            'homepage_url'   => $data['homepage_url'] ?? null,
            'redirect_uris'  => $redirectUris,
            'allowed_scopes' => $data['allowed_scopes'],
            'grant_types'    => $data['grant_types'],
            'auto_approve'   => $data['auto_approve'] ?? false,
            'is_active'      => $data['is_active'] ?? true,
        ]);

        AuditLogger::log(
            AuditLog::CATEGORY_OAUTH,
            AuditLog::EVENT_OAUTH_CLIENT_UPDATED,
            "แก้ไข OAuth Client '{$oauthClient->name}' (slug: {$oauthClient->slug})",
            ['before' => $before, 'after' => $oauthClient->only(array_keys($before))],
            null,
            'oauth_client',
            $oauthClient->id,
            $oauthClient->name,
            $request,
        );

        app(NotificationService::class)->dispatch('oauth_client_updated', [
            'client_name'  => $oauthClient->name,
            'performed_by' => auth()->user()?->username,
        ]);

        return redirect()->route('admin.oauth-clients.show', $oauthClient)
            ->with('success', 'OAuth client updated.');
    }

    // ── DELETE /admin/oauth-clients/{client} ───────────────────────────────────

    public function destroy(OAuthClient $oauthClient): RedirectResponse
    {
        abort_unless(auth()->user()?->canAccess('oauth_clients'), 403, 'สิทธิ์ไม่เพียงพอ');

        $revokedCount = $oauthClient->accessTokens()->whereNull('revoked_at')->update(['revoked_at' => now()]);
        $oauthClient->delete();

        AuditLogger::log(
            AuditLog::CATEGORY_OAUTH,
            AuditLog::EVENT_OAUTH_CLIENT_DELETED,
            "ลบ OAuth Client '{$oauthClient->name}' (slug: {$oauthClient->slug}) และ revoke tokens {$revokedCount} รายการ",
            ['slug' => $oauthClient->slug, 'client_id' => $oauthClient->client_id, 'tokens_revoked' => $revokedCount],
            null,
            'oauth_client',
            $oauthClient->id,
            $oauthClient->name,
        );

        app(NotificationService::class)->dispatch('oauth_client_deleted', [
            'client_name'   => $oauthClient->name,
            'tokens_revoked' => $revokedCount,
            'performed_by'  => auth()->user()?->username,
        ]);

        return redirect()->route('admin.oauth-clients.index')
            ->with('success', "OAuth client \"{$oauthClient->name}\" has been revoked and deleted.");
    }

    // ── POST /admin/oauth-clients/{client}/rotate-secret ──────────────────────

    /**
     * Rotate the client secret and revoke all existing tokens.
     * The new plain-text secret is flashed once to the session.
     */
    public function rotateSecret(OAuthClient $oauthClient): RedirectResponse
    {
        abort_unless(auth()->user()?->canAccess('oauth_clients'), 403, 'สิทธิ์ไม่เพียงพอ');

        if (! $oauthClient->is_confidential) {
            return back()->with('error', 'Public clients do not have a secret.');
        }

        $rawSecret = Str::random(64);

        $revokedCount = $oauthClient->accessTokens()->whereNull('revoked_at')->update(['revoked_at' => now()]);
        $oauthClient->update(['client_secret_hash' => bcrypt($rawSecret)]);

        AuditLogger::log(
            AuditLog::CATEGORY_OAUTH,
            AuditLog::EVENT_OAUTH_SECRET_ROTATED,
            "Rotate secret ของ OAuth Client '{$oauthClient->name}' และ revoke tokens {$revokedCount} รายการ",
            ['slug' => $oauthClient->slug, 'tokens_revoked' => $revokedCount],
            null,
            'oauth_client',
            $oauthClient->id,
            $oauthClient->name,
        );

        app(NotificationService::class)->dispatch('oauth_secret_rotated', [
            'client_name'    => $oauthClient->name,
            'tokens_revoked' => $revokedCount,
            'performed_by'   => auth()->user()?->username,
        ]);

        session()->flash('client_secret', $rawSecret);

        return redirect()->route('admin.oauth-clients.show', $oauthClient)
            ->with('success', 'Secret rotated. All previous tokens have been revoked. Copy the new secret now.');
    }
}
