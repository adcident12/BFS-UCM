@extends('layouts.app')

@section('title', $oauthClient->name . ' — OAuth Client')
@section('header', $oauthClient->name)

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}" class="text-slate-400 hover:text-slate-600 font-medium transition-colors whitespace-nowrap">Dashboard</a>
    <svg class="w-3.5 h-3.5 text-slate-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    <a href="{{ route('admin.oauth-clients.index') }}" class="text-slate-400 hover:text-slate-600 font-medium transition-colors whitespace-nowrap">OAuth Clients</a>
    <svg class="w-3.5 h-3.5 text-slate-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    <span class="font-semibold text-slate-800 truncate">{{ $oauthClient->name }}</span>
@endsection

@section('content')

@if (session('success'))
    <div class="mb-5 flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-4 py-3 text-sm">
        <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="mb-5 flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 rounded-xl px-4 py-3 text-sm">
        <svg class="w-4 h-4 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        {{ session('error') }}
    </div>
@endif

{{-- One-time secret banner --}}
@if ($flashedSecret)
    <div class="mb-6 bg-amber-50 border border-amber-300 rounded-2xl p-5">
        <div class="flex items-start gap-3 mb-3">
            <svg class="w-5 h-5 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <div>
                <p class="text-sm font-semibold text-amber-800">Client Secret — คัดลอกและเก็บไว้ทันที</p>
                <p class="text-xs text-amber-700 mt-0.5">Secret นี้จะแสดงเพียงครั้งเดียว ไม่สามารถดูได้อีก หากสูญหายต้อง Rotate Secret</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <code id="secret-value"
                  class="flex-1 bg-white border border-amber-300 rounded-xl px-4 py-2.5 text-sm font-mono text-amber-900 break-all select-all">{{ $flashedSecret }}</code>
            <button onclick="navigator.clipboard.writeText(document.getElementById('secret-value').textContent.trim()); this.textContent='✓ Copied'"
                    class="flex-shrink-0 px-3 py-2.5 rounded-xl text-xs font-semibold bg-amber-500 hover:bg-amber-600 text-white transition-colors">
                Copy
            </button>
        </div>
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Left column: details --}}
    <div class="lg:col-span-2 space-y-5">

        {{-- Credentials --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <h3 class="text-sm font-semibold text-slate-700 mb-4">Credentials</h3>
            <dl class="space-y-3">
                <div class="flex items-center gap-3">
                    <dt class="text-xs font-semibold text-slate-500 w-28 flex-shrink-0">Client ID</dt>
                    <dd class="flex-1 flex items-center gap-2">
                        <code class="text-sm font-mono text-slate-800 bg-slate-100 rounded-lg px-3 py-1.5 flex-1 break-all">{{ $oauthClient->client_id }}</code>
                        <button onclick="navigator.clipboard.writeText('{{ $oauthClient->client_id }}')"
                                class="flex-shrink-0 text-xs text-slate-400 hover:text-slate-700 transition-colors">
                            Copy
                        </button>
                    </dd>
                </div>
                @if ($oauthClient->is_confidential)
                    <div class="flex items-center gap-3">
                        <dt class="text-xs font-semibold text-slate-500 w-28 flex-shrink-0">Client Secret</dt>
                        <dd class="flex-1 flex items-center gap-2">
                            <code class="text-sm font-mono text-slate-500 bg-slate-100 rounded-lg px-3 py-1.5">••••••••••••••••</code>
                            <form method="POST" action="{{ route('admin.oauth-clients.rotate-secret', $oauthClient) }}"
                                  onsubmit="return confirm('Rotate secret จะ revoke token ทั้งหมดที่มีอยู่ ยืนยันหรือไม่?')">
                                @csrf
                                <button type="submit"
                                        class="flex-shrink-0 text-xs font-medium text-amber-600 hover:text-amber-800 transition-colors">
                                    Rotate Secret
                                </button>
                            </form>
                        </dd>
                    </div>
                @endif
            </dl>
        </div>

        {{-- Config --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-slate-700">การตั้งค่า</h3>
                <a href="{{ route('admin.oauth-clients.edit', $oauthClient) }}"
                   class="text-xs font-medium text-indigo-600 hover:text-indigo-800 transition-colors">
                    แก้ไข
                </a>
            </div>
            <dl class="space-y-4">
                <div>
                    <dt class="text-xs font-semibold text-slate-500 mb-1">Redirect URIs</dt>
                    <dd class="space-y-1">
                        @foreach ($oauthClient->redirect_uris as $uri)
                            <div class="text-sm font-mono text-slate-700 bg-slate-50 rounded-lg px-3 py-1.5 break-all">{{ $uri }}</div>
                        @endforeach
                    </dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold text-slate-500 mb-1.5">Allowed Scopes</dt>
                    <dd class="flex flex-wrap gap-1.5">
                        @foreach ($oauthClient->allowed_scopes as $scope)
                            <span class="text-xs bg-indigo-50 text-indigo-700 border border-indigo-200 rounded-full px-2.5 py-0.5 font-mono">{{ $scope }}</span>
                        @endforeach
                    </dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold text-slate-500 mb-1.5">Grant Types</dt>
                    <dd class="flex flex-wrap gap-1.5">
                        @foreach ($oauthClient->grant_types as $grant)
                            <span class="text-xs bg-slate-100 text-slate-600 border border-slate-200 rounded-full px-2.5 py-0.5">{{ $grant }}</span>
                        @endforeach
                    </dd>
                </div>
                <div class="grid grid-cols-3 gap-4 pt-2 border-t border-slate-100">
                    <div>
                        <dt class="text-xs font-semibold text-slate-500 mb-1">ประเภท</dt>
                        <dd class="text-sm text-slate-700">{{ $oauthClient->is_confidential ? 'Confidential' : 'Public (PKCE)' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold text-slate-500 mb-1">Auto Approve</dt>
                        <dd class="text-sm text-slate-700">{{ $oauthClient->auto_approve ? 'เปิด' : 'ปิด (consent screen)' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold text-slate-500 mb-1">สถานะ</dt>
                        <dd>
                            @if ($oauthClient->is_active)
                                <span class="text-sm text-emerald-700 font-medium">Active</span>
                            @else
                                <span class="text-sm text-slate-500 font-medium">Inactive</span>
                            @endif
                        </dd>
                    </div>
                </div>
            </dl>
        </div>

        {{-- Recent Tokens --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <h3 class="text-sm font-semibold text-slate-700 mb-4">Token ล่าสุด (20 รายการ)</h3>
            @if ($recentTokens->isEmpty())
                <p class="text-sm text-slate-400">ยังไม่มี token</p>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-xs">
                        <thead>
                            <tr class="text-left text-slate-500 border-b border-slate-100">
                                <th class="pb-2 font-semibold">ผู้ใช้</th>
                                <th class="pb-2 font-semibold">Scopes</th>
                                <th class="pb-2 font-semibold">หมดอายุ</th>
                                <th class="pb-2 font-semibold">สถานะ</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @foreach ($recentTokens as $token)
                                <tr>
                                    <td class="py-2 font-medium text-slate-700">
                                        {{ $token->user?->name ?: $token->user?->username ?? 'M2M (client)' }}
                                    </td>
                                    <td class="py-2 text-slate-500">{{ implode(', ', $token->scopes ?? []) }}</td>
                                    <td class="py-2 text-slate-500">{{ $token->expires_at?->diffForHumans() }}</td>
                                    <td class="py-2">
                                        @if ($token->revoked_at)
                                            <span class="text-red-500">Revoked</span>
                                        @elseif ($token->expires_at?->isPast())
                                            <span class="text-slate-400">Expired</span>
                                        @else
                                            <span class="text-emerald-600">Active</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    {{-- Right column: meta + actions --}}
    <div class="space-y-5">

        {{-- Meta --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
            <dl class="space-y-3 text-sm">
                <div>
                    <dt class="text-xs font-semibold text-slate-500">Slug</dt>
                    <dd class="font-mono text-slate-700 mt-0.5">{{ $oauthClient->slug }}</dd>
                </div>
                @if ($oauthClient->homepage_url)
                    <div>
                        <dt class="text-xs font-semibold text-slate-500">Homepage</dt>
                        <dd class="mt-0.5">
                            <a href="{{ $oauthClient->homepage_url }}" target="_blank" rel="noopener noreferrer"
                               class="text-indigo-600 hover:text-indigo-800 underline break-all">
                                {{ $oauthClient->homepage_url }}
                            </a>
                        </dd>
                    </div>
                @endif
                <div>
                    <dt class="text-xs font-semibold text-slate-500">ลงทะเบียนโดย</dt>
                    <dd class="text-slate-700 mt-0.5">{{ $oauthClient->registeredBy?->name ?: $oauthClient->registeredBy?->username ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold text-slate-500">สร้างเมื่อ</dt>
                    <dd class="text-slate-700 mt-0.5">{{ $oauthClient->created_at->format('d/m/Y H:i') }}</dd>
                </div>
                @if ($oauthClient->description)
                    <div>
                        <dt class="text-xs font-semibold text-slate-500">คำอธิบาย</dt>
                        <dd class="text-slate-600 mt-0.5 leading-relaxed">{{ $oauthClient->description }}</dd>
                    </div>
                @endif
            </dl>
        </div>

        {{-- Actions --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 space-y-2">
            <a href="{{ route('admin.oauth-clients.edit', $oauthClient) }}"
               class="flex items-center justify-center gap-2 w-full px-4 py-2.5 rounded-xl text-sm font-medium text-slate-700 bg-slate-100 hover:bg-slate-200 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                แก้ไขการตั้งค่า
            </a>

            <form method="POST" action="{{ route('admin.oauth-clients.destroy', $oauthClient) }}"
                  onsubmit="return confirm('ลบ OAuth Client นี้จะ revoke token ทั้งหมดและแอปจะเชื่อมต่อไม่ได้ ยืนยันหรือไม่?')">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="flex items-center justify-center gap-2 w-full px-4 py-2.5 rounded-xl text-sm font-medium text-red-600 bg-red-50 hover:bg-red-100 border border-red-200 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    ลบ Client
                </button>
            </form>
        </div>

    </div>
</div>

@endsection
