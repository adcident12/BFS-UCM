@extends('layouts.app')

@section('title', 'จัดการผู้ใช้')
@section('header', 'จัดการผู้ใช้')

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}" class="text-slate-400 hover:text-slate-600 font-medium transition-colors whitespace-nowrap">Dashboard</a>
    <svg class="w-3.5 h-3.5 text-slate-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    <span class="font-semibold text-slate-800 truncate">จัดการผู้ใช้</span>
@endsection

@section('content')

@php
    $canImportLdap      = auth()->user()->canAccess('user_import_ldap');
    $canInactiveReport  = auth()->user()->canAccess('user_inactive_report');
    $canRemoveUser      = auth()->user()->canAccess('user_remove');
    $canEditPermissions = auth()->user()->canAccess('permission_update');
@endphp

{{-- Export form (hidden, submitted by JS) --}}
<form id="export-form" method="GET" action="{{ route('users.export') }}" style="display:none">
    <div id="export-ids-container"></div>
</form>

{{-- Toolbar --}}
<div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between mb-5 gap-3">
    {{-- Search bar --}}
    <form method="GET" class="flex gap-2 w-full sm:flex-1 sm:max-w-md">
        <div class="relative flex-1">
            <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
                </svg>
            </div>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="ค้นหาชื่อ, username, แผนก..."
                   class="w-full pl-10 pr-4 py-2.5 border border-slate-200 rounded-xl text-sm text-slate-800 placeholder-slate-400
                          focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 transition-all bg-white shadow-sm">
            @if(request('search'))
                <a href="{{ route('users.index') }}"
                   class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-400 hover:text-slate-600 transition-colors"
                   title="ล้างการค้นหา">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </a>
            @endif
        </div>
        <button type="submit"
                class="px-4 py-2.5 bg-slate-700 hover:bg-slate-800 text-white text-sm font-semibold rounded-xl transition-colors duration-150 shadow-sm">
            ค้นหา
        </button>
    </form>

    {{-- AD Management (admin only) — global actions, not related to row selection --}}
    @if ($canImportLdap || $canInactiveReport)
    <div class="flex items-center gap-2 flex-shrink-0">
        @if ($canInactiveReport)
        <a href="{{ route('users.inactive') }}"
           class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 text-sm font-semibold rounded-xl transition-all duration-150 whitespace-nowrap">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            ไม่ได้ใช้งาน
        </a>
        @endif
        @if ($canImportLdap)
        <button id="btn-check-ad"
                class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200 text-sm font-semibold rounded-xl transition-all duration-150 whitespace-nowrap cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            ตรวจสอบ AD
        </button>
        <button id="btn-open-modal"
                class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl shadow-sm shadow-indigo-200 transition-all duration-150 hover:-translate-y-0.5 hover:shadow-md hover:shadow-indigo-200 whitespace-nowrap cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            นำเข้าจาก AD
        </button>
        @endif
    </div>
    @endif
</div>

{{-- Stats bar --}}
<div class="flex flex-wrap items-center gap-3 mb-5">
    <div class="flex items-center gap-2 bg-white rounded-xl px-4 py-2.5 shadow-sm ring-1 ring-slate-100">
        <div class="w-7 h-7 bg-indigo-100 rounded-lg flex items-center justify-center">
            <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
        </div>
        <div>
            <span class="text-lg font-bold text-slate-800 leading-none">{{ $users->total() }}</span>
            <span class="text-xs text-slate-500 font-medium ml-1.5">ผู้ใช้ทั้งหมด</span>
        </div>
    </div>
    @if(request('search'))
        <div class="flex items-center gap-2 bg-indigo-50 border border-indigo-100 rounded-xl px-3.5 py-2.5">
            <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
            </svg>
            <span class="text-xs font-semibold text-indigo-700">ค้นหา: "{{ request('search') }}"</span>
            <span class="text-xs text-indigo-500 font-medium">— พบ {{ $users->total() }} คน</span>
        </div>
    @endif
    @if($users->hasPages())
        <div class="text-xs text-slate-400 font-medium bg-white rounded-xl px-3.5 py-2.5 shadow-sm ring-1 ring-slate-100">
            หน้า {{ $users->currentPage() }} / {{ $users->lastPage() }}
        </div>
    @endif
</div>

{{-- AD Check Modal --}}
<div id="ad-check-backdrop" style="display:none"
     class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg flex flex-col ring-1 ring-slate-100" style="max-height:min(88vh,640px)">

        {{-- Header --}}
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between flex-shrink-0">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 bg-amber-100 rounded-xl flex items-center justify-center">
                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-slate-800 text-sm">ตรวจสอบสถานะ AD</h3>
                    <p class="text-xs text-slate-400">ค้นหาผู้ใช้ UCM ที่ไม่มีบัญชีใน Active Directory</p>
                </div>
            </div>
            <button id="btn-close-ad-check" class="w-8 h-8 flex items-center justify-center rounded-xl text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Body --}}
        <div class="flex flex-col flex-1 min-h-0 overflow-hidden">

            {{-- State: idle --}}
            <div id="adc-idle" class="px-6 py-10 text-center">
                <div class="w-14 h-14 bg-amber-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-7 h-7 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <p class="text-sm font-semibold text-slate-700 mb-1">ตรวจสอบผู้ใช้กับ Active Directory</p>
                <p class="text-xs text-slate-400 mb-5">ระบบจะตรวจสอบว่าผู้ใช้ทั้งหมดใน UCM ยังมีบัญชีอยู่ใน AD หรือไม่</p>
                <button id="btn-start-ad-check"
                        class="px-6 py-2.5 bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold rounded-xl shadow-sm transition-all duration-150 cursor-pointer">
                    เริ่มตรวจสอบ
                </button>
            </div>

            {{-- State: loading --}}
            <div id="adc-loading" style="display:none" class="px-6 py-12 flex flex-col items-center justify-center gap-3">
                <svg class="w-8 h-8 text-amber-400 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                </svg>
                <p class="text-sm text-slate-500 font-medium">กำลังตรวจสอบกับ AD...</p>
            </div>

            {{-- State: all good --}}
            <div id="adc-all-ok" style="display:none" class="px-6 py-10 text-center">
                <div class="w-14 h-14 bg-emerald-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-7 h-7 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <p class="text-sm font-semibold text-emerald-700 mb-1">ผู้ใช้ทั้งหมดมีบัญชีใน AD</p>
                <p class="text-xs text-slate-400">ไม่พบผู้ใช้ที่ต้องลบออกจากระบบ</p>
            </div>

            {{-- State: results --}}
            <div id="adc-results" style="display:none" class="flex flex-col flex-1 min-h-0">
                <div class="px-4 md:px-6 py-2.5 bg-red-50 border-b border-red-100 flex flex-wrap items-center justify-between gap-1 text-xs text-slate-500 flex-shrink-0">
                    <label class="flex items-center gap-2 cursor-pointer font-medium text-red-700">
                        <input type="checkbox" id="adc-select-all" class="w-4 h-4 text-red-600 rounded">
                        ไม่พบใน AD: <span id="adc-total-count" class="font-bold">0</span> คน
                    </label>
                    <span id="adc-selected-label" style="display:none" class="text-red-600 font-semibold">เลือกแล้ว <span id="adc-selected-count">0</span> คน</span>
                </div>
                <div id="adc-list" class="overflow-y-auto flex-1 min-h-0"></div>
            </div>

        </div>

        {{-- Footer --}}
        <div id="adc-footer" style="display:none" class="px-6 py-4 border-t border-slate-100 flex items-center justify-between flex-shrink-0 bg-slate-50/50">
            <button id="btn-recheck"
                    class="text-sm text-slate-500 hover:text-slate-700 font-medium transition-colors cursor-pointer">
                ตรวจสอบใหม่
            </button>
            <button id="btn-remove-selected" disabled
                    class="px-5 py-2.5 text-white text-sm font-semibold rounded-xl transition-colors bg-slate-300 cursor-not-allowed">
                ลบออกจาก UCM
            </button>
        </div>

    </div>
