@extends('layouts.app')

@section('title', 'ระบบที่เชื่อมต่อ')
@section('header', 'ระบบที่เชื่อมต่อ')

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}" class="text-slate-400 hover:text-slate-600 font-medium transition-colors whitespace-nowrap">Dashboard</a>
    <svg class="w-3.5 h-3.5 text-slate-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    <span class="font-semibold text-slate-800 truncate">ระบบที่เชื่อมต่อ</span>
@endsection

@php
    $canManageSystems = auth()->user()->canAccess('system_create_edit');
    $canEditPermissions = auth()->user()->canAccess('permission_update');
@endphp

@section('content')

{{-- Health Check Flash Area — injected dynamically by JS --}}
<div id="health-flash-area"></div>

{{-- Hero Banner --}}
<div class="mb-8 relative overflow-hidden rounded-2xl"
     style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 45%, #0c1a2e 100%)">
    <div class="absolute inset-0 pointer-events-none overflow-hidden">
        <div class="absolute -top-12 -right-12 w-72 h-72 bg-orange-500/6 rounded-full blur-2xl"></div>
        <div class="absolute -bottom-16 left-24 w-56 h-56 bg-indigo-400/5 rounded-full blur-2xl"></div>
        <div class="absolute inset-0"
             style="background-image:linear-gradient(rgba(255,255,255,0.025) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,0.025) 1px,transparent 1px);background-size:28px 28px"></div>
    </div>
    <div class="relative px-6 md:px-8 py-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-white/8 backdrop-blur-sm rounded-2xl flex items-center justify-center flex-shrink-0 border border-white/10">
                <svg class="w-6 h-6 text-orange-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                          d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"/>
                </svg>
            </div>
            <div>
                <h2 class="text-lg font-bold text-white">ระบบที่เชื่อมต่อ</h2>
                <p class="text-slate-400 text-xs font-medium mt-0.5">
                    {{ $systems->count() }} ระบบ
                    · {{ $systems->where('is_active', true)->count() }} Active
                </p>
            </div>
        </div>
        @if ($canManageSystems)
        <a href="{{ route('systems.create') }}"
           class="inline-flex items-center gap-1.5 px-4 py-2 bg-orange-500/20 hover:bg-orange-500/30 border border-orange-400/30 rounded-xl text-orange-300 hover:text-orange-200 text-xs font-semibold transition-all">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            เพิ่มระบบใหม่
        </a>
        @endif
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
    @forelse ($systems as $system)
        <div class="bg-white rounded-2xl shadow-sm ring-1 ring-slate-100 overflow-hidden group hover:shadow-md hover:ring-indigo-100 transition-all duration-200 hover:-translate-y-0.5">
            {{-- Top color bar --}}
            <div class="h-1.5 w-full" style="background: linear-gradient(90deg, {{ $system->color }}, {{ $system->color }}aa)"></div>

            <div class="p-6">
                {{-- Header --}}
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-start gap-3">
                        {{-- System icon --}}
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 shadow-sm"
                             style="background-color: {{ $system->color }}18">
                            <div class="w-4 h-4 rounded-lg" style="background-color: {{ $system->color }}"></div>
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-800 text-sm leading-tight group-hover:text-indigo-700 transition-colors">{{ $system->name }}</h3>
                            <code class="text-[11px] text-slate-400 font-mono bg-slate-50 px-1.5 py-0.5 rounded-md mt-1 inline-block">{{ $system->slug }}</code>
                        </div>
                    </div>
                    <span class="inline-flex items-center gap-1.5 text-[11px] font-semibold rounded-full px-2.5 py-1 flex-shrink-0
                                 {{ $system->is_active ? 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200/60' : 'bg-slate-100 text-slate-500 ring-1 ring-slate-200/60' }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $system->is_active ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                        {{ $system->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>

                {{-- Description --}}
                @if ($system->description)
                    <p class="text-xs text-slate-500 mb-4 leading-relaxed line-clamp-2">{{ $system->description }}</p>
                @else
                    <div class="mb-4"></div>
                @endif

                {{-- Stats --}}
                <div class="flex gap-4 mb-5 py-3 px-4 bg-slate-50 rounded-xl">
                    <div class="flex-1 text-center">
                        <div class="text-lg font-bold text-slate-800">{{ $system->permissions_count }}</div>
                        <div class="text-[11px] text-slate-400 font-medium">Permissions</div>
                    </div>
                    <div class="w-px bg-slate-200"></div>
                    <div class="flex-1 text-center">
                        <div class="text-lg font-bold text-slate-800">{{ $system->user_permissions_count }}</div>
                        <div class="text-[11px] text-slate-400 font-medium">Assignments</div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex gap-2">
                    <a href="{{ route('systems.show', $system) }}"
                       class="flex-1 text-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl transition-all duration-150 shadow-sm shadow-indigo-200 active:scale-95">
                        ดูรายละเอียด
                    </a>
                    @if ($canEditPermissions)
                    <button type="button"
                            onclick="healthCheck(this)"
                            data-health-url="{{ route('systems.health-check', $system) }}"
                            data-system-name="{{ $system->name }}"
                            class="px-3 py-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-semibold rounded-xl transition-colors duration-150 flex items-center gap-1.5 cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        ทดสอบ
                    </button>
                    @endif
                    @if ($canManageSystems)
                    <a href="{{ route('systems.edit', $system) }}"
                       class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold rounded-xl transition-colors duration-150">
                        แก้ไข
                    </a>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="col-span-full bg-white rounded-2xl shadow-sm ring-1 ring-slate-100 p-16 text-center">
            {{-- Illustration-style icon --}}
            <div class="relative w-20 h-20 mx-auto mb-5">
                <div class="w-20 h-20 bg-indigo-50 rounded-3xl flex items-center justify-center">
                    <svg class="w-10 h-10 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2"/>
                    </svg>
                </div>
                <div class="absolute -top-1 -right-1 w-7 h-7 bg-white rounded-xl shadow-sm ring-1 ring-slate-100 flex items-center justify-center">
                    <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                </div>
            </div>
            <h3 class="text-sm font-semibold text-slate-700 mb-1">ยังไม่มีระบบ</h3>
            <p class="text-xs text-slate-400 mb-6">เพิ่มระบบแรกเพื่อเริ่มจัดการสิทธิ์ผู้ใช้</p>
            @if ($canManageSystems)
            <a href="{{ route('systems.create') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl transition-all shadow-sm shadow-indigo-200 active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                เพิ่มระบบใหม่
            </a>
            @endif
        </div>
    @endforelse
</div>

@push('scripts')
<script>
function healthCheck(btn) {
    const url        = btn.dataset.healthUrl;
    const systemName = btn.dataset.systemName;
    const originalHtml = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/></svg> กำลังทดสอบ...';

    fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        },
    })
    .then(function (r) {
        if (!r.ok) { throw new Error('HTTP ' + r.status); }
        return r.json();
    })
    .then(function (data) { showHealthFlash(systemName, data.ok, data.message); })
    .catch(function ()    { showHealthFlash(systemName, false, 'ไม่สามารถเชื่อมต่อได้'); })
    .finally(function ()  { btn.disabled = false; btn.innerHTML = originalHtml; });
}

