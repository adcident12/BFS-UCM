@extends('layouts.app')

@section('title', 'Permission Matrix Report')

@push('styles')
<style>
/* ── Matrix container ─────────────────────────────────────────────── */
.matrix-wrap {
    overflow: auto;
    max-height: calc(100vh - 280px);
    min-height: 300px;
    border-radius: 0 0 16px 16px;
    width: 100%;
}
.matrix-table {
    border-collapse: separate;
    border-spacing: 0;
    font-size: 11px;
    white-space: nowrap;
}

/* ── Sticky user column ────────────────────────────────────────────── */
.col-user {
    position: sticky;
    left: 0;
    z-index: 20;
    min-width: 200px;
    max-width: 200px;
    background: #fff;
    border-right: 2px solid #e2e8f0;
}
thead .col-user {
    z-index: 40;
    background: #f8fafc;
}

/* ── Sticky thead ─────────────────────────────────────────────────── */
.row-system th {
    position: sticky;
    top: 0;
    z-index: 30;
    background: #f8fafc;
}
.row-system .col-user {
    z-index: 40;
}
.row-perm th {
    position: sticky;
    top: 41px; /* height of row-system */
    z-index: 30;
    background: #f8fafc;
}
.row-perm .col-user {
    z-index: 40;
}

/* ── Rotated permission labels ─────────────────────────────────────── */
.perm-label-wrap {
    display: flex;
    align-items: flex-end;
    justify-content: center;
    height: 100px;
    padding: 6px 4px 4px;
}
.perm-label-inner {
    writing-mode: vertical-rl;
    transform: rotate(180deg);
    font-size: 10px;
    font-weight: 600;
    color: #475569;
    line-height: 1.2;
    max-height: 88px;
    overflow: hidden;
    text-overflow: ellipsis;
    cursor: default;
}

/* ── System header ─────────────────────────────────────────────────── */
.sys-header {
    border-left: 2px solid #e2e8f0;
    border-bottom: 1px solid #e2e8f0;
    padding: 8px 10px;
    font-weight: 700;
    font-size: 11px;
    text-align: center;
    white-space: nowrap;
}

/* ── Permission cell ───────────────────────────────────────────────── */
.perm-cell {
    border-left: 1px solid #f1f5f9;
    border-bottom: 1px solid #f1f5f9;
    text-align: center;
    padding: 6px 4px;
    min-width: 36px;
    max-width: 36px;
    transition: background 0.1s;
}
.perm-cell.sys-first {
    border-left: 2px solid #e2e8f0;
}
tr:hover .perm-cell {
    background: rgba(99,102,241,0.04);
}
/* Column highlight */
.perm-cell.col-hover {
    background: rgba(99,102,241,0.08) !important;
}

/* ── User row ──────────────────────────────────────────────────────── */
.user-row:hover .col-user {
    background: #f8fafc;
}

/* ── Permission count badge ─────────────────────────────────────────── */
.perm-count {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 20px;
    height: 16px;
    border-radius: 99px;
    font-size: 9px;
    font-weight: 700;
    padding: 0 4px;
}
</style>
@endpush

@section('breadcrumbs')
<a href="{{ route('dashboard') }}" class="text-slate-400 hover:text-slate-600 font-medium transition-colors whitespace-nowrap">Dashboard</a>
<svg class="w-3.5 h-3.5 text-slate-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
<span class="font-semibold text-slate-800 truncate">Permission Matrix</span>
@endsection

@section('content')

{{-- Hero Banner --}}
<div class="mb-7 relative overflow-hidden rounded-2xl"
     style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 45%, #0c1a2e 100%)">
    <div class="absolute inset-0 pointer-events-none overflow-hidden">
        <div class="absolute -top-12 -right-12 w-72 h-72 bg-amber-500/6 rounded-full blur-2xl"></div>
        <div class="absolute -bottom-16 left-24 w-56 h-56 bg-orange-400/5 rounded-full blur-2xl"></div>
        <div class="absolute inset-0"
             style="background-image:linear-gradient(rgba(255,255,255,0.025) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,0.025) 1px,transparent 1px);background-size:28px 28px"></div>
    </div>
    <div class="relative px-6 md:px-8 py-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-white/8 backdrop-blur-sm rounded-2xl flex items-center justify-center flex-shrink-0 border border-white/10">
                <svg class="w-6 h-6 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                          d="M3 10h18M3 14h18M10 3v18M14 3v18M5 3h14a2 2 0 012 2v14a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2z"/>
                </svg>
            </div>
            <div>
                <h2 class="text-lg font-bold text-white">Permission Matrix</h2>
                <p class="text-slate-400 text-xs font-medium mt-0.5">ตารางสิทธิ์ผู้ใช้ทุก System</p>
            </div>
        </div>
        <a href="{{ route('reports.permission-matrix.export', request()->query()) }}"
           class="inline-flex items-center gap-1.5 px-4 py-2 bg-amber-500/20 hover:bg-amber-500/30 border border-amber-400/30 rounded-xl text-amber-300 hover:text-amber-200 text-xs font-semibold transition-all">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
            Export CSV
        </a>
    </div>
