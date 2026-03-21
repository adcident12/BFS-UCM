@extends('layouts.app')

@section('title', 'ระบบที่เชื่อมต่อ')
@section('header', 'ระบบที่เชื่อมต่อ')

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}" class="text-slate-400 hover:text-slate-600 font-medium transition-colors whitespace-nowrap">Dashboard</a>
    <svg class="w-3.5 h-3.5 text-slate-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    <span class="font-semibold text-slate-800 truncate">ระบบที่เชื่อมต่อ</span>
@endsection

@section('content')

{{-- Header bar --}}
<div class="flex flex-wrap items-center justify-between mb-7 gap-3">
    <div>
        <p class="text-sm text-slate-500 font-medium">ระบบทั้งหมด {{ $systems->count() }} ระบบ</p>
    </div>
    @if (auth()->user()->isSuperAdmin())
    <a href="{{ route('systems.create') }}"
       class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl shadow-sm shadow-indigo-200 transition-all duration-150 hover:-translate-y-0.5 hover:shadow-md hover:shadow-indigo-200">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        เพิ่มระบบใหม่
    </a>
    @endif
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
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
                       class="{{ auth()->user()->isSuperAdmin() ? '' : 'flex-1 text-center' }} px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition-colors duration-150 {{ auth()->user()->isSuperAdmin() ? 'flex-1 text-center' : '' }}">
                        ดูรายละเอียด
                    </a>
                    @if (auth()->user()->isSuperAdmin())
                    <a href="{{ route('systems.edit', $system) }}"
                       class="px-4 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-semibold rounded-xl transition-colors duration-150">
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
            <h3 class="text-base font-bold text-slate-700 mb-1">ยังไม่มีระบบ</h3>
            <p class="text-sm text-slate-400 mb-6">เพิ่มระบบแรกเพื่อเริ่มจัดการสิทธิ์ผู้ใช้</p>
            @if (auth()->user()->isSuperAdmin())
            <a href="{{ route('systems.create') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl transition-colors shadow-sm shadow-indigo-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                เพิ่มระบบใหม่
            </a>
            @endif
        </div>
    @endforelse
</div>
@endsection