</div>

{{-- Import Modal --}}
<div id="import-backdrop" style="display:none"
     class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div id="import-modal"
         class="bg-white rounded-2xl shadow-2xl w-full max-w-xl flex flex-col ring-1 ring-slate-100" style="height:min(88vh,680px)">

        {{-- Header --}}
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between flex-shrink-0">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 bg-indigo-100 rounded-xl flex items-center justify-center">
                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                </div>
                <h3 class="font-bold text-slate-800 text-sm">นำเข้าผู้ใช้</h3>
            </div>
            <button id="btn-close-modal" class="w-8 h-8 flex items-center justify-center rounded-xl text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Tabs --}}
        <div class="flex border-b border-slate-100 flex-shrink-0 px-2 pt-1">
            <button data-tab="ad"
                    class="tab-btn flex-1 py-2.5 px-4 text-sm font-semibold border-b-2 transition-colors rounded-t-lg border-indigo-600 text-indigo-600 cursor-pointer">
                ค้นหาจาก AD
            </button>
            @if($importableSystems->isNotEmpty())
            <button data-tab="system"
                    class="tab-btn flex-1 py-2.5 px-4 text-sm font-semibold border-b-2 transition-colors rounded-t-lg border-transparent text-slate-500 hover:text-slate-700 cursor-pointer">
                นำเข้าจากระบบ
            </button>
            @endif
        </div>

        {{-- Tab: AD Search --}}
        <div id="tab-ad" class="flex flex-col flex-1 min-h-0">
            <div class="px-6 py-3 border-b border-slate-100 flex-shrink-0">
                <div class="relative">
                    <input id="ad-search-input" type="text" placeholder="ค้นหาชื่อ, username, แผนก..."
                           class="w-full px-4 py-2.5 pl-10 border border-slate-200 rounded-xl text-sm text-slate-800 placeholder-slate-400
                                  focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 transition-all">
                    <svg class="absolute left-3.5 top-3 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
                    </svg>
                    <svg id="ad-spinner" style="display:none" class="absolute right-3 top-3 w-4 h-4 text-indigo-400 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                    </svg>
                </div>
            </div>
            <div class="overflow-y-auto flex-1 min-h-0">
                <div id="ad-hint" class="px-6 py-10 text-center text-slate-400 text-sm font-medium">พิมพ์อย่างน้อย 2 ตัวอักษรเพื่อค้นหา</div>
                <div id="ad-empty" style="display:none" class="px-6 py-10 text-center text-slate-400 text-sm font-medium">ไม่พบผู้ใช้ที่ค้นหา</div>
                <div id="ad-results-container" style="display:none">
                    <div class="px-6 py-2.5 bg-slate-50 border-b border-slate-100 flex items-center justify-between text-xs text-slate-500 sticky top-0">
                        <label class="flex items-center gap-2 cursor-pointer font-medium">
                            <input type="checkbox" id="ad-select-all" class="w-4 h-4 text-indigo-600 rounded">
                            เลือกทั้งหมด (<span id="ad-total-count">0</span> คน)
                        </label>
                        <span id="ad-selected-label" style="display:none" class="text-indigo-600 font-semibold">เลือกแล้ว <span id="ad-selected-count">0</span> คน</span>
                    </div>
                    <div id="ad-list"></div>
                </div>
            </div>
        </div>

        {{-- Tab: From System --}}
        @if($importableSystems->isNotEmpty())
        <div id="tab-system" style="display:none" class="flex flex-col flex-1 min-h-0">
            <div class="px-6 py-3 border-b border-slate-100 flex-shrink-0 flex gap-2">

                {{-- Custom searchable dropdown --}}
                <div id="sys-dropdown" class="relative flex-1">
                    <button type="button" id="sys-dropdown-btn"
                            class="w-full flex items-center justify-between px-4 py-2.5 border border-slate-200 rounded-xl text-sm bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 text-left transition-all cursor-pointer">
                        <span id="sys-dropdown-label" class="text-slate-400 truncate">— เลือกระบบ —</span>
                        <svg class="w-4 h-4 text-slate-400 flex-shrink-0 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div id="sys-dropdown-menu" style="display:none"
                         class="absolute z-20 w-full mt-1 bg-white rounded-xl border border-slate-200 shadow-xl shadow-slate-100/50">
                        <div class="p-2 border-b border-slate-100">
                            <input id="sys-search-input" type="text" placeholder="ค้นหาระบบ..."
                                   class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 transition-all">
                        </div>
                        <div id="sys-options" class="max-h-48 overflow-y-auto py-1">
                            <button type="button" data-value="" data-label="— เลือกระบบ —"
                                    class="sys-option w-full text-left px-3 py-2 text-sm text-slate-400 hover:bg-slate-50 rounded-lg mx-1 cursor-pointer" style="width:calc(100% - 8px)">
                                — เลือกระบบ —
                            </button>
                            @foreach($importableSystems as $sys)
                            <button type="button" data-value="{{ $sys->id }}" data-label="{{ $sys->name }}"
                                    class="sys-option w-full text-left px-3 py-2 text-sm text-slate-700 hover:bg-indigo-50 hover:text-indigo-700 rounded-lg mx-1 transition-colors cursor-pointer" style="width:calc(100% - 8px)">
                                {{ $sys->name }}
                            </button>
                            @endforeach
                        </div>
                    </div>
                </div>

                <button id="sys-refresh-btn" type="button" disabled
                        class="px-3 py-2 text-sm rounded-xl transition-colors bg-slate-100 text-slate-300 cursor-not-allowed flex items-center gap-1" title="รีเฟรช">
                    <svg id="sys-refresh-icon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                </button>
            </div>
            <div class="overflow-y-auto flex-1 min-h-0">
                <div id="sys-hint" class="px-6 py-10 text-center text-slate-400 text-sm font-medium">เลือกระบบเพื่อดูรายชื่อผู้ใช้</div>
                <div id="sys-loading" style="display:none" class="px-6 py-10 text-center text-slate-400 text-sm flex items-center justify-center gap-2">
                    <svg class="w-4 h-4 animate-spin text-indigo-400" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                    </svg>
                    กำลังโหลด...
                </div>
                <div id="sys-error" style="display:none" class="px-6 py-6 text-center text-red-500 text-sm font-medium"></div>
                <div id="sys-empty" style="display:none" class="px-6 py-10 text-center text-slate-400 text-sm font-medium">ไม่พบผู้ใช้ในระบบนี้</div>
                <div id="sys-results-container" style="display:none">
                    <div class="px-4 md:px-6 py-2.5 bg-slate-50 border-b border-slate-100 flex flex-wrap items-center justify-between gap-1 text-xs text-slate-500 sticky top-0">
                        <label class="flex items-center gap-2 cursor-pointer font-medium">
                            <input type="checkbox" id="sys-select-all" class="w-4 h-4 text-indigo-600 rounded">
                            ทั้งหมด <span id="sys-total-count">0</span> คน
                            <span class="text-slate-400 hidden sm:inline">(ใน UCM: <span id="sys-in-ucm-count" class="text-emerald-600 font-semibold">0</span>, ยังไม่ได้นำเข้า: <span id="sys-not-ucm-count" class="text-amber-600 font-semibold">0</span>)</span>
                        </label>
                        <span id="sys-selected-label" style="display:none" class="text-indigo-600 font-semibold">เลือกแล้ว <span id="sys-selected-count">0</span> คน</span>
                    </div>
                    <div id="sys-list"></div>
                </div>
            </div>
        </div>
        @endif

        {{-- Footer --}}
        <div class="px-6 py-4 border-t border-slate-100 flex items-center justify-between flex-shrink-0 bg-slate-50/50">
            <span id="footer-hint" class="text-sm text-slate-500 font-medium">เลือกผู้ใช้ที่ต้องการนำเข้า</span>
            <span id="footer-count" style="display:none" class="text-sm text-indigo-600 font-semibold">เลือกแล้ว <span id="footer-count-num">0</span> คน</span>
            <form id="import-form" method="POST" action="{{ route('users.import-bulk') }}">
                @csrf
                <input id="import-system-id" type="hidden" name="system_id" value="">
                <button id="import-submit-btn" type="submit" disabled
                        class="px-5 py-2.5 text-white text-sm font-semibold rounded-xl transition-colors bg-slate-300 cursor-not-allowed">
                    นำเข้า
                </button>
            </form>
        </div>

    </div>