</div>

{{-- Filter --}}
<div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 mb-5">
    <form method="GET" action="{{ route('reports.permission-matrix') }}" class="flex flex-col sm:flex-row gap-3">
        <div class="relative flex-1">
            <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="ค้นหา username, ชื่อ, แผนก..."
                   class="w-full pl-9 pr-4 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-300 focus:border-indigo-300 bg-white placeholder-slate-400">
        </div>
        <select name="system_id"
                class="sm:w-52 px-3 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-300 bg-white text-slate-700">
            <option value="">ทุก System</option>
            @foreach ($systems as $system)
                <option value="{{ $system->id }}" {{ request('system_id') == $system->id ? 'selected' : '' }}>
                    {{ $system->name }}
                </option>
            @endforeach
        </select>
        <button type="submit"
                class="px-5 py-2.5 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl transition-colors">
            ค้นหา
        </button>
        @if (request()->hasAny(['search', 'system_id']))
            <a href="{{ route('reports.permission-matrix') }}"
               class="px-4 py-2.5 text-sm font-semibold text-slate-600 border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors text-center">
                Reset
            </a>
        @endif
    </form>
</div>

{{-- System Legend --}}
@php
    $displaySystems = $selectedSystem ? collect([$selectedSystem]) : $systems;
    $totalPermCols  = $displaySystems->sum(fn ($s) => $s->permissions->count());
@endphp

@if ($displaySystems->isNotEmpty())
<div class="flex flex-wrap gap-2 mb-4">
    @foreach ($displaySystems as $system)
        @if ($system->permissions->isNotEmpty())
            <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border text-xs font-semibold"
                 style="border-color: {{ $system->color ?? '#6366f1' }}40; background: {{ $system->color ?? '#6366f1' }}10; color: {{ $system->color ?? '#6366f1' }}">
                <div class="w-2 h-2 rounded-full" style="background: {{ $system->color ?? '#6366f1' }}"></div>
                {{ $system->name }}
                <span class="opacity-60">({{ $system->permissions->count() }} สิทธิ์)</span>
            </div>
        @endif
    @endforeach
    <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-slate-200 bg-slate-50 text-xs font-semibold text-slate-500">
        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
        {{ number_format($users->total()) }} ผู้ใช้ · {{ $totalPermCols }} คอลัมน์สิทธิ์
    </div>
</div>
@endif

