@extends('layouts.app')
@section('title', $system->name)
@section('header', $system->name)

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}" class="text-slate-400 hover:text-slate-600 font-medium transition-colors whitespace-nowrap">Dashboard</a>
    <svg class="w-3.5 h-3.5 text-slate-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    <a href="{{ route('systems.index') }}" class="text-slate-400 hover:text-slate-600 font-medium transition-colors whitespace-nowrap">ระบบที่เชื่อมต่อ</a>
    <svg class="w-3.5 h-3.5 text-slate-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    <span class="font-semibold text-slate-800 truncate">{{ $system->name }}</span>
@endsection

@section('content')

{{-- Action bar --}}
<div class="flex flex-wrap items-center gap-2 justify-between mb-6">
    <div class="flex items-center gap-2.5">
        <div class="w-9 h-9 rounded-xl flex items-center justify-center shadow-sm"
             style="background-color: {{ $system->color }}20">
            <div class="w-3.5 h-3.5 rounded-md" style="background-color: {{ $system->color }}"></div>
        </div>
        <div>
            <p class="text-xs text-slate-400 font-medium">จัดการ Permissions</p>
            <p class="text-sm font-bold text-slate-700">{{ $system->name }}</p>
        </div>
    </div>
    <div class="flex items-center gap-2">
        @if (\App\Adapters\AdapterFactory::hasAdapter($system))
            <form method="POST" action="{{ route('systems.discover', $system) }}" class="inline">
                @csrf
                <button type="submit"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-xl shadow-sm shadow-emerald-100 transition-all hover:-translate-y-0.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    Discover Permissions
                </button>
            </form>
        @endif
        <a href="{{ route('systems.edit', $system) }}"
           class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-slate-700 hover:bg-slate-800 text-white text-xs font-semibold rounded-xl shadow-sm transition-all hover:-translate-y-0.5">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            แก้ไขระบบ
        </a>
    </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

    {{-- ── Left: Permission list ── --}}
    <div class="xl:col-span-2 space-y-4">

        {{-- Summary bar --}}
        <div class="bg-white rounded-2xl shadow-sm ring-1 ring-slate-100 px-5 py-4 flex flex-wrap items-center gap-4">
            @php
                $total     = $system->permissions->count();
                $exclusive = $system->permissions->where('is_exclusive', true)->count();
                $multi     = $total - $exclusive;
                $groups    = $system->permissions->groupBy('group')->count();
            @endphp
            <div class="flex items-center gap-2.5 flex-1 min-w-0">
                <div class="text-2xl font-bold text-slate-800">{{ $total }}</div>
                <div class="text-xs text-slate-400 font-medium leading-tight">
                    permission<br>ทั้งหมด
                </div>
            </div>
            <div class="h-8 w-px bg-slate-100"></div>
            <div class="text-center">
                <div class="text-lg font-bold text-indigo-600">{{ $groups }}</div>
                <div class="text-xs text-slate-400 font-medium">กลุ่ม</div>
            </div>
            <div class="h-8 w-px bg-slate-100"></div>
            <div class="text-center">
                <div class="text-lg font-bold text-violet-600">{{ $exclusive }}</div>
                <div class="text-xs text-slate-400 font-medium">Exclusive</div>
            </div>
            <div class="h-8 w-px bg-slate-100"></div>
            <div class="text-center">
                <div class="text-lg font-bold text-teal-600">{{ $multi }}</div>
                <div class="text-xs text-slate-400 font-medium">Multi-select</div>
            </div>
        </div>

        {{-- Permission list --}}
        <div class="bg-white rounded-2xl shadow-sm ring-1 ring-slate-100 overflow-hidden">
            @if ($system->permissions->isEmpty())
                <div class="px-6 py-16 text-center">
                    <div class="w-14 h-14 bg-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-7 h-7 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <p class="text-sm font-semibold text-slate-400">ยังไม่มี permission</p>
                    <p class="text-xs text-slate-300 mt-1">เพิ่ม permission ใหม่จากฟอร์มด้านขวา</p>
                </div>
            @else
                @foreach ($system->permissions->groupBy('group') as $group => $perms)
                    {{-- Group header --}}
                    <div class="px-5 pt-4 pb-1 {{ !$loop->first ? 'border-t border-slate-100' : '' }}">
                        <div class="flex items-center gap-2 mb-3">
                            @if ($group)
                                <div class="w-1.5 h-4 rounded-full flex-shrink-0" style="background-color: {{ $system->color }}60"></div>
                                <span class="text-xs font-bold text-slate-600 uppercase tracking-widest">{{ $group }}</span>
                            @else
                                <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">ทั่วไป</span>
                            @endif
                            @if ($perms->first()->is_exclusive)
                                <span class="inline-flex items-center gap-1 text-xs font-semibold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-full ring-1 ring-indigo-200/60">
                                    <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm0-2a6 6 0 110-12 6 6 0 010 12zm0-9a1 1 0 011 1v3a1 1 0 11-2 0V8a1 1 0 011-1z"/></svg>
                                    เลือกได้ 1
                                </span>
                            @else
                                <span class="text-xs font-semibold text-teal-600 bg-teal-50 px-2 py-0.5 rounded-full ring-1 ring-teal-200/60">เลือกได้หลายอัน</span>
                            @endif
                            <div class="h-px flex-1 bg-slate-100 ml-1"></div>
                            <span class="text-xs text-slate-300 font-medium">{{ $perms->count() }} รายการ</span>
                        </div>

                        {{-- Permission items --}}
                        <div class="space-y-1 mb-3">
                            @foreach ($perms as $perm)
                                @php
                                    $parts = explode('—', $perm->label);
                                    $shortLabel = trim(end($parts));
                                    $dotColor = match(true) {
                                        str_contains($shortLabel, 'Editable')  => '#6366f1',
                                        str_contains($shortLabel, 'Read Only') => '#0ea5e9',
                                        str_contains($shortLabel, 'Denied')    => '#f43f5e',
                                        default => $system->color,
                                    };
                                @endphp

                                {{-- View mode --}}
                                <div id="perm-view-{{ $perm->id }}"
                                     class="group flex flex-wrap items-center justify-between gap-2 px-3.5 py-2.5 rounded-xl hover:bg-slate-50 transition-colors border border-transparent hover:border-slate-100">
                                    <div class="flex items-center gap-2.5 flex-wrap min-w-0">
                                        <div class="w-2 h-2 rounded-full flex-shrink-0" style="background-color: {{ $dotColor }}"></div>
                                        <span class="text-sm font-semibold text-slate-700">{{ $perm->label }}</span>
                                        <code class="text-xs text-slate-400 bg-slate-100 px-1.5 py-0.5 rounded-md font-mono">{{ $perm->key }}</code>
                                        @if ($perm->remote_value && $perm->remote_value !== $perm->label)
                                            <span class="text-xs text-teal-600 bg-teal-50 px-2 py-0.5 rounded-md font-mono ring-1 ring-teal-200/60">→ {{ $perm->remote_value }}</span>
                                        @endif
                                        @if ($perm->description)
                                            <span class="text-xs text-slate-400">— {{ $perm->description }}</span>
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-1.5 flex-shrink-0 opacity-0 group-hover:opacity-100 transition-opacity">
                                        @if ($perm->remote_value && \App\Adapters\AdapterFactory::hasAdapter($system))
                                            <span class="inline-flex items-center gap-1 text-xs font-semibold text-amber-600 bg-amber-50 px-2 py-0.5 rounded-full ring-1 ring-amber-200/70" title="ลบที่ UCM แล้วจะลบใน {{ $system->name }} ด้วย">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                                                2-way
                                            </span>
                                        @endif
                                        <button type="button" onclick="permEdit({{ $perm->id }})"
                                                class="text-xs font-semibold text-indigo-500 hover:text-indigo-700 bg-indigo-50 hover:bg-indigo-100 px-2.5 py-1 rounded-lg transition-colors">แก้ไข</button>
                                        <form id="del-perm-{{ $perm->id }}" method="POST"
                                              action="{{ route('systems.permissions.destroy', [$system, $perm]) }}" class="hidden">
                                            @csrf @method('DELETE')
                                        </form>
                                        @if ($perm->remote_value && \App\Adapters\AdapterFactory::hasAdapter($system))
                                            <button type="button"
                                                    onclick="askConfirm('del-perm-{{ $perm->id }}', 'ลบ permission \'{{ addslashes($perm->label) }}\' ?', 'การลบนี้จะลบ &quot;{{ addslashes($perm->remote_value) }}&quot; ออกจาก {{ $system->name }} ด้วยทันที (2-way sync)')"
                                                    class="text-xs font-semibold text-red-400 hover:text-red-600 hover:bg-red-50 px-2.5 py-1 rounded-lg transition-colors">ลบ</button>
                                        @else
                                            <button type="button"
                                                    onclick="askConfirm('del-perm-{{ $perm->id }}', 'ลบ permission \'{{ addslashes($perm->label) }}\' ?')"
                                                    class="text-xs font-semibold text-red-400 hover:text-red-600 hover:bg-red-50 px-2.5 py-1 rounded-lg transition-colors">ลบ</button>
                                        @endif
                                    </div>
                                </div>

                                {{-- Edit mode --}}
                                <div id="perm-edit-{{ $perm->id }}" style="display:none"
                                     class="my-1 p-4 bg-indigo-50/60 rounded-xl border border-indigo-100">
                                    <form method="POST" action="{{ route('systems.permissions.update', [$system, $perm]) }}">
                                        @csrf @method('PUT')
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">
                                            <div>
                                                <label class="block text-xs font-bold text-slate-500 mb-1.5 uppercase tracking-wide">ชื่อที่แสดง *</label>
                                                <input type="text" name="label" required value="{{ $perm->label }}"
                                                       class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm text-slate-800 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 transition-all">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-bold text-slate-500 mb-1.5 uppercase tracking-wide">Remote Value <span class="font-normal normal-case text-slate-400">(ค่าจริงใน {{ $system->name }})</span></label>
                                                <input type="text" name="remote_value" value="{{ $perm->remote_value }}"
                                                       class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm font-mono text-slate-800 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 transition-all"
                                                       placeholder="{{ $perm->label }}">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-bold text-slate-500 mb-1.5 uppercase tracking-wide">กลุ่ม</label>
                                                <input type="text" name="group" value="{{ $perm->group }}"
                                                       class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm text-slate-800 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 transition-all">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-bold text-slate-500 mb-1.5 uppercase tracking-wide">คำอธิบาย</label>
                                                <input type="text" name="description" value="{{ $perm->description }}"
                                                       class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm text-slate-800 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 transition-all">
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-4 mb-3">
                                            <label class="flex items-center gap-2 cursor-pointer">
                                                <input type="hidden" name="is_exclusive" value="0">
                                                <input type="checkbox" name="is_exclusive" value="1" class="w-4 h-4 text-indigo-600 rounded" {{ $perm->is_exclusive ? 'checked' : '' }}>
                                                <span class="text-xs font-medium text-slate-600">เลือกได้แค่ 1 ใน group (Radio)</span>
                                            </label>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <code class="text-xs text-slate-400 font-mono mr-auto bg-white px-2 py-1 rounded-lg">{{ $perm->key }}</code>
                                            <button type="submit" class="px-3.5 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl transition-colors">บันทึก</button>
                                            <button type="button" onclick="permCancel({{ $perm->id }})"
                                                    class="px-3.5 py-1.5 bg-white border border-slate-200 text-slate-600 text-xs font-semibold rounded-xl hover:bg-slate-50 transition-colors">ยกเลิก</button>
                                        </div>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>

    {{-- ── Right: Add permission form ── --}}
    <div>
        <div class="bg-white rounded-2xl shadow-sm ring-1 ring-slate-100 overflow-hidden sticky top-20">
            <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/50">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 bg-indigo-100 rounded-lg flex items-center justify-center">
                        <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                    </div>
                    <h3 class="font-bold text-slate-800 text-sm">เพิ่ม Permission</h3>
                </div>
            </div>
            <div class="p-5">
                <form method="POST" action="{{ route('systems.permissions.store', $system) }}">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 mb-1.5 uppercase tracking-wide">
                                Key * <span class="text-slate-400 font-normal normal-case">(ตัวพิมพ์เล็ก, _)</span>
                            </label>
                            <input type="text" name="key" required
                                   class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm font-mono text-slate-800 placeholder-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 transition-all"
                                   placeholder="daily_edit">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 mb-1.5 uppercase tracking-wide">ชื่อที่แสดง *</label>
                            <input type="text" name="label" required
                                   class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm text-slate-800 placeholder-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 transition-all"
                                   placeholder="Daily Flight — Editable">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 mb-1.5 uppercase tracking-wide">
                                Remote Value <span class="text-slate-400 font-normal normal-case">(ค่าจริงใน {{ $system->name }})</span>
                            </label>
                            <input type="text" name="remote_value"
                                   class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm font-mono text-slate-800 placeholder-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 transition-all"
                                   placeholder="Daily Flight:2">
                            <p class="text-xs text-slate-400 mt-1.5 font-medium">ถ้าไม่กรอกจะใช้ชื่อที่แสดงแทน</p>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 mb-1.5 uppercase tracking-wide">กลุ่ม</label>
                            <input type="text" name="group"
                                   class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm text-slate-800 placeholder-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 transition-all"
                                   placeholder="Daily Flight">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 mb-1.5 uppercase tracking-wide">คำอธิบาย</label>
                            <input type="text" name="description"
                                   class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm text-slate-800 placeholder-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 transition-all">
                        </div>
                        <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-100">
                            <label class="flex items-start gap-2.5 cursor-pointer">
                                <input type="hidden" name="is_exclusive" value="0">
                                <input type="checkbox" name="is_exclusive" value="1" class="w-4 h-4 text-indigo-600 rounded mt-0.5">
                                <div>
                                    <span class="text-sm font-semibold text-slate-700 block">เลือกได้แค่ 1 ใน group</span>
                                    <span class="text-xs text-slate-400 font-medium">เหมาะสำหรับ Editable / Read Only / Denied</span>
                                </div>
                            </label>
                        </div>
                    </div>
                    @if (\App\Adapters\AdapterFactory::hasAdapter($system))
                        <div class="flex items-start gap-2.5 p-3 bg-amber-50 rounded-xl border border-amber-100">
                            <svg class="w-3.5 h-3.5 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                            </svg>
                            <p class="text-xs text-amber-700 font-medium leading-relaxed">
                                ระบบนี้มี adapter — permission ที่เพิ่มจะถูก <strong>สร้างใน {{ $system->name }} ด้วยอัตโนมัติ</strong> และการลบจะส่งผลต่อระบบภายนอกด้วย
                            </p>
                        </div>
                    @endif
                    <button type="submit"
                            class="mt-5 w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl shadow-sm shadow-indigo-200 transition-all duration-150 hover:-translate-y-0.5 hover:shadow-md hover:shadow-indigo-200 flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        เพิ่ม Permission
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>

