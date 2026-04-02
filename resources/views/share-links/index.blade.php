@extends('layouts.app')

@section('title', 'Share Links — Permission Matrix')

@section('breadcrumbs')
<a href="{{ route('dashboard') }}" class="text-slate-400 hover:text-slate-600 font-medium transition-colors whitespace-nowrap">Dashboard</a>
<svg class="w-3.5 h-3.5 text-slate-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
<span class="font-semibold text-slate-800 truncate">Share Links</span>
@endsection

@section('content')

{{-- Hero Banner --}}
<div class="mb-7 relative overflow-hidden rounded-2xl"
     style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 45%, #0c1a2e 100%)">
    <div class="absolute inset-0 pointer-events-none overflow-hidden">
        <div class="absolute -top-12 -right-12 w-72 h-72 bg-violet-500/6 rounded-full blur-2xl"></div>
        <div class="absolute -bottom-16 left-24 w-56 h-56 bg-indigo-400/5 rounded-full blur-2xl"></div>
        <div class="absolute inset-0"
             style="background-image:linear-gradient(rgba(255,255,255,0.025) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,0.025) 1px,transparent 1px);background-size:28px 28px"></div>
    </div>
    <div class="relative px-6 md:px-8 py-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-white/8 backdrop-blur-sm rounded-2xl flex items-center justify-center flex-shrink-0 border border-white/10">
                <svg class="w-6 h-6 text-violet-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                          d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                </svg>
            </div>
            <div>
                <h2 class="text-lg font-bold text-white">Share Links</h2>
                <p class="text-slate-400 text-xs font-medium mt-0.5">ลิงก์แชร์ดู Permission Matrix · ไม่ต้อง Login</p>
            </div>
        </div>
        <button onclick="document.getElementById('modal-create').style.display='flex'"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-violet-500/20 hover:bg-violet-500/30 border border-violet-400/30 rounded-xl text-violet-300 hover:text-violet-200 text-xs font-semibold transition-all cursor-pointer">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
            </svg>
            สร้าง Share Link ใหม่
        </button>
    </div>
</div>

