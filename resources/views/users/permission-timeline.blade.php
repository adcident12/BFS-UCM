@extends('layouts.app')

@section('title', 'Permission Timeline — '.$user->name)
@section('header', 'Permission Timeline')

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}" class="text-slate-400 hover:text-slate-600 font-medium transition-colors whitespace-nowrap">Dashboard</a>
    <svg class="w-3.5 h-3.5 text-slate-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    <a href="{{ route('users.index') }}" class="text-slate-400 hover:text-slate-600 font-medium transition-colors whitespace-nowrap">จัดการผู้ใช้</a>
    <svg class="w-3.5 h-3.5 text-slate-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    <a href="{{ route('users.show', $user) }}" class="text-slate-400 hover:text-slate-600 font-medium transition-colors whitespace-nowrap">{{ $user->name }}</a>
    <svg class="w-3.5 h-3.5 text-slate-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    <span class="font-semibold text-slate-800 truncate">Permission Timeline</span>
@endsection

@section('content')

{{-- Hero Banner --}}
<div class="mb-6 relative overflow-hidden rounded-2xl"
     style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 45%, #0c1a2e 100%)">
    <div class="absolute inset-0 pointer-events-none overflow-hidden">
        <div class="absolute -top-12 -right-12 w-72 h-72 bg-violet-500/6 rounded-full blur-2xl"></div>
        <div class="absolute -bottom-16 left-24 w-56 h-56 bg-indigo-400/5 rounded-full blur-2xl"></div>
        <div class="absolute inset-0"
             style="background-image:linear-gradient(rgba(255,255,255,0.025) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,0.025) 1px,transparent 1px);background-size:28px 28px"></div>
    </div>
    <div class="relative px-6 md:px-8 py-6 flex flex-col sm:flex-row items-start gap-4">
        {{-- Avatar --}}
        <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-xl font-bold text-white flex-shrink-0 border border-white/15"
             style="background: linear-gradient(135deg, #6366f140, #8b5cf640)">
            {{ strtoupper(substr($user->name, 0, 1)) }}
        </div>
        {{-- Info --}}
        <div class="flex-1 min-w-0">
            <h2 class="text-lg font-bold text-white">{{ $user->name }}</h2>
            <div class="flex flex-wrap items-center gap-2 mt-1.5">
                <code class="text-xs font-mono text-indigo-300 bg-indigo-500/15 border border-indigo-500/20 px-2.5 py-1 rounded-lg font-semibold">{{ $user->username }}</code>
                @if ($user->department)
                    <span class="text-xs text-slate-400 font-medium">{{ $user->department }}</span>
                @endif
                @if ($user->title)
                    <span class="text-xs text-slate-400 font-medium bg-white/8 border border-white/10 px-2 py-0.5 rounded-lg">{{ $user->title }}</span>
                @endif
            </div>
            <div class="flex flex-wrap items-center gap-3 mt-2">
                @php $totalPerms = array_sum(array_map('count', $permsBySystem)); @endphp
                <span class="text-xs text-slate-400 font-medium">
                    สิทธิ์รวม <span class="font-bold text-slate-200">{{ $totalPerms }}</span> รายการ
                    ใน <span class="font-bold text-slate-200">{{ count(array_filter($permsBySystem)) }}</span> ระบบ
                </span>
                <span class="text-slate-600">·</span>
                <span class="text-xs text-slate-400 font-medium">ประวัติ <span class="font-bold text-slate-200">{{ $logs->count() }}</span> รายการ</span>
            </div>
        </div>
        {{-- Back --}}
        <a href="{{ route('users.show', $user) }}"
           class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-400 hover:text-slate-200 hover:bg-white/10 px-3 py-2 rounded-xl transition-colors flex-shrink-0">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            กลับ
        </a>
    </div>
</div>

