@extends('layouts.app')

@section('title', 'ผู้ใช้ที่ไม่ได้ใช้งาน')
@section('header', 'ผู้ใช้ที่ไม่ได้ใช้งาน')

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}" class="text-slate-400 hover:text-slate-600 font-medium transition-colors whitespace-nowrap">Dashboard</a>
    <svg class="w-3.5 h-3.5 text-slate-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    <a href="{{ route('users.index') }}" class="text-slate-400 hover:text-slate-600 font-medium transition-colors whitespace-nowrap">จัดการผู้ใช้</a>
    <svg class="w-3.5 h-3.5 text-slate-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    <span class="font-semibold text-slate-800 truncate">ไม่ได้ใช้งาน</span>
@endsection

@section('content')

{{-- Filter / Search bar --}}
<div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 mb-6">
    {{-- Search --}}
    <form method="GET" class="flex gap-2 w-full sm:flex-1 sm:max-w-sm">
        <input type="hidden" name="days" value="{{ $days }}">
        <div class="relative flex-1">
            <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
                </svg>
            </div>
            <input type="text" name="search" value="{{ $search ?? '' }}"
                   placeholder="ค้นหาชื่อ, username, แผนก..."
                   class="w-full pl-10 pr-4 py-2.5 border border-slate-200 rounded-xl text-sm text-slate-800 placeholder-slate-400
                          focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 transition-all bg-white shadow-sm">
            @if ($search)
                <a href="{{ route('users.inactive', ['days' => $days]) }}"
                   class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-400 hover:text-slate-600 transition-colors"
                   title="ล้างการค้นหา">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </a>
            @endif
        </div>
        <button type="submit"
                class="px-4 py-2.5 bg-slate-700 hover:bg-slate-800 text-white text-sm font-semibold rounded-xl transition-colors shadow-sm">
            ค้นหา
        </button>
    </form>

    {{-- Day filter tabs --}}
    <div class="flex items-center gap-2 flex-shrink-0">
        @foreach ([30, 60, 90] as $d)
            <a href="{{ route('users.inactive', ['days' => $d, 'search' => $search]) }}"
               class="px-4 py-2.5 text-xs font-semibold rounded-xl transition-all duration-150 {{ $days === $d ? 'bg-indigo-600 text-white shadow-sm shadow-indigo-200' : 'bg-white text-slate-600 hover:bg-slate-50 ring-1 ring-slate-200' }}">
                {{ $d }} วัน
            </a>
        @endforeach
    </div>
</div>

{{-- Result summary --}}
<p class="text-sm text-slate-500 font-medium mb-4">
    พบ <span class="font-bold text-slate-700">{{ $users->total() }}</span> คน
    ที่ไม่ได้ Login ใน {{ $days }} วัน
    @if ($search)
        <span class="text-slate-400">· ค้นหา "<span class="text-slate-600 font-semibold">{{ $search }}</span>"</span>
    @endif
</p>

{{-- Alert note --}}
<div class="mb-5 flex items-start gap-3 bg-amber-50 border border-amber-200 rounded-xl px-4 py-3">
    <svg class="w-4 h-4 text-amber-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    <p class="text-xs text-amber-700 font-medium leading-relaxed">
        รายชื่อผู้ใช้ที่ไม่ได้ Login เกิน {{ $days }} วัน หรือไม่เคย Login มาก่อน — ควรพิจารณาตรวจสอบสถานะและสิทธิ์การเข้าถึง
        <br>
        <span class="text-amber-500">หมายเหตุ: ข้อมูล Login จะถูกบันทึกตั้งแต่เวอร์ชันนี้เป็นต้นไป ผู้ใช้ที่ไม่เคย Login ในระบบนี้จะแสดง "ไม่มีข้อมูล"</span>
    </p>
</div>

@if ($users->isEmpty())
    <div class="bg-white rounded-2xl shadow-sm ring-1 ring-slate-100 p-16 text-center">
        <div class="w-16 h-16 bg-emerald-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <p class="text-sm font-bold text-slate-500">ไม่พบผู้ใช้ที่ตรงเงื่อนไข</p>
        <p class="text-xs text-slate-400 mt-1">
            @if ($search)
                ไม่พบผลลัพธ์สำหรับ "{{ $search }}" ใน {{ $days }} วัน
            @else
                ผู้ใช้ทุกคน Login ภายใน {{ $days }} วันที่ผ่านมา
            @endif
        </p>
    </div>
@else
    <div class="bg-white rounded-2xl shadow-sm ring-1 ring-slate-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-100 bg-slate-50/70">
                    <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">ผู้ใช้</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide hidden sm:table-cell">แผนก</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide hidden md:table-cell">สิทธิ์</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Login ล่าสุด</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @foreach ($users as $user)
                    <tr class="hover:bg-slate-50/60 transition-colors group">
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-xl flex items-center justify-center text-sm font-bold text-white flex-shrink-0"
                                     style="background: linear-gradient(135deg, #6366f1, #8b5cf6)">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <div class="font-semibold text-slate-800 text-sm truncate">{{ $user->name }}</div>
                                    <code class="text-[11px] text-slate-400 font-mono">{{ $user->username }}</code>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3.5 hidden sm:table-cell">
                            <span class="text-xs text-slate-500 font-medium">{{ $user->department ?: '—' }}</span>
                        </td>
                        <td class="px-5 py-3.5 hidden md:table-cell">
                            @if ($user->system_permissions_count > 0)
                                <span class="inline-flex items-center gap-1 text-xs font-semibold text-amber-700 bg-amber-50 px-2 py-0.5 rounded-lg ring-1 ring-amber-200/50">
                                    {{ $user->system_permissions_count }} สิทธิ์
                                </span>
                            @else
                                <span class="text-xs text-slate-400 font-medium">ไม่มีสิทธิ์</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5">
                            @if ($user->last_login_at)
                                <div class="text-xs font-medium text-rose-600">
                                    {{ $user->last_login_at->diffForHumans() }}
                                </div>
                                <div class="text-[11px] text-slate-400 mt-0.5">{{ $user->last_login_at->format('d M Y') }}</div>
                            @else
                                <span class="text-xs text-slate-400 font-medium italic">ไม่มีข้อมูล</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-right">
                            <a href="{{ route('users.show', $user) }}"
                               class="inline-flex items-center gap-1 text-xs font-semibold text-indigo-600 hover:text-indigo-800 bg-indigo-50 hover:bg-indigo-100 px-3 py-1.5 rounded-lg transition-colors opacity-0 group-hover:opacity-100">
                                จัดการ
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Pagination --}}
        @if ($users->hasPages())
            <div class="px-5 py-3 border-t border-slate-100 bg-slate-50/50">
                {{ $users->links() }}
            </div>
        @endif
    </div>
@endif

@endsection
