@extends('layouts.app')

@section('title', 'Connector Wizard')

@section('breadcrumbs')
<a href="{{ route('dashboard') }}" class="text-slate-400 hover:text-slate-600 font-medium transition-colors whitespace-nowrap">Dashboard</a>
<svg class="w-3.5 h-3.5 text-slate-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
<span class="font-semibold text-slate-800 truncate">Connector Wizard</span>
@endsection

@section('content')

@php
    $canManageConnectors = auth()->user()->canAccess('connector_wizard');
@endphp

<div class="max-w-5xl mx-auto">

    {{-- Header --}}
    <div class="flex items-start justify-between mb-6">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Connector Wizard</h1>
            <p class="text-sm text-slate-500 mt-0.5">เชื่อมต่อฐานข้อมูลระบบภายนอกเข้ากับ UCM แบบ No-Code</p>
        </div>
        @if($canManageConnectors)
        <a href="{{ route('connectors.create') }}"
           class="inline-flex items-center gap-2 text-sm font-semibold text-white bg-gradient-to-br from-indigo-500 to-violet-600 px-4 py-2.5 rounded-xl shadow-md shadow-indigo-200 hover:opacity-90 active:scale-95 transition-all whitespace-nowrap">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            สร้าง Connector ใหม่
        </a>
        @endif
    </div>


    @if($configs->isEmpty())

    {{-- Empty State --}}
    <div class="bg-white rounded-2xl border border-dashed border-slate-300 p-16 text-center">
        <div class="w-16 h-16 bg-indigo-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"/>
            </svg>
        </div>
        <h2 class="text-base font-bold text-slate-700 mb-1">ยังไม่มี Connector</h2>
        <p class="text-sm text-slate-500 mb-5">สร้าง Connector เพื่อเชื่อมต่อฐานข้อมูลระบบภายนอกกับ UCM</p>
        @if($canManageConnectors)
        <a href="{{ route('connectors.create') }}"
           class="inline-flex items-center gap-2 text-sm font-semibold text-white bg-gradient-to-br from-indigo-500 to-violet-600 px-5 py-2.5 rounded-xl shadow-md shadow-indigo-200 hover:opacity-90 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            สร้าง Connector แรก
        </a>
        @endif
    </div>

    @else

    {{-- Connector Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
        @foreach($configs as $config)
        @php
            $system = $config->system;
            $color  = $system?->color ?? '#6366f1';
            $permModes = [
                'junction' => ['label' => 'Junction',  'bg' => 'bg-indigo-100', 'text' => 'text-indigo-700'],
                'column'   => ['label' => 'Column',    'bg' => 'bg-violet-100', 'text' => 'text-violet-700'],
                'manual'   => ['label' => 'Manual',    'bg' => 'bg-amber-100',  'text' => 'text-amber-700'],
            ];
            $pm = $permModes[$config->permission_mode] ?? ['label' => $config->permission_mode, 'bg' => 'bg-slate-100', 'text' => 'text-slate-600'];
        @endphp
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col">

            {{-- Card Header --}}
            <div class="flex items-center gap-3.5 px-5 py-4 border-b border-slate-100">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 overflow-hidden"
                     style="background-color: {{ $color }}22;">
                    @if(filled($system?->icon))
                        <span class="text-xl leading-none">{{ $system->icon }}</span>
                    @else
                        <span class="text-sm font-bold" style="color: {{ $color }}">
                            {{ mb_strtoupper(mb_substr($system?->name ?? '?', 0, 1)) }}
                        </span>
                    @endif
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-slate-900 truncate">{{ $system?->name ?? '(ระบบถูกลบ)' }}</p>
                    <p class="text-[11px] text-slate-400 font-mono mt-0.5">{{ $system?->slug ?? '-' }}</p>
                </div>
                <span class="flex-shrink-0 px-2.5 py-0.5 rounded-full text-[11px] font-semibold
                    {{ $system?->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                    {{ $system?->is_active ? 'Active' : 'Inactive' }}
                </span>
            </div>

            {{-- Details --}}
            <div class="px-5 py-3.5 flex-1">
                <div class="grid gap-y-1.5 text-xs" style="grid-template-columns: auto 1fr">
                    <span class="text-slate-400 pr-4">Driver</span>
                    <span class="font-mono text-slate-700">{{ $config->db_driver }}</span>

                    <span class="text-slate-400 pr-4">Host</span>
                    <span class="font-mono text-slate-700 truncate">{{ $config->db_host }}:{{ $config->db_port }}</span>

                    <span class="text-slate-400 pr-4">Database</span>
                    <span class="font-mono text-slate-700 truncate">{{ $config->db_name }}</span>

                    <span class="text-slate-400 pr-4">ตาราง Users</span>
                    <span class="font-mono text-slate-700">{{ $config->user_table ?? '-' }}</span>

                    <span class="text-slate-400 pr-4">Permission Mode</span>
                    <span class="flex items-center gap-1.5">
                        <span class="px-1.5 py-0.5 rounded text-[11px] font-bold {{ $pm['bg'] }} {{ $pm['text'] }}">
                            {{ $pm['label'] }}
                        </span>
                        @if($config->perm_def_table)
                        <span class="px-1.5 py-0.5 rounded text-[11px] font-semibold bg-violet-50 text-violet-600 border border-violet-100">
                            2-Way Sync
                        </span>
                        @endif
                    </span>
                </div>
            </div>

            {{-- Actions --}}
            @if($canManageConnectors)
            <div class="flex items-center px-4 py-2.5 border-t border-slate-100 bg-slate-50/60 gap-0.5">
                @if($system)
                <a href="{{ route('systems.show', $system) }}"
                   class="flex-1 text-center text-xs font-semibold text-indigo-600 hover:text-indigo-800 py-1.5 rounded-lg hover:bg-indigo-50 transition">
                    ดูระบบ
                </a>
                <div class="w-px h-4 bg-slate-200 mx-0.5"></div>
                @endif
                <a href="{{ route('connectors.edit', $config) }}"
                   class="flex-1 text-center text-xs font-semibold text-slate-600 hover:text-slate-900 py-1.5 rounded-lg hover:bg-slate-100 transition">
                    แก้ไข Connector
                </a>
                <div class="w-px h-4 bg-slate-200 mx-0.5"></div>
                <form id="del-connector-{{ $config->id }}" method="POST" action="{{ route('connectors.destroy', $config) }}">
                    @csrf @method('DELETE')
                    <button type="button"
                            onclick="askConfirm('del-connector-{{ $config->id }}', 'ลบ Connector นี้?', '{{ addslashes($system?->name ?? $config->db_name) }}')"
                            class="text-xs font-semibold text-rose-500 hover:text-rose-700 py-1.5 px-3 rounded-lg hover:bg-rose-50 transition cursor-pointer">
                        ลบ
                    </button>
                </form>
            </div>
            @endif
        </div>
        @endforeach
    </div>

    {{-- How it works --}}
    <div class="bg-gradient-to-br from-indigo-50 to-violet-50 border border-indigo-100 rounded-2xl p-5">
        <p class="text-[11px] font-bold text-indigo-900 uppercase tracking-wider mb-4">วิธีการทำงานของ Connector</p>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach([
                ['กำหนดการเชื่อมต่อฐานข้อมูลและ mapping คอลัมน์ผ่าน Wizard'],
                ['UCM สร้าง DynamicAdapter ที่อ่าน Config ในการ sync สิทธิ์โดยอัตโนมัติ'],
                ['สิทธิ์ที่ Admin กำหนดใน UCM จะถูก sync ไปยังฐานข้อมูลระบบปลายทางทันที'],
            ] as $i => [$desc])
            <div class="flex items-start gap-3">
                <div class="w-6 h-6 rounded-full bg-indigo-600 text-white text-[11px] font-bold flex items-center justify-center flex-shrink-0 mt-0.5 shadow-sm shadow-indigo-300">
                    {{ $i + 1 }}
                </div>
                <p class="text-sm text-indigo-800 leading-relaxed">{{ $desc }}</p>
            </div>
            @endforeach
        </div>
    </div>

    @endif
</div>
@endsection
