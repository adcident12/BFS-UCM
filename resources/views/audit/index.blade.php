@extends('layouts.app')

@section('title', 'Audit Log')

@push('styles')
<style>
.preset-btn {
    color: #64748b;
}
.preset-btn:hover {
    background-color: #eef2ff;
    color: #4f46e5;
    box-shadow: 0 1px 2px 0 rgb(0 0 0 / .05);
}
.preset-btn[data-active="1"] {
    background-color: #4f46e5;
    color: #ffffff;
    box-shadow: 0 1px 2px 0 rgb(79 70 229 / .2);
}
.preset-btn[data-active="1"]:hover {
    background-color: #4338ca;
    color: #ffffff;
}
</style>
@endpush

@section('breadcrumbs')
<a href="{{ route('dashboard') }}" class="text-slate-400 hover:text-slate-600 font-medium transition-colors whitespace-nowrap">Dashboard</a>
<svg class="w-3.5 h-3.5 text-slate-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
<span class="font-semibold text-slate-800 truncate">Audit Log</span>
@endsection

@section('content')

{{-- ── Hero Banner ─────────────────────────────────────────────────────── --}}
<div class="mb-7 relative overflow-hidden rounded-2xl"
     style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 45%, #0c1a2e 100%)">
    <div class="absolute inset-0 pointer-events-none overflow-hidden">
        <div class="absolute -top-12 -right-12 w-72 h-72 bg-violet-500/6 rounded-full blur-2xl"></div>
        <div class="absolute -bottom-16 left-24 w-56 h-56 bg-cyan-400/5 rounded-full blur-2xl"></div>
        <div class="absolute top-4 right-1/3 w-32 h-32 bg-indigo-400/5 rounded-full blur-xl"></div>
        <div class="absolute inset-0"
             style="background-image:linear-gradient(rgba(255,255,255,0.025) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,0.025) 1px,transparent 1px);background-size:28px 28px"></div>
    </div>
    <div class="relative px-6 md:px-8 py-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-white/8 backdrop-blur-sm rounded-2xl flex items-center justify-center flex-shrink-0 border border-white/10">
                <svg class="w-6 h-6 text-violet-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                </svg>
            </div>
            <div>
                <h2 class="text-lg font-bold text-white">Audit Log</h2>
                <p class="text-slate-400 text-xs font-medium mt-0.5">บันทึกการกระทำและเหตุการณ์ทั้งหมดในระบบ UCM</p>
            </div>
        </div>
        <div class="flex items-center gap-3 flex-shrink-0 flex-wrap">
            <div class="inline-flex items-center gap-2 px-3.5 py-2 bg-white/6 border border-white/10 rounded-xl">
                <div class="w-1.5 h-1.5 rounded-full bg-violet-400"></div>
                <span class="text-slate-300 text-xs font-semibold">{{ number_format($logs->total()) }} รายการ</span>
            </div>
            <div class="inline-flex items-center gap-2 px-3.5 py-2 bg-white/6 border border-white/10 rounded-xl">
                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span class="text-slate-300 text-xs font-semibold">{{ now()->format('d M Y') }}</span>
            </div>
            <a href="{{ route('audit.index') }}"
               class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-white/8 hover:bg-white/15 border border-white/10 rounded-xl text-white/70 hover:text-white text-xs font-semibold transition-all">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Reset
            </a>
        </div>
    </div>
</div>

