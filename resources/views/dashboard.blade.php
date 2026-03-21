@extends('layouts.app')

@section('title', 'Dashboard')
@section('header', 'Dashboard')

@section('content')

{{-- Hero Banner --}}
<div class="mb-8 relative overflow-hidden rounded-2xl"
     style="background: linear-gradient(135deg, #4f46e5 0%, #6d28d9 50%, #2563eb 100%)">
    <div class="absolute inset-0 pointer-events-none select-none overflow-hidden">
        <div class="absolute -top-12 -right-12 w-72 h-72 bg-white/5 rounded-full"></div>
        <div class="absolute -bottom-16 left-24 w-56 h-56 bg-violet-300/10 rounded-full"></div>
        <div class="absolute top-1/2 -translate-y-1/2 right-1/4 w-32 h-32 bg-white/3 rounded-full"></div>
        {{-- Grid overlay --}}
        <div class="absolute inset-0"
             style="background-image:linear-gradient(rgba(255,255,255,0.04) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,0.04) 1px,transparent 1px);background-size:32px 32px"></div>
    </div>
    <div class="relative px-6 md:px-8 py-7 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-white/15 backdrop-blur-sm rounded-2xl flex items-center justify-center flex-shrink-0 shadow-lg">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
            </div>
            <div>
                <h2 class="text-xl font-bold text-white leading-tight">User Centralized Management</h2>
                <p class="text-indigo-200 text-sm font-medium mt-0.5">Bangkok Flight Services — Permission Management System</p>
            </div>
        </div>
        <div class="flex items-center gap-6 bg-white/10 backdrop-blur-sm rounded-xl px-5 py-3 border border-white/10">
            <div class="text-center">
                <div class="text-white font-bold text-lg leading-none">{{ $stats['systems'] }}</div>
                <div class="text-indigo-200 text-xs font-medium mt-0.5">ระบบ</div>
            </div>
            <div class="w-px h-8 bg-white/20"></div>
            <div class="text-center">
                <div class="text-white font-bold text-lg leading-none">{{ $stats['users'] }}</div>
                <div class="text-indigo-200 text-xs font-medium mt-0.5">ผู้ใช้</div>
            </div>
            <div class="w-px h-8 bg-white/20"></div>
            <div class="flex items-center gap-2">
                <div class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></div>
                <span class="text-white text-xs font-semibold">Online</span>
            </div>
        </div>
    </div>
</div>

{{-- Stats Cards --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
    @php
        $cards = [
            [
                'label'    => 'ระบบที่เชื่อมต่อ',
                'value'    => $stats['systems'],
                'gradient' => 'from-indigo-500 to-indigo-600',
                'shadow'   => 'shadow-indigo-100',
                'ring'     => 'ring-indigo-100',
                'bg'       => 'bg-indigo-50',
                'text'     => 'text-indigo-700',
                'sub'      => 'Connected Systems',
                'icon'     => 'M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2',
                'href'     => '{{ route("systems.index") }}',
            ],
            [
                'label'    => 'ผู้ใช้งานทั้งหมด',
                'value'    => $stats['users'],
                'gradient' => 'from-sky-500 to-sky-600',
                'shadow'   => 'shadow-sky-100',
                'ring'     => 'ring-sky-100',
                'bg'       => 'bg-sky-50',
                'text'     => 'text-sky-700',
                'sub'      => 'Active Users',
                'icon'     => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z',
                'href'     => '{{ route("users.index") }}',
            ],
            [
                'label'    => 'Sync สำเร็จวันนี้',
                'value'    => $stats['sync_success'],
                'gradient' => 'from-emerald-500 to-emerald-600',
                'shadow'   => 'shadow-emerald-100',
                'ring'     => 'ring-emerald-100',
                'bg'       => 'bg-emerald-50',
                'text'     => 'text-emerald-700',
                'sub'      => 'Today\'s Success',
                'icon'     => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                'href'     => null,
            ],
            [
                'label'    => 'Sync ล้มเหลววันนี้',
                'value'    => $stats['sync_failed'],
                'gradient' => 'from-rose-500 to-rose-600',
                'shadow'   => 'shadow-rose-100',
                'ring'     => 'ring-rose-100',
                'bg'       => 'bg-rose-50',
                'text'     => 'text-rose-700',
                'sub'      => 'Today\'s Failed',
                'icon'     => 'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                'href'     => null,
            ],
        ];
    @endphp

    @foreach ($cards as $card)
        <div class="bg-white rounded-2xl shadow-sm ring-1 {{ $card['ring'] }} overflow-hidden
                    hover:shadow-md transition-all duration-200 group relative">
            {{-- Top gradient bar --}}
            <div class="h-1 w-full bg-gradient-to-r {{ $card['gradient'] }}"></div>
            <div class="p-5">
                <div class="flex items-start justify-between mb-4">
                    <div class="w-11 h-11 rounded-xl bg-gradient-to-br {{ $card['gradient'] }} flex items-center justify-center shadow-sm {{ $card['shadow'] }}">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $card['icon'] }}"/>
                        </svg>
                    </div>
                    @if ($card['value'] > 0 && str_contains($card['label'], 'ล้มเหลว'))
                        <span class="inline-flex items-center gap-1 text-[10px] font-bold text-rose-700 bg-rose-100 px-2 py-1 rounded-full ring-1 ring-rose-200">
                            <span class="w-1.5 h-1.5 bg-rose-500 rounded-full animate-pulse"></span>
                            Alert
                        </span>
                    @endif
                </div>
                <div class="text-3xl font-bold text-slate-800 tracking-tight leading-none mb-1.5">
                    {{ number_format($card['value']) }}
                </div>
                <div class="text-xs font-semibold text-slate-500">{{ $card['label'] }}</div>
                <div class="text-[11px] text-slate-400 mt-0.5">{{ $card['sub'] }}</div>
            </div>
        </div>
    @endforeach
