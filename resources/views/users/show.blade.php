@extends('layouts.app')

@section('title', $user->name)
@section('header', 'จัดการสิทธิ์')

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}" class="text-slate-400 hover:text-slate-600 font-medium transition-colors whitespace-nowrap">Dashboard</a>
    <svg class="w-3.5 h-3.5 text-slate-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    <a href="{{ route('users.index') }}" class="text-slate-400 hover:text-slate-600 font-medium transition-colors whitespace-nowrap">จัดการผู้ใช้</a>
    <svg class="w-3.5 h-3.5 text-slate-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    <span class="font-semibold text-slate-800 truncate">{{ $user->name }}</span>
@endsection

@section('content')

{{-- User Profile Card --}}
<div class="bg-white rounded-2xl shadow-sm ring-1 ring-slate-100 overflow-hidden mb-6">
    <div class="h-1.5 w-full" style="background: linear-gradient(90deg, #6366f1, #8b5cf6, #06b6d4)"></div>
    <div class="p-4 md:p-6 flex flex-col sm:flex-row items-start gap-4 md:gap-5">
        <div class="w-16 h-16 rounded-2xl flex items-center justify-center text-2xl font-bold text-white flex-shrink-0 shadow-lg shadow-indigo-200"
             style="background: linear-gradient(135deg, #6366f1, #8b5cf6)">
            {{ strtoupper(substr($user->name, 0, 1)) }}
        </div>
        <div class="flex-1 min-w-0">
            <h2 class="text-xl font-bold text-slate-800 tracking-tight">{{ $user->name }}</h2>
            <div class="flex flex-wrap items-center gap-2 mt-2">
                <code class="text-xs font-mono text-slate-600 bg-slate-100 px-2.5 py-1 rounded-lg font-semibold">{{ $user->username }}</code>
                @if ($user->email)
                    <span class="inline-flex items-center gap-1 text-xs text-slate-500 font-medium">
                        <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        {{ $user->email }}
                    </span>
                @endif
                @if ($user->department)
                    <span class="inline-flex items-center gap-1 text-xs text-slate-500 font-medium">
                        <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        {{ $user->department }}
                    </span>
                @endif
                @if ($user->title)
                    <span class="inline-flex items-center text-xs text-slate-500 font-medium bg-slate-50 px-2 py-0.5 rounded-lg">{{ $user->title }}</span>
                @endif
            </div>
            <div class="mt-3 flex flex-wrap items-center gap-2">
                <span class="text-xs text-slate-400 font-medium">รหัสพนักงาน:</span>
                <span id="emp-view" class="text-sm font-mono font-semibold text-slate-700">{{ $user->employee_number ?: '—' }}</span>
                @if (auth()->user()->isSuperAdmin())
                <button id="emp-edit-btn" onclick="empEdit()"
                        class="text-xs font-semibold text-indigo-500 hover:text-indigo-700 bg-indigo-50 hover:bg-indigo-100 px-2 py-0.5 rounded-lg transition-colors">แก้ไข</button>
                <form id="emp-form" style="display:none"
                      action="{{ route('users.info.update', $user) }}" method="POST"
                      class="flex flex-wrap items-center gap-1.5">
                    @csrf @method('PATCH')
                    <input type="text" name="employee_number" value="{{ $user->employee_number }}" placeholder="เช่น EMP001"
                           class="text-sm border border-slate-200 rounded-lg px-3 py-1.5 w-28 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 transition-all">
                    <button type="submit" class="text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg px-3 py-1.5 transition-colors">บันทึก</button>
                    <button type="button" onclick="empCancel()" class="text-xs font-semibold text-slate-500 hover:text-slate-700 hover:bg-slate-100 rounded-lg px-2 py-1.5 transition-colors">ยกเลิก</button>
                </form>
                @endif
            </div>
        </div>
        <div class="flex items-center gap-2 flex-wrap sm:flex-shrink-0 sm:flex-col sm:items-end">
            @if (auth()->user()->isAdmin())
            <form action="{{ route('users.import') }}" method="POST">
                @csrf
                <input type="hidden" name="username" value="{{ $user->username }}">
                <button type="submit" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-600 hover:text-slate-800 border border-slate-200 hover:border-slate-300 hover:bg-slate-50 rounded-xl px-3 py-2 transition-all">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    ดึงข้อมูลจาก LDAP
                </button>
            </form>
            @endif
            <a href="{{ route('users.index') }}"
               class="inline-flex items-center gap-1.5 text-sm font-semibold text-slate-500 hover:text-slate-700 hover:bg-slate-100 px-3 py-2 rounded-xl transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                กลับ
            </a>
        </div>
    </div>