{{-- ── Stats Pills ──────────────────────────────────────────────────────── --}}
@php
    $statDefs = [
        'auth'        => ['label' => 'Auth',        'color' => 'purple', 'bg' => 'bg-purple-50',  'ring' => 'ring-purple-100', 'text' => 'text-purple-700', 'icon_color' => 'text-purple-500'],
        'users'       => ['label' => 'ผู้ใช้',       'color' => 'blue',   'bg' => 'bg-blue-50',    'ring' => 'ring-blue-100',   'text' => 'text-blue-700',   'icon_color' => 'text-blue-500'],
        'permissions' => ['label' => 'สิทธิ์',       'color' => 'indigo', 'bg' => 'bg-indigo-50',  'ring' => 'ring-indigo-100', 'text' => 'text-indigo-700', 'icon_color' => 'text-indigo-500'],
        'systems'     => ['label' => 'ระบบ',         'color' => 'orange', 'bg' => 'bg-orange-50',  'ring' => 'ring-orange-100', 'text' => 'text-orange-700', 'icon_color' => 'text-orange-500'],
        'connectors'  => ['label' => 'Connectors',  'color' => 'teal',   'bg' => 'bg-teal-50',    'ring' => 'ring-teal-100',   'text' => 'text-teal-700',   'icon_color' => 'text-teal-500'],
        'api'         => ['label' => 'API',          'color' => 'slate',  'bg' => 'bg-slate-50',   'ring' => 'ring-slate-100',  'text' => 'text-slate-700',  'icon_color' => 'text-slate-500'],
    ];
@endphp
<div class="grid grid-cols-3 sm:grid-cols-6 gap-3 mb-6">
    @foreach ($statDefs as $key => $s)
        @php $cnt = $categoryCounts[$key] ?? 0; @endphp
        <div class="bg-white rounded-2xl ring-1 {{ $s['ring'] }} px-4 py-3.5 flex flex-col items-center gap-1 shadow-sm">
            <span class="text-2xl font-bold {{ $s['text'] }}">{{ number_format($cnt) }}</span>
            <span class="text-xs font-medium text-slate-500">{{ $s['label'] }}</span>
        </div>
    @endforeach
</div>

