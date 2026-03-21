@extends('layouts.app')

@section('title', 'Connector Wizard')

@section('content')
<div class="max-w-5xl mx-auto py-8 px-4">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Connector Wizard</h1>
            <p class="text-sm text-gray-500 mt-0.5">เชื่อมต่อฐานข้อมูลระบบภายนอกเข้ากับ UCM แบบ No-Code</p>
        </div>
        @if(auth()->user()->isSuperAdmin())
        <a href="{{ route('connectors.create') }}" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            สร้าง Connector ใหม่
        </a>
        @endif
    </div>

    {{-- Flash --}}
    @if(session('success'))
    <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg text-sm text-green-700 font-medium">
        {{ session('success') }}
    </div>
    @endif

    {{-- Empty State --}}
    @if($configs->isEmpty())
    <div class="bg-white rounded-xl border border-dashed border-gray-300 p-16 text-center">
        <div class="text-5xl mb-4">🔌</div>
        <h2 class="text-lg font-semibold text-gray-700 mb-2">ยังไม่มี Connector</h2>
        <p class="text-sm text-gray-500 mb-6">สร้าง Connector เพื่อเชื่อมต่อฐานข้อมูลระบบภายนอกกับ UCM</p>
        @if(auth()->user()->isSuperAdmin())
        <a href="{{ route('connectors.create') }}" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-5 py-2 rounded-lg transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            สร้าง Connector แรก
        </a>
        @endif
    </div>
    @else
    {{-- Grid --}}
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        @foreach($configs as $config)
        @php $system = $config->system; @endphp
        <div class="bg-white rounded-xl border border-gray-200 p-5 flex flex-col gap-4">
            {{-- Top --}}
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-full flex items-center justify-center text-xl flex-shrink-0"
                    style="background-color: {{ $system?->color ?? '#6366f1' }}22; color: {{ $system?->color ?? '#6366f1' }}">
                    {{ $system?->icon ?? '🔌' }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-gray-900 truncate">{{ $system?->name ?? '(ระบบถูกลบ)' }}</p>
                    <p class="text-xs text-gray-400 font-mono">{{ $system?->slug ?? '-' }}</p>
                </div>
                <span class="px-2 py-0.5 rounded-full text-xs font-semibold
                    {{ $system?->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                    {{ $system?->is_active ? 'Active' : 'Inactive' }}
                </span>
            </div>

            {{-- Details --}}
            <div class="grid grid-cols-2 gap-x-4 gap-y-1 text-xs">
                <div class="text-gray-500">Driver</div>
                <div class="font-mono text-gray-700">{{ $config->db_driver }}</div>
                <div class="text-gray-500">Host</div>
                <div class="font-mono text-gray-700 truncate">{{ $config->db_host }}:{{ $config->db_port }}</div>
                <div class="text-gray-500">Database</div>
                <div class="font-mono text-gray-700 truncate">{{ $config->db_name }}</div>
                <div class="text-gray-500">ตาราง Users</div>
                <div class="font-mono text-gray-700">{{ $config->user_table }}</div>
                <div class="text-gray-500">Permission Mode</div>
                <div class="font-mono text-gray-700">{{ $config->permission_mode }}</div>
            </div>

            {{-- Actions --}}
            @if(auth()->user()->isSuperAdmin())
            <div class="flex gap-2 pt-1 border-t border-gray-100">
                @if($system)
                <a href="{{ route('systems.show', $system) }}" class="flex-1 text-center text-xs font-medium text-indigo-600 hover:text-indigo-800 py-1.5 rounded-lg hover:bg-indigo-50 transition">
                    ดูระบบ
                </a>
                @endif
                <a href="{{ route('connectors.edit', $config) }}" class="flex-1 text-center text-xs font-medium text-gray-600 hover:text-gray-900 py-1.5 rounded-lg hover:bg-gray-50 transition">
                    แก้ไข Connector
                </a>
                <form method="POST" action="{{ route('connectors.destroy', $config) }}" onsubmit="return confirm('ลบ Connector นี้?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-xs font-medium text-red-500 hover:text-red-700 py-1.5 px-3 rounded-lg hover:bg-red-50 transition">
                        ลบ
                    </button>
                </form>
            </div>
            @endif
        </div>
        @endforeach
    </div>

    {{-- How it works --}}
    <div class="mt-10 bg-indigo-50 rounded-xl p-6">
        <h3 class="text-sm font-semibold text-indigo-900 mb-3">วิธีการทำงานของ Connector</h3>
        <div class="grid grid-cols-1 gap-3 md:grid-cols-3 text-sm text-indigo-800">
            <div class="flex gap-3">
                <span class="font-bold text-indigo-400 text-lg leading-none">1</span>
                <p>กำหนดการเชื่อมต่อฐานข้อมูลและ mapping คอลัมน์ผ่าน Wizard</p>
            </div>
            <div class="flex gap-3">
                <span class="font-bold text-indigo-400 text-lg leading-none">2</span>
                <p>UCM สร้าง DynamicAdapter ที่อ่าน Config ในการ sync สิทธิ์โดยอัตโนมัติ</p>
            </div>
            <div class="flex gap-3">
                <span class="font-bold text-indigo-400 text-lg leading-none">3</span>
                <p>สิทธิ์ที่ Admin กำหนดใน UCM จะถูก sync ไปยังฐานข้อมูลระบบปลายทางทันที</p>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
