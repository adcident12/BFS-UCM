@extends('layouts.app')

@section('title', 'OAuth Clients')
@section('header', 'OAuth Clients')

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}" class="text-slate-400 hover:text-slate-600 font-medium transition-colors whitespace-nowrap">Dashboard</a>
    <svg class="w-3.5 h-3.5 text-slate-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    <span class="font-semibold text-slate-800 truncate">OAuth Clients</span>
@endsection

@section('content')

@if (session('success'))
    <div class="mb-5 flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-4 py-3 text-sm">
        <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        {{ session('success') }}
    </div>
@endif

{{-- Hero Banner --}}
<div class="mb-8 relative overflow-hidden rounded-2xl"
     style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 45%, #0c1a2e 100%)">
    <div class="absolute inset-0 pointer-events-none overflow-hidden">
        <div class="absolute -top-12 -right-12 w-72 h-72 bg-indigo-500/6 rounded-full blur-2xl"></div>
        <div class="absolute -bottom-16 left-24 w-56 h-56 bg-violet-400/5 rounded-full blur-2xl"></div>
        <div class="absolute inset-0"
             style="background-image:linear-gradient(rgba(255,255,255,0.025) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,0.025) 1px,transparent 1px);background-size:28px 28px"></div>
    </div>
    <div class="relative px-6 md:px-8 py-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-white/8 backdrop-blur-sm rounded-2xl flex items-center justify-center flex-shrink-0 border border-white/10">
                <svg class="w-6 h-6 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                          d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                </svg>
            </div>
            <div>
                <h2 class="text-lg font-bold text-white">OAuth Clients</h2>
                <p class="text-slate-400 text-xs font-medium mt-0.5">
                    ระบบภายนอกที่ได้รับอนุญาตให้ใช้งาน SSO และ API ของ UCM
                </p>
            </div>
        </div>
        <a href="{{ route('admin.oauth-clients.create') }}"
           class="inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-500/20 hover:bg-indigo-500/30 border border-indigo-400/30 rounded-xl text-indigo-300 hover:text-indigo-200 text-xs font-semibold transition-all">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            เพิ่ม OAuth Client
        </a>
    </div>
</div>

@if ($clients->isEmpty())

    {{-- Empty State --}}
    <div class="bg-white rounded-2xl border border-dashed border-slate-300 p-16 text-center">
        <div class="w-12 h-12 bg-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
            <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
            </svg>
        </div>
        <h2 class="text-sm font-semibold text-slate-700 mb-1">ยังไม่มี OAuth Client</h2>
        <p class="text-xs text-slate-400 mb-5">เพิ่ม OAuth Client เพื่ออนุญาตให้ระบบภายนอกใช้งาน SSO และ API ของ UCM</p>
        <a href="{{ route('admin.oauth-clients.create') }}"
           class="inline-flex items-center gap-2 text-sm font-semibold text-white bg-gradient-to-br from-indigo-500 to-violet-600 px-5 py-2.5 rounded-xl shadow-md shadow-indigo-200 hover:opacity-90 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            เพิ่ม OAuth Client แรก
        </a>
    </div>

@else

    {{-- Table --}}
    <div class="bg-white rounded-2xl shadow-sm ring-1 ring-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-100 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">
                        <th class="px-5 py-3.5">แอปพลิเคชัน</th>
                        <th class="px-5 py-3.5">Client ID</th>
                        <th class="px-5 py-3.5">ประเภท</th>
                        <th class="px-5 py-3.5">Scopes</th>
                        <th class="px-5 py-3.5">สถานะ</th>
                        <th class="px-5 py-3.5">ลงทะเบียนโดย</th>
                        <th class="px-5 py-3.5"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($clients as $client)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-5 py-4">
                                <div class="font-semibold text-slate-800">{{ $client->name }}</div>
                                @if ($client->description)
                                    <div class="text-xs text-slate-400 mt-0.5 truncate max-w-xs">{{ $client->description }}</div>
                                @endif
                            </td>
                            <td class="px-5 py-4 font-mono text-xs text-slate-500">
                                {{ Str::limit($client->client_id, 20) }}
                            </td>
                            <td class="px-5 py-4">
                                @if ($client->is_confidential)
                                    <span class="inline-flex items-center gap-1 text-xs font-medium text-indigo-700 bg-indigo-50 border border-indigo-200 rounded-full px-2.5 py-0.5">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                        Confidential
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 text-xs font-medium text-amber-700 bg-amber-50 border border-amber-200 rounded-full px-2.5 py-0.5">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                                        Public (PKCE)
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex flex-wrap gap-1">
                                    @foreach ($client->allowed_scopes as $scope)
                                        <span class="text-xs bg-slate-100 text-slate-600 rounded px-1.5 py-0.5 font-mono">{{ $scope }}</span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                @if ($client->is_active)
                                    <span class="inline-flex items-center gap-1 text-xs font-medium text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-full px-2.5 py-0.5">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 text-xs font-medium text-slate-500 bg-slate-100 border border-slate-200 rounded-full px-2.5 py-0.5">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                        Inactive
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-xs text-slate-500">
                                {{ $client->registeredBy?->name ?: $client->registeredBy?->username ?? '—' }}
                            </td>
                            <td class="px-5 py-4 text-right">
                                <a href="{{ route('admin.oauth-clients.show', $client) }}"
                                   class="inline-flex items-center gap-1.5 text-xs font-semibold text-indigo-600 hover:text-indigo-800 transition-colors">
                                    ดูรายละเอียด
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if ($clients->hasPages())
            <div class="px-5 py-4 border-t border-slate-100 bg-slate-50/30">
                {{ $clients->links() }}
            </div>
        @endif
    </div>

@endif

@endsection
