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
    <div class="mb-6 bg-amber-50 border border-amber-300 rounded-2xl overflow-hidden">
        <div class="flex items-start gap-3 px-5 py-4 border-b border-amber-200">
            <svg class="w-5 h-5 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <div>
                <p class="text-sm font-semibold text-amber-800">Client Secret — คัดลอกและเก็บไว้ทันที</p>
                <p class="text-xs text-amber-700 mt-0.5">Secret นี้จะแสดงเพียงครั้งเดียว ไม่สามารถดูได้อีก หากสูญหายต้อง Rotate Secret</p>
            </div>
        </div>
        <div class="flex items-center gap-2 px-5 py-4">
            <code id="secret-value"
                  class="flex-1 bg-white border border-amber-200 rounded-xl px-4 py-2.5 text-sm font-mono text-amber-900 break-all select-all">{{ $flashedSecret }}</code>
            <button onclick="navigator.clipboard.writeText(document.getElementById('secret-value').textContent.trim()).then(()=>{this.innerHTML='<svg class=\'w-4 h-4\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M5 13l4 4L19 7\'/></svg> Copied';this.classList.add(\'bg-emerald-600\');})"
                    class="flex-shrink-0 inline-flex items-center gap-1.5 px-3.5 py-2.5 rounded-xl text-xs font-semibold bg-amber-500 hover:bg-amber-600 text-white transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                Copy
            </button>
        </div>
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Left column: details --}}
    <div class="lg:col-span-2 space-y-5">

        {{-- Credentials --}}
        <div class="bg-white rounded-2xl shadow-sm ring-1 ring-slate-100 overflow-hidden">
            <div class="flex items-center gap-3 px-6 py-4 border-b border-slate-100 bg-slate-50/80">
                <div class="w-8 h-8 bg-sky-100 rounded-xl flex items-center justify-center">
                    <svg class="w-4 h-4 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                    </svg>
                </div>
                <h3 class="font-bold text-slate-800">Credentials</h3>
            </div>
            <div class="px-6 py-5 space-y-3">

                <div class="flex items-center gap-3">
                    <span class="text-xs font-semibold text-slate-500 w-28 flex-shrink-0">Client ID</span>
                    <div class="flex-1 flex items-center gap-2 min-w-0">
                        <code class="flex-1 text-sm font-mono text-slate-800 bg-slate-100 rounded-lg px-3 py-1.5 break-all">{{ $oauthClient->client_id }}</code>
                        <button onclick="navigator.clipboard.writeText('{{ addslashes($oauthClient->client_id) }}').then(()=>{this.textContent='✓';setTimeout(()=>{this.textContent='Copy'},1500)})"
                                class="flex-shrink-0 text-xs font-medium text-slate-400 hover:text-indigo-600 transition-colors px-1">
                            Copy
                        </button>
                    </div>
                </div>

                @if ($oauthClient->is_confidential)
                    <div class="flex items-center gap-3">
                        <span class="text-xs font-semibold text-slate-500 w-28 flex-shrink-0">Client Secret</span>
                        <div class="flex-1 flex items-center gap-2">
                            <code class="text-sm font-mono text-slate-400 bg-slate-100 rounded-lg px-3 py-1.5">••••••••••••••••</code>
                            <form method="POST" action="{{ route('admin.oauth-clients.rotate-secret', $oauthClient) }}"
                                  onsubmit="return confirm('Rotate secret จะ revoke token ทั้งหมดที่มีอยู่ ยืนยันหรือไม่?')">
                                @csrf
                                <button type="submit"
                                        class="text-xs font-semibold text-amber-600 hover:text-amber-800 transition-colors">
                                    Rotate Secret
                                </button>
                            </form>
                        </div>
                    </div>
                @endif

            </div>
        </div>

        {{-- Configuration --}}
        <div class="bg-white rounded-2xl shadow-sm ring-1 ring-slate-100 overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 bg-slate-50/80">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-indigo-100 rounded-xl flex items-center justify-center">
                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <h3 class="font-bold text-slate-800">การตั้งค่า</h3>
                </div>
                <a href="{{ route('admin.oauth-clients.edit', $oauthClient) }}"
                   class="inline-flex items-center gap-1 text-xs font-semibold text-indigo-600 hover:text-indigo-800 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    แก้ไข
                </a>
            </div>
            <div class="px-6 py-5 space-y-4">

                <div>
                    <p class="text-xs font-semibold text-slate-500 mb-1.5">Redirect URIs</p>
                    <div class="space-y-1">
                        @foreach ($oauthClient->redirect_uris as $uri)
                            <div class="text-sm font-mono text-slate-700 bg-slate-50 rounded-lg px-3 py-1.5 break-all border border-slate-100">{{ $uri }}</div>
                        @endforeach
                    </div>
                </div>

                <div>
                    <p class="text-xs font-semibold text-slate-500 mb-1.5">Allowed Scopes</p>
                    <div class="flex flex-wrap gap-1.5">
                        @foreach ($oauthClient->allowed_scopes as $scope)
                            <span class="text-xs bg-indigo-50 text-indigo-700 border border-indigo-200 rounded-full px-2.5 py-0.5 font-mono">{{ $scope }}</span>
                        @endforeach
                    </div>
                </div>

                <div>
                    <p class="text-xs font-semibold text-slate-500 mb-1.5">Grant Types</p>
                    <div class="flex flex-wrap gap-1.5">
                        @foreach ($oauthClient->grant_types as $grant)
                            <span class="text-xs bg-violet-50 text-violet-700 border border-violet-200 rounded-full px-2.5 py-0.5">{{ $grant }}</span>
                        @endforeach
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-4 pt-3 border-t border-slate-100">
                    <div>
                        <p class="text-xs font-semibold text-slate-500 mb-1">ประเภท</p>
                        <p class="text-sm text-slate-700 font-medium">{{ $oauthClient->is_confidential ? 'Confidential' : 'Public (PKCE)' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-slate-500 mb-1">Auto Approve</p>
                        <p class="text-sm font-medium {{ $oauthClient->auto_approve ? 'text-emerald-700' : 'text-slate-500' }}">
                            {{ $oauthClient->auto_approve ? 'เปิด' : 'ปิด (consent screen)' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-slate-500 mb-1">สถานะ</p>
                        @if ($oauthClient->is_active)
                            <span class="inline-flex items-center gap-1 text-xs font-semibold text-emerald-700">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Active
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 text-xs font-semibold text-slate-500">
                                <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> Inactive
                            </span>
                        @endif
                    </div>
                </div>

            </div>
        </div>

        {{-- Recent Tokens --}}
        <div class="bg-white rounded-2xl shadow-sm ring-1 ring-slate-100 overflow-hidden">
            <div class="flex items-center gap-3 px-6 py-4 border-b border-slate-100 bg-slate-50/80">
                <div class="w-8 h-8 bg-slate-100 rounded-xl flex items-center justify-center">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <h3 class="font-bold text-slate-800">Token ล่าสุด</h3>
                <span class="ml-auto text-xs text-slate-400">20 รายการ</span>
            </div>

            @if ($recentTokens->isEmpty())
                <div class="px-6 py-8 text-center">
                    <p class="text-sm text-slate-400">ยังไม่มี token ที่ออกให้กับ client นี้</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-xs">
                        <thead>
                            <tr class="bg-slate-50/70 border-b border-slate-100 text-left">
                                <th class="px-6 py-3 font-semibold text-slate-500 uppercase tracking-wide">ผู้ใช้</th>
                                <th class="px-6 py-3 font-semibold text-slate-500 uppercase tracking-wide">Scopes</th>
                                <th class="px-6 py-3 font-semibold text-slate-500 uppercase tracking-wide">หมดอายุ</th>
                                <th class="px-6 py-3 font-semibold text-slate-500 uppercase tracking-wide">สถานะ</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @foreach ($recentTokens as $token)
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="px-6 py-3 font-medium text-slate-700">
                                        {{ $token->user?->name ?: $token->user?->username ?? 'M2M (client)' }}
                                    </td>
                                    <td class="px-6 py-3 text-slate-500 font-mono">{{ implode(', ', $token->scopes ?? []) }}</td>
                                    <td class="px-6 py-3 text-slate-500">{{ $token->expires_at?->diffForHumans() ?? '—' }}</td>
                                    <td class="px-6 py-3">
                                        @if ($token->revoked_at)
                                            <span class="inline-flex items-center gap-1 text-red-600 font-semibold">
                                                <span class="w-1.5 h-1.5 rounded-full bg-red-400"></span> Revoked
                                            </span>
                                        @elseif ($token->expires_at?->isPast())
                                            <span class="inline-flex items-center gap-1 text-slate-400 font-semibold">
                                                <span class="w-1.5 h-1.5 rounded-full bg-slate-300"></span> Expired
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 text-emerald-600 font-semibold">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> Active
                                            </span>
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
        <div class="bg-white rounded-2xl shadow-sm ring-1 ring-slate-100 overflow-hidden">
            <div class="flex items-center gap-3 px-5 py-4 border-b border-slate-100 bg-slate-50/80">
                <div class="w-7 h-7 bg-slate-100 rounded-lg flex items-center justify-center">
                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="font-bold text-slate-700 text-sm">ข้อมูล</h3>
            </div>
            <div class="px-5 py-4 space-y-3 text-sm">

                <div>
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-0.5">Slug</p>
                    <p class="font-mono text-slate-700 text-xs bg-slate-50 rounded-lg px-2.5 py-1.5 border border-slate-100">{{ $oauthClient->slug }}</p>
                </div>

                @if ($oauthClient->description)
                    <div>
                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-0.5">คำอธิบาย</p>
                        <p class="text-slate-600 text-xs leading-relaxed">{{ $oauthClient->description }}</p>
                    </div>
                @endif

                @if ($oauthClient->homepage_url)
                    <div>
                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-0.5">Homepage</p>
                        <a href="{{ $oauthClient->homepage_url }}" target="_blank" rel="noopener noreferrer"
                           class="text-xs text-indigo-600 hover:text-indigo-800 underline break-all">
                            {{ $oauthClient->homepage_url }}
                        </a>
                    </div>
                @endif

                <div>
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-0.5">ลงทะเบียนโดย</p>
                    <p class="text-slate-700 text-xs">{{ $oauthClient->registeredBy?->name ?: $oauthClient->registeredBy?->username ?? '—' }}</p>
                </div>

                <div>
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-0.5">สร้างเมื่อ</p>
                    <p class="text-slate-700 text-xs">{{ $oauthClient->created_at->format('d/m/Y H:i') }}</p>
                </div>

            </div>
        </div>

        {{-- Actions --}}
        <div class="bg-white rounded-2xl shadow-sm ring-1 ring-slate-100 overflow-hidden">
            <div class="flex items-center gap-3 px-5 py-4 border-b border-slate-100 bg-slate-50/80">
                <div class="w-7 h-7 bg-slate-100 rounded-lg flex items-center justify-center">
                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>
                    </svg>
                </div>
                <h3 class="font-bold text-slate-700 text-sm">การดำเนินการ</h3>
            </div>
            <div class="px-5 py-4 space-y-2">

                <a href="{{ route('admin.oauth-clients.edit', $oauthClient) }}"
                   class="flex items-center gap-2 w-full px-4 py-2.5 rounded-xl text-sm font-semibold text-indigo-600 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 transition-all hover:-translate-y-0.5 hover:shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    แก้ไขการตั้งค่า
                </a>

                <form method="POST" action="{{ route('admin.oauth-clients.destroy', $oauthClient) }}"
                      onsubmit="return confirm('ลบ OAuth Client นี้จะ revoke token ทั้งหมดและแอปจะเชื่อมต่อไม่ได้ ยืนยันหรือไม่?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="flex items-center gap-2 w-full px-4 py-2.5 rounded-xl text-sm font-semibold text-red-600 bg-red-50 hover:bg-red-100 border border-red-200 transition-all hover:-translate-y-0.5 hover:shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        ลบ Client
                    </button>
                </form>

            </div>
        </div>

    </div>

</div>

@endsection