</div>

<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">

    {{-- Connected Systems --}}
    <div class="md:col-span-1 xl:col-span-1 bg-white rounded-2xl shadow-sm ring-1 ring-slate-100 overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 bg-indigo-100 rounded-lg flex items-center justify-center">
                    <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2"/>
                    </svg>
                </div>
                <h2 class="font-bold text-slate-800 text-sm">ระบบที่เชื่อมต่อ</h2>
            </div>
            <a href="{{ route('systems.create') }}"
               class="inline-flex items-center gap-1 text-xs font-semibold text-indigo-600 hover:text-indigo-800 bg-indigo-50 hover:bg-indigo-100 px-3 py-1.5 rounded-lg transition-colors">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                เพิ่ม
            </a>
        </div>
        <div class="divide-y divide-slate-50">
            @forelse ($systems as $system)
                <a href="{{ route('systems.show', $system) }}"
                   class="flex items-center gap-3 px-5 py-3.5 hover:bg-slate-50/80 transition-colors group">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0 shadow-sm"
                         style="background-color: {{ $system->color }}18">
                        <div class="w-3.5 h-3.5 rounded-full shadow-sm" style="background-color: {{ $system->color }}"></div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-semibold text-slate-700 truncate group-hover:text-indigo-700 transition-colors">{{ $system->name }}</div>
                        <div class="text-xs text-slate-400 font-medium mt-0.5">
                            <span class="font-mono bg-slate-100 text-slate-500 px-1.5 py-0.5 rounded text-[10px]">{{ $system->slug }}</span>
                            <span class="ml-1.5">{{ $system->user_permissions_count }} สิทธิ์</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <span class="inline-flex items-center gap-1 text-[11px] font-semibold rounded-full px-2 py-0.5
                                     {{ $system->is_active ? 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200/50' : 'bg-slate-100 text-slate-500' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $system->is_active ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                            {{ $system->is_active ? 'Active' : 'Off' }}
                        </span>
                        <svg class="w-3.5 h-3.5 text-slate-300 group-hover:text-indigo-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                </a>
            @empty
                <div class="px-6 py-14 text-center">
                    <div class="w-12 h-12 bg-indigo-50 rounded-2xl flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2"/>
                        </svg>
                    </div>
                    <p class="text-sm font-semibold text-slate-400 mb-1">ยังไม่มีระบบ</p>
                    <p class="text-xs text-slate-300">กด "+ เพิ่ม" เพื่อเริ่มต้น</p>
                </div>
            @endforelse
        </div>
        @if ($systems->count() > 0)
            <div class="px-5 py-3 border-t border-slate-50 bg-slate-50/50">
                <a href="{{ route('systems.index') }}" class="text-xs text-slate-500 hover:text-indigo-700 font-semibold transition-colors flex items-center gap-1">
                    ดูทั้งหมด {{ $systems->count() }} ระบบ
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
        @endif
    </div>

    {{-- Recent Activity Feed --}}
    <div class="md:col-span-1 xl:col-span-2 bg-white rounded-2xl shadow-sm ring-1 ring-slate-100 overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 bg-violet-100 rounded-lg flex items-center justify-center">
                    <svg class="w-3.5 h-3.5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <h2 class="font-bold text-slate-800 text-sm">กิจกรรมล่าสุด</h2>
            </div>
            <span class="text-xs font-semibold text-slate-500 bg-slate-100 px-2.5 py-1 rounded-lg">
                {{ $recentLogs->count() }} รายการ
            </span>
        </div>

        <div class="px-6 py-5">
            @forelse ($recentLogs as $log)
                @php
                    $isSuccess = $log->status === 'success';
                    $isFailed  = $log->status === 'failed';
                    $isPending = !$isSuccess && !$isFailed;
                    $isGrant   = $log->action === 'grant';
                    $isRevoke  = $log->action === 'revoke';
                @endphp
                <div class="relative flex gap-4 {{ !$loop->last ? 'mb-4' : '' }}">
                    {{-- Connector line --}}
                    @if (!$loop->last)
                        <div class="absolute left-3.5 top-8 bottom-0 w-px
                            {{ $isSuccess ? 'bg-emerald-100' : ($isFailed ? 'bg-red-100' : 'bg-amber-100') }}"></div>
                    @endif

                    {{-- Status icon --}}
                    <div class="w-7 h-7 rounded-xl flex items-center justify-center flex-shrink-0 mt-0.5 ring-2
                        {{ $isSuccess ? 'bg-emerald-50 ring-emerald-100' : ($isFailed ? 'bg-red-50 ring-red-100' : 'bg-amber-50 ring-amber-100') }}">
                        @if ($isSuccess)
                            <svg class="w-3.5 h-3.5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        @elseif ($isFailed)
                            <svg class="w-3.5 h-3.5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                        @else
                            <svg class="w-3.5 h-3.5 text-amber-500 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                            </svg>
                        @endif
                    </div>

                    {{-- Content --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap mb-0.5">
                            <a href="{{ route('users.show', $log->user_id) }}"
                               class="text-sm font-bold text-slate-800 hover:text-indigo-700 transition-colors">
                                {{ $log->user?->name ?? 'N/A' }}
                            </a>
                            <span class="text-slate-300 text-xs">→</span>
                            <span class="text-xs font-medium text-slate-500">{{ $log->system?->name ?? 'N/A' }}</span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold
                                {{ $isGrant ? 'bg-emerald-100 text-emerald-700 ring-1 ring-emerald-200/50'
                                    : ($isRevoke ? 'bg-rose-100 text-rose-700 ring-1 ring-rose-200/50'
                                    : 'bg-slate-100 text-slate-600') }}">
                                {{ strtoupper($log->action) }}
                            </span>
                            @if ($isFailed)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-red-700 ring-1 ring-red-200/50">
                                    FAILED
                                </span>
                            @endif
                        </div>
                        @if ($isFailed && $log->error_message)
                            <div class="text-[11px] text-red-500 bg-red-50 px-2.5 py-1 rounded-lg mt-1 font-mono line-clamp-1 border border-red-100">
                                {{ Str::limit($log->error_message, 80) }}
                            </div>
                        @endif
                        <div class="text-xs text-slate-400 font-medium mt-0.5 flex items-center gap-1.5">
                            <svg class="w-3 h-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            {{ $log->created_at->diffForHumans() }}
                        </div>
                    </div>
                </div>
            @empty
                <div class="py-14 text-center">
                    <div class="w-14 h-14 bg-violet-50 rounded-2xl flex items-center justify-center mx-auto mb-3">
                        <svg class="w-7 h-7 text-violet-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <p class="text-sm font-bold text-slate-400">ยังไม่มีกิจกรรม</p>
                    <p class="text-xs text-slate-300 mt-1">กิจกรรม Sync จะแสดงที่นี่</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

@endsection