{{-- ── Filter Panel ─────────────────────────────────────────────────────── --}}
<div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 mb-5">

    {{-- Category Tabs --}}
    @php
        $categories = [
            'all'         => ['label' => 'ทั้งหมด',     'icon' => 'M4 6h16M4 10h16M4 14h16M4 18h16'],
            'auth'        => ['label' => 'Auth',         'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'],
            'users'       => ['label' => 'ผู้ใช้',       'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
            'permissions' => ['label' => 'สิทธิ์',       'icon' => 'M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z'],
            'systems'     => ['label' => 'ระบบ',         'icon' => 'M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01'],
            'connectors'  => ['label' => 'Connectors',  'icon' => 'M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1'],
            'api'         => ['label' => 'API',          'icon' => 'M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4'],
        ];
        $currentCategory = request('category', '');
    @endphp

    <div class="flex flex-wrap gap-1.5 mb-5">
        @foreach ($categories as $key => $cat)
            @php
                $isActive = ($key === 'all' && ! $currentCategory) || $key === $currentCategory;
                $href = $key === 'all'
                    ? route('audit.index', array_filter(['search' => request('search'), 'date_from' => request('date_from'), 'date_to' => request('date_to')]))
                    : route('audit.index', array_filter(['category' => $key, 'search' => request('search'), 'date_from' => request('date_from'), 'date_to' => request('date_to')]));
            @endphp
            <a href="{{ $href }}"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold transition-all border
                      {{ $isActive
                          ? 'bg-indigo-600 text-white border-indigo-600 shadow-sm shadow-indigo-200'
                          : 'bg-white text-slate-500 border-slate-200 hover:border-indigo-300 hover:text-indigo-600 hover:bg-indigo-50/50' }}">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $cat['icon'] }}"/>
                </svg>
                {{ $cat['label'] }}
            </a>
        @endforeach
    </div>

    {{-- Search + Date Row --}}
    <form id="filter-form" method="GET" action="{{ route('audit.index') }}">
        @if ($currentCategory)
            <input type="hidden" name="category" value="{{ $currentCategory }}">
        @endif

        <div class="flex flex-col lg:flex-row gap-3">

            {{-- Search --}}
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input type="text"
                       name="search"
                       id="search-input"
                       value="{{ request('search') }}"
                       placeholder="ค้นหา username, คำอธิบาย, subject..."
                       class="w-full pl-9 pr-4 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-300 focus:border-indigo-300 bg-white placeholder-slate-400 transition-all">
            </div>

            {{-- Date Presets + Inputs --}}
            <div class="flex flex-col sm:flex-row gap-2">

                {{-- Preset Buttons --}}
                <div class="flex items-center gap-1 bg-slate-50 border border-slate-200 rounded-xl p-1" id="preset-group">
                    @foreach ([
                        ['CD', 'วันนี้'],
                        ['PD', 'เมื่อวาน'],
                        ['CW', 'อาทิตย์นี้'],
                        ['PW', 'อาทิตย์ก่อน'],
                        ['CM', 'เดือนนี้'],
                        ['PM', 'เดือนก่อน'],
                    ] as [$code, $label])
                        <button type="button"
                                data-preset="{{ $code }}"
                                title="{{ $label }}"
                                class="preset-btn px-2.5 py-1.5 rounded-lg text-xs font-bold transition-all">
                            {{ $code }}
                        </button>
                    @endforeach
                </div>

                {{-- Date From --}}
                <div class="relative">
                    <input type="text"
                           name="date_from"
                           id="date-from"
                           value="{{ request('date_from') }}"
                           placeholder="วันเริ่มต้น"
                           autocomplete="off"
                           class="w-full sm:w-40 px-3 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-300 focus:border-indigo-300 bg-white text-slate-700 placeholder-slate-400 cursor-pointer transition-all">
                </div>

                {{-- Date To --}}
                <div class="relative">
                    <input type="text"
                           name="date_to"
                           id="date-to"
                           value="{{ request('date_to') }}"
                           placeholder="วันสิ้นสุด"
                           autocomplete="off"
                           class="w-full sm:w-40 px-3 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-300 focus:border-indigo-300 bg-white text-slate-700 placeholder-slate-400 cursor-pointer transition-all">
                </div>

                {{-- Actions --}}
                <button type="submit"
                        class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl transition-colors shadow-sm shadow-indigo-200 whitespace-nowrap">
                    ค้นหา
                </button>

                @if (request('search') || request('date_from') || request('date_to'))
                    <a href="{{ route('audit.index', array_filter(['category' => $currentCategory])) }}"
                       class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 text-sm font-semibold rounded-xl transition-colors text-center whitespace-nowrap">
                        ล้าง
                    </a>
                @endif
            </div>
        </div>
    </form>
</div>

{{-- ── Table ────────────────────────────────────────────────────────────── --}}
<div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
    @if ($logs->isEmpty())
        <div class="flex flex-col items-center justify-center py-24 text-slate-400">
            <div class="w-16 h-16 rounded-2xl bg-slate-50 flex items-center justify-center mb-4">
                <svg class="w-8 h-8 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
            </div>
            <p class="text-sm font-semibold text-slate-500">ไม่พบรายการ Audit Log</p>
            <p class="text-xs text-slate-400 mt-1">ลองเปลี่ยนเงื่อนไขการค้นหา</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/80">
                        <th class="text-left px-5 py-3.5 text-xs font-bold text-slate-400 uppercase tracking-wider w-36">เวลา</th>
                        <th class="text-left px-4 py-3.5 text-xs font-bold text-slate-400 uppercase tracking-wider w-28">หมวดหมู่</th>
                        <th class="text-left px-4 py-3.5 text-xs font-bold text-slate-400 uppercase tracking-wider w-36">เหตุการณ์</th>
                        <th class="text-left px-4 py-3.5 text-xs font-bold text-slate-400 uppercase tracking-wider w-32">ผู้ดำเนินการ</th>
                        <th class="text-left px-4 py-3.5 text-xs font-bold text-slate-400 uppercase tracking-wider">รายละเอียด</th>
                        <th class="text-left px-4 py-3.5 text-xs font-bold text-slate-400 uppercase tracking-wider w-28 hidden xl:table-cell">IP Address</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach ($logs as $log)
                        @php
                            [$catBg, $catText, $catBorder] = match($log->event_category) {
                                'auth'        => ['bg-purple-50', 'text-purple-700', 'border-purple-200'],
                                'users'       => ['bg-blue-50',   'text-blue-700',   'border-blue-200'],
                                'permissions' => ['bg-indigo-50', 'text-indigo-700', 'border-indigo-200'],
                                'systems'     => ['bg-orange-50', 'text-orange-700', 'border-orange-200'],
                                'connectors'  => ['bg-teal-50',   'text-teal-700',   'border-teal-200'],
                                'api'         => ['bg-slate-50',  'text-slate-700',  'border-slate-200'],
                                default       => ['bg-gray-50',   'text-gray-700',   'border-gray-200'],
                            };

                            $catLabel = match($log->event_category) {
                                'auth'        => 'Auth',
                                'users'       => 'ผู้ใช้',
                                'permissions' => 'สิทธิ์',
                                'systems'     => 'ระบบ',
                                'connectors'  => 'Connectors',
                                'api'         => 'API',
                                default       => $log->event_category,
                            };

                            $eventLabel = match($log->event_type) {
                                'login'                     => 'เข้าสู่ระบบ',
                                'logout'                    => 'ออกจากระบบ',
                                'login_failed'              => 'เข้าระบบล้มเหลว',
                                'user_imported'             => 'นำเข้าผู้ใช้',
                                'user_bulk_imported'        => 'Bulk Import',
                                'user_removed'              => 'ลบผู้ใช้',
                                'user_info_updated'         => 'แก้ไขข้อมูล',
                                'admin_level_updated'       => 'เปลี่ยน Admin Level',
                                'permissions_updated'       => 'อัปเดตสิทธิ์',
                                'permissions_discovered'    => 'Discover สิทธิ์',
                                'account_status_changed'    => 'เปิด/ปิด Account',
                                'system_created'            => 'สร้างระบบ',
                                'system_updated'            => 'แก้ไขระบบ',
                                'system_deleted'            => 'ลบระบบ',
                                'system_2way_toggled'       => 'Toggle 2-Way Sync',
                                'permission_def_created'    => 'เพิ่ม Permission Key',
                                'permission_def_updated'    => 'แก้ไข Permission Key',
                                'permission_def_deleted'    => 'ลบ Permission Key',
                                'permission_def_discovered' => 'Discover Perm Keys',
                                'connector_created'         => 'สร้าง Connector',
                                'connector_updated'         => 'แก้ไข Connector',
                                'connector_deleted'         => 'ลบ Connector',
                                'api_token_issued'          => 'ออก API Token',
                                'api_token_revoked'         => 'ยกเลิก API Token',
                                'api_user_login'            => 'User Login ผ่าน API',
                                default                     => $log->event_type,
                            };

                            $isWarning = in_array($log->event_type, [
                                'login_failed', 'user_removed', 'system_deleted',
                                'connector_deleted', 'permission_def_deleted', 'account_status_changed',
                            ]);

                            $avatarLetter = strtoupper(substr($log->actor_username ?? 'S', 0, 1));
                            $avatarColors = ['bg-indigo-100 text-indigo-700', 'bg-violet-100 text-violet-700', 'bg-sky-100 text-sky-700', 'bg-emerald-100 text-emerald-700', 'bg-amber-100 text-amber-700'];
                            $avatarColor = $avatarColors[ord($avatarLetter) % count($avatarColors)];
                        @endphp
                        <tr class="hover:bg-slate-50/60 transition-colors {{ $isWarning ? 'border-l-2 border-l-rose-300' : '' }}">

                            {{-- Time --}}
                            <td class="px-5 py-3.5 whitespace-nowrap">
                                <div class="text-xs font-semibold text-slate-700">{{ $log->created_at->format('d/m/Y') }}</div>
                                <div class="text-xs text-slate-400 font-mono mt-0.5">{{ $log->created_at->format('H:i:s') }}</div>
                            </td>

                            {{-- Category --}}
                            <td class="px-4 py-3.5">
                                <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-semibold border {{ $catBg }} {{ $catText }} {{ $catBorder }}">
                                    {{ $catLabel }}
                                </span>
                            </td>

                            {{-- Event --}}
                            <td class="px-4 py-3.5">
                                <span class="text-xs font-semibold {{ $isWarning ? 'text-rose-600' : 'text-slate-700' }} leading-tight">
                                    @if ($isWarning)
                                        <span class="inline-block w-1.5 h-1.5 rounded-full bg-rose-400 mr-1 align-middle"></span>
                                    @endif
                                    {{ $eventLabel }}
                                </span>
                            </td>

                            {{-- Actor --}}
                            <td class="px-4 py-3.5">
                                @if ($log->actor_username)
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-full {{ $avatarColor }} flex items-center justify-center text-xs font-bold shrink-0">
                                            {{ $avatarLetter }}
                                        </div>
                                        <span class="text-xs font-medium text-slate-700 truncate max-w-24">{{ $log->actor_username }}</span>
                                    </div>
                                @else
                                    <span class="text-xs text-slate-300">—</span>
                                @endif
                            </td>

                            {{-- Description --}}
                            <td class="px-4 py-3.5 max-w-sm">
                                <p class="text-sm text-slate-700 leading-snug">{{ $log->description }}</p>
                                @if ($log->subject_label && $log->subject_label !== $log->actor_username)
                                    <span class="text-xs text-slate-400 mt-0.5 block">
                                        {{ ucfirst($log->subject_type ?? '') }}: <span class="font-medium text-slate-500">{{ $log->subject_label }}</span>
                                    </span>
                                @endif
                                @if ($log->metadata)
                                    <button type="button"
                                            onclick="this.nextElementSibling.classList.toggle('hidden'); this.textContent = this.nextElementSibling.classList.contains('hidden') ? '▸ ดูรายละเอียด' : '▾ ซ่อน'"
                                            class="text-xs text-indigo-500 hover:text-indigo-700 mt-1.5 font-medium cursor-pointer">
                                        ▸ ดูรายละเอียด
                                    </button>
                                    <pre class="hidden mt-2 text-xs bg-slate-50 border border-slate-100 rounded-xl p-3 overflow-auto max-h-40 text-slate-600 font-mono leading-relaxed">{{ json_encode($log->metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                @endif
                            </td>

                            {{-- IP --}}
                            <td class="px-4 py-3.5 hidden xl:table-cell">
                                <span class="text-xs font-mono text-slate-400">{{ $log->ip_address ?? '—' }}</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($logs->hasPages())
            <div class="px-5 py-4 border-t border-slate-100 bg-slate-50/50">
                {{ $logs->links() }}
            </div>
        @endif
    @endif
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Flatpickr date pickers ────────────────────────────────────────────
    const fpConfig = {
        dateFormat: 'Y-m-d',
        locale: window.flatpickrThai,
        allowInput: true,
        disableMobile: false,
    };

    const fpFrom = flatpickr('#date-from', {
        ...fpConfig,
        onChange: ([date]) => {
            if (date && fpTo.selectedDates[0] && date > fpTo.selectedDates[0]) fpTo.setDate(date);
            clearActivePreset();
        },
    });

    const fpTo = flatpickr('#date-to', {
        ...fpConfig,
        onChange: ([date]) => {
            if (date && fpFrom.selectedDates[0] && date < fpFrom.selectedDates[0]) fpFrom.setDate(date);
            clearActivePreset();
        },
    });

    // ── Date helpers ──────────────────────────────────────────────────────
    function fmt(d) {
        return d.getFullYear() + '-' +
            String(d.getMonth() + 1).padStart(2, '0') + '-' +
            String(d.getDate()).padStart(2, '0');
    }

    function startOfWeek(d) {
        const day = d.getDay();
        const diff = day === 0 ? -6 : 1 - day;
        const mon = new Date(d);
        mon.setDate(d.getDate() + diff);
        return mon;
    }

    // ── Compute date range with offset counter ────────────────────────────
    // C = forward (+counter), P = backward (-counter)
    // D = day unit, W = week unit, M = month unit
    function getPresetRange(code, counter) {
        const today = new Date();
        const n = counter;

        if (code === 'CD') {
            const d = new Date(today);
            d.setDate(today.getDate() + n);
            return [fmt(d), fmt(d)];
        }
        if (code === 'PD') {
            const d = new Date(today);
            d.setDate(today.getDate() - 1 - n);
            return [fmt(d), fmt(d)];
        }
        if (code === 'CW') {
            const mon = startOfWeek(today);
            mon.setDate(mon.getDate() + n * 7);
            const sun = new Date(mon);
            sun.setDate(mon.getDate() + 6);
            return [fmt(mon), fmt(sun)];
        }
        if (code === 'PW') {
            const mon = startOfWeek(today);
            mon.setDate(mon.getDate() - 7 - n * 7);
            const sun = new Date(mon);
            sun.setDate(mon.getDate() + 6);
            return [fmt(mon), fmt(sun)];
        }
        if (code === 'CM') {
            const first = new Date(today.getFullYear(), today.getMonth() + n, 1);
            const last  = new Date(today.getFullYear(), today.getMonth() + n + 1, 0);
            return [fmt(first), fmt(last)];
        }
        if (code === 'PM') {
            const first = new Date(today.getFullYear(), today.getMonth() - 1 - n, 1);
            const last  = new Date(today.getFullYear(), today.getMonth() - n, 0);
            return [fmt(first), fmt(last)];
        }
        return [null, null];
    }

    // ── State ─────────────────────────────────────────────────────────────
    let activePreset = null;
    const counters = { CD: 0, PD: 0, CW: 0, PW: 0, CM: 0, PM: 0 };

    function applyPreset(code) {
        const [from, to] = getPresetRange(code, counters[code]);
        fpFrom.setDate(from, false);
        fpTo.setDate(to, false);
        activePreset = code;
        renderButtons();
    }

    function clearActivePreset() {
        activePreset = null;
        renderButtons();
    }

    function renderButtons() {
        document.querySelectorAll('.preset-btn').forEach(btn => {
            const code     = btn.dataset.preset;
            const isActive = code === activePreset;
            btn.dataset.active = isActive ? '1' : '0';

            // badge แสดง offset เมื่อ > 0
            const existingBadge = btn.querySelector('.preset-badge');
            if (existingBadge) existingBadge.remove();

            if (isActive && counters[code] > 0) {
                const dir    = code[0] === 'C' ? '+' : '-';
                const unit   = { D: 'D', W: 'W', M: 'M' }[code[1]];
                const badge  = document.createElement('span');
                badge.className = 'preset-badge ml-0.5 text-[9px] font-black opacity-80 leading-none';
                badge.textContent = `${dir}${counters[code]}${unit}`;
                btn.appendChild(badge);
            }
        });
    }

    document.querySelectorAll('.preset-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const clicked = this.dataset.preset;
            if (activePreset === clicked) {
                // คลิ๊กซ้ำ → บวก/ลบตามความหมายของปุ่ม
                counters[clicked]++;
            } else {
                // เปลี่ยนปุ่ม → reset counter ของปุ่มเดิม (ไม่ reset ปุ่มใหม่)
                if (activePreset) counters[activePreset] = 0;
                activePreset = clicked;
            }
            applyPreset(clicked);
        });
    });

    // ── Restore state on load (ถ้ามี date_from/date_to จาก URL) ──────────
    const urlFrom = document.getElementById('date-from').value;
    const urlTo   = document.getElementById('date-to').value;
    if (urlFrom || urlTo) {
        // ลอง match preset=0 ก่อน (counter=0)
        for (const code of ['CD','PD','CW','PW','CM','PM']) {
            const [f, t] = getPresetRange(code, 0);
            if (f === urlFrom && t === urlTo) {
                activePreset = code;
                renderButtons();
                break;
            }
        }
    }
});
</script>
@endpush
