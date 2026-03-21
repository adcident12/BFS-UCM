@extends('layouts.app')

@section('title', 'Queue Monitor')
@section('header', 'Queue Monitor')

@section('content')

{{-- ── Hero Banner ─────────────────────────────────────────────────────── --}}
<div class="mb-7 relative overflow-hidden rounded-2xl"
     style="background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #0c1a3a 100%)">
    <div class="absolute inset-0 pointer-events-none overflow-hidden">
        <div class="absolute -top-10 -right-10 w-64 h-64 bg-indigo-500/5 rounded-full"></div>
        <div class="absolute -bottom-14 left-20 w-48 h-48 bg-violet-400/5 rounded-full"></div>
        <div class="absolute inset-0"
             style="background-image:linear-gradient(rgba(255,255,255,0.03) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,0.03) 1px,transparent 1px);background-size:32px 32px"></div>
    </div>
    <div class="relative px-6 md:px-8 py-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-11 h-11 bg-white/10 backdrop-blur-sm rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                </svg>
            </div>
            <div>
                <h2 class="text-lg font-bold text-white">Queue Monitor</h2>
                <p class="text-indigo-300/70 text-xs font-medium mt-0.5">ติดตามสถานะ Queue worker และ Sync jobs</p>
            </div>
        </div>
        <div class="flex items-center gap-3 flex-shrink-0">
            {{-- Queue depth pill --}}
            @php $totalQueued = $pendingJobs + $processingJobs; @endphp
            @if ($processingJobs > 0)
                <div class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-500/15 border border-emerald-400/20 rounded-xl">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span class="text-emerald-300 text-xs font-semibold">Processing {{ $processingJobs }} job</span>
                </div>
            @elseif ($pendingJobs > 0)
                <div class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-500/15 border border-amber-400/20 rounded-xl">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                    <span class="text-amber-300 text-xs font-semibold">{{ $pendingJobs }} jobs รอ worker</span>
                </div>
            @else
                <div class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white/5 border border-white/10 rounded-xl">
                    <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                    <span class="text-slate-400 text-xs font-semibold">Queue ว่าง</span>
                </div>
            @endif

            {{-- Refresh button --}}
            <a href="{{ route('queue.monitor') }}"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white/8 hover:bg-white/15 border border-white/10 rounded-xl text-white/70 hover:text-white text-xs font-semibold transition-all">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Refresh
            </a>
        </div>
    </div>
</div>

{{-- ── Stats Cards ──────────────────────────────────────────────────────── --}}
<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4 mb-7">

    {{-- Pending --}}
    <div class="bg-white rounded-2xl shadow-sm ring-1 ring-slate-100 px-5 py-4">
        <div class="flex items-center justify-between mb-3">
            <div class="w-8 h-8 rounded-xl bg-amber-50 flex items-center justify-center">
                <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <span class="text-[10px] font-bold text-slate-300 uppercase tracking-wider">Pending</span>
        </div>
        <div class="text-2xl font-bold text-slate-800 tabular-nums">{{ number_format($pendingJobs) }}</div>
        <div class="text-xs text-slate-400 font-medium mt-0.5">รอประมวลผล</div>
    </div>

    {{-- Processing --}}
    <div class="bg-white rounded-2xl shadow-sm ring-1 ring-slate-100 px-5 py-4">
        <div class="flex items-center justify-between mb-3">
            <div class="w-8 h-8 rounded-xl bg-indigo-50 flex items-center justify-center">
                <svg class="w-4 h-4 text-indigo-500 {{ $processingJobs > 0 ? 'animate-spin' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <span class="text-[10px] font-bold text-slate-300 uppercase tracking-wider">Running</span>
        </div>
        <div class="text-2xl font-bold {{ $processingJobs > 0 ? 'text-indigo-600' : 'text-slate-800' }} tabular-nums">{{ number_format($processingJobs) }}</div>
        <div class="text-xs text-slate-400 font-medium mt-0.5">กำลังประมวลผล</div>
    </div>

    {{-- Failed --}}
    <div class="bg-white rounded-2xl shadow-sm ring-1 {{ $failedCount > 0 ? 'ring-red-100' : 'ring-slate-100' }} px-5 py-4">
        <div class="flex items-center justify-between mb-3">
            <div class="w-8 h-8 rounded-xl {{ $failedCount > 0 ? 'bg-red-50' : 'bg-slate-50' }} flex items-center justify-center">
                <svg class="w-4 h-4 {{ $failedCount > 0 ? 'text-red-500' : 'text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <span class="text-[10px] font-bold {{ $failedCount > 0 ? 'text-red-300' : 'text-slate-300' }} uppercase tracking-wider">Failed</span>
        </div>
        <div class="text-2xl font-bold {{ $failedCount > 0 ? 'text-red-600' : 'text-slate-800' }} tabular-nums">{{ number_format($failedCount) }}</div>
        <div class="text-xs text-slate-400 font-medium mt-0.5">Jobs ที่ล้มเหลว</div>
    </div>

    {{-- Today success --}}
    <div class="bg-white rounded-2xl shadow-sm ring-1 ring-slate-100 px-5 py-4">
        <div class="flex items-center justify-between mb-3">
            <div class="w-8 h-8 rounded-xl bg-emerald-50 flex items-center justify-center">
                <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <span class="text-[10px] font-bold text-slate-300 uppercase tracking-wider">วันนี้ ✓</span>
        </div>
        <div class="text-2xl font-bold text-emerald-600 tabular-nums">{{ number_format($syncSuccess) }}</div>
        <div class="text-xs text-slate-400 font-medium mt-0.5">Sync สำเร็จ</div>
    </div>

    {{-- Today failed syncs --}}
    <div class="bg-white rounded-2xl shadow-sm ring-1 {{ $syncFailed > 0 ? 'ring-rose-100' : 'ring-slate-100' }} px-5 py-4">
        <div class="flex items-center justify-between mb-3">
            <div class="w-8 h-8 rounded-xl {{ $syncFailed > 0 ? 'bg-rose-50' : 'bg-slate-50' }} flex items-center justify-center">
                <svg class="w-4 h-4 {{ $syncFailed > 0 ? 'text-rose-500' : 'text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <span class="text-[10px] font-bold {{ $syncFailed > 0 ? 'text-rose-300' : 'text-slate-300' }} uppercase tracking-wider">วันนี้ ✗</span>
        </div>
        <div class="text-2xl font-bold {{ $syncFailed > 0 ? 'text-rose-600' : 'text-slate-800' }} tabular-nums">{{ number_format($syncFailed) }}</div>
        <div class="text-xs text-slate-400 font-medium mt-0.5">Sync ล้มเหลว</div>
    </div>