@if (!empty($managedGroups))
{{-- ── Managed Reference Data ── --}}
<div class="mt-8">
    <div class="flex items-center gap-3 mb-3">
        <div class="w-7 h-7 bg-amber-100 rounded-lg flex items-center justify-center flex-shrink-0">
            <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2 1 3 3 3h10c2 0 3-1 3-3V7M4 7c0-2 1-3 3-3h10c2 0 3 1 3 3M4 7h16"/>
            </svg>
        </div>
        <div>
            <h2 class="text-sm font-bold text-slate-800">ข้อมูล Reference ใน {{ $system->name }}</h2>
            <p class="text-xs text-slate-400 font-medium">จัดการตาราง reference โดยตรง — หลังเพิ่มแล้วกด Discover Permissions เพื่อซิงค์</p>
        </div>
    </div>

    {{-- 2-way sync warning banner --}}
    <div class="flex items-start gap-3 mb-4 px-4 py-3 bg-red-50 rounded-xl border border-red-100">
        <svg class="w-4 h-4 text-red-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
        <div class="text-xs text-red-700 leading-relaxed">
            <strong class="font-bold">ข้อมูลในส่วนนี้เขียนตรงไปยังฐานข้อมูลของ {{ $system->name }} ทันที</strong><br>
            การ<strong>เพิ่ม</strong>หรือ<strong>แก้ไข</strong>จะมีผลกับระบบภายนอกโดยตรง — การ<strong>ลบ</strong>จะลบข้อมูลออกจาก {{ $system->name }} อย่างถาวร ไม่สามารถกู้คืนได้
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        @foreach ($managedGroups as $group)
            @php
                $groupSlug = 'grp-' . preg_replace('/[^a-z0-9]/', '-', strtolower($group));
            @endphp
            <div class="bg-white rounded-2xl shadow-sm ring-1 ring-slate-100 overflow-hidden flex flex-col">
                {{-- Card header --}}
                <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between gap-2">
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full flex-shrink-0" style="background-color: {{ $system->color }}80"></div>
                        <span class="text-xs font-bold text-slate-700 uppercase tracking-widest">{{ $group }}</span>
                        <span id="{{ $groupSlug }}-count" class="text-xs text-slate-400 font-medium"></span>
                    </div>
                    <button type="button" onclick="groupReload('{{ $group }}', '{{ $groupSlug }}')"
                            class="text-slate-300 hover:text-slate-500 transition-colors" title="รีโหลด">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                    </button>
                </div>

                {{-- Records list --}}
                <div id="{{ $groupSlug }}-list" class="flex-1 divide-y divide-slate-50 min-h-[80px] relative">
                    <div class="flex items-center justify-center py-8 text-slate-300">
                        <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                        </svg>
                    </div>
                </div>

                {{-- Add form --}}
                <div class="px-4 py-3.5 border-t border-slate-100 bg-slate-50/30">
                    <form method="POST" action="{{ route('systems.group-records.store', $system) }}"
                          class="flex items-center gap-2" onsubmit="return groupAddSubmit(this)">
                        @csrf
                        <input type="hidden" name="group" value="{{ $group }}">
                        <input type="text" name="name" required placeholder="ชื่อ {{ $group }} ใหม่"
                               class="flex-1 min-w-0 px-3 py-2 border border-slate-200 rounded-xl text-sm text-slate-800 placeholder-slate-300 focus:outline-none focus:ring-2 focus:ring-amber-400/30 focus:border-amber-400 transition-all">
                        <button type="submit"
                                class="flex-shrink-0 inline-flex items-center gap-1 px-3 py-2 bg-amber-500 hover:bg-amber-600 text-white text-xs font-semibold rounded-xl transition-all hover:-translate-y-0.5 shadow-sm shadow-amber-100">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            เพิ่ม
                        </button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
    function permEdit(id) {
        document.getElementById('perm-view-' + id).style.display = 'none';
        document.getElementById('perm-edit-' + id).style.display = '';
    }
    function permCancel(id) {
        document.getElementById('perm-view-' + id).style.display = '';
        document.getElementById('perm-edit-' + id).style.display = 'none';
    }

    // ── Managed Group CRUD ────────────────────────────────────────────────
    var _groupData = {};

    function groupSlugOf(group) {
        return 'grp-' + group.toLowerCase().replace(/[^a-z0-9]/g, '-');
    }

    function groupReload(group, slug) {
        var list = document.getElementById(slug + '-list');
        list.innerHTML = '<div class="flex items-center justify-center py-8 text-slate-300"><svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg></div>';

        fetch('{{ route('systems.group-records.index', [$system, '__GROUP__']) }}'.replace('__GROUP__', encodeURIComponent(group)))
            .then(function(r) { return r.json(); })
            .then(function(rows) {
                _groupData[group] = rows;
                groupRender(group, slug, rows);
            })
            .catch(function() {
                list.innerHTML = '<p class="text-xs text-red-400 px-5 py-4">โหลดข้อมูลล้มเหลว</p>';
            });
    }

    function groupRender(group, slug, rows) {
        var list = document.getElementById(slug + '-list');
        var count = document.getElementById(slug + '-count');
        count.textContent = '(' + rows.length + ' รายการ)';

        if (rows.length === 0) {
            list.innerHTML = '<p class="text-xs text-slate-300 font-medium px-5 py-6 text-center">ยังไม่มีข้อมูล</p>';
            return;
        }

        var html = '';
        rows.forEach(function(row) {
            var editId = slug + '-edit-' + row.id;
            var viewId = slug + '-view-' + row.id;
            html += '<div id="' + viewId + '" class="group flex items-center justify-between gap-2 px-4 py-2.5 hover:bg-slate-50 transition-colors">'
                + '<span class="text-sm text-slate-700 font-medium truncate">' + escHtml(row.name) + '</span>'
                + '<div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity flex-shrink-0">'
                + '<button type="button" onclick="groupEditShow(\'' + slug + '\',' + row.id + ')" class="text-xs font-semibold text-indigo-500 hover:text-indigo-700 bg-indigo-50 hover:bg-indigo-100 px-2.5 py-1 rounded-lg transition-colors">แก้ไข</button>'
                + '<button type="button" onclick="groupDeleteConfirm(\'' + group + '\',' + row.id + ',\'' + escHtml(row.name) + '\')" class="text-xs font-semibold text-red-400 hover:text-red-600 hover:bg-red-50 px-2.5 py-1 rounded-lg transition-colors">ลบ</button>'
                + '</div>'
                + '</div>'
                + '<div id="' + editId + '" style="display:none" class="px-4 py-2.5 bg-indigo-50/60 border-y border-indigo-100">'
                + '<form method="POST" action="{{ route('systems.group-records.update', [$system, '__GROUP__', '__ID__']) }}'.replace('__GROUP__', encodeURIComponent(group)).replace('__ID__', row.id) + '" class="flex items-center gap-2">'
                + '<input type="hidden" name="_token" value="{{ csrf_token() }}">'
                + '<input type="hidden" name="_method" value="PUT">'
                + '<input type="text" name="name" value="' + escAttr(row.name) + '" required class="flex-1 min-w-0 px-3 py-1.5 border border-slate-200 rounded-lg text-sm text-slate-800 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-400/30 focus:border-indigo-400 transition-all">'
                + '<button type="submit" class="flex-shrink-0 px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg transition-colors">บันทึก</button>'
                + '<button type="button" onclick="groupEditHide(\'' + slug + '\',' + row.id + ')" class="flex-shrink-0 px-3 py-1.5 bg-white border border-slate-200 text-slate-600 text-xs font-semibold rounded-lg hover:bg-slate-50 transition-colors">ยกเลิก</button>'
                + '</form>'
                + '</div>';
        });
        list.innerHTML = html;
    }

    function groupEditShow(slug, id) {
        document.getElementById(slug + '-view-' + id).style.display = 'none';
        document.getElementById(slug + '-edit-' + id).style.display = '';
    }
    function groupEditHide(slug, id) {
        document.getElementById(slug + '-view-' + id).style.display = '';
        document.getElementById(slug + '-edit-' + id).style.display = 'none';
    }

    function groupDeleteConfirm(group, id, name) {
        var formId = 'del-grp-dyn-' + id;
        var existing = document.getElementById(formId);
        if (existing) existing.remove();

        var form = document.createElement('form');
        form.id = formId;
        form.method = 'POST';
        form.action = '{{ route('systems.group-records.destroy', [$system, '__GROUP__', '__ID__']) }}'.replace('__GROUP__', encodeURIComponent(group)).replace('__ID__', id);
        form.className = 'hidden';
        form.innerHTML = '<input type="hidden" name="_token" value="{{ csrf_token() }}"><input type="hidden" name="_method" value="DELETE">';
        document.body.appendChild(form);

        askConfirm(
            formId,
            'ลบ "' + name + '" ออกจาก ' + group + ' ?',
            'ข้อมูลนี้จะถูกลบออกจากฐานข้อมูลของ {{ $system->name }} โดยตรงและไม่สามารถกู้คืนได้'
        );
    }

    function groupAddSubmit(form) {
        // let default submit happen — will redirect back
        return true;
    }

    function escHtml(str) {
        return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }
    function escAttr(str) {
        return String(str).replace(/"/g, '&quot;').replace(/'/g, '&#39;');
    }

    // Auto-load all managed groups on page ready
    @foreach ($managedGroups as $group)
    groupReload('{{ $group }}', '{{ 'grp-' . preg_replace('/[^a-z0-9]/', '-', strtolower($group)) }}');
    @endforeach
</script>
@endpush
