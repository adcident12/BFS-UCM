@extends('layouts.app')

@section('title', isset($editConfig) ? 'แก้ไข Connector' : 'Connector Wizard')

@section('breadcrumbs')
<a href="{{ route('dashboard') }}" class="text-slate-400 hover:text-slate-600 font-medium transition-colors whitespace-nowrap">Dashboard</a>
<svg class="w-3.5 h-3.5 text-slate-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
<a href="{{ route('connectors.index') }}" class="text-slate-400 hover:text-slate-600 font-medium transition-colors whitespace-nowrap">Connector Wizard</a>
<svg class="w-3.5 h-3.5 text-slate-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
<span class="font-semibold text-slate-800 truncate">{{ isset($editConfig) ? 'แก้ไข Connector' : 'สร้างใหม่' }}</span>
@endsection

@section('content')

@php
$inp  = 'w-full px-3 py-2 text-sm text-slate-800 bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/10 placeholder:text-slate-400 transition';
$mono = 'w-full px-3 py-2 text-sm font-mono text-slate-800 bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/10 placeholder:text-slate-400 transition';
$sel  = 'w-full pl-3 pr-8 py-2 text-sm text-slate-800 bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/10 appearance-none transition';
$lbl  = 'block text-[13px] font-semibold text-slate-600 mb-1.5';
$hint = 'text-xs text-slate-400 mt-1';
$btnP = 'inline-flex items-center gap-1.5 px-5 py-2.5 text-sm font-semibold text-white bg-gradient-to-br from-indigo-500 to-violet-600 rounded-xl shadow-md shadow-indigo-200 hover:opacity-90 active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed transition-all';
$btnG = 'inline-flex items-center gap-1.5 px-4 py-2.5 text-sm font-medium text-slate-500 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 hover:text-slate-700 transition';
$btnO = 'inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold text-indigo-600 bg-indigo-50 border border-indigo-200 rounded-lg hover:bg-indigo-100 hover:border-indigo-300 transition whitespace-nowrap';
$icn  = 'w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 bg-gradient-to-br from-indigo-500 to-violet-600 shadow-lg shadow-indigo-200/60';
$arr  = '<div class="pointer-events-none absolute inset-y-0 right-2.5 flex items-center"><svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 20 20"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 8l4 4 4-4"/></svg></div>';
@endphp

