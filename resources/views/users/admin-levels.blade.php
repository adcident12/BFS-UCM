@extends('layouts.app')
@section('title', 'จัดการสิทธิ์ Admin')
@section('header', 'จัดการสิทธิ์ Admin')

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}" class="text-slate-400 hover:text-slate-600 font-medium transition-colors whitespace-nowrap">Dashboard</a>
    <svg class="w-3.5 h-3.5 text-slate-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    <span class="font-semibold text-slate-800 truncate">จัดการสิทธิ์ Admin</span>
@endsection

@section('content')

@php
    $countL0 = $users->where('is_admin', 0)->count();
    $countL1 = $users->where('is_admin', 1)->count();
    $countL2 = $users->where('is_admin', 2)->count();
@endphp

{{-- ── Header ──────────────────────────────────────────────────────────── --}}
<div class="flex flex-wrap items-start justify-between gap-4 mb-7">
    <div>
        <h2 class="text-lg font-bold text-slate-800">สิทธิ์ผู้ดูแลระบบ</h2>
        <p class="text-sm text-slate-400 font-medium mt-0.5">กำหนดระดับ Admin ให้ผู้ใช้ — เฉพาะ Admin ระดับ 2 เท่านั้น</p>
    </div>
    {{-- Stat summary --}}
    <div class="flex items-center gap-2 flex-shrink-0">
        <div class="inline-flex items-center gap-2 px-4 py-2 bg-white rounded-xl ring-1 ring-slate-100 text-xs font-semibold">
            <span class="inline-flex items-center gap-1.5 text-slate-500">
                <span class="w-2 h-2 rounded-full bg-slate-300"></span>
                ทั่วไป <span class="text-slate-800 tabular-nums">{{ $countL0 }}</span>
            </span>
            <span class="w-px h-3.5 bg-slate-200"></span>
            <span class="inline-flex items-center gap-1.5 text-indigo-600">
                <span class="w-2 h-2 rounded-full bg-indigo-400"></span>
                L1 <span class="tabular-nums">{{ $countL1 }}</span>
            </span>
            <span class="w-px h-3.5 bg-slate-200"></span>
            <span class="inline-flex items-center gap-1.5 text-amber-600">
                <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                L2 <span class="tabular-nums">{{ $countL2 }}</span>
            </span>
        </div>
    </div>
</div>