function showHealthFlash(systemName, ok, message) {
    const area = document.getElementById('health-flash-area');
    const id   = 'hf-' + Date.now();

    const cfg = ok
        ? { wrap: 'bg-emerald-50 border-emerald-200/80 text-emerald-800 shadow-emerald-100',
            icon: 'bg-emerald-100', close: 'text-emerald-400 hover:text-emerald-600',
            svg: '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>',
            suffix: ' — เชื่อมต่อสำเร็จ' }
        : { wrap: 'bg-red-50 border-red-200/80 text-red-800 shadow-red-100',
            icon: 'bg-red-100', close: 'text-red-400 hover:text-red-600',
            svg: '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>',
            suffix: ' — เชื่อมต่อไม่ได้' };

    // Build DOM nodes — no innerHTML for user-controlled strings
    const el = document.createElement('div');
    el.id        = id;
    el.className = 'flash-msg mb-4 flex items-center gap-3 p-4 border rounded-2xl text-sm shadow-sm ' + cfg.wrap;

    // Icon
    const iconWrap = document.createElement('div');
    iconWrap.className = 'w-8 h-8 rounded-xl flex items-center justify-center flex-shrink-0 ' + cfg.icon;
    iconWrap.innerHTML = '<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">' + cfg.svg + '</svg>';

    // Text — textContent to prevent XSS
    const textWrap = document.createElement('div');
    textWrap.className = 'flex-1 font-medium';
    const mainText = document.createTextNode(systemName + cfg.suffix);
    textWrap.appendChild(mainText);
    if (message) {
        const span = document.createElement('span');
        span.className = 'opacity-70';
        span.textContent = ' — ' + message;
        textWrap.appendChild(span);
    }

    // Close button
    const closeBtn = document.createElement('button');
    closeBtn.type      = 'button';
    closeBtn.className = 'transition-colors flex-shrink-0 ' + cfg.close;
    closeBtn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>';
    closeBtn.addEventListener('click', function () { el.remove(); });

    el.appendChild(iconWrap);
    el.appendChild(textWrap);
    el.appendChild(closeBtn);
    area.prepend(el);

    clearTimeout(window._healthFlashTimer);
    window._healthFlashTimer = setTimeout(function () {
        el.style.transition = 'opacity 0.5s';
        el.style.opacity = '0';
        setTimeout(function () { el.remove(); }, 500);
    }, 5000);
}
</script>
@endpush

@endsection
