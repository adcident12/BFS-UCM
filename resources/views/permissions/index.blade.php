@extends('layouts.app')

@section('title', 'จัดการ Permissions')
@section('header', 'จัดการ Permissions')

@section('breadcrumbs')
<a href="{{ route('dashboard') }}" class="text-slate-400 hover:text-slate-600 font-medium transition-colors whitespace-nowrap">Dashboard</a>
<svg class="w-3.5 h-3.5 text-slate-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
</svg>
<span class="font-semibold text-slate-800 truncate">จัดการ Permissions</span>
@if ($selectedSystem)
<svg class="w-3.5 h-3.5 text-slate-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
</svg>
<span class="font-semibold text-slate-800 truncate">{{ $selectedSystem->name }}</span>
@endif
@endsection

@section('content')

@php
    $canEdit   = auth()->user()->canAccess('permission_update');
    $canManage = auth()->user()->canAccess('system_create_edit');
@endphp

<div class="flex gap-5 items-start">

    {{-- ── Left: System selector ─────────────────────────────────────── --}}
    <div class="w-64 flex-shrink-0 space-y-2 sticky top-4">

        {{-- Search --}}
        <div class="relative">
            <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input id="sys-search" type="text" placeholder="ค้นหาระบบ..." oninput="filterSystems(this.value)"
                   class="w-full pl-9 pr-3 py-2 text-sm bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 transition-all">
        </div>

        {{-- System list --}}
        <div id="sys-list" class="space-y-1">
            @forelse ($systems as $sys)
            @php $isActive = $selectedSystem?->id === $sys->id; @endphp
            <a href="{{ route('permissions.index', ['system' => $sys->id]) }}"
               data-sys-name="{{ strtolower($sys->name) }}"
               class="sys-card flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all
                      {{ $isActive ? 'bg-indigo-600 text-white shadow-sm shadow-indigo-200' : 'bg-white hover:bg-slate-50 text-slate-700 ring-1 ring-slate-100' }}">
                <div class="w-7 h-7 rounded-lg flex-shrink-0 flex items-center justify-center"
                     style="background-color: {{ $sys->color ?? '#6366f1' }}{{ $isActive ? '40' : '20' }}">
                    <div class="w-2.5 h-2.5 rounded-sm" style="background-color: {{ $sys->color ?? '#6366f1' }}"></div>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold truncate leading-tight">{{ $sys->name }}</p>
                    <p class="text-xs {{ $isActive ? 'text-indigo-200' : 'text-slate-400' }} leading-tight mt-0.5">
                        {{ $sys->permissions_count }} permissions · {{ $sys->user_permissions_count }} users
                    </p>
                </div>
                <div class="flex flex-col items-end gap-0.5 flex-shrink-0">
                    @if ($sys->two_way_permissions)
                    <span class="text-[10px] font-bold px-1.5 py-0.5 rounded-md {{ $isActive ? 'bg-amber-400/30 text-amber-100' : 'bg-amber-50 text-amber-600' }}">2-way</span>
                    @endif
                    @if (! $sys->is_active)
                    <span class="text-[10px] font-semibold px-1.5 py-0.5 rounded-md {{ $isActive ? 'bg-indigo-500 text-indigo-100' : 'bg-slate-100 text-slate-400' }}">ปิด</span>
                    @endif
                </div>
            </a>
            @empty
            <div class="bg-white ring-1 ring-slate-100 rounded-2xl px-4 py-6 text-center">
                <p class="text-sm text-slate-400">ยังไม่มีระบบที่เชื่อมต่อ</p>
            </div>
            @endforelse
        </div>

        <p id="sys-empty" class="hidden text-xs text-slate-400 text-center pt-2">ไม่พบระบบที่ค้นหา</p>
    </div>

    {{-- ── Right: Permission detail ──────────────────────────────────── --}}
    <div class="flex-1 min-w-0">

        @if ($selectedSystem)

        {{-- System header --}}
        <div class="bg-white rounded-2xl shadow-sm ring-1 ring-slate-100 overflow-hidden mb-4">
            <div class="h-1 w-full" style="background-color: {{ $selectedSystem->color ?? '#6366f1' }}"></div>
            <div class="flex flex-wrap items-center gap-3 px-5 py-4">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 shadow-sm"
                     style="background-color: {{ $selectedSystem->color ?? '#6366f1' }}20">
                    <div class="w-4 h-4 rounded-md" style="background-color: {{ $selectedSystem->color ?? '#6366f1' }}"></div>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                        <h2 class="text-base font-bold text-slate-800 leading-tight">{{ $selectedSystem->name }}</h2>
                        @if ($selectedSystem->two_way_permissions)
                        <span class="inline-flex items-center gap-1 text-[11px] font-bold px-2 py-0.5 rounded-lg bg-amber-50 text-amber-600 border border-amber-200">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                            </svg>
                            2-way sync ON
                        </span>
                        @else
                        <span class="inline-flex items-center gap-1 text-[11px] font-semibold px-2 py-0.5 rounded-lg bg-slate-100 text-slate-400 border border-slate-200">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                            </svg>
                            2-way sync OFF
                        </span>
                        @endif
                    </div>
                    <p class="text-xs text-slate-400 mt-0.5">
                        {{ $selectedSystem->permissions_count ?? $selectedSystem->permissions->count() }} permissions
                        · {{ $userAssignments->count() }} users ที่มีสิทธิ์
                    </p>
                </div>
                <a href="{{ route('systems.show', $selectedSystem) }}"
                   class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-slate-700 border border-slate-200 hover:border-slate-300 hover:bg-slate-50 rounded-xl px-3 py-2 transition-all">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    ตั้งค่าระบบ
                </a>
            </div>

            {{-- Tabs --}}
            <div class="px-5 pb-0 border-t border-slate-100 flex gap-1">
                <button type="button" id="tab-perms-btn" onclick="switchTab('perms')"
                        class="tab-btn px-4 py-2.5 text-sm font-semibold border-b-2 transition-colors -mb-px">
                    รายการ Permissions
                </button>
                <button type="button" id="tab-users-btn" onclick="switchTab('users')"
                        class="tab-btn px-4 py-2.5 text-sm font-semibold border-b-2 transition-colors -mb-px">
                    ผู้ใช้ที่มีสิทธิ์
                    @if ($userAssignments->count() > 0)
                    <span class="ml-1.5 text-xs font-semibold bg-indigo-100 text-indigo-700 px-1.5 py-0.5 rounded-full">{{ $userAssignments->count() }}</span>
                    @endif
                </button>
            </div>
        </div>

        {{-- ── Tab: Permissions ─────────────────────────────────────── --}}
        <div id="tab-perms">

            {{-- Flash messages --}}
            @if (session('success'))
            <div class="mb-4 flex items-center gap-2.5 bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm font-medium px-4 py-3 rounded-xl">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                {{ session('success') }}
            </div>
            @endif
            @if ($errors->any())
            <div class="mb-4 bg-rose-50 border border-rose-200 text-rose-700 text-sm px-4 py-3 rounded-xl">
                @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
                @endforeach
            </div>
            @endif

            @if ($permissionsByGroup->isEmpty())
            <div class="bg-white rounded-2xl shadow-sm ring-1 ring-slate-100 px-6 py-10 text-center">
                <div class="w-12 h-12 bg-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <p class="text-sm font-semibold text-slate-600">ยังไม่มี Permission</p>
                <p class="text-xs text-slate-400 mt-1">เพิ่ม Permission ด้านล่างหรือใช้ Discover จากระบบ</p>
            </div>
            @else

            <div class="space-y-4">
                @foreach ($permissionsByGroup as $group => $perms)
                <div class="bg-white rounded-2xl shadow-sm ring-1 ring-slate-100 overflow-hidden">
                    {{-- Group header --}}
                    <div class="flex items-center gap-2 px-5 py-3 bg-slate-50/60 border-b border-slate-100">
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                        </svg>
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">
                            {{ $group === '__none__' ? 'ไม่มีกลุ่ม' : $group }}
                        </span>
                        <span class="ml-auto text-xs text-slate-400 font-medium">{{ $perms->count() }} รายการ</span>
                    </div>

                    {{-- Permission rows --}}
                    <div class="divide-y divide-slate-50">
                        @foreach ($perms as $perm)
                        <div class="px-5 py-3" id="perm-row-{{ $perm->id }}">
                            {{-- Display view --}}
                            <div id="perm-view-{{ $perm->id }}" class="flex flex-wrap items-start gap-3">
                                <div class="flex-1 min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <code class="text-xs font-mono font-semibold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded-lg">{{ $perm->key }}</code>
                                        <span class="text-sm font-semibold text-slate-700">{{ $perm->label }}</span>
                                        @if ($perm->is_exclusive)
                                        <span class="inline-flex items-center gap-1 text-[10px] font-semibold text-amber-600 bg-amber-50 border border-amber-200 px-1.5 py-0.5 rounded-md">
                                            <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 018 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/>
                                            </svg>
                                            Exclusive
                                        </span>
                                        @endif
                                        @if ($perm->remote_value)
                                        <span class="text-[10px] font-mono text-slate-400 bg-slate-50 border border-slate-200 px-1.5 py-0.5 rounded-md">remote: {{ $perm->remote_value }}</span>
                                        @endif
                                    </div>
                                    @if ($perm->description)
                                    <p class="text-xs text-slate-400 mt-1">{{ $perm->description }}</p>
                                    @endif
                                </div>
                                <div class="flex items-center gap-1.5 flex-shrink-0">
                                    {{-- User count badge --}}
                                    @php
                                    $permUserCount = $userAssignments->filter(fn($a) => in_array($perm->key, $a['keys']))->count();
                                    @endphp
                                    @if ($permUserCount > 0)
                                    <span class="text-xs font-semibold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-lg"
                                          title="{{ $permUserCount }} ผู้ใช้ที่มีสิทธิ์นี้">
                                        {{ $permUserCount }} users
                                    </span>
                                    @endif

                                    @if ($canEdit)
                                    <button type="button" onclick="toggleEdit({{ $perm->id }})"
                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-semibold text-slate-600 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        แก้ไข
                                    </button>
                                    @endif

                                    @if ($canManage)
                                    <form id="del-perm-{{ $perm->id }}" method="POST" action="{{ route('systems.permissions.destroy', [$selectedSystem, $perm]) }}">
                                        @csrf @method('DELETE')
                                        <button type="button"
                                                onclick="askConfirm('del-perm-{{ $perm->id }}', 'ลบ Permission &quot;{{ addslashes($perm->key) }}&quot;', 'ผู้ใช้ทั้งหมดที่มีสิทธิ์นี้จะถูกถอนสิทธิ์ด้วย')"
                                                class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-semibold text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                            ลบ
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </div>

                            {{-- Inline edit form --}}
                            @if ($canEdit)
                            <div id="perm-edit-{{ $perm->id }}" class="hidden mt-3 pt-3 border-t border-slate-100">
                                <form method="POST" action="{{ route('systems.permissions.update', [$selectedSystem, $perm]) }}">
                                    @csrf @method('PUT')
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">
                                        <div>
                                            <label class="block text-xs font-semibold text-slate-500 mb-1">Label <span class="text-rose-400">*</span></label>
                                            <input type="text" name="label" value="{{ old('label', $perm->label) }}" required
                                                   class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 transition-all">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-slate-500 mb-1">Remote Value</label>
                                            <input type="text" name="remote_value" value="{{ old('remote_value', $perm->remote_value) }}"
                                                   class="w-full text-sm font-mono border border-slate-200 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 transition-all">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-slate-500 mb-1">Group</label>
                                            <input type="text" name="group" value="{{ old('group', $group === '__none__' ? '' : $group) }}"
                                                   class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 transition-all">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-slate-500 mb-1">Sort Order</label>
                                            <input type="number" name="sort_order" value="{{ old('sort_order', $perm->sort_order) }}" min="0"
                                                   class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 transition-all">
                                        </div>
                                        <div class="sm:col-span-2">
                                            <label class="block text-xs font-semibold text-slate-500 mb-1">Description</label>
                                            <input type="text" name="description" value="{{ old('description', $perm->description) }}"
                                                   class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 transition-all">
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="hidden" name="is_exclusive" value="0">
                                            <input type="checkbox" name="is_exclusive" value="1" {{ $perm->is_exclusive ? 'checked' : '' }}
                                                   class="w-4 h-4 text-amber-500 rounded border-slate-300 focus:ring-amber-400">
                                            <span class="text-xs font-semibold text-slate-600">Exclusive (เลือกได้เพียงรายการเดียวในกลุ่ม)</span>
                                        </label>
                                        <div class="flex items-center gap-2">
                                            <button type="button" onclick="toggleEdit({{ $perm->id }})"
                                                    class="px-3 py-1.5 text-xs font-semibold text-slate-500 hover:text-slate-700 hover:bg-slate-100 rounded-xl transition-colors">
                                                ยกเลิก
                                            </button>
                                            <button type="submit"
                                                    class="px-3 py-1.5 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl transition-colors shadow-sm">
                                                บันทึก
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            {{-- Add permission form (L2 only) --}}
            @if ($canManage)
            <div class="bg-white rounded-2xl shadow-sm ring-1 ring-slate-100 overflow-hidden mt-4">
                <button type="button" id="add-perm-toggle" onclick="toggleAddForm()"
                        class="w-full flex items-center justify-between px-5 py-3.5 text-left hover:bg-slate-50/60 transition-colors">
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 bg-indigo-100 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                        </div>
                        <span class="text-sm font-semibold text-slate-700">เพิ่ม Permission ใหม่</span>
                    </div>
                    <svg id="add-perm-arrow" class="w-4 h-4 text-slate-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div id="add-perm-form" class="hidden px-5 pb-5 pt-1 border-t border-slate-100">
                    <form method="POST" action="{{ route('systems.permissions.store', $selectedSystem) }}">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 mb-1">Key <span class="text-rose-400">*</span></label>
                                <input type="text" name="key" value="{{ old('key') }}" required placeholder="เช่น view_report"
                                       class="w-full text-sm font-mono border border-slate-200 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 transition-all">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 mb-1">Label <span class="text-rose-400">*</span></label>
                                <input type="text" name="label" value="{{ old('label') }}" required placeholder="ชื่อที่แสดง"
                                       class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 transition-all">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 mb-1">Remote Value</label>
                                <input type="text" name="remote_value" value="{{ old('remote_value') }}" placeholder="ค่าในระบบปลายทาง"
                                       class="w-full text-sm font-mono border border-slate-200 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 transition-all">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 mb-1">Group</label>
                                <input type="text" name="group" value="{{ old('group') }}" placeholder="เช่น รายงาน"
                                       class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 transition-all">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 mb-1">Sort Order</label>
                                <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" min="0"
                                       class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 transition-all">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 mb-1">Description</label>
                                <input type="text" name="description" value="{{ old('description') }}" placeholder="คำอธิบาย"
                                       class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 transition-all">
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="hidden" name="is_exclusive" value="0">
                                <input type="checkbox" name="is_exclusive" value="1" {{ old('is_exclusive') ? 'checked' : '' }}
                                       class="w-4 h-4 text-amber-500 rounded border-slate-300 focus:ring-amber-400">
                                <span class="text-xs font-semibold text-slate-600">Exclusive</span>
                            </label>
                            <button type="submit"
                                    class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-sm transition-all hover:-translate-y-0.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                เพิ่ม Permission
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            @endif

        </div>{{-- end tab-perms --}}

        {{-- ── Tab: Users ───────────────────────────────────────────── --}}
        <div id="tab-users" class="hidden">

            @if ($userAssignments->isEmpty())
            <div class="bg-white rounded-2xl shadow-sm ring-1 ring-slate-100 px-6 py-10 text-center">
                <div class="w-12 h-12 bg-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <p class="text-sm font-semibold text-slate-600">ยังไม่มีผู้ใช้ที่ได้รับสิทธิ์</p>
                <p class="text-xs text-slate-400 mt-1">ไปที่หน้าผู้ใช้เพื่อมอบสิทธิ์ให้กับระบบนี้</p>
            </div>
            @else

            {{-- Search --}}
            <div class="mb-3 relative">
                <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" id="user-search" placeholder="ค้นหาผู้ใช้..." oninput="filterUsers(this.value)"
                       class="w-full pl-9 pr-3 py-2 text-sm bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 transition-all">
            </div>

            <div class="bg-white rounded-2xl shadow-sm ring-1 ring-slate-100 overflow-hidden">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-100">
                            <th class="text-left text-xs font-semibold text-slate-500 px-5 py-3">ผู้ใช้</th>
                            <th class="text-left text-xs font-semibold text-slate-500 px-4 py-3">แผนก</th>
                            <th class="text-left text-xs font-semibold text-slate-500 px-4 py-3">Permissions</th>
                            <th class="text-xs font-semibold text-slate-500 px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50" id="user-table-body">
                        @foreach ($userAssignments as $assignment)
                        @php $user = $assignment['user']; $keys = $assignment['keys']; @endphp
                        <tr class="user-row hover:bg-slate-50/50 transition-colors"
                            data-user="{{ strtolower(($user?->name ?? '') . ' ' . ($user?->username ?? '') . ' ' . ($user?->department ?? '')) }}">
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-xl flex items-center justify-center text-xs font-bold text-white flex-shrink-0"
                                         style="background: linear-gradient(135deg, #6366f1, #8b5cf6)">
                                        {{ strtoupper(substr($user?->name ?? '?', 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold text-slate-700 truncate">{{ $user?->name ?? 'Unknown' }}</p>
                                        <p class="text-xs text-slate-400 font-mono">{{ $user?->username ?? '—' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-xs text-slate-500">{{ $user?->department ?: '—' }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-1">
                                    @foreach ($keys as $key)
                                    @php $permDef = $selectedSystem->permissions->firstWhere('key', $key); @endphp
                                    <span class="inline-block text-xs font-medium bg-indigo-50 text-indigo-700 border border-indigo-100 px-2 py-0.5 rounded-lg"
                                          title="{{ $permDef?->label ?? $key }}">
                                        {{ $permDef?->label ?? $key }}
                                    </span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-3 py-3 text-right whitespace-nowrap">
                                @if ($user)
                                <a href="{{ route('users.show', $user) }}"
                                   title="แก้ไขสิทธิ์ของ {{ $user->name }}"
                                   class="inline-flex items-center justify-center w-8 h-8 text-indigo-500 hover:text-indigo-700 hover:bg-indigo-50 rounded-lg transition-colors border border-transparent hover:border-indigo-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div id="user-empty" class="hidden px-6 py-6 text-center text-sm text-slate-400">ไม่พบผู้ใช้ที่ค้นหา</div>
                <div class="px-5 py-3 bg-slate-50/50 border-t border-slate-100 flex items-center justify-between">
                    <p class="text-xs text-slate-400">แสดง {{ $userAssignments->count() }} ผู้ใช้ที่มีสิทธิ์ในระบบนี้</p>
                    <a href="{{ route('reports.permission-matrix') }}?system_id={{ $selectedSystem->id }}"
                       class="inline-flex items-center gap-1.5 text-xs font-semibold text-indigo-600 hover:text-indigo-800 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18M10 3v18M14 3v18M3 6a3 3 0 013-3h12a3 3 0 013 3v12a3 3 0 01-3 3H6a3 3 0 01-3-3V6z"/>
                        </svg>
                        ดู Permission Matrix
                    </a>
                </div>
            </div>

            @endif
        </div>{{-- end tab-users --}}

        @else

        {{-- No system selected placeholder --}}
        <div class="bg-white rounded-2xl shadow-sm ring-1 ring-slate-100 px-8 py-16 text-center">
            <div class="w-16 h-16 bg-indigo-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
            <h3 class="text-base font-bold text-slate-700">เลือกระบบจากแถบซ้าย</h3>
            <p class="text-sm text-slate-400 mt-1">เลือกระบบที่ต้องการจัดการ Permissions</p>
        </div>

        @endif
    </div>{{-- end right --}}
</div>

@push('styles')
<style>
.tab-btn { color: #94a3b8; border-color: transparent; }
.tab-btn.active { color: #4f46e5; border-color: #4f46e5; }
</style>
@endpush

@push('scripts')
<script>
/* ── Tab switching ─────────────────────────────────────────── */
function switchTab(name) {
    ['perms', 'users'].forEach(function(t) {
        var panel = document.getElementById('tab-' + t);
        var btn   = document.getElementById('tab-' + t + '-btn');
        if (panel) { panel.classList.toggle('hidden', t !== name); }
        if (btn)   { btn.classList.toggle('active', t === name); }
    });
}

/* ── Inline edit toggle ────────────────────────────────────── */
function toggleEdit(id) {
    var view = document.getElementById('perm-view-' + id);
    var form = document.getElementById('perm-edit-' + id);
    if (view && form) {
        var isHidden = form.classList.contains('hidden');
        form.classList.toggle('hidden', !isHidden);
    }
}

/* ── Add permission form toggle ────────────────────────────── */
function toggleAddForm() {
    var form  = document.getElementById('add-perm-form');
    var arrow = document.getElementById('add-perm-arrow');
    if (form) {
        var hidden = form.classList.contains('hidden');
        form.classList.toggle('hidden', !hidden);
        if (arrow) { arrow.style.transform = hidden ? 'rotate(180deg)' : ''; }
    }
}

/* ── System search filter ──────────────────────────────────── */
function filterSystems(q) {
    var cards   = document.querySelectorAll('.sys-card');
    var empty   = document.getElementById('sys-empty');
    var lq      = q.toLowerCase().trim();
    var visible = 0;
    cards.forEach(function(c) {
        var match = !lq || (c.dataset.sysName || '').includes(lq);
        c.style.display = match ? '' : 'none';
        if (match) { visible++; }
    });
    if (empty) { empty.classList.toggle('hidden', visible > 0); }
}

/* ── User search filter ────────────────────────────────────── */
function filterUsers(q) {
    var rows  = document.querySelectorAll('.user-row');
    var empty = document.getElementById('user-empty');
    var lq    = q.toLowerCase().trim();
    var shown = 0;
    rows.forEach(function(r) {
        var match = !lq || (r.dataset.user || '').includes(lq);
        r.style.display = match ? '' : 'none';
        if (match) { shown++; }
    });
    if (empty) { empty.classList.toggle('hidden', shown > 0); }
}

/* ── Auto-open add form on validation error ────────────────── */
@if ($errors->has('key') || $errors->has('label'))
document.addEventListener('DOMContentLoaded', function() {
    var form  = document.getElementById('add-perm-form');
    var arrow = document.getElementById('add-perm-arrow');
    if (form)  { form.classList.remove('hidden'); }
    if (arrow) { arrow.style.transform = 'rotate(180deg)'; }
});
@endif

/* ── Init active tab on load ───────────────────────────────── */
document.addEventListener('DOMContentLoaded', function() { switchTab('perms'); });
</script>
@endpush

@endsection