{{-- ── Level capability cards ───────────────────────────────────────────── --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-7">

    {{-- ทั่วไป --}}
    <div class="bg-white rounded-2xl ring-1 ring-slate-100 shadow-sm px-5 py-4">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-8 h-8 rounded-xl bg-slate-100 flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <div>
                <span class="text-xs font-bold text-slate-700">ทั่วไป</span>
                <span class="ml-2 text-[10px] font-bold text-slate-400 bg-slate-100 px-1.5 py-0.5 rounded-full tabular-nums">{{ $countL0 }} คน</span>
            </div>
        </div>
        <ul class="space-y-1.5 text-xs text-slate-500 font-medium">
            <li class="flex items-center gap-2">
                <svg class="w-3.5 h-3.5 text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                ดูข้อมูลและ Permission ของตัวเอง
            </li>
            <li class="flex items-center gap-2">
                <svg class="w-3.5 h-3.5 text-slate-200 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                <span class="text-slate-300">จัดการข้อมูล Reference</span>
            </li>
            <li class="flex items-center gap-2">
                <svg class="w-3.5 h-3.5 text-slate-200 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                <span class="text-slate-300">จัดการผู้ใช้หรือระบบ</span>
            </li>
        </ul>
    </div>

    {{-- Admin L1 --}}
    <div class="bg-white rounded-2xl ring-1 ring-indigo-100 shadow-sm px-5 py-4">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-8 h-8 rounded-xl bg-indigo-50 flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <div>
                <span class="text-xs font-bold text-indigo-700">Admin ระดับ 1</span>
                <span class="ml-2 text-[10px] font-bold text-indigo-400 bg-indigo-50 px-1.5 py-0.5 rounded-full tabular-nums">{{ $countL1 }} คน</span>
            </div>
        </div>
        <ul class="space-y-1.5 text-xs text-slate-500 font-medium">
            <li class="flex items-center gap-2">
                <svg class="w-3.5 h-3.5 text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                เพิ่ม Reference (Department / PageGroup)
            </li>
            <li class="flex items-center gap-2">
                <svg class="w-3.5 h-3.5 text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                ดู Queue Monitor
            </li>
            <li class="flex items-center gap-2">
                <svg class="w-3.5 h-3.5 text-slate-200 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                <span class="text-slate-300">แก้ไข / ลบ Reference, เปิด 2-way</span>
            </li>
        </ul>
    </div>

    {{-- Admin L2 --}}
    <div class="bg-white rounded-2xl ring-1 ring-amber-100 shadow-sm px-5 py-4">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-8 h-8 rounded-xl bg-amber-50 flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
            </div>
            <div>
                <span class="text-xs font-bold text-amber-700">Admin ระดับ 2</span>
                <span class="ml-2 text-[10px] font-bold text-amber-500 bg-amber-50 px-1.5 py-0.5 rounded-full tabular-nums">{{ $countL2 }} คน</span>
            </div>
        </div>
        <ul class="space-y-1.5 text-xs text-slate-500 font-medium">
            <li class="flex items-center gap-2">
                <svg class="w-3.5 h-3.5 text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                ทุกสิทธิ์ของ Admin ระดับ 1
            </li>
            <li class="flex items-center gap-2">
                <svg class="w-3.5 h-3.5 text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                แก้ไข / ลบ Reference, เปิด 2-way Sync
            </li>
            <li class="flex items-center gap-2">
                <svg class="w-3.5 h-3.5 text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                จัดการสิทธิ์ Admin ของผู้ใช้อื่น
            </li>
        </ul>
    </div>

</div>

{{-- ── User list ────────────────────────────────────────────────────────── --}}
{{-- NOTE: ห้ามใส่ overflow-hidden บน card นี้ เพราะทำให้ sticky toolbar ไม่ทำงาน --}}
<div class="bg-white rounded-2xl shadow-sm ring-1 ring-slate-100">

    {{-- Sticky toolbar: search + level filter --}}
    {{-- top-[52px] = ความสูงของ app header (py-3.5 + text-sm content ≈ 48-52px) --}}
    <div class="sticky top-[52px] z-10 bg-white/95 backdrop-blur-sm border-b border-slate-100 px-5 py-3 flex flex-wrap items-center gap-3 rounded-t-2xl">
        {{-- Search --}}
        <div class="relative flex-1 min-w-[200px]">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-300 pointer-events-none"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input id="admin-search" type="text" placeholder="ค้นหาชื่อหรือ Username..."
                   class="w-full pl-9 pr-3 py-2 text-sm text-slate-700 placeholder-slate-300 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400/30 focus:border-indigo-400 transition-all">
        </div>
        {{-- Level filter tabs --}}
        <div class="flex items-center rounded-xl ring-1 ring-slate-200 bg-slate-50 p-0.5 gap-0.5 flex-shrink-0">
            <button type="button" data-level-filter="all"
                    class="lvl-tab px-3 py-1.5 text-xs font-bold rounded-lg transition-all bg-white text-slate-700 shadow-sm ring-1 ring-slate-200/80">
                ทั้งหมด <span class="tabular-nums text-slate-400 font-medium">{{ $users->count() }}</span>
            </button>
            <button type="button" data-level-filter="0"
                    class="lvl-tab px-3 py-1.5 text-xs font-bold rounded-lg transition-all text-slate-400 hover:bg-white/60">
                ทั่วไป <span class="tabular-nums font-medium">{{ $countL0 }}</span>
            </button>
            <button type="button" data-level-filter="1"
                    class="lvl-tab px-3 py-1.5 text-xs font-bold rounded-lg transition-all text-slate-400 hover:bg-white/60">
                L1 <span class="tabular-nums font-medium">{{ $countL1 }}</span>
            </button>
            <button type="button" data-level-filter="2"
                    class="lvl-tab px-3 py-1.5 text-xs font-bold rounded-lg transition-all text-slate-400 hover:bg-white/60">
                L2 <span class="tabular-nums font-medium">{{ $countL2 }}</span>
            </button>
        </div>
        {{-- Result count --}}
        <span id="admin-result-count" class="text-xs text-slate-400 font-medium flex-shrink-0 ml-auto whitespace-nowrap">
            {{ $users->count() }} คน
        </span>
    </div>

    {{-- Empty state (hidden by default) --}}
    <div id="admin-empty-state" style="display:none"
         class="flex flex-col items-center justify-center py-14 text-slate-300 rounded-b-2xl">
        <svg class="w-8 h-8 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
        <p class="text-sm font-semibold text-slate-400">ไม่พบผู้ใช้</p>
        <p class="text-xs mt-1">ลองเปลี่ยน keyword หรือ filter</p>
    </div>

    <div id="admin-user-list" class="divide-y divide-slate-50 overflow-hidden rounded-b-2xl">
        @foreach ($users as $user)
        @php
            $isSelf     = $user->id === auth()->id();
            $level      = (int) $user->is_admin;
            $levelLabel = match($level) {
                2       => 'Admin ระดับ 2',
                1       => 'Admin ระดับ 1',
                default => 'ทั่วไป',
            };
            $avatarCls  = match($level) {
                2       => 'bg-amber-500',
                1       => 'bg-indigo-500',
                default => 'bg-slate-400',
            };
        @endphp
        <div class="user-row flex flex-wrap items-center justify-between gap-3 px-6 py-3 hover:bg-slate-50/60 transition-colors group"
             data-name="{{ strtolower($user->name) }}"
             data-username="{{ strtolower($user->username) }}"
             data-level="{{ $level }}">

            {{-- Avatar + Info --}}
            <div class="flex items-center gap-3 min-w-0 flex-1">
                <div class="relative flex-shrink-0">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center text-sm font-bold text-white {{ $avatarCls }}">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    {{-- Level indicator dot --}}
                    @if ($level > 0)
                    <div class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 rounded-full border-2 border-white flex items-center justify-center
                                {{ $level === 2 ? 'bg-amber-400' : 'bg-indigo-400' }}">
                        <span class="text-[7px] font-black text-white leading-none">{{ $level }}</span>
                    </div>
                    @endif
                </div>
                <div class="min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="text-sm font-semibold text-slate-800 truncate">{{ $user->name }}</span>
                        @if ($isSelf)
                            <span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded-md ring-1 ring-emerald-200/70">
                                <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                ตัวเอง
                            </span>
                        @endif
                    </div>
                    <div class="flex items-center gap-2 mt-0.5">
                        <span class="text-xs font-mono text-slate-400">{{ $user->username }}</span>
                        @if ($user->department)
                            <span class="text-slate-200 text-xs">·</span>
                            <span class="text-xs text-slate-400 truncate max-w-[160px]">{{ $user->department }}</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Level switcher --}}
            <div class="flex items-center gap-3 flex-shrink-0">
                @if ($isSelf)
                    {{-- Can't change own level --}}
                    <div class="inline-flex items-center gap-2 px-3 py-2 bg-slate-50 rounded-xl ring-1 ring-slate-100">
                        <svg class="w-3.5 h-3.5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        <span class="text-xs font-semibold
                            {{ $level === 2 ? 'text-amber-600' : ($level === 1 ? 'text-indigo-600' : 'text-slate-500') }}">
                            {{ $levelLabel }}
                        </span>
                    </div>
                @else
                    {{-- Segmented level control --}}
                    <div class="inline-flex items-center rounded-xl ring-1 ring-slate-200 bg-slate-50 p-0.5 gap-0.5">
                        @foreach ([0 => ['label' => 'ทั่วไป', 'active' => 'bg-white text-slate-700 shadow-sm ring-1 ring-slate-200/80', 'hover' => 'hover:bg-white/60 text-slate-400'],
                                   1 => ['label' => 'L1', 'active' => 'bg-indigo-500 text-white shadow-sm shadow-indigo-200/60', 'hover' => 'hover:bg-white/60 text-slate-400'],
                                   2 => ['label' => 'L2', 'active' => 'bg-amber-500 text-white shadow-sm shadow-amber-200/60', 'hover' => 'hover:bg-white/60 text-slate-400']] as $lvl => $cfg)
                        <form method="POST" action="{{ route('users.admin-level.update', $user) }}" class="contents">
                            @csrf
                            <input type="hidden" name="level" value="{{ $lvl }}">
                            <button type="submit"
                                    @if ($level === $lvl) disabled @endif
                                    title="{{ $cfg['label'] }}"
                                    class="px-3 py-1.5 text-xs font-bold rounded-lg transition-all
                                           {{ $level === $lvl
                                               ? $cfg['active']
                                               : 'text-slate-400 ' . $cfg['hover'] . ' cursor-pointer' }}">
                                {{ $cfg['label'] }}
                            </button>
                        </form>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>
        @endforeach
    </div>{{-- #admin-user-list --}}

</div>

@endsection

@push('scripts')
<script>
(function () {
    var search    = document.getElementById('admin-search');
    var rows      = document.querySelectorAll('.user-row');
    var tabs      = document.querySelectorAll('.lvl-tab');
    var countEl   = document.getElementById('admin-result-count');
    var emptyEl   = document.getElementById('admin-empty-state');
    var listEl    = document.getElementById('admin-user-list');

    var activeLevel = 'all';   // 'all' | '0' | '1' | '2'

    // ── Tab styles ───────────────────────────────────────────────────────────
    var tabStyles = {
        'all': 'bg-white text-slate-700 shadow-sm ring-1 ring-slate-200/80',
        '0':   'bg-white text-slate-700 shadow-sm ring-1 ring-slate-200/80',
        '1':   'bg-indigo-500 text-white shadow-sm',
        '2':   'bg-amber-500 text-white shadow-sm',
    };
    var tabInactive = 'text-slate-400 hover:bg-white/60';

    function applyTabStyle(tab) {
        var lv = tab.dataset.levelFilter;
        var isActive = lv === activeLevel;
        // Remove all possible active/inactive classes first
        tab.className = tab.className
            .replace(/bg-white|bg-indigo-500|bg-amber-500|text-white|text-slate-700|shadow-sm|ring-1|ring-slate-200\/80|text-slate-400|hover:bg-white\/60/g, '')
            .trim();
        tab.className += ' lvl-tab px-3 py-1.5 text-xs font-bold rounded-lg transition-all ';
        tab.className += isActive ? tabStyles[lv] : tabInactive;
    }

    function applyAllTabStyles() {
        tabs.forEach(applyTabStyle);
    }

    // ── Filter logic ─────────────────────────────────────────────────────────
    function filter() {
        var q   = search.value.toLowerCase().trim();
        var cnt = 0;

        rows.forEach(function (row) {
            var matchText  = !q || row.dataset.name.includes(q) || row.dataset.username.includes(q);
            var matchLevel = activeLevel === 'all' || row.dataset.level === activeLevel;
            var show       = matchText && matchLevel;
            row.style.display = show ? '' : 'none';
            if (show) cnt++;
        });

        countEl.textContent = cnt + ' คน';
        var noResults = cnt === 0;
        emptyEl.style.display = noResults ? '' : 'none';
        listEl.style.display  = noResults ? 'none' : '';
    }

    // ── Event listeners ──────────────────────────────────────────────────────
    search.addEventListener('input', filter);

    tabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
            activeLevel = tab.dataset.levelFilter;
            applyAllTabStyles();
            filter();
        });
    });

    // Init styles
    applyAllTabStyles();
})();
</script>
@endpush