{{-- 2-column layout: Matrix (left) + Timeline (right, both fixed-height scroll) --}}
<div class="grid grid-cols-1 xl:grid-cols-5 gap-6 items-start">

    {{-- ── Permission Matrix ────────────────────────────────────────── --}}
    <div class="xl:col-span-2 xl:sticky xl:top-6 order-2 xl:order-1">
        <div class="bg-white rounded-2xl shadow-sm ring-1 ring-slate-100 overflow-hidden">
            <div class="flex items-center gap-2.5 px-5 py-4 border-b border-slate-100">
                <div class="w-7 h-7 bg-indigo-100 rounded-lg flex items-center justify-center">
                    <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-slate-800 text-sm">สิทธิ์ปัจจุบัน</h3>
                    <p class="text-[11px] text-slate-400 font-medium">สถานะล่าสุดจาก UCM</p>
                </div>
            </div>

            @if ($systems->isEmpty())
                <div class="px-5 py-10 text-center">
                    <p class="text-xs text-slate-400 font-medium">ยังไม่มีระบบ</p>
                </div>
            @else
                <div class="divide-y divide-slate-50">
                    @foreach ($systems as $system)
                        @php
                            $perms = $permsBySystem[$system->id] ?? [];
                            $allKeys = $system->permissions->pluck('label', 'key');
                            $hasPerms = count($perms) > 0;
                        @endphp
                        <div class="px-5 py-4">
                            {{-- System header --}}
                            <div class="flex items-center justify-between gap-2 mb-3">
                                <div class="flex items-center gap-2 min-w-0">
                                    <div class="w-6 h-6 rounded-lg flex-shrink-0 flex items-center justify-center shadow-sm"
                                         style="background-color: {{ $system->color }}20">
                                        <div class="w-2.5 h-2.5 rounded-md" style="background-color: {{ $system->color }}"></div>
                                    </div>
                                    <span class="text-xs font-bold text-slate-700 truncate">{{ $system->name }}</span>
                                </div>
                                @if ($hasPerms)
                                    <span class="text-[10px] font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-full ring-1 ring-indigo-100 flex-shrink-0">
                                        {{ count($perms) }}/{{ $allKeys->count() }}
                                    </span>
                                @else
                                    <span class="text-[10px] font-medium text-slate-400 bg-slate-50 px-2 py-0.5 rounded-full ring-1 ring-slate-100 flex-shrink-0">ไม่มีสิทธิ์</span>
                                @endif
                            </div>

                            {{-- Permission chips --}}
                            @if ($allKeys->isNotEmpty())
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach ($allKeys as $key => $label)
                                        @php $granted = in_array($key, $perms, true); @endphp
                                        <span class="inline-flex items-center gap-1 text-[10px] font-semibold px-2 py-0.5 rounded-md
                                                     {{ $granted
                                                         ? 'bg-indigo-50 text-indigo-700 ring-1 ring-indigo-200/60'
                                                         : 'bg-slate-50 text-slate-400 ring-1 ring-slate-100' }}">
                                            @if ($granted)
                                                <svg class="w-2.5 h-2.5 text-indigo-500" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                </svg>
                                            @else
                                                <svg class="w-2.5 h-2.5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                            @endif
                                            {{ $label ?: $key }}
                                        </span>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-[11px] text-slate-400 italic">ระบบนี้ยังไม่มี Permission กำหนดไว้</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- ── Permission Timeline ──────────────────────────────────────── --}}
    <div class="xl:col-span-3 order-1 xl:order-2">
        <div class="bg-white rounded-2xl shadow-sm ring-1 ring-slate-100 overflow-hidden xl:max-h-[calc(100vh-8rem)] xl:flex xl:flex-col">
            <div class="flex items-center gap-2.5 px-5 py-4 border-b border-slate-100 xl:flex-shrink-0">
                <div class="w-7 h-7 bg-violet-100 rounded-lg flex items-center justify-center">
                    <svg class="w-3.5 h-3.5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-slate-800 text-sm">ประวัติการเปลี่ยนสิทธิ์</h3>
                    <p class="text-[11px] text-slate-400 font-medium">เรียงจากล่าสุด</p>
                </div>
                @if ($logs->count() > 0)
                    <span class="ml-auto text-xs font-semibold text-slate-500 bg-slate-100 px-2.5 py-1 rounded-lg">
                        {{ $logs->count() }} รายการ
                    </span>
                @endif
            </div>

            @if ($logs->isEmpty())
                <div class="px-6 py-16 text-center">
                    <div class="w-14 h-14 bg-slate-50 rounded-2xl flex items-center justify-center mx-auto mb-3">
                        <svg class="w-7 h-7 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <p class="text-sm font-semibold text-slate-400">ยังไม่มีประวัติการเปลี่ยนสิทธิ์</p>
                </div>
            @else
                @php $systemsById = $systems->keyBy('id'); @endphp
                <div class="px-5 py-5 xl:overflow-y-auto xl:flex-1">
                    @foreach ($logs as $log)
                        @php
                            $isDiscovered   = $log->event_type === 'permissions_discovered';
                            $isStatusChange = $log->event_type === 'account_status_changed';
                            $dotColor  = $isStatusChange ? 'bg-amber-400'  : ($isDiscovered ? 'bg-sky-400'   : 'bg-indigo-400');
                            $ringColor = $isStatusChange ? 'ring-amber-100' : ($isDiscovered ? 'ring-sky-100' : 'ring-indigo-100');
                            $bgColor   = $isStatusChange ? 'bg-amber-50'   : ($isDiscovered ? 'bg-sky-50'    : 'bg-indigo-50');

                            // ระบบที่เกี่ยวข้องกับ log entry นี้
                            $logSystemId  = $log->metadata['system_id'] ?? null;
                            $logSystem    = $logSystemId ? ($systemsById[$logSystemId] ?? null) : null;
                            $logPerms     = isset($log->metadata['permissions']) && is_array($log->metadata['permissions'])
                                                ? $log->metadata['permissions']
                                                : null;
                            $hasMatrix    = $logSystem && ! $isStatusChange && $logPerms !== null;
                        @endphp
                        <div class="relative flex gap-3 {{ !$loop->last ? 'pb-6' : '' }}">
                            {{-- Connector line --}}
                            @if (!$loop->last)
                                <div class="absolute left-3 top-7 bottom-0 w-px bg-slate-100"></div>
                            @endif

                            {{-- Dot --}}
                            <div class="w-6 h-6 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5 ring-2 {{ $bgColor }} {{ $ringColor }}">
                                <div class="w-2.5 h-2.5 rounded-full {{ $dotColor }}"></div>
                            </div>

                            {{-- Content --}}
                            <div class="flex-1 min-w-0">
                                {{-- Meta row: badge + system + actor --}}
                                <div class="flex flex-wrap items-center gap-1.5 mb-1">
                                    @if ($isDiscovered)
                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-sky-100 text-sky-700 ring-1 ring-sky-200/60">DISCOVER</span>
                                    @elseif ($isStatusChange)
                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-amber-100 text-amber-700 ring-1 ring-amber-200/60">STATUS</span>
                                    @else
                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-indigo-100 text-indigo-700 ring-1 ring-indigo-200/60">PERM</span>
                                    @endif

                                    @if (!empty($log->metadata['system_name']))
                                        @if ($logSystem)
                                            <span class="inline-flex items-center gap-1 text-[10px] font-semibold text-slate-600 bg-slate-100 px-2 py-0.5 rounded-md">
                                                <span class="w-2 h-2 rounded-sm flex-shrink-0" style="background-color: {{ $logSystem->color }}"></span>
                                                {{ $log->metadata['system_name'] }}
                                            </span>
                                        @else
                                            <span class="text-xs font-semibold text-slate-700">{{ $log->metadata['system_name'] }}</span>
                                        @endif
                                    @endif

                                    <span class="text-xs text-slate-400 font-medium">
                                        โดย {{ $log->actor?->name ?? $log->actor_username ?? 'ระบบ' }}
                                    </span>
                                </div>

                                {{-- Description --}}
                                <p class="text-xs text-slate-500 leading-relaxed mb-2">{{ $log->description }}</p>

                                {{-- ── Permission Matrix Snapshot ──────────────────────── --}}
                                @if ($hasMatrix)
                                    @php
                                        $allSystemPerms = $logSystem->permissions;
                                        $grantedCount   = count($logPerms);
                                        $totalCount     = $allSystemPerms->count();
                                    @endphp
                                    <div class="rounded-xl ring-1 overflow-hidden
                                                {{ $isDiscovered ? 'ring-sky-100 bg-sky-50/40' : 'ring-indigo-100/80 bg-indigo-50/30' }}">
                                        {{-- Matrix header --}}
                                        <div class="flex items-center justify-between px-3 py-2 border-b
                                                    {{ $isDiscovered ? 'border-sky-100 bg-sky-50/60' : 'border-indigo-100/60 bg-indigo-50/50' }}">
                                            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wide">Permission Matrix</span>
                                            <span class="text-[10px] font-bold
                                                         {{ $grantedCount > 0 ? ($isDiscovered ? 'text-sky-600' : 'text-indigo-600') : 'text-slate-400' }}">
                                                {{ $grantedCount }}/{{ $totalCount }} สิทธิ์
                                            </span>
                                        </div>

                                        {{-- Chips --}}
                                        <div class="px-3 py-2.5 flex flex-wrap gap-1.5">
                                            @forelse ($allSystemPerms as $perm)
                                                @php $granted = in_array($perm->key, $logPerms, true); @endphp
                                                <span class="inline-flex items-center gap-1 text-[10px] font-semibold px-2 py-0.5 rounded-md transition-colors
                                                             {{ $granted
                                                                 ? ($isDiscovered
                                                                     ? 'bg-sky-100 text-sky-700 ring-1 ring-sky-200/70'
                                                                     : 'bg-indigo-100 text-indigo-700 ring-1 ring-indigo-200/70')
                                                                 : 'bg-white text-slate-400 ring-1 ring-slate-100' }}">
                                                    @if ($granted)
                                                        <svg class="w-2.5 h-2.5 {{ $isDiscovered ? 'text-sky-500' : 'text-indigo-500' }}" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                        </svg>
                                                    @else
                                                        <svg class="w-2.5 h-2.5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 12H6"/>
                                                        </svg>
                                                    @endif
                                                    {{ $perm->label ?: $perm->key }}
                                                </span>
                                            @empty
                                                {{-- ไม่มี permission definition — fallback แสดงจาก metadata --}}
                                                @forelse ($logPerms as $key)
                                                    <span class="inline-flex items-center gap-1 text-[10px] font-mono font-semibold px-2 py-0.5 rounded-md bg-indigo-100 text-indigo-700 ring-1 ring-indigo-200/70">
                                                        <svg class="w-2.5 h-2.5 text-indigo-500" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                        </svg>
                                                        {{ $key }}
                                                    </span>
                                                @empty
                                                    <span class="text-[10px] text-slate-400 italic font-medium">ไม่มีสิทธิ์</span>
                                                @endforelse
                                            @endforelse
                                        </div>
                                    </div>

                                {{-- STATUS event: แสดง active/disabled badge --}}
                                @elseif ($isStatusChange && isset($log->metadata['active']))
                                    @php $isActive = (bool) $log->metadata['active']; @endphp
                                    <div class="rounded-xl ring-1 ring-amber-100 bg-amber-50/40 overflow-hidden">
                                        <div class="flex items-center gap-2.5 px-3 py-2.5">
                                            <span class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0
                                                         {{ $isActive ? 'bg-emerald-100' : 'bg-red-100' }}">
                                                @if ($isActive)
                                                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                @else
                                                    <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                                    </svg>
                                                @endif
                                            </span>
                                            <div>
                                                <div class="text-xs font-bold {{ $isActive ? 'text-emerald-700' : 'text-red-700' }}">
                                                    Account {{ $isActive ? 'เปิดใช้งาน' : 'ปิดการใช้งาน' }}
                                                </div>
                                                @if (!empty($log->metadata['system_name']))
                                                    <div class="text-[11px] text-slate-400 font-medium mt-0.5">ในระบบ {{ $log->metadata['system_name'] }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                {{-- Timestamp --}}
                                <div class="flex items-center gap-1 mt-2 text-[11px] text-slate-400 font-medium">
                                    <svg class="w-3 h-3 text-slate-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span title="{{ $log->created_at->format('d/m/Y H:i:s') }}">
                                        {{ $log->created_at->diffForHumans() }}
                                        <span class="text-slate-300 mx-1">·</span>
                                        {{ $log->created_at->format('d M Y H:i') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                @if ($logs->count() >= 100)
                    <div class="px-5 py-3 border-t border-slate-50 bg-slate-50/50">
                        <p class="text-xs text-slate-400 font-medium">แสดง 100 รายการล่าสุด</p>
                    </div>
                @endif
            @endif
        </div>
    </div>

</div>

@endsection
