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
                                        <button type="button" onclick="permEdit({{ $perm->id }})"
                                                class="text-xs font-semibold text-indigo-500 hover:text-indigo-700 bg-indigo-50 hover:bg-indigo-100 px-2.5 py-1 rounded-lg transition-colors">แก้ไข</button>
                                        <form id="del-perm-{{ $perm->id }}" method="POST"
                                              action="{{ route('systems.permissions.destroy', [$system, $perm]) }}" class="hidden">
                                            @csrf @method('DELETE')
                                        </form>
                                        <button type="button"
                                                onclick="askConfirm('del-perm-{{ $perm->id }}', 'ลบ permission \'{{ addslashes($perm->label) }}\' ?')"
                                                class="text-xs font-semibold text-red-400 hover:text-red-600 hover:bg-red-50 px-2.5 py-1 rounded-lg transition-colors">ลบ</button>
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
</script>
@endpush