</div>

{{-- ── Main content: Failed Jobs + Sync Activity ───────────────────────── --}}
<div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

    {{-- ────── Failed Jobs table (col-span 3) ────── --}}
    <div class="lg:col-span-3 bg-white rounded-2xl shadow-sm ring-1 ring-slate-100 overflow-hidden flex flex-col">

        {{-- Header --}}
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-red-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-800">Failed Jobs</h3>
                    <p class="text-xs text-slate-400 font-medium">Jobs ที่ล้มเหลวและรอ retry</p>
                </div>
            </div>
            @if ($failedCount > 0 && auth()->user()->isSuperAdmin())
            <div class="flex items-center gap-2 flex-shrink-0">
                <form method="POST" action="{{ route('queue.failed.retry-all') }}">
                    @csrf
                    <button type="submit"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 active:scale-95 text-indigo-600 text-xs font-bold rounded-xl transition-all">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Retry ทั้งหมด
                    </button>
                </form>
                <form method="POST" action="{{ route('queue.failed.flush') }}"
                      onsubmit="return confirm('ล้าง Failed Jobs ทั้ง {{ $failedCount }} รายการ?\nไม่สามารถกู้คืนได้')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-50 hover:bg-red-100 active:scale-95 text-red-500 text-xs font-bold rounded-xl transition-all">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Flush
                    </button>
                </form>
            </div>
            @endif
        </div>

        {{-- Table --}}
        @if ($failedJobs->isEmpty())
        <div class="flex flex-col items-center justify-center py-14 text-slate-300">
            <svg class="w-10 h-10 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-sm font-semibold text-slate-400">ไม่มี Failed Jobs</p>
            <p class="text-xs text-slate-300 mt-1">Queue ทำงานปกติ</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100">
                        <th class="text-left px-5 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Job</th>
                        <th class="text-left px-3 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider hidden md:table-cell">Queue</th>
                        <th class="text-left px-3 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider">เวลาที่ล้มเหลว</th>
                        <th class="text-left px-3 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider hidden lg:table-cell">Exception</th>
                        @if (auth()->user()->isSuperAdmin())
                        <th class="px-3 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider text-right">Actions</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach ($failedJobs as $job)
                    @php
                        $payload     = json_decode($job->payload, true);
                        $displayName = $payload['displayName'] ?? ($payload['job'] ?? 'Unknown Job');
                        // Strip namespace for display
                        $shortName   = class_basename($displayName);
                        $failedAt    = \Carbon\Carbon::parse($job->failed_at);
                        // Truncate exception
                        $exception   = Str::limit($job->exception, 120);
                    @endphp
                    <tr class="group hover:bg-slate-50/70 transition-colors">
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded-lg bg-red-50 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-3 h-3 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <span class="text-xs font-bold text-slate-700 block truncate max-w-[140px]">{{ $shortName }}</span>
                                    <span class="text-[10px] font-mono text-slate-300 truncate block max-w-[140px]">{{ Str::limit($job->uuid, 18) }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-3 py-3 hidden md:table-cell">
                            <span class="inline-flex items-center px-2 py-0.5 bg-slate-100 text-slate-500 text-[10px] font-bold rounded-lg">{{ $job->queue }}</span>
                        </td>
                        <td class="px-3 py-3 text-xs text-slate-500 whitespace-nowrap">
                            <span title="{{ $failedAt->format('d/m/Y H:i:s') }}">
                                {{ $failedAt->diffForHumans() }}
                            </span>
                        </td>
                        <td class="px-3 py-3 hidden lg:table-cell">
                            <span class="text-[10px] font-mono text-slate-400 leading-relaxed line-clamp-2">{{ $exception }}</span>
                        </td>
                        @if (auth()->user()->isSuperAdmin())
                        <td class="px-3 py-3 text-right">
                            <div class="flex items-center justify-end gap-1 opacity-100 lg:opacity-0 lg:group-hover:opacity-100 transition-opacity">
                                <form method="POST" action="{{ route('queue.failed.retry', $job->uuid) }}">
                                    @csrf
                                    <button type="submit" title="Retry"
                                            class="w-7 h-7 rounded-lg flex items-center justify-center text-slate-300 hover:text-indigo-500 hover:bg-indigo-50 transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                        </svg>
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('queue.failed.destroy', $job->uuid) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" title="ลบ"
                                            onclick="return confirm('ลบ failed job นี้?')"
                                            class="w-7 h-7 rounded-lg flex items-center justify-center text-slate-300 hover:text-red-500 hover:bg-red-50 transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                        @endif
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($failedJobs->hasPages())
        <div class="px-5 py-3 border-t border-slate-100">
            {{ $failedJobs->links() }}
        </div>
        @endif
        @endif

    </div>

    {{-- ────── Recent Sync Activity (col-span 2) ────── --}}
    <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm ring-1 ring-slate-100 overflow-hidden flex flex-col">

        <div class="px-5 py-4 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-indigo-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-800">Sync Activity</h3>
                    <p class="text-xs text-slate-400 font-medium">40 รายการล่าสุด</p>
                </div>
            </div>
        </div>

        <div class="flex-1 overflow-y-auto max-h-[600px] divide-y divide-slate-50">
            @forelse ($recentSyncs as $log)
            @php
                $isSuccess = $log->status === 'success';
                $isPending = $log->status === 'pending';
                $isGrant   = $log->action === 'grant';
            @endphp
            <div class="px-5 py-3 hover:bg-slate-50/70 transition-colors">
                <div class="flex items-start gap-3">
                    {{-- Status dot --}}
                    <div class="flex-shrink-0 mt-0.5">
                        @if ($isPending)
                            <div class="w-5 h-5 rounded-full bg-amber-50 flex items-center justify-center">
                                <div class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></div>
                            </div>
                        @elseif ($isSuccess)
                            <div class="w-5 h-5 rounded-full bg-emerald-50 flex items-center justify-center">
                                <svg class="w-3 h-3 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                        @else
                            <div class="w-5 h-5 rounded-full bg-red-50 flex items-center justify-center">
                                <svg class="w-3 h-3 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </div>
                        @endif
                    </div>

                    {{-- Content --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-1.5 flex-wrap">
                            <span class="text-xs font-bold text-slate-700 truncate">
                                {{ $log->user?->name ?? $log->user?->username ?? '—' }}
                            </span>
                            {{-- Action badge --}}
                            <span class="inline-flex items-center px-1.5 py-0.5 text-[9px] font-bold rounded uppercase tracking-wider
                                {{ $isGrant ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }}">
                                {{ strtoupper($log->action ?? '—') }}
                            </span>
                            @if ($log->system)
                            <span class="text-[10px] text-slate-400 font-medium truncate">→ {{ $log->system->name }}</span>
                            @endif
                        </div>

                        @if (!$isSuccess && $log->error_message)
                        <p class="text-[10px] text-red-400 font-medium mt-0.5 truncate">
                            {{ Str::limit($log->error_message, 60) }}
                        </p>
                        @endif

                        <div class="flex items-center gap-2 mt-1">
                            @if ($log->performedBy && $log->performed_by !== $log->user_id)
                            <span class="text-[10px] text-slate-300 font-medium truncate">by {{ $log->performedBy->username }}</span>
                            @endif
                            <span class="text-[10px] text-slate-300 ml-auto whitespace-nowrap tabular-nums"
                                  title="{{ $log->created_at->format('d/m/Y H:i:s') }}">
                                {{ $log->created_at->diffForHumans() }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="flex flex-col items-center justify-center py-14 text-slate-300">
                <svg class="w-8 h-8 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <p class="text-xs font-medium">ยังไม่มี Sync Log</p>
            </div>
            @endforelse
        </div>

    </div>

</div>

@endsection