{{-- Share Links List --}}
<div class="space-y-3">
    @forelse ($shareLinks as $link)
        @php
            $isExpired  = $link->expires_at->isPast();
            $isRevoked  = ! $link->is_active;
            $isDead     = $isExpired || $isRevoked;
            $shareUrl   = route('share.matrix', $link->token);

            // Days remaining
            $daysLeft   = $isDead ? 0 : (int) now()->diffInDays($link->expires_at, false);
            $totalDays  = (int) $link->created_at->diffInDays($link->expires_at);
            $pct        = $totalDays > 0 ? min(100, round(($daysLeft / $totalDays) * 100)) : 0;
        @endphp
        <div class="bg-white rounded-2xl shadow-sm ring-1 ring-slate-100 overflow-hidden {{ $isDead ? 'opacity-60' : '' }}">

            {{-- Top accent bar --}}
            @if (! $isDead)
                <div class="h-0.5 w-full" style="background: linear-gradient(90deg, #7c3aed, #6366f1)"></div>
            @endif

            <div class="px-6 py-5">
                <div class="flex items-start gap-4">

                    {{-- Icon --}}
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 mt-0.5
                                {{ $isRevoked ? 'bg-red-50' : ($isExpired ? 'bg-slate-50' : 'bg-violet-50') }}">
                        @if ($isRevoked)
                            <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                            </svg>
                        @elseif ($isExpired)
                            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        @else
                            <svg class="w-5 h-5 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                            </svg>
                        @endif
                    </div>

                    {{-- Main info --}}
                    <div class="flex-1 min-w-0">

                        {{-- Label + badges --}}
                        <div class="flex items-center gap-2 flex-wrap mb-2.5">
                            <span class="font-bold text-slate-800 text-sm leading-tight">{{ $link->label }}</span>
                            @if ($isRevoked)
                                <span class="inline-flex items-center gap-1 text-[11px] font-bold px-2 py-0.5 rounded-full bg-red-50 text-red-600 border border-red-100">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-400 flex-shrink-0"></span>ถูกยกเลิก
                                </span>
                            @elseif ($isExpired)
                                <span class="inline-flex items-center gap-1 text-[11px] font-bold px-2 py-0.5 rounded-full bg-slate-50 text-slate-500 border border-slate-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-300 flex-shrink-0"></span>หมดอายุ
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 text-[11px] font-bold px-2 py-0.5 rounded-full bg-green-50 text-green-700 border border-green-100">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500 flex-shrink-0 animate-pulse"></span>ใช้งานได้
                                </span>
                            @endif
                        </div>

                        {{-- URL row --}}
                        <div class="flex items-center gap-2 mb-3 bg-slate-50 rounded-xl px-3 py-2 border border-slate-100">
                            <svg class="w-3.5 h-3.5 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                            </svg>
                            <code class="text-xs text-slate-500 font-mono flex-1 truncate min-w-0">{{ $shareUrl }}</code>
                            <div class="flex items-center gap-1 flex-shrink-0">
                                <button onclick="copyUrl('{{ $shareUrl }}', this)"
                                        class="inline-flex items-center gap-1 px-2.5 py-1 text-[11px] font-semibold text-slate-500 hover:text-violet-600 hover:bg-violet-50 rounded-lg transition-all cursor-pointer">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                    </svg>
                                    คัดลอก
                                </button>
                                @if (! $isDead)
                                <a href="{{ $shareUrl }}" target="_blank"
                                   class="inline-flex items-center gap-1 px-2.5 py-1 text-[11px] font-semibold text-slate-500 hover:text-violet-600 hover:bg-violet-50 rounded-lg transition-all">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                    </svg>
                                    เปิด
                                </a>
                                @endif
                            </div>
                        </div>

                        {{-- Metadata row --}}
                        <div class="flex flex-wrap items-center gap-x-4 gap-y-1.5 text-[11px] text-slate-400 font-medium">
                            {{-- Expiry + progress --}}
                            <span class="flex items-center gap-1.5">
                                <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                @if ($isExpired)
                                    <span class="text-slate-400">หมดอายุ {{ $link->expires_at->format('d/m/Y') }}</span>
                                @elseif ($isRevoked)
                                    <span>หมดอายุ {{ $link->expires_at->format('d/m/Y') }}</span>
                                @else
                                    <span class="{{ $daysLeft <= 1 ? 'text-red-500 font-semibold' : ($daysLeft <= 3 ? 'text-amber-500 font-semibold' : '') }}">
                                        เหลือ {{ $daysLeft }} วัน ({{ $link->expires_at->format('d/m/Y') }})
                                    </span>
                                @endif
                            </span>

                            {{-- Views --}}
                            <span class="flex items-center gap-1.5">
                                <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                {{ number_format($link->view_count) }} ครั้ง
                            </span>

                            {{-- Creator --}}
                            <span class="flex items-center gap-1.5">
                                <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                {{ $link->creator?->name ?? '—' }}
                            </span>

                            {{-- Filters --}}
                            @if ($link->hasFilters())
                                <span class="flex items-center gap-1.5 text-violet-500">
                                    <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                                    </svg>
                                    @php $filterParts = []; @endphp
                                    @if (! empty($link->filter_system_ids)) @php $filterParts[] = count($link->filter_system_ids).' ระบบ' @endphp @endif
                                    @if (! empty($link->filter_departments)) @php $filterParts[] = count($link->filter_departments).' แผนก' @endphp @endif
                                    @if (! empty($link->filter_usernames)) @php $filterParts[] = count($link->filter_usernames).' user' @endphp @endif
                                    กรอง: {{ implode(' · ', $filterParts) }}
                                </span>
                            @else
                                <span class="flex items-center gap-1.5 text-slate-300">
                                    <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                                    </svg>
                                    แสดงทุกระบบ/แผนก
                                </span>
                            @endif
                        </div>

                        {{-- Lifetime progress bar (active only) --}}
                        @if (! $isDead)
                            <div class="mt-3">
                                <div class="w-full h-1 bg-slate-100 rounded-full overflow-hidden">
                                    <div class="h-full rounded-full transition-all
                                                {{ $pct > 50 ? 'bg-violet-400' : ($pct > 20 ? 'bg-amber-400' : 'bg-red-400') }}"
                                         style="width: {{ $pct }}%"></div>
                                </div>
                            </div>
                        @endif

                    </div>

                    {{-- Actions --}}
                    <div class="flex-shrink-0 flex flex-col items-end gap-2">
                        <form id="form-toggle-{{ $link->id }}" method="POST" action="{{ route('share-links.toggle', $link) }}">
                            @csrf
                            <button type="button"
                                    onclick="confirmToggle('form-toggle-{{ $link->id }}', {{ $link->is_active ? 'true' : 'false' }}, '{{ addslashes($link->label) }}')"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-xl border transition-all cursor-pointer
                                           {{ $link->is_active
                                               ? 'border-slate-200 text-slate-500 hover:border-red-200 hover:text-red-600 hover:bg-red-50'
                                               : 'border-green-200 text-green-700 hover:bg-green-50' }}">
                                @if ($link->is_active)
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                    </svg>
                                    ยกเลิก
                                @else
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                    </svg>
                                    เปิดใช้อีกครั้ง
                                @endif
                            </button>
                        </form>
                        @if ($link->last_viewed_at)
                            <span class="text-[10px] text-slate-300 font-medium text-right">
                                ดูล่าสุด {{ $link->last_viewed_at->diffForHumans() }}
                            </span>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    @empty
        <div class="bg-white rounded-2xl shadow-sm ring-1 ring-slate-100 px-6 py-20 text-center">
            <div class="w-16 h-16 bg-slate-50 rounded-2xl flex items-center justify-center mx-auto mb-5 border border-slate-100">
                <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                </svg>
            </div>
            <p class="text-sm font-bold text-slate-400 mb-1">ยังไม่มี Share Link</p>
            <p class="text-xs text-slate-300 mb-5">สร้างลิงก์เพื่อแชร์ Permission Matrix ให้ผู้ที่ไม่มีบัญชีในระบบ</p>
            <button onclick="document.getElementById('modal-create').style.display='flex'"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-violet-600 hover:bg-violet-700 text-white text-xs font-semibold rounded-xl transition-colors cursor-pointer">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                สร้าง Share Link แรก
            </button>
        </div>
    @endforelse