<div class="max-w-2xl mx-auto pb-16">

    {{-- ── Step Indicator ── --}}
    @php
    $steps = [
        ['label' => 'ข้อมูลระบบ'],
        ['label' => 'เชื่อมต่อ DB'],
        ['label' => 'ตาราง Users'],
        ['label' => 'Permissions'],
        ['label' => 'ยืนยัน'],
    ];
    @endphp
    <div class="flex items-start mb-8" id="step-indicator">
        @foreach($steps as $i => $step)
        <div class="flex flex-col items-center gap-1.5 flex-shrink-0">
            <div class="w-9 h-9 rounded-full flex items-center justify-center text-xs font-bold border-2 relative z-10 transition-all duration-200 {{ $i === 0 ? 'border-indigo-500 bg-indigo-500 text-white ring-4 ring-indigo-100' : 'border-slate-200 bg-white text-slate-400' }}"
                 data-step-circle="{{ $i + 1 }}">
                <span data-step-num="{{ $i + 1 }}">{{ $i + 1 }}</span>
                <svg class="hidden w-3.5 h-3.5" data-step-check="{{ $i + 1 }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <span class="text-[11px] font-semibold whitespace-nowrap transition-colors hidden sm:block {{ $i === 0 ? 'text-indigo-600' : 'text-slate-400' }}"
                  data-step-label="{{ $i + 1 }}">{{ $step['label'] }}</span>
        </div>
        @if(! $loop->last)
        <div class="flex-1 h-0.5 bg-slate-200 mx-2 mt-[1.125rem] transition-colors" data-step-line="{{ $i + 1 }}"></div>
        @endif
        @endforeach
    </div>

    {{-- ── Alert ── --}}
    <div id="wiz-alert" style="display:none" class="rounded-xl text-sm font-medium mb-4 p-4"></div>

    {{-- ══════════════════════════════════════════════════
         STEP 1 — System Info
    ══════════════════════════════════════════════════ --}}
    <div class="wiz-step" data-step="1">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="flex items-start gap-4 px-6 py-5 border-b border-slate-100 bg-slate-50/50">
                <div class="{{ $icn }}">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-800">ข้อมูลระบบ</h2>
                    <p class="text-xs text-slate-400 mt-0.5">กำหนดชื่อและรายละเอียดของระบบที่ต้องการเชื่อมต่อ</p>
                </div>
            </div>
            <div class="px-6 py-6 space-y-5">

                @if(isset($editConfig))
                    <input type="hidden" id="field_system_id" value="{{ $editConfig->system_id }}">
                    <div class="flex items-center gap-3 p-4 bg-indigo-50 border border-indigo-100 rounded-xl">
                        <div class="w-8 h-8 bg-gradient-to-br from-indigo-500 to-violet-600 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-indigo-700">แก้ไข Connector</p>
                            <p class="text-xs text-indigo-500 mt-0.5">ระบบ: <strong>{{ $editConfig->system->name }}</strong></p>
                        </div>
                    </div>
                @else

                    <div>
                        <label class="{{ $lbl }}">เชื่อมกับระบบที่มีอยู่แล้ว
                            <span class="inline text-[10px] font-bold px-1.5 py-px bg-slate-100 text-slate-400 rounded ml-1">ไม่บังคับ</span>
                        </label>
                        <div class="relative">
                            <select id="field_system_id" class="{{ $sel }}">
                                <option value="">— สร้างระบบใหม่ —</option>
                                @foreach(\App\Models\System::orderBy('name')->get() as $sys)
                                <option value="{{ $sys->id }}">{{ $sys->name }} ({{ $sys->slug }})</option>
                                @endforeach
                            </select>
                            {!! $arr !!}
                        </div>
                        <p class="{{ $hint }}">เลือกเฉพาะถ้าต้องการเพิ่ม Connector ให้ระบบที่มีอยู่แล้ว</p>
                    </div>

                    <hr class="border-t border-slate-100">

                    <div id="new-system-fields" class="space-y-4">
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">ข้อมูลระบบใหม่</p>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="{{ $lbl }}">ชื่อระบบ <span class="text-rose-500">*</span></label>
                                <input type="text" id="field_system_name" class="{{ $inp }}" placeholder="เช่น ระบบซ่อมบำรุง">
                            </div>
                            <div>
                                <label class="{{ $lbl }}">Slug <span class="text-rose-500">*</span></label>
                                <input type="text" id="field_system_slug" class="{{ $mono }}" placeholder="repair-system">
                                <p class="{{ $hint }}">ตัวพิมพ์เล็ก, ตัวเลข และ "-" เท่านั้น</p>
                            </div>
                        </div>

                        <div>
                            <label class="{{ $lbl }}">คำอธิบาย
                                <span class="inline text-[10px] font-bold px-1.5 py-px bg-slate-100 text-slate-400 rounded ml-1">ไม่บังคับ</span>
                            </label>
                            <textarea id="field_system_description" class="{{ $inp }} resize-none" rows="2" placeholder="คำอธิบายสั้นๆ ของระบบนี้"></textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="{{ $lbl }}">สีประจำระบบ</label>
                                <div class="flex gap-2 items-center">
                                    <input type="color" id="field_system_color" value="#6366f1"
                                           class="w-10 h-10 border border-slate-200 rounded-lg p-0.5 cursor-pointer bg-white flex-shrink-0">
                                    <input type="text" id="field_system_color_text" value="#6366f1" class="{{ $mono }}" placeholder="#6366f1">
                                </div>
                            </div>
                            <div>
                                <label class="{{ $lbl }}">ไอคอน (Emoji)
                                    <span class="inline text-[10px] font-bold px-1.5 py-px bg-slate-100 text-slate-400 rounded ml-1">ไม่บังคับ</span>
                                </label>
                                <input type="text" id="field_system_icon" class="{{ $inp }} text-2xl text-center" placeholder="🔧" maxlength="5">
                            </div>
                        </div>
                    </div>

                @endif
            </div>
        </div>
        <div class="flex items-center justify-between mt-5">
            <span class="text-xs text-slate-400 font-semibold">ขั้นที่ 1 จาก 5</span>
            <button class="{{ $btnP }}" onclick="wizNext(1)">
                ถัดไป
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </button>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════
         STEP 2 — Database Connection
    ══════════════════════════════════════════════════ --}}
    <div class="wiz-step hidden" data-step="2">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="flex items-start gap-4 px-6 py-5 border-b border-slate-100 bg-slate-50/50">
                <div class="{{ $icn }}">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-800">การเชื่อมต่อฐานข้อมูล</h2>
                    <p class="text-xs text-slate-400 mt-0.5">กรอกข้อมูล credentials สำหรับเชื่อมต่อ DB ของระบบภายนอก</p>
                </div>
            </div>
            <div class="px-6 py-6 space-y-5">

                {{-- Driver Cards --}}
                <div>
                    <label class="{{ $lbl }}">Database Driver <span class="text-rose-500">*</span></label>
                    <div class="flex gap-3">
                        @foreach(['mysql' => ['MySQL / MariaDB', '🐬', '3306'], 'pgsql' => ['PostgreSQL', '🐘', '5432'], 'sqlsrv' => ['SQL Server', '🪟', '1433']] as $drv => [$drvLabel, $emoji, $port])
                        <label id="driver-card-{{ $drv }}" data-driver
                               class="flex-1 flex flex-col items-center gap-1.5 py-3 px-2 border-2 rounded-xl cursor-pointer text-center transition {{ $drv === 'mysql' ? 'border-indigo-500 bg-indigo-50' : 'border-slate-200 bg-white' }}"
                               onclick="selectDriver('{{ $drv }}')">
                            <input type="radio" name="db_driver" value="{{ $drv }}" {{ $drv === 'mysql' ? 'checked' : '' }} class="hidden" onchange="wizDriverChange(this.value)">
                            <span class="text-2xl leading-none">{{ $emoji }}</span>
                            <span class="text-[11px] font-bold {{ $drv === 'mysql' ? 'text-indigo-600' : 'text-slate-500' }}" data-driver-lbl>{{ $drvLabel }}</span>
                            <span class="text-[10px] text-slate-400 font-mono">:{{ $port }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                <hr class="border-t border-slate-100">

                <div class="grid grid-cols-4 gap-4">
                    <div class="col-span-3">
                        <label class="{{ $lbl }}">Host <span class="text-rose-500">*</span></label>
                        <input type="text" id="field_db_host" class="{{ $mono }}" placeholder="192.168.1.100" value="{{ $editConfig->db_host ?? '' }}">
                    </div>
                    <div>
                        <label class="{{ $lbl }}">Port <span class="text-rose-500">*</span></label>
                        <input type="number" id="field_db_port" class="{{ $mono }}" value="{{ $editConfig->db_port ?? 3306 }}" min="1" max="65535">
                    </div>
                </div>

                <div>
                    <label class="{{ $lbl }}">Database Name <span class="text-rose-500">*</span></label>
                    <input type="text" id="field_db_name" class="{{ $mono }}" placeholder="myapp_db" value="{{ $editConfig->db_name ?? '' }}">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="{{ $lbl }}">Username <span class="text-rose-500">*</span></label>
                        <input type="text" id="field_db_user" class="{{ $mono }}" placeholder="db_readonly" value="{{ $editConfig->db_user ?? '' }}" autocomplete="off">
                    </div>
                    <div>
                        <label class="{{ $lbl }}">Password
                            <span class="inline text-[10px] font-bold px-1.5 py-px bg-slate-100 text-slate-400 rounded ml-1">{{ isset($editConfig) ? 'เว้นว่าง = ไม่เปลี่ยน' : 'ถ้ามี' }}</span>
                        </label>
                        <input type="password" id="field_db_password" class="{{ $mono }}"
                               placeholder="{{ isset($editConfig) ? '••••••••' : 'password' }}" autocomplete="new-password">
                    </div>
                </div>

                <hr class="border-t border-slate-100">

                <button class="{{ $btnO }} w-full justify-center py-2.5" onclick="wizTestConnection()" id="test-conn-btn">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    ทดสอบการเชื่อมต่อ
                </button>
                <div id="conn-result" style="display:none" class="rounded-xl text-sm font-medium p-3 flex items-center gap-2"></div>

            </div>
        </div>
        <div class="flex items-center justify-between mt-5">
            <button class="{{ $btnG }}" onclick="wizPrev(2)">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                ย้อนกลับ
            </button>
            <div class="flex items-center gap-3">
                <span class="text-xs text-slate-400 font-semibold">ขั้นที่ 2 จาก 5</span>
                <button class="{{ $btnP }}" id="step2-next" onclick="wizNext(2)">
                    ถัดไป
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </button>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════
         STEP 3 — User Table
    ══════════════════════════════════════════════════ --}}
    <div class="wiz-step hidden" data-step="3">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="flex items-start gap-4 px-6 py-5 border-b border-slate-100 bg-slate-50/50">
                <div class="{{ $icn }}">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-800">ตาราง Users</h2>
                    <p class="text-xs text-slate-400 mt-0.5">กำหนดตารางและคอลัมน์ที่เก็บข้อมูลผู้ใช้ในระบบภายนอก</p>
                </div>
            </div>
            <div class="px-6 py-6 space-y-5">

                <div>
                    <label class="{{ $lbl }}">ตาราง Users <span class="text-rose-500">*</span></label>
                    <div class="flex gap-2">
                        <div class="relative flex-1">
                            <select id="field_user_table" class="{{ $sel }}" onchange="wizLoadUserColumns()">
                                <option value="">— เลือกตาราง —</option>
                            </select>
                            {!! $arr !!}
                        </div>
                        <button class="{{ $btnO }} flex-shrink-0" onclick="wizLoadTables('user_table','wizLoadUserColumns')" title="โหลดรายการตาราง">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                        </button>
                    </div>
                    <p class="{{ $hint }}">กดปุ่ม ⟳ เพื่อโหลดรายการตาราง (ต้องผ่านขั้นตอนที่ 2 แล้ว)</p>
                </div>

                {{-- UCM Identifier --}}
                <div class="bg-slate-50 border border-slate-200 rounded-xl p-4">
                    <p class="text-xs font-bold text-slate-600 mb-3">UCM จะจับคู่ผู้ใช้โดยใช้:</p>
                    <div class="flex gap-3">
                        <label class="flex-1 flex items-start gap-3 p-3.5 border-2 rounded-xl cursor-pointer transition border-indigo-500 bg-indigo-50/40" id="ucm-id-card-username" onclick="selectUcmId('username')">
                            <input type="radio" name="user_ucm_identifier" value="username" checked class="mt-0.5 accent-indigo-600 flex-shrink-0">
                            <div class="w-8 h-8 rounded-lg bg-indigo-100 flex items-center justify-center flex-shrink-0 text-base">👤</div>
                            <div>
                                <p class="text-sm font-bold text-slate-700">Username (LDAP)</p>
                                <p class="text-[11px] text-slate-400 mt-0.5">ใช้ username จาก Active Directory</p>
                            </div>
                        </label>
                        <label class="flex-1 flex items-start gap-3 p-3.5 border-2 rounded-xl cursor-pointer transition border-slate-200 bg-white" id="ucm-id-card-employee_number" onclick="selectUcmId('employee_number')">
                            <input type="radio" name="user_ucm_identifier" value="employee_number" class="mt-0.5 accent-indigo-600 flex-shrink-0">
                            <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center flex-shrink-0 text-base">🪪</div>
                            <div>
                                <p class="text-sm font-bold text-slate-700">Employee Number</p>
                                <p class="text-[11px] text-slate-400 mt-0.5">ใช้รหัสพนักงาน</p>
                            </div>
                        </label>
                    </div>
                </div>

                <hr class="border-t border-slate-100">

                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Mapping คอลัมน์</p>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="{{ $lbl }}">คอลัมน์ Identifier <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <select id="field_user_identifier_col" class="{{ $sel }}"><option value="">— เลือกคอลัมน์ —</option></select>
                            {!! $arr !!}
                        </div>
                        <p class="{{ $hint }}">ตรงกับ UCM username / employee_number</p>
                    </div>
                    <div>
                        <label class="{{ $lbl }}">ชื่อ-นามสกุล <span class="inline text-[10px] font-bold px-1.5 py-px bg-slate-100 text-slate-400 rounded ml-1">ไม่บังคับ</span></label>
                        <div class="relative">
                            <select id="field_user_name_col" class="{{ $sel }}"><option value="">(ไม่ระบุ)</option></select>
                            {!! $arr !!}
                        </div>
                    </div>
                    <div>
                        <label class="{{ $lbl }}">อีเมล <span class="inline text-[10px] font-bold px-1.5 py-px bg-slate-100 text-slate-400 rounded ml-1">ไม่บังคับ</span></label>
                        <div class="relative">
                            <select id="field_user_email_col" class="{{ $sel }}"><option value="">(ไม่ระบุ)</option></select>
                            {!! $arr !!}
                        </div>
                    </div>
                    <div>
                        <label class="{{ $lbl }}">แผนก <span class="inline text-[10px] font-bold px-1.5 py-px bg-slate-100 text-slate-400 rounded ml-1">ไม่บังคับ</span></label>
                        <div class="relative">
                            <select id="field_user_dept_col" class="{{ $sel }}"><option value="">(ไม่ระบุ)</option></select>
                            {!! $arr !!}
                        </div>
                    </div>
                    <div>
                        <label class="{{ $lbl }}">สถานะ Active <span class="inline text-[10px] font-bold px-1.5 py-px bg-slate-100 text-slate-400 rounded ml-1">ไม่บังคับ</span></label>
                        <div class="relative">
                            <select id="field_user_status_col" class="{{ $sel }}" onchange="wizToggleStatusVal()"><option value="">(ไม่ระบุ)</option></select>
                            {!! $arr !!}
                        </div>
                    </div>
                    <div id="status-val-wrap" class="hidden">
                        <label class="{{ $lbl }}">ค่าที่หมายถึง "Active"</label>
                        <input type="text" id="field_user_status_active_val" class="{{ $inp }}" placeholder="เช่น 1, Y, active, true">
                    </div>
                </div>

                <button class="{{ $btnO }} w-full justify-center" onclick="wizPreviewUsers()">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    ดูตัวอย่างข้อมูล 10 รายการแรก
                </button>
                <div id="user-preview" style="display:none" class="border border-slate-200 rounded-xl overflow-hidden overflow-x-auto">
                    <table class="w-full text-xs border-collapse">
                        <thead><tr id="user-preview-head" class="bg-slate-50 border-b border-slate-200"></tr></thead>
                        <tbody id="user-preview-body"></tbody>
                    </table>
                </div>

            </div>
        </div>
        <div class="flex items-center justify-between mt-5">
            <button class="{{ $btnG }}" onclick="wizPrev(3)">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                ย้อนกลับ
            </button>
            <div class="flex items-center gap-3">
                <span class="text-xs text-slate-400 font-semibold">ขั้นที่ 3 จาก 5</span>
                <button class="{{ $btnP }}" onclick="wizNext(3)">
                    ถัดไป
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </button>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════
         STEP 4 — Permissions
    ══════════════════════════════════════════════════ --}}
    <div class="wiz-step hidden" data-step="4">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="flex items-start gap-4 px-6 py-5 border-b border-slate-100 bg-slate-50/50">
                <div class="{{ $icn }}">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-800">Permission Mode</h2>
                    <p class="text-xs text-slate-400 mt-0.5">เลือกวิธีที่ระบบภายนอกจัดเก็บสิทธิ์ของผู้ใช้</p>
                </div>
            </div>
            <div class="px-6 py-6 space-y-5">

                {{-- Mode Selection --}}
                <div class="flex flex-col gap-2.5">
                    <label class="flex items-start gap-3.5 p-3.5 border-2 rounded-xl cursor-pointer transition border-indigo-500 bg-indigo-50/40" id="perm-card-junction" onclick="selectPermMode('junction')">
                        <input type="radio" name="permission_mode" value="junction" checked class="mt-0.5 accent-indigo-600 flex-shrink-0" onchange="wizPermModeChange(this.value)">
                        <div class="w-8 h-8 rounded-lg bg-indigo-100 flex items-center justify-center flex-shrink-0 text-base">🔗</div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-bold text-slate-800">Junction Table</p>
                            <p class="text-xs text-slate-500 mt-0.5">ตาราง mapping แยก เช่น <code class="text-[11px] bg-slate-100 px-1.5 py-0.5 rounded font-mono">user_roles(user_id, role)</code></p>
                        </div>
                        <div class="flex-shrink-0 w-5 h-5 rounded-full border-2 flex items-center justify-center" id="perm-check-junction" style="border-color:#6366f1">
                            <div class="w-2 h-2 rounded-full bg-indigo-500" id="perm-dot-junction"></div>
                        </div>
                    </label>
                    <label class="flex items-start gap-3.5 p-3.5 border-2 rounded-xl cursor-pointer transition border-slate-200 bg-white" id="perm-card-column" onclick="selectPermMode('column')">
                        <input type="radio" name="permission_mode" value="column" class="mt-0.5 accent-indigo-600 flex-shrink-0" onchange="wizPermModeChange(this.value)">
                        <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center flex-shrink-0 text-base">📋</div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-bold text-slate-800">Single Column</p>
                            <p class="text-xs text-slate-500 mt-0.5">คอลัมน์ <code class="text-[11px] bg-slate-100 px-1.5 py-0.5 rounded font-mono">role</code> อยู่บนตาราง users เดิม</p>
                        </div>
                        <div class="flex-shrink-0 w-5 h-5 rounded-full border-2 border-slate-200 flex items-center justify-center" id="perm-check-column">
                            <div class="w-2 h-2 rounded-full bg-indigo-500" id="perm-dot-column" style="display:none"></div>
                        </div>
                    </label>
                    <label class="flex items-start gap-3.5 p-3.5 border-2 rounded-xl cursor-pointer transition border-slate-200 bg-white" id="perm-card-manual" onclick="selectPermMode('manual')">
                        <input type="radio" name="permission_mode" value="manual" class="mt-0.5 accent-indigo-600 flex-shrink-0" onchange="wizPermModeChange(this.value)">
                        <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center flex-shrink-0 text-base">✍️</div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-bold text-slate-800">กำหนดด้วยตนเอง</p>
                            <p class="text-xs text-slate-500 mt-0.5">ไม่มีตาราง permission — กำหนด list เองใน UCM</p>
                        </div>
                        <div class="flex-shrink-0 w-5 h-5 rounded-full border-2 border-slate-200 flex items-center justify-center" id="perm-check-manual">
                            <div class="w-2 h-2 rounded-full bg-indigo-500" id="perm-dot-manual" style="display:none"></div>
                        </div>
                    </label>
                </div>

                {{-- Junction / Column DB Fields --}}
                <div id="perm-db-fields">
                    <hr class="border-t border-slate-100">
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-4">Mapping คอลัมน์</p>

                    <div class="mb-4">
                        <label class="{{ $lbl }}">ตาราง Permissions <span class="text-rose-500">*</span></label>
                        <div class="flex gap-2">
                            <div class="relative flex-1">
                                <select id="field_perm_table" class="{{ $sel }}" onchange="wizLoadPermColumns()">
                                    <option value="">— เลือกตาราง —</option>
                                </select>
                                {!! $arr !!}
                            </div>
                            <button class="{{ $btnO }} flex-shrink-0" onclick="wizLoadTables('perm_table','wizLoadPermColumns')" title="โหลดรายการตาราง">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                            </button>
                        </div>
                        <p class="{{ $hint }}" id="perm-table-hint">ตาราง junction ที่เก็บความสัมพันธ์ user ↔ permission</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div id="perm-user-fk-wrap">
                            <label class="{{ $lbl }}">FK ชี้ไปยัง User <span class="text-rose-500">*</span></label>
                            <div class="relative">
                                <select id="field_perm_user_fk_col" class="{{ $sel }}"><option value="">— เลือกคอลัมน์ —</option></select>
                                {!! $arr !!}
                            </div>
                        </div>
                        <div>
                            <label class="{{ $lbl }}">Permission Value <span class="text-rose-500">*</span></label>
                            <div class="relative">
                                <select id="field_perm_value_col" class="{{ $sel }}"><option value="">— เลือกคอลัมน์ —</option></select>
                                {!! $arr !!}
                            </div>
                        </div>
                        <div>
                            <label class="{{ $lbl }}">Label <span class="inline text-[10px] font-bold px-1.5 py-px bg-slate-100 text-slate-400 rounded ml-1">ไม่บังคับ</span></label>
                            <div class="relative">
                                <select id="field_perm_label_col" class="{{ $sel }}"><option value="">(ไม่ระบุ)</option></select>
                                {!! $arr !!}
                            </div>
                        </div>
                        <div>
                            <label class="{{ $lbl }}">Group <span class="inline text-[10px] font-bold px-1.5 py-px bg-slate-100 text-slate-400 rounded ml-1">ไม่บังคับ</span></label>
                            <div class="relative">
                                <select id="field_perm_group_col" class="{{ $sel }}"><option value="">(ไม่ระบุ)</option></select>
                                {!! $arr !!}
                            </div>
                        </div>
                    </div>

                    <button class="{{ $btnO }} w-full justify-center mt-4" onclick="wizPreviewPermissions()">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        ดูตัวอย่าง Permissions (20 รายการ)
                    </button>
                    <div id="perm-preview" style="display:none" class="mt-3 border border-slate-200 rounded-xl overflow-hidden overflow-x-auto">
                        <table class="w-full text-xs border-collapse">
                            <thead><tr id="perm-preview-head" class="bg-slate-50 border-b border-slate-200"></tr></thead>
                            <tbody id="perm-preview-body"></tbody>
                        </table>
                    </div>
                </div>

                {{-- Manual Fields --}}
                <div id="perm-manual-fields" class="hidden">
                    <hr class="border-t border-slate-100">
                    <div class="flex items-center justify-between mb-3.5">
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">รายการ Permissions</p>
                        <button class="{{ $btnO }}" onclick="wizAddManualPerm()">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            เพิ่ม Permission
                        </button>
                    </div>
                    <div class="grid gap-1.5 px-2.5 mb-1.5" style="grid-template-columns:1fr 1fr 7rem auto">
                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wide">Key</p>
                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wide">Label</p>
                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wide">Group</p>
                        <div></div>
                    </div>
                    <div id="manual-perm-list" class="flex flex-col gap-1.5"></div>
                    <p id="manual-empty" class="text-xs text-slate-400 text-center py-4">ยังไม่มี permission — กด "เพิ่ม Permission" เพื่อเริ่มต้น</p>
                </div>

            </div>
        </div>
        <div class="flex items-center justify-between mt-5">
            <button class="{{ $btnG }}" onclick="wizPrev(4)">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                ย้อนกลับ
            </button>
            <div class="flex items-center gap-3">
                <span class="text-xs text-slate-400 font-semibold">ขั้นที่ 4 จาก 5</span>
                <button class="{{ $btnP }}" onclick="wizNext(4)">
                    ถัดไป
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </button>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════
         STEP 5 — Confirm
    ══════════════════════════════════════════════════ --}}
    <div class="wiz-step hidden" data-step="5">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="flex items-start gap-4 px-6 py-5 border-b border-slate-100 bg-slate-50/50">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 bg-gradient-to-br from-emerald-500 to-green-600 shadow-lg shadow-emerald-200/60">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-800">ยืนยันการตั้งค่า</h2>
                    <p class="text-xs text-slate-400 mt-0.5">ตรวจสอบข้อมูลก่อนสร้าง Connector</p>
                </div>
            </div>
            <div class="px-6 py-6">
                <div id="confirm-summary"></div>
            </div>
        </div>
        <div class="flex items-center justify-between mt-5">
            <button class="{{ $btnG }}" onclick="wizPrev(5)">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                ย้อนกลับ
            </button>
            <div class="flex items-center gap-3">
                <span class="text-xs text-slate-400 font-semibold">ขั้นที่ 5 จาก 5</span>
                <button class="inline-flex items-center gap-1.5 px-5 py-2.5 text-sm font-semibold text-white bg-gradient-to-br from-emerald-500 to-green-600 rounded-xl shadow-md shadow-emerald-200 hover:opacity-90 active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed transition-all" id="submit-btn" onclick="wizSubmit()">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    {{ isset($editConfig) ? 'บันทึกการเปลี่ยนแปลง' : 'สร้าง Connector' }}
                </button>
            </div>
        </div>
    </div>