</div>

{{-- Permission Matrix --}}
<div class="space-y-5">
    @forelse ($systems as $system)
        @php
            $permData       = $permsPerSystem[$system->id] ?? ['perms' => [], 'ucmPerms' => [], 'source' => 'ucm', 'outOfSync' => false, 'accountStatus' => null];
            $userPerms      = $permData['perms'];
            $ucmPerms       = $permData['ucmPerms'];
            $source         = $permData['source'];
            $outOfSync      = $permData['outOfSync'];
            $accountStatus  = $permData['accountStatus'];
            $grouped        = $system->permissions->groupBy('group');
            $syncLog        = $latestSyncLogs[$system->id] ?? null;
            $exclusiveGroups = $grouped->filter(fn($p) => $p->first()->is_exclusive);
            $checkboxGroups  = $grouped->filter(fn($p) => !$p->first()->is_exclusive);
            $selectedCount   = count($userPerms);
            $totalCount      = $system->permissions->count();
        @endphp

        <div class="bg-white rounded-2xl shadow-sm ring-1 ring-slate-100 overflow-hidden">

            {{-- System header --}}
            <div class="flex flex-col sm:flex-row sm:items-center gap-3 px-5 py-4 border-b border-slate-100"
                 style="background: linear-gradient(135deg, {{ $system->color }}08, {{ $system->color }}03)">

                {{-- Color + name --}}
                <div class="flex items-center gap-3 flex-1 min-w-0">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0 shadow-sm"
                         style="background-color: {{ $system->color }}20">
                        <div class="w-3.5 h-3.5 rounded-md" style="background-color: {{ $system->color }}"></div>
                    </div>
                    <div class="min-w-0">
                        <h3 class="font-bold text-slate-800 text-sm leading-tight">{{ $system->name }}</h3>
                        @if ($system->description)
                            <p class="text-xs text-slate-400 font-medium mt-0.5 truncate">{{ $system->description }}</p>
                        @endif
                    </div>
                </div>

                {{-- Badges --}}
                <div class="flex items-center gap-2 flex-wrap">

                    {{-- Account status --}}
                    @if ($accountStatus === true)
                        <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-full ring-1 ring-emerald-200/60">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Active
                        </span>
                        @if (auth()->user()->isSuperAdmin())
                        <form id="form-disable-{{ $system->id }}" action="{{ route('users.system.status', [$user, $system]) }}" method="POST" class="hidden">
                            @csrf <input type="hidden" name="active" value="0">
                        </form>
                        <button type="button"
                                onclick="askConfirm('form-disable-{{ $system->id }}', 'ปิดการใช้งาน account?', '{{ addslashes($user->name) }} ใน {{ addslashes($system->name) }}')"
                                class="text-xs font-semibold text-red-600 hover:text-red-800 border border-red-200 hover:bg-red-50 rounded-lg px-2.5 py-1 transition-colors">
                            ปิดการใช้งาน
                        </button>
                        @endif
                    @elseif ($accountStatus === false)
                        <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-red-700 bg-red-50 px-2.5 py-1 rounded-full ring-1 ring-red-200/60">
                            <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>Disabled
                        </span>
                        @if (auth()->user()->isSuperAdmin())
                        <form id="form-enable-{{ $system->id }}" action="{{ route('users.system.status', [$user, $system]) }}" method="POST" class="hidden">
                            @csrf <input type="hidden" name="active" value="1">
                        </form>
                        <button type="button"
                                onclick="askConfirm('form-enable-{{ $system->id }}', 'เปิดใช้งาน account?', '{{ addslashes($user->name) }} ใน {{ addslashes($system->name) }}')"
                                class="text-xs font-semibold text-emerald-600 hover:text-emerald-800 border border-emerald-200 hover:bg-emerald-50 rounded-lg px-2.5 py-1 transition-colors">
                            เปิดใช้งาน
                        </button>
                        @endif
                    @endif

                    {{-- Out-of-sync --}}
                    @if ($outOfSync)
                        <span class="inline-flex items-center gap-1 text-xs font-semibold text-amber-700 bg-amber-50 px-2.5 py-1 rounded-full ring-1 ring-amber-200/60"
                              title="UCM: {{ implode(', ', $ucmPerms) ?: 'ไม่มี' }} | ระบบจริง: {{ implode(', ', $userPerms) ?: 'ไม่มี' }}">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            Out of Sync
                        </span>
                    @endif

                    {{-- Sync status --}}
                    @if ($syncLog)
                        @if ($syncLog->status === 'success')
                            <span class="inline-flex items-center gap-1 text-xs font-semibold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-full ring-1 ring-emerald-200/60"
                                  title="Synced: {{ $syncLog->synced_at }}">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                Synced
                            </span>
                        @elseif ($syncLog->status === 'failed')
                            <span class="inline-flex items-center gap-1 text-xs font-semibold text-red-700 bg-red-50 px-2.5 py-1 rounded-full ring-1 ring-red-200/60"
                                  title="{{ $syncLog->error_message }}">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                Sync Failed
                            </span>
                        @else
                            <span data-sync-pending class="inline-flex items-center gap-1 text-xs font-semibold text-amber-700 bg-amber-50 px-2.5 py-1 rounded-full ring-1 ring-amber-200/60">
                                <svg class="w-3 h-3 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/></svg>
                                Syncing...
                            </span>
                        @endif
                    @endif

                    {{-- Permission count --}}
                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full
                                 {{ $selectedCount > 0 ? 'text-indigo-700 bg-indigo-50 ring-1 ring-indigo-200/60' : 'text-slate-400 bg-slate-100' }}">
                        {{ $selectedCount }} / {{ $totalCount }} สิทธิ์
                    </span>
                </div>
            </div>

            {{-- Body --}}
            @if ($system->permissions->isEmpty())
                <div class="px-6 py-8 flex items-center gap-3 text-sm text-slate-400">
                    <div class="w-8 h-8 rounded-xl bg-slate-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <span>ยังไม่มี permission ในระบบนี้ —
                        <a href="{{ route('systems.show', $system) }}" class="text-indigo-600 hover:text-indigo-800 font-semibold hover:underline">ไปเพิ่ม →</a>
                    </span>
                </div>
            @else
                @if (auth()->user()->isAdmin())
                {{-- ── Editable permission form (L1+) ── --}}
                <form method="POST" action="{{ route('users.permissions.update', $user) }}">
                    @csrf
                    <input type="hidden" name="system_id" value="{{ $system->id }}">

                    {{-- Out-of-sync info banner --}}
                    @if ($source === 'remote' && $outOfSync)
                        <div class="mx-5 mt-4 px-4 py-3 bg-amber-50 border border-amber-200 rounded-xl flex items-start gap-2.5">
                            <svg class="w-4 h-4 text-amber-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            <div class="text-xs text-amber-800">
                                <span class="font-semibold">ข้อมูลจาก {{ $system->name }} โดยตรง</span>
                                — UCM เก็บไว้: <code class="bg-amber-100 px-1.5 py-0.5 rounded font-mono">{{ implode(', ', $ucmPerms) ?: 'ไม่มี' }}</code>
                            </div>
                        </div>
                    @elseif ($source === 'remote')
                        <div class="mx-5 mt-4 px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-500 font-medium">
                            แสดงสิทธิ์จาก <strong class="text-slate-700">{{ $system->name }}</strong> โดยตรง
                        </div>
                    @endif

                    {{-- ── Exclusive permission groups (radio pills per row) ── --}}
                    @if ($exclusiveGroups->isNotEmpty())
                        <div class="px-5 pt-5 {{ $checkboxGroups->isNotEmpty() ? 'pb-2' : 'pb-4' }}">
                            <div class="rounded-xl border border-slate-100 overflow-hidden divide-y divide-slate-50">
                                {{-- Column header --}}
                                @php
                                    $firstGroup = $exclusiveGroups->first();
                                    $optionHeaders = $firstGroup->map(function($p) {
                                        $parts = explode('—', $p->label);
                                        return trim(end($parts));
                                    });
                                @endphp
                                <div class="flex items-center gap-3 px-4 py-2 bg-slate-50/80">
                                    <div class="flex-1 text-xs font-bold text-slate-400 uppercase tracking-wider">กลุ่มสิทธิ์</div>
                                    <div class="flex items-center gap-1.5 flex-shrink-0">
                                        @foreach ($optionHeaders as $header)
                                            @php
                                                $hdrColor = match(true) {
                                                    str_contains($header, 'Editable') => 'text-indigo-500',
                                                    str_contains($header, 'Read Only') => 'text-sky-500',
                                                    str_contains($header, 'Denied') => 'text-rose-400',
                                                    default => 'text-slate-500',
                                                };
                                            @endphp
                                            <span class="w-24 text-center text-xs font-bold {{ $hdrColor }} uppercase tracking-wide">{{ $header }}</span>
                                        @endforeach
                                    </div>
                                </div>

                                {{-- Permission rows --}}
                                @foreach ($exclusiveGroups as $group => $perms)
                                    <div class="flex items-center gap-3 px-4 py-2.5 bg-white hover:bg-slate-50/60 transition-colors">
                                        <div class="flex-1 min-w-0">
                                            <span class="text-sm font-medium text-slate-700">{{ $group ?: 'ทั่วไป' }}</span>
                                        </div>
                                        <div class="flex items-center gap-1.5 flex-shrink-0">
                                            @foreach ($perms as $perm)
                                                @php
                                                    $parts = explode('—', $perm->label);
                                                    $shortLabel = trim(end($parts));
                                                    $isChecked  = in_array($perm->key, $userPerms);

                                                    $checkedCls = match(true) {
                                                        str_contains($shortLabel, 'Editable')  => 'bg-indigo-600 border-indigo-600 text-white shadow-sm shadow-indigo-200',
                                                        str_contains($shortLabel, 'Read Only') => 'bg-sky-500 border-sky-500 text-white shadow-sm shadow-sky-200',
                                                        str_contains($shortLabel, 'Denied')    => 'bg-rose-500 border-rose-500 text-white shadow-sm shadow-rose-200',
                                                        default                                => 'bg-violet-600 border-violet-600 text-white shadow-sm shadow-violet-200',
                                                    };
                                                    $uncheckedCls = match(true) {
                                                        str_contains($shortLabel, 'Editable')  => 'bg-white border-slate-200 text-slate-500 hover:border-indigo-300 hover:text-indigo-600 hover:bg-indigo-50',
                                                        str_contains($shortLabel, 'Read Only') => 'bg-white border-slate-200 text-slate-500 hover:border-sky-300 hover:text-sky-600 hover:bg-sky-50',
                                                        str_contains($shortLabel, 'Denied')    => 'bg-white border-slate-200 text-slate-500 hover:border-rose-300 hover:text-rose-600 hover:bg-rose-50',
                                                        default                                => 'bg-white border-slate-200 text-slate-500 hover:border-violet-300 hover:text-violet-600 hover:bg-violet-50',
                                                    };
                                                @endphp
                                                <label class="radio-pill w-24 justify-center inline-flex items-center gap-1 px-2 py-1.5 rounded-lg cursor-pointer border transition-all text-xs font-semibold {{ $isChecked ? $checkedCls : $uncheckedCls }}"
                                                       data-checked="{{ $checkedCls }}"
                                                       data-unchecked="{{ $uncheckedCls }}">
                                                    <input type="radio"
                                                           name="exclusive_group[{{ $group ?: '_default' }}]"
                                                           value="{{ $perm->key }}"
                                                           class="sr-only"
                                                           {{ $isChecked ? 'checked' : '' }}>
                                                    {{ $shortLabel }}
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- ── Non-exclusive (checkbox) groups ── --}}
                    @if ($checkboxGroups->isNotEmpty())
                        <div class="px-5 pt-4 pb-5">
                            @foreach ($checkboxGroups as $group => $perms)
                                @if ($group)
                                    <div class="flex items-center gap-2 mb-3 mt-5 first:mt-0">
                                        <div class="h-px flex-1 bg-slate-100"></div>
                                        <span class="text-xs font-bold text-slate-400 uppercase tracking-widest px-1">{{ $group }}</span>
                                        <div class="h-px flex-1 bg-slate-100"></div>
                                    </div>
                                @endif
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-1.5 md:grid-cols-3 lg:grid-cols-4">
                                    @foreach ($perms as $perm)
                                        <label class="flex items-center gap-2.5 p-3 rounded-xl cursor-pointer border transition-all group
                                                      {{ in_array($perm->key, $userPerms) ? 'bg-indigo-50 border-indigo-200' : 'bg-white border-slate-100 hover:border-indigo-100 hover:bg-indigo-50/40' }}">
                                            <input type="checkbox"
                                                   name="permissions[]"
                                                   value="{{ $perm->key }}"
                                                   class="w-4 h-4 text-indigo-600 rounded border-slate-300 focus:ring-indigo-500"
                                                   {{ in_array($perm->key, $userPerms) ? 'checked' : '' }}>
                                            <span class="text-sm font-medium transition-colors
                                                         {{ in_array($perm->key, $userPerms) ? 'text-indigo-700' : 'text-slate-600 group-hover:text-indigo-700' }}">
                                                {{ $perm->label }}
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                    @endif

                    {{-- Footer: save --}}
                    <div class="px-5 py-3.5 bg-slate-50/80 border-t border-slate-100 flex items-center justify-between">
                        <span class="text-xs text-slate-400 font-medium hidden sm:block">
                            เลือกสิทธิ์แล้ว <span id="count-selected-{{ $system->id }}" class="font-bold text-slate-600">{{ $selectedCount }}</span> จาก {{ $totalCount }} รายการ
                        </span>
                        <button type="submit"
                                class="ml-auto px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl shadow-sm shadow-indigo-200 transition-all duration-150 hover:-translate-y-0.5 hover:shadow-md hover:shadow-indigo-200 inline-flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            บันทึกสิทธิ์ระบบนี้
                        </button>
                    </div>
                </form>
                @else
                {{-- ── Read-only permission view (L0 only) ── --}}
                <div class="px-5 py-5">
                    @if ($selectedCount === 0)
                        <p class="text-sm text-slate-400 font-medium">ไม่มีสิทธิ์ที่ได้รับมอบหมาย</p>
                    @else
                        @if ($exclusiveGroups->isNotEmpty())
                            <div class="rounded-xl border border-slate-100 overflow-hidden divide-y divide-slate-50 mb-4">
                                @php
                                    $firstGroup = $exclusiveGroups->first();
                                    $optionHeaders = $firstGroup->map(function($p) {
                                        $parts = explode('—', $p->label);
                                        return trim(end($parts));
                                    });
                                @endphp
                                <div class="flex items-center gap-3 px-4 py-2 bg-slate-50/80">
                                    <div class="flex-1 text-xs font-bold text-slate-400 uppercase tracking-wider">กลุ่มสิทธิ์</div>
                                    <div class="flex items-center gap-1.5 flex-shrink-0">
                                        @foreach ($optionHeaders as $header)
                                            @php
                                                $hdrColor = match(true) {
                                                    str_contains($header, 'Editable') => 'text-indigo-500',
                                                    str_contains($header, 'Read Only') => 'text-sky-500',
                                                    str_contains($header, 'Denied') => 'text-rose-400',
                                                    default => 'text-slate-500',
                                                };
                                            @endphp
                                            <span class="w-24 text-center text-xs font-bold {{ $hdrColor }} uppercase tracking-wide">{{ $header }}</span>
                                        @endforeach
                                    </div>
                                </div>
                                @foreach ($exclusiveGroups as $group => $perms)
                                    <div class="flex items-center gap-3 px-4 py-2.5 bg-white">
                                        <div class="flex-1 min-w-0">
                                            <span class="text-sm font-medium text-slate-700">{{ $group ?: 'ทั่วไป' }}</span>
                                        </div>
                                        <div class="flex items-center gap-1.5 flex-shrink-0">
                                            @foreach ($perms as $perm)
                                                @php
                                                    $parts = explode('—', $perm->label);
                                                    $shortLabel = trim(end($parts));
                                                    $isChecked  = in_array($perm->key, $userPerms);
                                                    $activeCls = match(true) {
                                                        str_contains($shortLabel, 'Editable')  => 'bg-indigo-600 border-indigo-600 text-white',
                                                        str_contains($shortLabel, 'Read Only') => 'bg-sky-500 border-sky-500 text-white',
                                                        str_contains($shortLabel, 'Denied')    => 'bg-rose-500 border-rose-500 text-white',
                                                        default                                => 'bg-violet-600 border-violet-600 text-white',
                                                    };
                                                @endphp
                                                <span class="w-24 text-center inline-flex justify-center items-center px-2 py-1.5 rounded-lg border text-xs font-semibold
                                                             {{ $isChecked ? $activeCls : 'bg-white border-slate-100 text-slate-300' }}">
                                                    {{ $shortLabel }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        @if ($checkboxGroups->isNotEmpty())
                            @foreach ($checkboxGroups as $group => $perms)
                                @if ($group)
                                    <div class="flex items-center gap-2 mb-3 mt-5 first:mt-0">
                                        <div class="h-px flex-1 bg-slate-100"></div>
                                        <span class="text-xs font-bold text-slate-400 uppercase tracking-widest px-1">{{ $group }}</span>
                                        <div class="h-px flex-1 bg-slate-100"></div>
                                    </div>
                                @endif
                                <div class="flex flex-wrap gap-2">
                                    @foreach ($perms as $perm)
                                        @if (in_array($perm->key, $userPerms))
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-50 text-indigo-700 text-xs font-semibold rounded-xl ring-1 ring-indigo-200/60">
                                                <svg class="w-3 h-3 text-indigo-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                                {{ $perm->label }}
                                            </span>
                                        @endif
                                    @endforeach
                                </div>
                            @endforeach
                        @endif
                    @endif
                </div>
                @endif {{-- isAdmin --}}
            @endif
        </div>
    @empty
        <div class="bg-white rounded-2xl shadow-sm ring-1 ring-slate-100 p-16 text-center">
            <div class="w-14 h-14 bg-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                <svg class="w-7 h-7 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2"/>
                </svg>
            </div>
            <p class="text-sm font-semibold text-slate-400 mb-1">ยังไม่มีระบบที่เชื่อมต่อ</p>
            @if (auth()->user()->isSuperAdmin())
            <a href="{{ route('systems.create') }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-800 hover:underline">เพิ่มระบบ →</a>
            @endif
        </div>
    @endforelse
</div>

@push('scripts')
<script>
    // Employee number inline edit
    function empEdit() {
        document.getElementById('emp-view').style.display = 'none';
        document.getElementById('emp-edit-btn').style.display = 'none';
        document.getElementById('emp-form').style.display = 'flex';
    }
    function empCancel() {
        document.getElementById('emp-view').style.display = '';
        document.getElementById('emp-edit-btn').style.display = '';
        document.getElementById('emp-form').style.display = 'none';
    }

    // Radio pill: update visual state on change
    document.querySelectorAll('.radio-pill input[type="radio"]').forEach(function(radio) {
        radio.addEventListener('change', function() {
            var myName = this.name;
            document.querySelectorAll('.radio-pill input[type="radio"]').forEach(function(r) {
                if (r.name !== myName) return;
                var label = r.closest('.radio-pill');
                if (!label) return;
                var checkedCls   = (label.dataset.checked   || '').split(' ').filter(Boolean);
                var uncheckedCls = (label.dataset.unchecked || '').split(' ').filter(Boolean);
                checkedCls.forEach(function(c)   { label.classList.remove(c); });
                uncheckedCls.forEach(function(c) { label.classList.remove(c); });
                var apply = r.checked ? checkedCls : uncheckedCls;
                apply.forEach(function(c) { label.classList.add(c); });
            });
        });
    });

    // Auto-reload if sync is pending
    if (document.querySelector('[data-sync-pending]')) {
        setTimeout(function() { window.location.reload(); }, 3000);
    }
</script>
@endpush
@endsection