</div>

{{-- Create Modal --}}
<div id="modal-create" style="display:none"
     class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/50 backdrop-blur-sm px-4"
     onclick="if(event.target===this)this.style.display='none'">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg border border-slate-100 overflow-hidden">

        {{-- Modal header --}}
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between"
             style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%)">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-violet-500/20 rounded-xl flex items-center justify-center border border-violet-400/20">
                    <svg class="w-4 h-4 text-violet-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-white leading-tight">สร้าง Share Link ใหม่</h3>
                    <p class="text-[11px] text-slate-400 font-medium">Permission Matrix · อ่านอย่างเดียว</p>
                </div>
            </div>
            <button onclick="document.getElementById('modal-create').style.display='none'"
                    class="w-7 h-7 flex items-center justify-center rounded-lg text-slate-400 hover:bg-white/10 transition-colors cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form method="POST" action="{{ route('share-links.store') }}" class="px-6 py-5 space-y-4">
            @csrf

            {{-- Label + Expiry row --}}
            <div class="grid grid-cols-3 gap-3">
                <div class="col-span-2">
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">ชื่อลิงก์ <span class="text-red-400">*</span></label>
                    <input type="text" name="label" value="{{ old('label') }}" required
                           placeholder="เช่น รายงานสิทธิ์ Q2-2026"
                           class="w-full px-3 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-400 placeholder-slate-300 @error('label') border-red-300 @enderror">
                    @error('label')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">อายุ (วัน) <span class="text-red-400">*</span></label>
                    <input type="number" name="expires_days" value="{{ old('expires_days', 7) }}" min="1" max="365" required
                           class="w-full px-3 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-400 text-center font-semibold @error('expires_days') border-red-300 @enderror">
                    @error('expires_days')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Separator --}}
            <div class="flex items-center gap-3">
                <div class="h-px flex-1 bg-slate-100"></div>
                <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">ตัวกรอง (ไม่บังคับ)</span>
                <div class="h-px flex-1 bg-slate-100"></div>
            </div>

            {{-- Filter Systems --}}
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1.5">ระบบที่แสดง
                    <span class="text-slate-400 font-normal">(ไม่เลือก = ทุกระบบ)</span>
                </label>
                <div class="border border-slate-200 rounded-xl p-3 space-y-1.5 max-h-36 overflow-y-auto bg-slate-50/50">
                    @foreach ($systems as $system)
                        <label class="flex items-center gap-2.5 cursor-pointer group py-0.5">
                            <input type="checkbox" name="filter_system_ids[]" value="{{ $system->id }}"
                                   {{ in_array($system->id, old('filter_system_ids', [])) ? 'checked' : '' }}
                                   class="w-4 h-4 rounded border-slate-300 text-violet-600 focus:ring-violet-300">
                            <div class="w-2 h-2 rounded-full flex-shrink-0" style="background: {{ $system->color ?? '#6366f1' }}"></div>
                            <span class="text-xs text-slate-700 font-medium group-hover:text-slate-900 transition-colors">{{ $system->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Filter Departments --}}
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1.5">แผนก
                    <span class="text-slate-400 font-normal">(ไม่กรอก = ทุกแผนก)</span>
                </label>
                <div id="dept-tags" class="flex flex-wrap gap-1.5 mb-2 empty:hidden"></div>
                <input type="text" id="dept-input" placeholder="พิมพ์ชื่อแผนกแล้วกด Enter…"
                       class="w-full px-3 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-400 placeholder-slate-300">
                <div id="dept-hidden-inputs"></div>
            </div>

            {{-- Filter Usernames --}}
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Username
                    <span class="text-slate-400 font-normal">(คั่นด้วย Enter / ลูกน้ำ / เว้นวรรค)</span>
                </label>
                <textarea name="filter_usernames" rows="3"
                          placeholder="jsmith&#10;tanya.s&#10;panya001"
                          class="w-full px-3 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-400 placeholder-slate-300 font-mono resize-none">{{ old('filter_usernames') }}</textarea>
            </div>

            <div class="flex justify-end gap-2 pt-1 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('modal-create').style.display='none'"
                        class="px-4 py-2.5 text-xs font-semibold text-slate-600 border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors cursor-pointer">
                    ยกเลิก
                </button>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2.5 text-xs font-semibold text-white bg-violet-600 hover:bg-violet-700 rounded-xl shadow-sm shadow-violet-200 transition-all hover:-translate-y-0.5 hover:shadow-md hover:shadow-violet-200 cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                    </svg>
                    สร้าง Share Link
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function copyUrl(url, btn) {
    navigator.clipboard.writeText(url).then(function () {
        var prev = btn.innerHTML;
        btn.innerHTML = '<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg> คัดลอกแล้ว!';
        btn.classList.add('text-green-700', 'bg-green-50');
        setTimeout(function () {
            btn.innerHTML = prev;
            btn.classList.remove('text-green-700', 'bg-green-50');
        }, 2000);
    });
}