</div>

{{-- Users Table --}}
<div class="bg-white rounded-2xl shadow-sm ring-1 ring-slate-100 overflow-hidden">

    {{-- Card header: default state --}}
    <div id="table-header-default" class="flex items-center justify-between px-4 md:px-6 py-4 border-b border-slate-100 bg-slate-50/50">
        <div class="flex items-center gap-2.5">
            <div class="w-7 h-7 bg-indigo-100 rounded-lg flex items-center justify-center">
                <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <div>
                <span class="text-sm font-bold text-slate-700">รายชื่อผู้ใช้งาน</span>
                <span class="ml-2 text-xs text-slate-400 font-medium">{{ $users->count() }} / {{ $users->total() }} คน</span>
            </div>
        </div>
        <button id="btn-export-all" onclick="doExport()"
                class="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 px-3 py-1.5 rounded-lg transition-colors whitespace-nowrap cursor-pointer">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
            ส่งออก CSV ทั้งหมด
        </button>
    </div>

    {{-- Card header: selection state (appears when rows checked) --}}
    <div id="table-header-selection" style="display:none"
         class="flex items-center justify-between px-4 md:px-6 py-3.5 border-b border-emerald-200 bg-emerald-50">
        <div class="flex items-center gap-3">
            <button onclick="clearSelection()"
                    class="w-7 h-7 flex items-center justify-center rounded-lg text-emerald-600 hover:bg-emerald-100 transition-colors cursor-pointer" title="ยกเลิกการเลือก">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
            <span class="text-sm font-semibold text-emerald-800">
                เลือกแล้ว <span id="selected-count-num">0</span> คน
            </span>
        </div>
        <button id="btn-export-selected" onclick="doExport()"
                class="inline-flex items-center gap-1.5 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 px-3.5 py-1.5 rounded-lg transition-colors shadow-sm shadow-emerald-200 whitespace-nowrap cursor-pointer">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
            ส่งออก <span id="btn-export-selected-count">0</span> คน
        </button>
    </div>

    {{-- "Select all pages" notice (appears only when all current-page rows checked AND more pages exist) --}}
    @if ($users->hasPages())
    <div id="select-all-pages-notice" style="display:none"
         class="flex items-center justify-center gap-3 px-4 py-2.5 border-b border-blue-100 bg-blue-50 text-xs">
        <span id="notice-text-page" class="text-blue-700 font-medium">
            เลือก {{ $users->count() }} คนในหน้านี้แล้ว
        </span>
        <span class="text-blue-300">·</span>
        <button id="btn-select-all-pages" onclick="selectAllPages()"
                class="text-blue-700 font-bold underline underline-offset-2 hover:text-blue-900 transition-colors whitespace-nowrap">
            เลือกทั้งหมด {{ $users->total() }} คนทุกหน้า
        </button>
        <span id="notice-all-selected" style="display:none" class="flex items-center gap-2">
            <span class="text-blue-700 font-semibold">เลือกทั้งหมด {{ $users->total() }} คนแล้ว</span>
            <button onclick="cancelAllPages()" class="text-blue-500 hover:text-blue-700 font-medium underline underline-offset-2">ยกเลิก</button>
        </span>
    </div>
    @endif

    <div class="overflow-x-auto">
        <table class="w-full text-sm min-w-[560px]">
            <thead>
                <tr class="border-b border-slate-100 bg-slate-50/80">
                    <th class="px-3 md:px-4 py-3.5 w-12">
                        <div class="flex flex-col items-center gap-1">
                            <input type="checkbox" id="select-all-chk"
                                   class="w-4 h-4 text-emerald-600 rounded border-slate-300 focus:ring-emerald-500/30 cursor-pointer"
                                   title="เลือกทั้งหมดในหน้านี้">
                            <span class="text-[9px] font-bold text-emerald-500 uppercase tracking-wider leading-none">CSV</span>
                        </div>
                    </th>
                    <th class="text-left px-4 md:px-6 py-3 font-semibold text-slate-500 text-xs uppercase tracking-wide">ชื่อ-สกุล</th>
                    <th class="text-left px-4 md:px-6 py-3.5 font-semibold text-slate-500 text-xs uppercase tracking-wide hidden sm:table-cell">Username</th>
                    <th class="text-left px-4 md:px-6 py-3.5 font-semibold text-slate-500 text-xs uppercase tracking-wide hidden lg:table-cell">แผนก</th>
                    <th class="text-left px-4 md:px-6 py-3.5 font-semibold text-slate-500 text-xs uppercase tracking-wide hidden xl:table-cell">ตำแหน่ง</th>
                    <th class="text-right px-4 md:px-6 py-3.5 font-semibold text-slate-500 text-xs uppercase tracking-wide">สิทธิ์</th>
                    <th class="px-4 md:px-6 py-3.5"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse ($users as $user)
                    <tr class="hover:bg-indigo-50/30 transition-colors group" data-user-id="{{ $user->id }}">
                        <td class="px-3 md:px-4 py-3.5">
                            <input type="checkbox" class="user-chk w-4 h-4 text-emerald-600 rounded border-slate-300 focus:ring-emerald-500/30 cursor-pointer"
                                   value="{{ $user->id }}" data-name="{{ $user->name }}">
                        </td>
                        <td class="px-4 md:px-6 py-3.5">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 bg-gradient-to-br from-indigo-500 to-violet-600 text-white rounded-xl flex items-center justify-center font-bold text-sm shadow-sm shadow-indigo-100 flex-shrink-0">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <div class="font-semibold text-slate-800 text-sm truncate">{{ $user->name }}</div>
                                    <div class="text-xs text-slate-400 font-medium truncate sm:hidden">{{ $user->username }}</div>
                                    <div class="text-xs text-slate-400 font-medium truncate hidden sm:block">{{ $user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 md:px-6 py-3.5 hidden sm:table-cell">
                            <code class="text-xs font-mono text-slate-600 bg-slate-100 px-2 py-1 rounded-lg">{{ $user->username }}</code>
                        </td>
                        <td class="px-4 md:px-6 py-3.5 text-sm text-slate-600 font-medium hidden lg:table-cell">{{ $user->department ?: '—' }}</td>
                        <td class="px-4 md:px-6 py-3.5 text-sm text-slate-500 hidden xl:table-cell">{{ $user->title ?: '—' }}</td>
                        <td class="px-4 md:px-6 py-3.5 text-right">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-700 ring-1 ring-indigo-200/60">
                                {{ $user->system_permissions_count }}
                            </span>
                        </td>
                        <td class="px-4 md:px-6 py-3.5 text-right">
                            <a href="{{ route('users.show', $user) }}"
                               class="inline-flex items-center gap-1 text-xs font-semibold text-indigo-600 hover:text-indigo-800 bg-indigo-50 hover:bg-indigo-100 px-3 py-1.5 rounded-lg transition-colors whitespace-nowrap">
                                จัดการสิทธิ์
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-16 text-center">
                            <div class="w-14 h-14 bg-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                                <svg class="w-7 h-7 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <p class="text-sm font-semibold text-slate-400 mb-1">ยังไม่มีผู้ใช้</p>
                            <p class="text-xs text-slate-300">กด "นำเข้าจาก AD" เพื่อเริ่มต้น</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($users->hasPages())
        <div class="px-4 md:px-6 py-4 border-t border-slate-100 bg-slate-50/50">
            {{ $users->links() }}
        </div>
    @endif
</div>

{{-- Import Progress Overlay --}}
<div id="import-overlay"
     style="display:none"
     class="fixed inset-0 z-[9999] bg-slate-900/70 backdrop-blur-sm items-center justify-center">
    <div class="bg-white rounded-[1.25rem] p-10 w-[90%] max-w-[480px] shadow-[0_25px_80px_rgba(0,0,0,0.4)] border border-slate-200">
        <div class="flex items-center gap-3 mb-7">
            <div class="w-10 h-10 bg-indigo-50 rounded-xl flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                </svg>
            </div>
            <div>
                <h3 id="overlay-title" class="text-base font-bold text-slate-800 m-0">กำลังนำเข้าผู้ใช้...</h3>
                <p id="overlay-msg" class="text-[0.8rem] text-slate-500 mt-[0.2rem] mb-0">กำลังเชื่อมต่อ AD และระบบต่างๆ...</p>
            </div>
        </div>
        <div class="mb-5">
            <div class="flex justify-between mb-2">
                <span id="overlay-count" class="text-[0.8125rem] text-slate-600 font-medium"></span>
                <span id="overlay-pct" class="text-[0.8125rem] font-bold text-indigo-500">5%</span>
            </div>
            <div class="bg-slate-200 rounded-full h-2.5 overflow-hidden">
                <div id="overlay-bar" class="h-2.5 rounded-full bg-gradient-to-r from-indigo-500 to-violet-500 w-[5%] [transition:width_0.6s_ease-out]"></div>
            </div>
        </div>
        <div id="overlay-warning" class="flex items-center gap-2.5 bg-amber-50 rounded-[0.625rem] py-3 px-4 text-xs text-amber-700 border border-amber-200">
            <svg class="w-4 h-4 shrink-0 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" class="opacity-25"/>
                <path fill="currentColor" d="M4 12a8 8 0 018-8v8H4z" class="opacity-75"/>
            </svg>
            กรุณาอย่าปิดหน้าต่างหรือออกจากหน้านี้
        </div>
        <div id="overlay-done" style="display:none" class="items-center gap-2.5 bg-green-50 rounded-[0.625rem] py-3 px-4 text-sm text-green-700 font-semibold border border-green-200">
            <svg class="w-[18px] h-[18px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            เสร็จแล้ว! กำลังโหลดหน้าใหม่...
        </div>
    </div>
</div>

@push('scripts')
<script>
// ── Export + Checkbox logic ──────────────────────────────────────────────────
(function () {
    var selected      = {}; // id → true
    var allPagesMode  = false; // true = ส่งออกทุก page (ไม่ส่ง user_ids)

    var PAGE_COUNT    = {{ $users->count() }};
    var TOTAL_COUNT   = {{ $users->total() }};
    var HAS_PAGES     = {{ $users->hasPages() ? 'true' : 'false' }};

    var selectAllChk       = document.getElementById('select-all-chk');
    var headerDefault      = document.getElementById('table-header-default');
    var headerSelection    = document.getElementById('table-header-selection');
    var countNum           = document.getElementById('selected-count-num');
    var btnExportSelCount  = document.getElementById('btn-export-selected-count');
    var exportForm         = document.getElementById('export-form');
    var idsContainer       = document.getElementById('export-ids-container');
    var noticeBar          = document.getElementById('select-all-pages-notice');
    var noticeTextPage     = document.getElementById('notice-text-page');
    var btnSelectAllPages  = document.getElementById('btn-select-all-pages');
    var noticeAllSelected  = document.getElementById('notice-all-selected');

    function getCheckboxes() {
        return Array.prototype.slice.call(document.querySelectorAll('.user-chk'));
    }

    function allCurrentPageChecked() {
        var chks = getCheckboxes();
        return chks.length > 0 && chks.every(function(c) { return selected[c.value]; });
    }

    function updateUI() {
        var ids   = Object.keys(selected).filter(function(k) { return selected[k]; });
        var count = allPagesMode ? TOTAL_COUNT : ids.length;

        if (allPagesMode || count > 0) {
            headerDefault.style.display   = 'none';
            headerSelection.style.display = '';
            countNum.textContent          = count;
            btnExportSelCount.textContent = count;
        } else {
            headerDefault.style.display   = '';
            headerSelection.style.display = 'none';
        }

        // sync select-all checkbox
        if (selectAllChk) {
            var chks       = getCheckboxes();
            var allChecked = allCurrentPageChecked();
            selectAllChk.checked       = allChecked || allPagesMode;
            selectAllChk.indeterminate = !selectAllChk.checked && ids.length > 0;
        }

        // show/hide "select all pages" notice
        if (noticeBar) {
            if (!allPagesMode && HAS_PAGES && allCurrentPageChecked()) {
                noticeBar.style.display         = '';
                noticeTextPage.style.display    = '';
                btnSelectAllPages.style.display = '';
                noticeAllSelected.style.display = 'none';
            } else if (allPagesMode) {
                noticeBar.style.display         = '';
                noticeTextPage.style.display    = 'none';
                btnSelectAllPages.style.display = 'none';
                noticeAllSelected.style.display = '';
            } else {
                noticeBar.style.display = 'none';
            }
        }

        // highlight selected rows
        document.querySelectorAll('tr[data-user-id]').forEach(function(row) {
            var id = row.getAttribute('data-user-id');
            if (allPagesMode || selected[id]) {
                row.classList.add('bg-emerald-50/60');
            } else {
                row.classList.remove('bg-emerald-50/60');
            }
        });
    }

    // Individual checkbox change
    document.addEventListener('change', function(e) {
        if (!e.target.classList.contains('user-chk')) return;
        allPagesMode = false; // ถ้า uncheck ใดๆ ยกเลิก all-pages mode
        selected[e.target.value] = e.target.checked;
        updateUI();
    });

    // Select all in current page
    if (selectAllChk) {
        selectAllChk.addEventListener('change', function() {
            if (allPagesMode) {
                // uncheck select-all → ยกเลิก all-pages mode
                allPagesMode = false;
                selected = {};
                getCheckboxes().forEach(function(chk) { chk.checked = false; });
            } else {
                getCheckboxes().forEach(function(chk) {
                    chk.checked = selectAllChk.checked;
                    selected[chk.value] = selectAllChk.checked;
                });
            }
            updateUI();
        });
    }

    // เลือกทั้งหมดทุก page
    window.selectAllPages = function() {
        allPagesMode = true;
        // check all current-page boxes ด้วย (visual feedback)
        getCheckboxes().forEach(function(chk) {
            chk.checked = true;
            selected[chk.value] = true;
        });
        updateUI();
    };

    // ยกเลิก all-pages mode
    window.cancelAllPages = function() {
        allPagesMode = false;
        selected = {};
        getCheckboxes().forEach(function(chk) { chk.checked = false; });
        updateUI();
    };

    // Clear selection ทั้งหมด
    window.clearSelection = function() {
        allPagesMode = false;
        selected = {};
        getCheckboxes().forEach(function(chk) { chk.checked = false; });
        updateUI();
    };

    // Export
    window.doExport = function() {
        idsContainer.innerHTML = '';
        if (!allPagesMode) {
            // ส่งเฉพาะ selected ids (ถ้าไม่มีคือ export ทั้งหมด)
            var ids = Object.keys(selected).filter(function(k) { return selected[k]; });
            ids.forEach(function(id) {
                var inp = document.createElement('input');
                inp.type  = 'hidden';
                inp.name  = 'user_ids[]';
                inp.value = id;
                idsContainer.appendChild(inp);
            });
        }
        // allPagesMode = true → ไม่ส่ง user_ids ใดๆ → controller export ทั้งหมด
        exportForm.submit();
    };
})();
</script>

@if ($canImportLdap)
<script>
function _noPermAlert() {
    window.showAlert('คุณไม่มีสิทธิ์ใช้งานฟีเจอร์นี้ กรุณาติดต่อผู้ดูแลระบบ', 'error');
}
</script>
<script>
(function () {
    // ─── AD Check Modal ──────────────────────────────────────────────────
    var CHECK_URL  = '{{ route("users.check-ad-status") }}';
    var REMOVE_URL = '{{ route("users.remove") }}';
    var CSRF       = '{{ csrf_token() }}';

    var adcBackdrop  = document.getElementById('ad-check-backdrop');
    var adcSelected  = {};
    var adcUsers     = [];

    document.getElementById('btn-check-ad').addEventListener('click', function () {
        adcSelected = {};
        adcUsers    = [];
        showAdcState('idle');
        document.getElementById('adc-footer').style.display = 'none';
        adcBackdrop.style.display = 'flex';
    });

    document.getElementById('btn-close-ad-check').addEventListener('click', closeAdcModal);
    adcBackdrop.addEventListener('click', function (e) { if (e.target === adcBackdrop) closeAdcModal(); });

    function closeAdcModal() { adcBackdrop.style.display = 'none'; }

    function showAdcState(state) {
        ['idle','loading','all-ok','results'].forEach(function (s) {
            var el = document.getElementById('adc-' + s);
            if (el) el.style.display = 'none';
        });
        var target = document.getElementById('adc-' + state);
        if (target) target.style.display = (state === 'loading') ? 'flex' : '';
    }

    document.getElementById('btn-start-ad-check').addEventListener('click', runAdCheck);
    document.getElementById('btn-recheck').addEventListener('click', runAdCheck);

    function runAdCheck() {
        adcSelected = {};
        adcUsers    = [];
        showAdcState('loading');
        document.getElementById('adc-footer').style.display = 'none';

        fetch(CHECK_URL, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function (r) {
                if (r.status === 403) throw new Error('forbidden');
                if (!r.ok) throw new Error('HTTP ' + r.status);
                return r.json();
            })
            .then(function (data) {
                adcUsers = Array.isArray(data) ? data : [];
                if (adcUsers.length === 0) {
                    showAdcState('all-ok');
                    document.getElementById('adc-footer').style.display = 'none';
                } else {
                    renderAdcList();
                    showAdcState('results');
                    document.getElementById('adc-footer').style.display = 'flex';
                }
            })
            .catch(function (err) {
                showAdcState('idle');
                if (err && err.message === 'forbidden') { _noPermAlert(); return; }
                showAlert('ตรวจสอบ AD ไม่สำเร็จ: ' + err.message, 'error');
            });
    }

    function renderAdcList() {
        document.getElementById('adc-total-count').textContent = adcUsers.length;
        var list = document.getElementById('adc-list');
        list.innerHTML = '';
        adcUsers.forEach(function (u) {
            var name = u.name || u.username;
            var dept = u.department ? ' \u2022 ' + esc(u.department) : '';
            list.insertAdjacentHTML('beforeend',
                '<label class="flex items-center gap-3 px-6 py-3 hover:bg-red-50/50 cursor-pointer border-b border-slate-50">' +
                '<input type="checkbox" data-user-id="' + u.id + '" ' + (adcSelected[u.id] ? 'checked' : '') +
                ' class="adc-checkbox w-4 h-4 text-red-600 rounded flex-shrink-0">' +
                '<div class="w-8 h-8 bg-red-100 text-red-700 rounded-full flex items-center justify-center font-semibold text-sm flex-shrink-0">' +
                esc(name.charAt(0).toUpperCase()) + '</div>' +
                '<div class="flex-1 min-w-0">' +
                '<div class="text-sm font-medium text-slate-800">' + esc(name) + '</div>' +
                '<div class="text-xs text-slate-400"><span class="font-mono">' + esc(u.username) + '</span>' + dept + '</div>' +
                '</div>' +
                '<span class="text-xs text-red-500 bg-red-50 px-2 py-0.5 rounded-full font-medium flex-shrink-0">ไม่พบใน AD</span>' +
                '</label>'
            );
        });
        bindAdcCheckboxes();
        syncAdcSelectAll();
        updateAdcRemoveBtn();
    }

    function bindAdcCheckboxes() {
        document.getElementById('adc-list').querySelectorAll('.adc-checkbox').forEach(function (cb) {
            cb.addEventListener('change', function () {
                adcSelected[this.dataset.userId] = this.checked;
                syncAdcSelectAll();
                updateAdcRemoveBtn();
            });
        });
    }

    document.getElementById('adc-select-all').addEventListener('change', function () {
        adcUsers.forEach(function (u) { adcSelected[u.id] = this.checked; }, this);
        renderAdcList();
        updateAdcRemoveBtn();
    });

    function syncAdcSelectAll() {
        var el = document.getElementById('adc-select-all');
        el.checked = adcUsers.length > 0 && adcUsers.every(function (u) { return adcSelected[u.id]; });
    }

    function getAdcSelectedIds() {
        return Object.keys(adcSelected).filter(function (k) { return adcSelected[k]; });
    }

    function updateAdcRemoveBtn() {
        var count = getAdcSelectedIds().length;
        var btn   = document.getElementById('btn-remove-selected');
        var lbl   = document.getElementById('adc-selected-label');
        if (count > 0) {
            btn.disabled   = false;
            btn.textContent = 'ลบออกจาก UCM (' + count + ' คน)';
            btn.className  = 'px-5 py-2.5 text-white text-sm font-semibold rounded-xl transition-colors bg-red-600 hover:bg-red-700 cursor-pointer';
            lbl.style.display = '';
            document.getElementById('adc-selected-count').textContent = count;
        } else {
            btn.disabled   = true;
            btn.textContent = 'ลบออกจาก UCM';
            btn.className  = 'px-5 py-2.5 text-white text-sm font-semibold rounded-xl transition-colors bg-slate-300 cursor-not-allowed';
            lbl.style.display = 'none';
        }
    }

    document.getElementById('btn-remove-selected').addEventListener('click', function () {
        var ids   = getAdcSelectedIds();
        var count = ids.length;
        if (count === 0) return;
        askConfirm('__adc_remove__', 'ลบผู้ใช้ ' + count + ' คน ออกจาก UCM?', 'สิทธิ์ทั้งหมดของผู้ใช้เหล่านี้จะถูกลบด้วย และไม่สามารถกู้คืนได้');

        // override submitConfirm once for this action
        window.__adcRemoveOverride__ = function () {
            window.__adcRemoveOverride__ = null;
            doRemoveUsers(ids);
        };
    });

    function esc(str) {
        return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    // patch submitConfirm to support our override
    var _origSubmit = window.submitConfirm;
    window.submitConfirm = function () {
        if (window.__adcRemoveOverride__) {
            window.__adcRemoveOverride__();
            window.closeConfirm();
        } else {
            _origSubmit();
        }
    };

    function doRemoveUsers(ids) {
        var body = new FormData();
        body.append('_token', CSRF);
        ids.forEach(function (id) { body.append('user_ids[]', id); });

        fetch(REMOVE_URL, { method: 'POST', body: body, headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function (r) {
                if (r.status === 403) throw new Error('forbidden');
                if (!r.ok) throw new Error('HTTP ' + r.status);
                return r.json();
            })
            .then(function (data) {
                closeAdcModal();
                showAlert(data.message || 'ลบเรียบร้อย', 'info');
                setTimeout(function () { window.location.reload(); }, 1500);
            })
            .catch(function (err) {
                if (err && err.message === 'forbidden') { _noPermAlert(); return; }
                showAlert('เกิดข้อผิดพลาด: ' + err.message, 'error');
            });
    }
})();
</script>

<script>
(function () {
    // ─── Import Modal (AD + ระบบ) ──────────────────────────────────────
    var SEARCH_URL  = '{{ route("users.search-ldap") }}';
    var IMPORT_URL  = '{{ route("users.import-bulk") }}';
    var systemUrls  = {!! Js::from($importableSystems->mapWithKeys(fn($s) => [$s->id => route('systems.users-for-import', $s)])) !!};

    // ─── State ──────────────────────────────────────────────────────────
    var currentTab     = 'ad';
    var adResults      = [];
    var systemResults  = [];
    var selected       = {};
    var currentSysId   = '';
    var adTimer        = null;

    // ─── Elements ───────────────────────────────────────────────────────
    var backdrop  = document.getElementById('import-backdrop');
    var modal     = document.getElementById('import-modal');
    var importForm = document.getElementById('import-form');

    // ─── Open / Close ───────────────────────────────────────────────────
    document.getElementById('btn-open-modal').addEventListener('click', openModal);
    document.getElementById('btn-close-modal').addEventListener('click', closeModal);
    backdrop.addEventListener('click', function (e) { if (e.target === backdrop) closeModal(); });

    function openModal() {
        resetModal();
        backdrop.style.display = 'flex';
    }
    function closeModal() {
        backdrop.style.display = 'none';
    }
    function resetModal() {
        currentTab    = 'ad';
        adResults     = [];
        systemResults = [];
        selected      = {};
        currentSysId  = '';
        document.getElementById('ad-search-input').value = '';
        showTab('ad');
        showAdState('hint');
        showSysState('hint');
        updateFooter();
        // reset system dropdown
        var lbl = document.getElementById('sys-dropdown-label');
        if (lbl) { lbl.textContent = '— เลือกระบบ —'; lbl.className = 'text-slate-400 truncate'; }
        setSysRefresh(false);
    }

    // ─── Tabs ────────────────────────────────────────────────────────────
    document.querySelectorAll('.tab-btn').forEach(function (btn) {
        btn.addEventListener('click', function () { showTab(this.dataset.tab); });
    });

    function showTab(tab) {
        currentTab = tab;

        var tabAd  = document.getElementById('tab-ad');
        var tabSys = document.getElementById('tab-system');

        tabAd.style.display = tab === 'ad' ? 'flex' : 'none';
        if (tabSys) tabSys.style.display = tab === 'system' ? 'flex' : 'none';

        document.querySelectorAll('.tab-btn').forEach(function (btn) {
            var active = btn.dataset.tab === tab;
            btn.className = 'tab-btn flex-1 py-2.5 px-4 text-sm font-semibold border-b-2 transition-colors rounded-t-lg ' +
                (active ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-slate-500 hover:text-slate-700');
        });

        selected = {};
        updateFooter();
    }

    // ─── AD Search ──────────────────────────────────────────────────────
    document.getElementById('ad-search-input').addEventListener('input', function () {
        var q = this.value;
        clearTimeout(adTimer);
        if (q.length < 2) { adResults = []; showAdState('hint'); return; }
        document.getElementById('ad-spinner').style.display = '';
        adTimer = setTimeout(function () {
            fetch(SEARCH_URL + '?q=' + encodeURIComponent(q))
                .then(function (r) {
                    if (r.status === 403) throw new Error('forbidden');
                    return r.json();
                })
                .then(function (data) {
                    document.getElementById('ad-spinner').style.display = 'none';
                    adResults = Array.isArray(data) ? data : [];
                    renderAdList();
                })
                .catch(function (err) {
                    document.getElementById('ad-spinner').style.display = 'none';
                    if (err && err.message === 'forbidden') { _noPermAlert(); }
                });
        }, 350);
    });

    function showAdState(state) {
        document.getElementById('ad-hint').style.display              = state === 'hint'    ? '' : 'none';
        document.getElementById('ad-empty').style.display             = state === 'empty'   ? '' : 'none';
        document.getElementById('ad-results-container').style.display = state === 'results' ? '' : 'none';
    }

    function renderAdList() {
        if (adResults.length === 0) { showAdState('empty'); return; }
        showAdState('results');
        document.getElementById('ad-total-count').textContent = adResults.length;
        var list = document.getElementById('ad-list');
        list.innerHTML = '';
        adResults.forEach(function (u) {
            var name  = u.name || u.username;
            var dept  = u.department ? ' \u2022 ' + esc(u.department) : '';
            list.insertAdjacentHTML('beforeend',
                '<label class="flex items-center gap-3 px-6 py-3 hover:bg-slate-50 cursor-pointer border-b border-slate-50">' +
                '<input type="checkbox" data-username="' + escAttr(u.username) + '" ' + (selected[u.username] ? 'checked' : '') + ' class="user-checkbox w-4 h-4 text-indigo-600 rounded flex-shrink-0">' +
                '<div class="w-8 h-8 bg-indigo-100 text-indigo-700 rounded-full flex items-center justify-center font-semibold text-sm flex-shrink-0">' + esc(name.charAt(0).toUpperCase()) + '</div>' +
                '<div class="flex-1 min-w-0">' +
                '<div class="text-sm font-medium text-slate-800">' + esc(name) + '</div>' +
                '<div class="text-xs text-slate-400"><span class="font-mono">' + esc(u.username) + '</span>' + dept + '</div>' +
                '</div>' +
                '<div class="text-xs text-slate-400 flex-shrink-0">' + esc(u.title || '') + '</div>' +
                '</label>');
        });
        bindCheckboxes('ad-list');
        syncSelectAll('ad-select-all', adResults);
        updateFooter();
    }

    document.getElementById('ad-select-all').addEventListener('change', function () {
        adResults.forEach(function (u) { selected[u.username] = this.checked; }, this);
        renderAdList();
    });

    // ─── System tab ─────────────────────────────────────────────────────
    var sysDropdownBtn  = document.getElementById('sys-dropdown-btn');
    var sysDropdownMenu = document.getElementById('sys-dropdown-menu');
    var sysSearchInput  = document.getElementById('sys-search-input');

    if (sysDropdownBtn) {
        sysDropdownBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            var open = sysDropdownMenu.style.display !== 'none';
            sysDropdownMenu.style.display = open ? 'none' : '';
            if (!open) sysSearchInput.focus();
        });

        sysSearchInput.addEventListener('input', function () {
            var q = this.value.toLowerCase();
            document.querySelectorAll('.sys-option').forEach(function (opt) {
                opt.style.display = opt.dataset.label.toLowerCase().includes(q) ? '' : 'none';
            });
        });

        document.querySelectorAll('.sys-option').forEach(function (opt) {
            opt.addEventListener('click', function () {
                var val   = this.dataset.value;
                var label = this.dataset.label;
                currentSysId = val;
                var lbl = document.getElementById('sys-dropdown-label');
                lbl.textContent = label;
                lbl.className   = val ? 'text-slate-700 truncate' : 'text-slate-400 truncate';
                sysDropdownMenu.style.display = 'none';
                sysSearchInput.value = '';
                document.querySelectorAll('.sys-option').forEach(function (o) { o.style.display = ''; });
                setSysRefresh(!!val);
                selected = {};
                systemResults = [];
                showSysState('hint');
                updateFooter();
                if (val) loadSystemUsers();
            });
        });

        document.addEventListener('click', function (e) {
            var dd = document.getElementById('sys-dropdown');
            if (dd && !dd.contains(e.target)) sysDropdownMenu.style.display = 'none';
        });
    }

    var sysRefreshBtn = document.getElementById('sys-refresh-btn');
    if (sysRefreshBtn) {
        sysRefreshBtn.addEventListener('click', function () { if (currentSysId) loadSystemUsers(); });
    }

    function setSysRefresh(enabled) {
        if (!sysRefreshBtn) return;
        sysRefreshBtn.disabled = !enabled;
        sysRefreshBtn.className = 'px-3 py-2 text-sm rounded-xl transition-colors flex items-center gap-1 ' +
            (enabled ? 'bg-slate-100 hover:bg-slate-200 text-slate-600' : 'bg-slate-100 text-slate-300 cursor-not-allowed');
    }

    function showSysState(state) {
        var map = { hint: 'sys-hint', loading: 'sys-loading', error: 'sys-error', empty: 'sys-empty', results: 'sys-results-container' };
        Object.values(map).forEach(function (id) {
            var el = document.getElementById(id);
            if (el) el.style.display = 'none';
        });
        var target = document.getElementById(map[state]);
        if (target) target.style.display = (state === 'loading') ? 'flex' : '';
    }

    function loadSystemUsers() {
        if (!currentSysId || !systemUrls[currentSysId]) return;
        var icon = document.getElementById('sys-refresh-icon');
        if (icon) icon.classList.add('animate-spin');
        showSysState('loading');
        fetch(systemUrls[currentSysId])
            .then(function (r) {
                if (r.status === 403) throw new Error('forbidden');
                if (!r.ok) return r.json().then(function (d) { throw new Error(d.error || 'HTTP ' + r.status); });
                return r.json();
            })
            .then(function (data) {
                if (icon) icon.classList.remove('animate-spin');
                systemResults = Array.isArray(data) ? data : [];
                if (systemResults.length === 0) { showSysState('empty'); return; }
                renderSystemList();
                showSysState('results');
            })
            .catch(function (err) {
                if (icon) icon.classList.remove('animate-spin');
                if (err && err.message === 'forbidden') { _noPermAlert(); return; }
                var errEl = document.getElementById('sys-error');
                if (errEl) errEl.textContent = 'โหลดไม่สำเร็จ: ' + err.message;
                showSysState('error');
            });
    }

    function renderSystemList() {
        var inUcm  = systemResults.filter(function (u) { return u.in_ucm; }).length;
        var notUcm = systemResults.length - inUcm;
        setText('sys-total-count', systemResults.length);
        setText('sys-in-ucm-count', inUcm);
        setText('sys-not-ucm-count', notUcm);
        var list = document.getElementById('sys-list');
        list.innerHTML = '';
        systemResults.forEach(function (u) {
            var name        = u.name || u.username;
            var avatarClass = u.in_ucm ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700';
            var ucmBadge    = u.in_ucm
                ? '<span class="text-xs text-emerald-700 bg-emerald-50 px-1.5 rounded-full">มีใน UCM แล้ว</span>'
                : '<span class="text-xs text-amber-700 bg-amber-50 px-1.5 rounded-full">ยังไม่ได้นำเข้า</span>';
            var disBadge    = !u.status ? '<span class="text-xs text-red-600 bg-red-50 px-1.5 rounded-full">Disabled</span>' : '';
            var dept        = u.department ? ' \u2022 ' + esc(u.department) : '';
            list.insertAdjacentHTML('beforeend',
                '<label class="flex items-center gap-3 px-6 py-3 hover:bg-slate-50 cursor-pointer border-b border-slate-50">' +
                '<input type="checkbox" data-username="' + escAttr(u.username) + '" ' + (selected[u.username] ? 'checked' : '') + ' class="user-checkbox w-4 h-4 text-indigo-600 rounded flex-shrink-0">' +
                '<div class="w-8 h-8 rounded-full flex items-center justify-center font-semibold text-sm flex-shrink-0 ' + avatarClass + '">' + esc(name.charAt(0).toUpperCase()) + '</div>' +
                '<div class="flex-1 min-w-0">' +
                '<div class="flex items-center gap-2"><span class="text-sm font-medium text-slate-800">' + esc(name) + '</span>' + ucmBadge + disBadge + '</div>' +
                '<div class="text-xs text-slate-400"><span class="font-mono">' + esc(u.username) + '</span>' + dept + '</div>' +
                '</div></label>');
        });
        bindCheckboxes('sys-list');
        syncSelectAll('sys-select-all', systemResults);
        updateFooter();
    }

    var sysSelectAll = document.getElementById('sys-select-all');
    if (sysSelectAll) {
        sysSelectAll.addEventListener('change', function () {
            systemResults.forEach(function (u) { selected[u.username] = this.checked; }, this);
            renderSystemList();
        });
    }

    // ─── Checkbox helpers ────────────────────────────────────────────────
    function bindCheckboxes(listId) {
        document.getElementById(listId).querySelectorAll('.user-checkbox').forEach(function (cb) {
            cb.addEventListener('change', function () {
                selected[this.dataset.username] = this.checked;
                var results = listId === 'ad-list' ? adResults : systemResults;
                var saId    = listId === 'ad-list' ? 'ad-select-all' : 'sys-select-all';
                syncSelectAll(saId, results);
                updateFooter();
            });
        });
    }

    function syncSelectAll(saId, results) {
        var el = document.getElementById(saId);
        if (el) el.checked = results.length > 0 && results.every(function (u) { return selected[u.username]; });
    }

    // ─── Footer / submit button ──────────────────────────────────────────
    function getSelectedUsernames() {
        return Object.keys(selected).filter(function (k) { return selected[k]; });
    }

    function updateFooter() {
        var count    = getSelectedUsernames().length;
        var hint     = document.getElementById('footer-hint');
        var countEl  = document.getElementById('footer-count');
        var countNum = document.getElementById('footer-count-num');
        var btn      = document.getElementById('import-submit-btn');

        if (count > 0) {
            hint.style.display    = 'none';
            countEl.style.display = '';
            countNum.textContent  = count;
            btn.disabled          = false;
            btn.textContent       = 'นำเข้า (' + count + ' คน)';
            btn.className         = 'px-5 py-2.5 text-white text-sm font-semibold rounded-xl transition-colors bg-indigo-600 hover:bg-indigo-700 cursor-pointer';
        } else {
            hint.style.display    = '';
            countEl.style.display = 'none';
            btn.disabled          = true;
            btn.textContent       = 'นำเข้า';
            btn.className         = 'px-5 py-2.5 text-white text-sm font-semibold rounded-xl transition-colors bg-slate-300 cursor-not-allowed';
        }

        // selected count labels inside each tab
        showSelectedLabel('ad-selected-label',  'ad-selected-count',  count);
        showSelectedLabel('sys-selected-label', 'sys-selected-count', count);
    }

    function showSelectedLabel(lblId, numId, count) {
        var lbl = document.getElementById(lblId);
        if (!lbl) return;
        if (count > 0) { lbl.style.display = ''; setText(numId, count); }
        else lbl.style.display = 'none';
    }

    // ─── Populate form inputs before submit ──────────────────────────────
    importForm.addEventListener('submit', function () {
        importForm.querySelectorAll('input[name="usernames[]"]').forEach(function (el) { el.remove(); });
        getSelectedUsernames().forEach(function (username) {
            var inp = document.createElement('input');
            inp.type  = 'hidden';
            inp.name  = 'usernames[]';
            inp.value = username;
            importForm.appendChild(inp);
        });
        document.getElementById('import-system-id').value = currentTab === 'system' ? currentSysId : '';
    });

    // ─── Helpers ─────────────────────────────────────────────────────────
    function esc(str) {
        return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }
    function escAttr(str) {
        return String(str).replace(/"/g,'&quot;').replace(/'/g,'&#39;');
    }
    function setText(id, val) {
        var el = document.getElementById(id); if (el) el.textContent = val;
    }

})();
</script>

<script>
(function () {
    var form = document.getElementById('import-form');
    if (!form) return;

    var overlay  = document.getElementById('import-overlay');
    var bar      = document.getElementById('overlay-bar');
    var pct      = document.getElementById('overlay-pct');
    var msg      = document.getElementById('overlay-msg');
    var countEl  = document.getElementById('overlay-count');
    var warning  = document.getElementById('overlay-warning');
    var doneEl   = document.getElementById('overlay-done');

    var progress = 5;
    var timer    = null;

    function setProgress(val) {
        progress = val;
        bar.style.width  = Math.round(val) + '%';
        pct.textContent  = Math.round(val) + '%';
        if (val >= 100) { bar.style.background = '#22c55e'; pct.style.color = '#15803d'; }
    }

    function navHandler(e) { e.preventDefault(); e.returnValue = 'กำลังนำเข้าผู้ใช้ กรุณารอสักครู่'; }

    form.addEventListener('submit', async function (e) {
        e.preventDefault();

        var fd    = new FormData(form);
        var total = fd.getAll('usernames[]').length;
        if (total === 0) return;

        setProgress(5);
        countEl.textContent       = 'นำเข้า ' + total + ' คน';
        msg.textContent           = 'กำลังเชื่อมต่อ AD และระบบต่างๆ...';
        warning.style.display     = 'flex';
        doneEl.style.display      = 'none';
        overlay.style.display     = 'flex';
        window.addEventListener('beforeunload', navHandler);

        timer = setInterval(function () {
            if (progress < 85) setProgress(progress + (85 - progress) * 0.06);
        }, 500);

        try {
            var res = await fetch('{{ route("users.import-bulk") }}', {
                method: 'POST', body: fd,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (res.status === 403) throw new Error('forbidden');
            if (!res.ok) {
                var text = await res.text();
                throw new Error('HTTP ' + res.status + ': ' + text.substring(0, 300));
            }
            var data = await res.json();
            clearInterval(timer);
            setProgress(100);
            msg.textContent       = data.message || 'นำเข้าเสร็จแล้ว';
            warning.style.display = 'none';
            doneEl.style.display  = 'flex';
            window.removeEventListener('beforeunload', navHandler);
            setTimeout(function () { window.location.href = data.redirect || window.location.pathname; }, 1200);
        } catch (err) {
            clearInterval(timer);
            overlay.style.display = 'none';
            window.removeEventListener('beforeunload', navHandler);
            if (err && err.message === 'forbidden') { _noPermAlert(); return; }
            showAlert('เกิดข้อผิดพลาด: ' + err.message, 'error');
        }
    });
})();
</script>
@endif {{-- isAdmin --}}
@endpush
@endsection