</div>

<script>
(function () {
    var CSRF   = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
    var IS_EDIT = @json(isset($editConfig));
    var EDIT_CONFIG = @json($editConfig ?? null);
    var EDIT_CONFIG_ID = EDIT_CONFIG ? EDIT_CONFIG.id : null;

    var currentStep = 1;
    var tables      = [];
    var connTested  = false;

    // ── Base class strings for step circles / labels / lines ───────────────

    var CIRCLE_BASE = 'w-9 h-9 rounded-full flex items-center justify-center text-xs font-bold border-2 relative z-10 transition-all duration-200';
    var LABEL_BASE  = 'text-[11px] font-semibold whitespace-nowrap transition-colors hidden sm:block';

    // ── UI State Helpers ───────────────────────────────────────────────────

    window.selectDriver = function (drv) {
        document.querySelectorAll('[data-driver]').forEach(function (c) {
            c.classList.remove('border-indigo-500', 'bg-indigo-50');
            c.classList.add('border-slate-200', 'bg-white');
            var lbl = c.querySelector('[data-driver-lbl]');
            if (lbl) { lbl.classList.remove('text-indigo-600'); lbl.classList.add('text-slate-500'); }
        });
        var card = document.getElementById('driver-card-' + drv);
        if (card) {
            card.classList.remove('border-slate-200', 'bg-white');
            card.classList.add('border-indigo-500', 'bg-indigo-50');
            var lbl = card.querySelector('[data-driver-lbl]');
            if (lbl) { lbl.classList.remove('text-slate-500'); lbl.classList.add('text-indigo-600'); }
        }
        var radio = document.querySelector('input[name="db_driver"][value="' + drv + '"]');
        if (radio) { radio.checked = true; wizDriverChange(drv); }
    };

    window.selectUcmId = function (val) {
        ['username', 'employee_number'].forEach(function (v) {
            var c = document.getElementById('ucm-id-card-' + v);
            if (!c) return;
            if (v === val) {
                c.classList.remove('border-slate-200', 'bg-white');
                c.classList.add('border-indigo-500', 'bg-indigo-50/40');
            } else {
                c.classList.remove('border-indigo-500', 'bg-indigo-50/40');
                c.classList.add('border-slate-200', 'bg-white');
            }
        });
        var radio = document.querySelector('input[name="user_ucm_identifier"][value="' + val + '"]');
        if (radio) radio.checked = true;
    };

    window.selectPermMode = function (mode) {
        ['junction', 'column', 'manual'].forEach(function (m) {
            var card = document.getElementById('perm-card-' + m);
            var dot  = document.getElementById('perm-dot-' + m);
            var chk  = document.getElementById('perm-check-' + m);
            if (card) {
                if (m === mode) {
                    card.classList.remove('border-slate-200', 'bg-white');
                    card.classList.add('border-indigo-500', 'bg-indigo-50/40');
                } else {
                    card.classList.remove('border-indigo-500', 'bg-indigo-50/40');
                    card.classList.add('border-slate-200', 'bg-white');
                }
            }
            if (dot)  dot.style.display      = m === mode ? 'block' : 'none';
            if (chk)  chk.style.borderColor  = m === mode ? '#6366f1' : '#e2e8f0';
        });
        var radio = document.querySelector('input[name="permission_mode"][value="' + mode + '"]');
        if (radio) { radio.checked = true; wizPermModeChange(mode); }
    };

    // ── Step Navigation ────────────────────────────────────────────────────

    window.wizNext = function (from) {
        if (! wizValidate(from)) return;
        showStep(from + 1);
        if (from + 1 === 5) wizBuildSummary();
    };

    window.wizPrev = function (from) {
        showStep(from - 1);
    };

    function showStep(n) {
        currentStep = n;
        document.querySelectorAll('.wiz-step').forEach(function (el) {
            el.classList.toggle('hidden', parseInt(el.dataset.step) !== n);
        });
        for (var i = 1; i <= 5; i++) {
            var circle = document.querySelector('[data-step-circle="' + i + '"]');
            var label  = document.querySelector('[data-step-label="' + i + '"]');
            var line   = document.querySelector('[data-step-line="' + i + '"]');
            var numEl  = document.querySelector('[data-step-num="' + i + '"]');
            var chkEl  = document.querySelector('[data-step-check="' + i + '"]');

            if (circle) {
                if (i < n) {
                    circle.className = CIRCLE_BASE + ' border-indigo-500 bg-indigo-500 text-white';
                    numEl && numEl.classList.add('hidden');
                    chkEl && chkEl.classList.remove('hidden');
                } else if (i === n) {
                    circle.className = CIRCLE_BASE + ' border-indigo-500 bg-indigo-500 text-white ring-4 ring-indigo-100';
                    numEl && numEl.classList.remove('hidden');
                    chkEl && chkEl.classList.add('hidden');
                } else {
                    circle.className = CIRCLE_BASE + ' border-slate-200 bg-white text-slate-400';
                    numEl && numEl.classList.remove('hidden');
                    chkEl && chkEl.classList.add('hidden');
                }
            }
            if (label) {
                label.className = LABEL_BASE + ' ' + (i <= n ? 'text-indigo-600' : 'text-slate-400');
            }
            if (line) {
                line.className = 'flex-1 h-0.5 mx-2 mt-[1.125rem] transition-colors ' + (i < n ? 'bg-indigo-500' : 'bg-slate-200');
            }
        }
        wizHideAlert();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    // ── Validation ─────────────────────────────────────────────────────────

    function wizValidate(step) {
        wizHideAlert();
        if (step === 1) {
            if (IS_EDIT) return true;
            var sysId = val('system_id');
            if (! sysId) {
                if (! val('system_name')) return showErr('กรุณากรอกชื่อระบบ');
                if (! val('system_slug')) return showErr('กรุณากรอก Slug');
                if (! /^[a-z0-9-]+$/.test(val('system_slug'))) return showErr('Slug ต้องเป็นตัวพิมพ์เล็ก, ตัวเลข และ "-" เท่านั้น');
            }
            return true;
        }
        if (step === 2) {
            if (! val('db_host')) return showErr('กรุณากรอก Host');
            if (! val('db_port')) return showErr('กรุณากรอก Port');
            if (! val('db_name')) return showErr('กรุณากรอก Database Name');
            if (! val('db_user')) return showErr('กรุณากรอก Username');
            return true;
        }
        if (step === 3) {
            if (! val('user_table')) return showErr('กรุณาเลือกตาราง Users');
            if (! val('user_identifier_col')) return showErr('กรุณาเลือกคอลัมน์ Identifier');
            return true;
        }
        if (step === 4) {
            var mode = getPermMode();
            if (mode !== 'manual') {
                if (! val('perm_table')) return showErr('กรุณาเลือกตาราง Permissions');
                if (! val('perm_value_col')) return showErr('กรุณาเลือกคอลัมน์ Permission Value');
            }
            return true;
        }
        return true;
    }

    function val(id) {
        var el = document.getElementById('field_' + id);
        return el ? el.value.trim() : '';
    }

    function radioVal(name) {
        var el = document.querySelector('input[name="' + name + '"]:checked');
        return el ? el.value : '';
    }

    function getPermMode() { return radioVal('permission_mode'); }

    function showErr(msg) {
        var el = document.getElementById('wiz-alert');
        el.style.display = 'flex';
        el.style.alignItems = 'center';
        el.style.gap = '.625rem';
        el.style.background = '#fef2f2';
        el.style.border = '1.5px solid #fecaca';
        el.style.color = '#b91c1c';
        el.innerHTML = '<svg style="width:1rem;height:1rem;flex-shrink:0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><span>' + escHtml(msg) + '</span>';
        el.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        return false;
    }

    function wizHideAlert() {
        var el = document.getElementById('wiz-alert');
        el.style.display = 'none';
    }

    // ── AJAX Helpers ───────────────────────────────────────────────────────

    function connData() {
        return {
            db_driver:   radioVal('db_driver'),
            db_host:     val('db_host'),
            db_port:     val('db_port'),
            db_name:     val('db_name'),
            db_user:     val('db_user'),
            db_password: document.getElementById('field_db_password')?.value ?? '',
        };
    }

    function post(url, data, cb) {
        data._token = CSRF;
        fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body: JSON.stringify(data),
        }).then(function (r) { return r.json(); }).then(cb).catch(function (e) {
            cb({ ok: false, message: e.message });
        });
    }

    // ── Test Connection ────────────────────────────────────────────────────

    window.wizTestConnection = function () {
        var el  = document.getElementById('conn-result');
        var btn = document.getElementById('test-conn-btn');
        btn.disabled = true;
        el.style.display = 'flex';
        el.style.alignItems = 'center';
        el.style.gap = '.5rem';
        el.style.background = '#eff6ff';
        el.style.border = '1.5px solid #bfdbfe';
        el.style.color = '#1d4ed8';
        el.innerHTML = '<svg style="width:1rem;height:1rem;flex-shrink:0;animation:spin 1s linear infinite" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg><span>กำลังทดสอบการเชื่อมต่อ...</span>';

        post('{{ route("connectors.ajax.test-connection") }}', connData(), function (res) {
            btn.disabled = false;
            if (res.ok) {
                el.style.background = '#f0fdf4';
                el.style.border = '1.5px solid #bbf7d0';
                el.style.color = '#15803d';
                el.innerHTML = '<svg style="width:1rem;height:1rem;flex-shrink:0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><span>เชื่อมต่อสำเร็จ — พร้อมดำเนินการต่อ</span>';
                connTested = true;
                wizFetchTablesInternal();
            } else {
                el.style.background = '#fef2f2';
                el.style.border = '1.5px solid #fecaca';
                el.style.color = '#b91c1c';
                el.innerHTML = '<svg style="width:1rem;height:1rem;flex-shrink:0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><span>' + escHtml(res.message) + '</span>';
                connTested = false;
            }
        });
    };

    function wizFetchTablesInternal(cb) {
        post('{{ route("connectors.ajax.fetch-tables") }}', connData(), function (res) {
            if (res.ok) {
                tables = res.tables || [];
                if (cb) cb(tables);
            }
        });
    }

    // ── Load Tables into a Select ──────────────────────────────────────────

    window.wizLoadTables = function (fieldId, onChangeFn) {
        wizFetchTablesInternal(function (tbls) {
            var sel = document.getElementById('field_' + fieldId);
            if (! sel) return;
            var current = sel.value;
            sel.innerHTML = '<option value="">— เลือกตาราง —</option>';
            tbls.forEach(function (t) {
                var opt = document.createElement('option');
                opt.value = t; opt.textContent = t;
                if (t === current) opt.selected = true;
                sel.appendChild(opt);
            });
            if (onChangeFn && window[onChangeFn]) window[onChangeFn]();
        });
    };

    // ── Load Columns ───────────────────────────────────────────────────────

    function loadColumnsFor(tableName, targetSelectIds) {
        if (! tableName) return;
        var data = Object.assign(connData(), { table: tableName });
        post('{{ route("connectors.ajax.fetch-columns") }}', data, function (res) {
            if (! res.ok) return;
            var cols = res.columns || [];
            targetSelectIds.forEach(function (info) {
                var sel = document.getElementById(info.id);
                if (! sel) return;
                var current = info.preselect || sel.value;
                sel.innerHTML = (info.nullable ? '<option value="">(ไม่ระบุ)</option>' : '<option value="">— เลือกคอลัมน์ —</option>');
                cols.forEach(function (c) {
                    var opt = document.createElement('option');
                    opt.value = c; opt.textContent = c;
                    if (c === current) opt.selected = true;
                    sel.appendChild(opt);
                });
            });
        });
    }

    window.wizLoadUserColumns = function () {
        var table = val('user_table');
        loadColumnsFor(table, [
            { id: 'field_user_identifier_col', preselect: EDIT_CONFIG?.user_identifier_col },
            { id: 'field_user_name_col', nullable: true, preselect: EDIT_CONFIG?.user_name_col },
            { id: 'field_user_email_col', nullable: true, preselect: EDIT_CONFIG?.user_email_col },
            { id: 'field_user_dept_col', nullable: true, preselect: EDIT_CONFIG?.user_dept_col },
            { id: 'field_user_status_col', nullable: true, preselect: EDIT_CONFIG?.user_status_col },
        ]);
    };

    window.wizLoadPermColumns = function () {
        var table = val('perm_table');
        loadColumnsFor(table, [
            { id: 'field_perm_user_fk_col', preselect: EDIT_CONFIG?.perm_user_fk_col },
            { id: 'field_perm_value_col', preselect: EDIT_CONFIG?.perm_value_col },
            { id: 'field_perm_label_col', nullable: true, preselect: EDIT_CONFIG?.perm_label_col },
            { id: 'field_perm_group_col', nullable: true, preselect: EDIT_CONFIG?.perm_group_col },
        ]);
    };

    // ── Driver Change ──────────────────────────────────────────────────────

    window.wizDriverChange = function (driver) {
        var portMap = { mysql: 3306, pgsql: 5432, sqlsrv: 1433 };
        var portEl = document.getElementById('field_db_port');
        if (portEl && portMap[driver]) portEl.value = portMap[driver];
    };

    // ── Status Val toggle ──────────────────────────────────────────────────

    window.wizToggleStatusVal = function () {
        var hasCol = !! val('user_status_col');
        document.getElementById('status-val-wrap').classList.toggle('hidden', ! hasCol);
    };

    // ── Perm Mode Change ───────────────────────────────────────────────────

    window.wizPermModeChange = function (mode) {
        document.getElementById('perm-db-fields').classList.toggle('hidden', mode === 'manual');
        document.getElementById('perm-manual-fields').classList.toggle('hidden', mode !== 'manual');
        var fkWrap = document.getElementById('perm-user-fk-wrap');
        if (fkWrap) fkWrap.classList.toggle('hidden', mode === 'column');
        var hint = document.getElementById('perm-table-hint');
        if (hint) hint.textContent = mode === 'column'
            ? 'Column mode: ให้เลือกตาราง users เดิมของระบบ'
            : 'ตาราง junction ที่เก็บความสัมพันธ์ user ↔ permission';
    };

    // ── System fields visibility ───────────────────────────────────────────

    if (! IS_EDIT) {
        var sysIdSel = document.getElementById('field_system_id');
        if (sysIdSel) {
            sysIdSel.addEventListener('change', function () {
                var wrap = document.getElementById('new-system-fields');
                if (wrap) wrap.classList.toggle('hidden', !! sysIdSel.value);
            });
        }
        var nameEl = document.getElementById('field_system_name');
        var slugEl = document.getElementById('field_system_slug');
        if (nameEl && slugEl) {
            nameEl.addEventListener('input', function () {
                if (! slugEl.dataset.manual) {
                    slugEl.value = nameEl.value.toLowerCase()
                        .replace(/[\s_]+/g, '-')
                        .replace(/[^a-z0-9-]/g, '')
                        .replace(/-+/g, '-');
                }
            });
            slugEl.addEventListener('input', function () { slugEl.dataset.manual = '1'; });
        }
        var colorPicker = document.getElementById('field_system_color');
        var colorText   = document.getElementById('field_system_color_text');
        if (colorPicker && colorText) {
            colorPicker.addEventListener('input', function () { colorText.value = colorPicker.value; });
            colorText.addEventListener('input', function () {
                if (/^#[0-9A-Fa-f]{6}$/.test(colorText.value)) colorPicker.value = colorText.value;
            });
        }
    }

    // ── Preview Users ──────────────────────────────────────────────────────

    window.wizPreviewUsers = function () {
        var data = Object.assign(connData(), {
            user_table: val('user_table'),
            user_identifier_col: val('user_identifier_col'),
            user_name_col: val('user_name_col'),
        });
        post('{{ route("connectors.ajax.preview-users") }}', data, function (res) {
            var container = document.getElementById('user-preview');
            var head = document.getElementById('user-preview-head');
            var body = document.getElementById('user-preview-body');
            if (! res.ok || ! res.rows || ! res.rows.length) {
                container.style.display = 'none';
                showErr(res.message || 'ไม่พบข้อมูล');
                return;
            }
            var cols = Object.keys(res.rows[0]);
            head.innerHTML = cols.map(function (c) {
                return '<th class="px-3 py-2 text-left text-[11px] font-bold text-slate-500 uppercase tracking-wide">' + escHtml(c) + '</th>';
            }).join('');
            body.innerHTML = res.rows.map(function (row) {
                return '<tr class="border-t border-slate-100 hover:bg-slate-50">' + cols.map(function (c) {
                    return '<td class="px-3 py-2 text-slate-700">' + escHtml(String(row[c] ?? '')) + '</td>';
                }).join('') + '</tr>';
            }).join('');
            container.style.display = 'block';
        });
    };

    // ── Preview Permissions ────────────────────────────────────────────────

    window.wizPreviewPermissions = function () {
        var data = Object.assign(connData(), {
            perm_table: val('perm_table'),
            perm_value_col: val('perm_value_col'),
            perm_label_col: val('perm_label_col'),
            perm_group_col: val('perm_group_col'),
        });
        post('{{ route("connectors.ajax.preview-permissions") }}', data, function (res) {
            var container = document.getElementById('perm-preview');
            var head = document.getElementById('perm-preview-head');
            var body = document.getElementById('perm-preview-body');
            if (! res.ok || ! res.rows || ! res.rows.length) {
                container.style.display = 'none';
                showErr(res.message || 'ไม่พบข้อมูล');
                return;
            }
            var cols = Object.keys(res.rows[0]);
            head.innerHTML = cols.map(function (c) {
                return '<th class="px-3 py-2 text-left text-[11px] font-bold text-slate-500 uppercase tracking-wide">' + escHtml(c) + '</th>';
            }).join('');
            body.innerHTML = res.rows.map(function (row) {
                return '<tr class="border-t border-slate-100 hover:bg-slate-50">' + cols.map(function (c) {
                    return '<td class="px-3 py-2 text-slate-700">' + escHtml(String(row[c] ?? '')) + '</td>';
                }).join('') + '</tr>';
            }).join('');
            container.style.display = 'block';
        });
    };

    // ── Manual Permissions ─────────────────────────────────────────────────

    var manualPerms = EDIT_CONFIG?.manual_permissions || [];

    window.wizAddManualPerm = function (perm) {
        perm = perm || { key: '', label: '', group: '' };
        var list  = document.getElementById('manual-perm-list');
        var empty = document.getElementById('manual-empty');
        if (empty) empty.style.display = 'none';
        var idx = list.children.length;
        var row = document.createElement('div');
        row.className = 'mp-row grid gap-1.5 items-center bg-slate-50 px-2.5 py-2 rounded-lg border border-slate-200';
        row.style.gridTemplateColumns = '1fr 1fr 7rem auto';
        var iCls = 'w-full px-2 py-1.5 text-xs text-slate-800 bg-white border border-slate-200 rounded focus:outline-none focus:border-indigo-500 transition';
        row.innerHTML = [
            '<input type="text" placeholder="key (เช่น admin)" value="' + escAttr(perm.key) + '" class="' + iCls + ' font-mono" data-mp="key" data-idx="' + idx + '">',
            '<input type="text" placeholder="label (ชื่อที่แสดง)" value="' + escAttr(perm.label) + '" class="' + iCls + '" data-mp="label" data-idx="' + idx + '">',
            '<input type="text" placeholder="group" value="' + escAttr(perm.group || '') + '" class="' + iCls + '" data-mp="group" data-idx="' + idx + '">',
            '<button type="button" onclick="removeManualPerm(this)" class="w-7 h-7 flex items-center justify-center rounded bg-rose-50 border border-rose-200 text-rose-500 hover:bg-rose-100 cursor-pointer text-base flex-shrink-0">×</button>',
        ].join('');
        list.appendChild(row);
    };

    window.removeManualPerm = function (btn) {
        btn.closest('.mp-row').remove();
        var list  = document.getElementById('manual-perm-list');
        var empty = document.getElementById('manual-empty');
        if (empty) empty.style.display = list.children.length === 0 ? 'block' : 'none';
    };

    function getManualPerms() {
        var rows = document.querySelectorAll('#manual-perm-list > .mp-row');
        var result = [];
        rows.forEach(function (row) {
            var k = row.querySelector('[data-mp="key"]')?.value.trim() || '';
            var l = row.querySelector('[data-mp="label"]')?.value.trim() || '';
            var g = row.querySelector('[data-mp="group"]')?.value.trim() || '';
            if (k) result.push({ key: k, label: l || k, group: g || 'ทั่วไป' });
        });
        return result;
    }

    // ── Confirm Summary ────────────────────────────────────────────────────

    window.wizBuildSummary = function () {
        var sysId = val('system_id');
        var mode  = getPermMode();
        var modeLabels = { junction: 'Junction Table', column: 'Single Column', manual: 'Manual' };

        var sections = [
            {
                title: 'ระบบ',
                rows: IS_EDIT
                    ? [['ระบบ', EDIT_CONFIG.system?.name || 'ไม่ระบุ']]
                    : sysId
                        ? [['ระบบ (เดิม)', document.querySelector('#field_system_id option[value="' + sysId + '"]')?.textContent || sysId]]
                        : [['ชื่อระบบ', val('system_name')], ['Slug', val('system_slug')]],
            },
            {
                title: 'Database',
                rows: [
                    ['Driver', radioVal('db_driver').toUpperCase()],
                    ['Host:Port', val('db_host') + ':' + val('db_port')],
                    ['Database', val('db_name')],
                    ['Username', val('db_user')],
                ],
            },
            {
                title: 'User Table',
                rows: [
                    ['ตาราง', val('user_table')],
                    ['UCM Identifier', radioVal('user_ucm_identifier')],
                    ['คอลัมน์ Identifier', val('user_identifier_col')],
                ],
            },
            {
                title: 'Permissions',
                rows: mode !== 'manual'
                    ? [['Mode', modeLabels[mode] || mode], ['ตาราง', val('perm_table')], ['Value Column', val('perm_value_col')]]
                    : [['Mode', 'Manual'], ['จำนวน', getManualPerms().length + ' permissions']],
            },
        ];

        var html = sections.map(function (sec) {
            return '<div class="mb-5">'
                + '<p class="text-[11px] font-extrabold uppercase tracking-widest text-slate-400 mb-2.5">' + escHtml(sec.title) + '</p>'
                + '<div class="rounded-xl border border-slate-100 overflow-hidden">'
                + sec.rows.map(function (r, ri) {
                    return '<div class="flex items-center justify-between px-4 py-2.5 ' + (ri % 2 === 0 ? 'bg-white' : 'bg-slate-50') + '">'
                        + '<span class="text-sm text-slate-500 font-medium">' + escHtml(r[0]) + '</span>'
                        + '<span class="text-xs text-slate-800 font-mono bg-slate-100 px-2 py-0.5 rounded max-w-[55%] break-all text-right">' + escHtml(r[1]) + '</span>'
                        + '</div>';
                }).join('')
                + '</div></div>';
        }).join('');

        document.getElementById('confirm-summary').innerHTML = html;
    };

    // ── Submit ─────────────────────────────────────────────────────────────

    window.wizSubmit = function () {
        var btn = document.getElementById('submit-btn');
        btn.disabled = true;
        btn.innerHTML = '<svg style="width:1rem;height:1rem;animation:spin 1s linear infinite" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg> กำลังบันทึก...';

        var mode = getPermMode();
        var payload = Object.assign(connData(), {
            system_id:              IS_EDIT ? EDIT_CONFIG.system_id : (val('system_id') || null),
            system_name:            val('system_name'),
            system_slug:            val('system_slug'),
            system_description:     val('system_description'),
            system_color:           document.getElementById('field_system_color')?.value || '#6366f1',
            system_icon:            val('system_icon'),
            user_table:             val('user_table'),
            user_ucm_identifier:    radioVal('user_ucm_identifier'),
            user_identifier_col:    val('user_identifier_col'),
            user_name_col:          val('user_name_col') || null,
            user_email_col:         val('user_email_col') || null,
            user_dept_col:          val('user_dept_col') || null,
            user_status_col:        val('user_status_col') || null,
            user_status_active_val: val('user_status_active_val') || null,
            permission_mode:        mode,
            perm_table:             mode !== 'manual' ? val('perm_table') : null,
            perm_user_fk_col:       mode === 'junction' ? val('perm_user_fk_col') : null,
            perm_value_col:         mode !== 'manual' ? val('perm_value_col') : null,
            perm_label_col:         mode !== 'manual' ? (val('perm_label_col') || null) : null,
            perm_group_col:         mode !== 'manual' ? (val('perm_group_col') || null) : null,
            manual_permissions:     mode === 'manual' ? JSON.stringify(getManualPerms()) : null,
        });

        if (IS_EDIT) { payload._method = 'PUT'; }

        fetch('{{ route("connectors.store") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body: JSON.stringify(payload),
        }).then(function (r) { return r.json(); }).then(function (res) {
            if (res.ok) {
                window.location.href = res.redirect || '{{ route("connectors.index") }}';
            } else {
                btn.disabled = false;
                btn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>{{ isset($editConfig) ? 'บันทึกการเปลี่ยนแปลง' : 'สร้าง Connector' }}';
                showErr(res.message || 'เกิดข้อผิดพลาด กรุณาลองใหม่');
            }
        }).catch(function (e) {
            btn.disabled = false;
            btn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>{{ isset($editConfig) ? 'บันทึกการเปลี่ยนแปลง' : 'สร้าง Connector' }}';
            showErr(e.message);
        });
    };

    // ── Utilities ──────────────────────────────────────────────────────────

    function escHtml(s) {
        return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }
    function escAttr(s) {
        return String(s || '').replace(/&/g,'&amp;').replace(/"/g,'&quot;');
    }

    // ── CSS Animation ──────────────────────────────────────────────────────
    var spinStyle = document.createElement('style');
    spinStyle.textContent = '@keyframes spin { from { transform:rotate(0deg); } to { transform:rotate(360deg); } }';
    document.head.appendChild(spinStyle);

    // ── Init for Edit Mode ─────────────────────────────────────────────────

    if (IS_EDIT && EDIT_CONFIG) {
        selectDriver(EDIT_CONFIG.db_driver || 'mysql');
        selectUcmId(EDIT_CONFIG.user_ucm_identifier || 'username');

        var pMode = EDIT_CONFIG.permission_mode || 'junction';
        selectPermMode(pMode);
        wizPermModeChange(pMode);

        if (EDIT_CONFIG.user_status_col) wizToggleStatusVal();
        if (document.getElementById('field_user_status_active_val')) {
            document.getElementById('field_user_status_active_val').value = EDIT_CONFIG.user_status_active_val || '';
        }

        if (pMode === 'manual' && EDIT_CONFIG.manual_permissions) {
            EDIT_CONFIG.manual_permissions.forEach(function (p) { wizAddManualPerm(p); });
        }

        wizFetchTablesInternal(function (tbls) {
            ['user_table', 'perm_table'].forEach(function (fieldId) {
                var sel = document.getElementById('field_' + fieldId);
                if (! sel) return;
                var preVal = EDIT_CONFIG[fieldId];
                sel.innerHTML = '<option value="">— เลือกตาราง —</option>';
                tbls.forEach(function (t) {
                    var opt = document.createElement('option');
                    opt.value = t; opt.textContent = t;
                    if (t === preVal) opt.selected = true;
                    sel.appendChild(opt);
                });
            });
            wizLoadUserColumns();
            wizLoadPermColumns();
        });
    }

})();
</script>

@endsection