function confirmToggle(formId, isActive, label) {
    var title  = isActive ? 'ยกเลิก Share Link?' : 'เปิดใช้งาน Share Link อีกครั้ง?';
    var detail = isActive
        ? 'ลิงก์ "' + label + '" จะไม่สามารถเข้าถึงได้ทันที'
        : 'ลิงก์ "' + label + '" จะกลับมาใช้งานได้อีกครั้ง';
    askConfirm(formId, title, detail);
}

(function () {
    var input      = document.getElementById('dept-input');
    var tagWrap    = document.getElementById('dept-tags');
    var hiddenWrap = document.getElementById('dept-hidden-inputs');
    if (! input) { return; }
    var tags = [];

    function renderTags() {
        tagWrap.innerHTML   = '';
        hiddenWrap.innerHTML = '';
        tags.forEach(function (t, i) {
            var span = document.createElement('span');
            span.className = 'inline-flex items-center gap-1 px-2 py-1 bg-violet-50 border border-violet-200 text-violet-700 text-xs font-medium rounded-lg';
            span.innerHTML = t + '<button type="button" onclick="removeDept(' + i + ')" class="ml-0.5 text-violet-400 hover:text-violet-700 cursor-pointer leading-none">×</button>';
            tagWrap.appendChild(span);
            var h = document.createElement('input');
            h.type  = 'hidden';
            h.name  = 'filter_departments[]';
            h.value = t;
            hiddenWrap.appendChild(h);
        });
    }

    window.removeDept = function (i) { tags.splice(i, 1); renderTags(); };

    input.addEventListener('keydown', function (e) {
        if (e.key !== 'Enter') { return; }
        e.preventDefault();
        var val = input.value.trim();
        if (val && ! tags.includes(val)) { tags.push(val); renderTags(); }
        input.value = '';
    });
})();

@if ($errors->any())
    document.addEventListener('DOMContentLoaded', function () {
        document.getElementById('modal-create').style.display = 'flex';
    });
@endif
</script>
@endpush

@endsection