{{-- Matrix Table --}}
<div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
    <div class="matrix-wrap" id="matrix-wrap">
        <table class="matrix-table" id="matrix-table">
            <thead>
                {{-- Row 1: System names --}}
                <tr class="row-system">
                    <th class="col-user border-b border-slate-200 text-left px-4 py-2.5">
                        <span class="text-xs font-semibold text-slate-500">ผู้ใช้</span>
                    </th>
                    @foreach ($displaySystems as $system)
                        @if ($system->permissions->isNotEmpty())
                            <th colspan="{{ $system->permissions->count() }}"
                                class="sys-header"
                                style="color: {{ $system->color ?? '#6366f1' }}; background: {{ $system->color ?? '#6366f1' }}0d; border-top: 3px solid {{ $system->color ?? '#6366f1' }};">
                                <div class="flex items-center justify-center gap-1.5 whitespace-nowrap">
                                    <div class="w-2 h-2 rounded-full flex-shrink-0" style="background: {{ $system->color ?? '#6366f1' }}"></div>
                                    {{ $system->name }}
                                </div>
                            </th>
                        @endif
                    @endforeach
                    {{-- Total column --}}
                    <th class="sys-header border-l-2 border-slate-200 text-slate-500 bg-slate-50" style="min-width:52px">
                        รวม
                    </th>
                </tr>
                {{-- Row 2: Permission names (rotated) --}}
                <tr class="row-perm border-b-2 border-slate-200">
                    <th class="col-user px-4 pb-1 pt-0 text-left">
                        <span class="text-[10px] font-medium text-slate-400">เลื่อน → เพื่อดูสิทธิ์เพิ่มเติม</span>
                    </th>
                    @php $colIndex = 0; @endphp
                    @foreach ($displaySystems as $sysIdx => $system)
                        @foreach ($system->permissions as $permIdx => $perm)
                            @php
                                $isFirst = $permIdx === 0;
                                $colIndex++;
                            @endphp
                            <th class="text-center {{ $isFirst ? 'border-l-2 border-slate-200' : '' }}"
                                data-col="{{ $colIndex }}"
                                style="background: {{ $system->color ?? '#6366f1' }}08;"
                                title="{{ $system->name }} — {{ $perm->label }}">
                                <div class="perm-label-wrap">
                                    <span class="perm-label-inner" style="color: {{ $system->color ?? '#6366f1' }}cc">
                                        {{ $perm->label }}
                                    </span>
                                </div>
                            </th>
                        @endforeach
                    @endforeach
                    <th class="border-l-2 border-slate-200 bg-slate-50" style="min-width:52px">
                        <div class="perm-label-wrap">
                            <span class="perm-label-inner text-slate-400">จำนวนสิทธิ์</span>
                        </div>
                    </th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                    @php
                        $grantedMap = [];
                        foreach ($user->systemPermissions as $up) {
                            $grantedMap[$up->system_id][$up->permission_key] = true;
                        }
                        $totalGranted = count($user->systemPermissions);
                    @endphp
                    <tr class="user-row" data-user="{{ $user->id }}">
                        <td class="col-user px-4 py-2.5 border-b border-slate-50">
                            <div class="flex items-start justify-between gap-2">
                                <div class="min-w-0">
                                    <div class="font-semibold text-slate-700 text-xs truncate">{{ $user->name }}</div>
                                    <div class="text-slate-400 font-mono text-[10px]">{{ $user->username }}</div>
                                    @if ($user->department)
                                        <div class="text-slate-400 text-[10px] truncate max-w-[140px]" title="{{ $user->department }}">
                                            {{ $user->department }}
                                        </div>
                                    @endif
                                </div>
                                @if ($totalGranted > 0)
                                    <span class="perm-count bg-indigo-100 text-indigo-700 flex-shrink-0 mt-0.5">
                                        {{ $totalGranted }}
                                    </span>
                                @endif
                            </div>
                        </td>
                        @php $colIndex = 0; @endphp
                        @foreach ($displaySystems as $system)
                            @foreach ($system->permissions as $permIdx => $perm)
                                @php
                                    $isFirst  = $permIdx === 0;
                                    $granted  = isset($grantedMap[$system->id][$perm->key]);
                                    $colIndex++;
                                    $hexColor = $system->color ?? '#6366f1';
                                @endphp
                                <td class="perm-cell {{ $isFirst ? 'sys-first' : '' }} border-b border-slate-50"
                                    data-col="{{ $colIndex }}"
                                    style="{{ $granted ? "background: {$hexColor}12;" : '' }}"
                                    title="{{ $granted ? '✓ ' : '' }}{{ $user->name }} — {{ $system->name }} › {{ $perm->label }}">
                                    @if ($granted)
                                        <div class="inline-flex items-center justify-center w-5 h-5 rounded-full mx-auto"
                                             style="background: {{ $hexColor }}22;">
                                            <svg class="w-3 h-3" fill="none" stroke="{{ $hexColor }}" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </div>
                                    @else
                                        <div class="w-3 h-px bg-slate-100 mx-auto"></div>
                                    @endif
                                </td>
                            @endforeach
                        @endforeach
                        {{-- Total column --}}
                        <td class="perm-cell sys-first border-b border-slate-50 border-l-2 border-l-slate-200 bg-slate-50/50">
                            @if ($totalGranted > 0)
                                <span class="perm-count bg-indigo-100 text-indigo-700 mx-auto">{{ $totalGranted }}</span>
                            @else
                                <div class="w-3 h-px bg-slate-200 mx-auto"></div>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="999" class="px-6 py-14 text-center text-slate-400 text-sm font-medium">
                            ไม่พบข้อมูลผู้ใช้
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination + summary --}}
    @if ($users->hasPages() || $users->total() > 0)
        <div class="px-5 py-3 border-t border-slate-100 flex items-center justify-between gap-4 flex-wrap">
            <span class="text-xs text-slate-500 font-medium">
                แสดง {{ $users->firstItem() }}–{{ $users->lastItem() }} จาก {{ number_format($users->total()) }} ผู้ใช้
            </span>
            {{ $users->links() }}
        </div>
    @endif
</div>

@push('scripts')
<script>
(function () {
    // Column hover highlight
    const table = document.getElementById('matrix-table');
    if (! table) { return; }

    let activeCol = null;

    table.addEventListener('mouseover', function (e) {
        const cell = e.target.closest('[data-col]');
        if (! cell) { return; }
        const col = cell.dataset.col;
        if (col === activeCol) { return; }
        activeCol = col;
        table.querySelectorAll('.perm-cell.col-hover').forEach(el => el.classList.remove('col-hover'));
        table.querySelectorAll(`[data-col="${col}"]`).forEach(el => {
            if (el.tagName === 'TD') { el.classList.add('col-hover'); }
        });
    });

    table.addEventListener('mouseleave', function () {
        activeCol = null;
        table.querySelectorAll('.perm-cell.col-hover').forEach(el => el.classList.remove('col-hover'));
    });
})();
</script>
@endpush

@endsection
