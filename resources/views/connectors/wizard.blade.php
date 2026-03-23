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
        ['label' => '2-Way Sync'],
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

                {{-- ── AI Schema Analysis Panel (shown after successful connection test) ── --}}
                <div id="analysis-panel" style="display:none" class="border border-indigo-200 rounded-xl overflow-hidden">
                    <div class="flex items-center gap-3 px-4 py-3 bg-gradient-to-r from-indigo-50 to-violet-50 border-b border-indigo-100">
                        <svg class="w-4 h-4 text-indigo-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.347.416A3 3 0 0118 19.5H6a3 3 0 01-2.39-2.751L3.263 16.1a5.002 5.002 0 012.08-4.1"/>
                        </svg>
                        <span class="text-sm font-bold text-indigo-700">วิเคราะห์ Schema อัตโนมัติ</span>
                        <span class="ml-auto text-[11px] text-slate-400">ช่วยแนะนำการตั้งค่า Steps 3–4</span>
                    </div>
                    <div class="px-4 py-3 bg-white space-y-3">

                        @if($aiAvailable ?? false)
                        {{-- AI toggle --}}
                        <label class="flex items-center gap-2.5 cursor-pointer select-none">
                            <div class="relative">
                                <input type="checkbox" id="use_ai_toggle" class="sr-only" onchange="wizToggleAI(this.checked)">
                                <div id="ai-toggle-track" class="w-9 h-5 rounded-full bg-slate-200 transition-colors"></div>
                                <div id="ai-toggle-thumb" class="absolute top-0.5 left-0.5 w-4 h-4 rounded-full bg-white shadow transition-transform"></div>
                            </div>
                            <span class="text-[13px] font-semibold text-slate-700">ใช้ AI วิเคราะห์ (Claude)</span>
                            <span class="text-[11px] text-slate-400">— แม่นยำกว่า แต่ใช้เวลานานขึ้น</span>
                        </label>

                        {{-- ZIP upload (shown when AI enabled) --}}
                        <div id="zip-upload-row" style="display:none" class="p-3 bg-slate-50 rounded-lg border border-slate-200">
                            <p class="text-[12px] font-semibold text-slate-600 mb-2">
                                <svg class="inline w-3.5 h-3.5 mr-1 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                อัปโหลด Source Code (ไม่บังคับ)
                            </p>
                            <div id="zip-drop-area" class="border-2 border-dashed border-slate-300 rounded-lg p-4 text-center cursor-pointer hover:border-indigo-400 hover:bg-indigo-50/50 transition"
                                 onclick="document.getElementById('source_zip_input').click()"
                                 ondragover="event.preventDefault()" ondrop="wizHandleZipDrop(event)">
                                <input type="file" id="source_zip_input" accept=".zip" class="hidden" onchange="wizHandleZipSelect(this)">
                                <div id="zip-placeholder">
                                    <p class="text-[12px] text-slate-500">คลิกหรือลาก ZIP ไฟล์มาวาง</p>
                                    <p class="text-[11px] text-slate-400 mt-0.5">ไฟล์ source code ของเว็บที่จะเชื่อมต่อ (สูงสุด 50 MB)</p>
                                </div>
                                <div id="zip-selected" style="display:none" class="flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4 text-indigo-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    <span id="zip-filename" class="text-[12px] font-semibold text-indigo-700"></span>
                                    <button type="button" class="text-slate-400 hover:text-rose-500 transition" onclick="wizClearZip(event)">✕</button>
                                </div>
                            </div>
                        </div>
                        @endif

                        <button type="button" id="run-analysis-btn"
                                class="{{ $btnO }} w-full justify-center py-2"
                                onclick="wizRunAnalysis()">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                            วิเคราะห์และแนะนำการตั้งค่า
                        </button>

                        <div id="analysis-result" style="display:none"></div>
                    </div>
                </div>

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
                                @if(isset($editConfig) && $editConfig->user_table)
                                    <option value="{{ $editConfig->user_table }}" selected>{{ $editConfig->user_table }}</option>
                                @endif
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
                            <select id="field_user_identifier_col" class="{{ $sel }}">
                                <option value="">— เลือกคอลัมน์ —</option>
                                @if(isset($editConfig) && $editConfig->user_identifier_col)
                                    <option value="{{ $editConfig->user_identifier_col }}" selected>{{ $editConfig->user_identifier_col }}</option>
                                @endif
                            </select>
                            {!! $arr !!}
                        </div>
                        <p class="{{ $hint }}">ตรงกับ UCM username / employee_number</p>
                    </div>
                    <div>
                        <label class="{{ $lbl }}">คอลัมน์ PK ของ User Table <span class="inline text-[10px] font-bold px-1.5 py-px bg-slate-100 text-slate-400 rounded ml-1">ไม่บังคับ</span></label>
                        <div class="relative">
                            <select id="field_user_pk_col" class="{{ $sel }}">
                                <option value="">(ไม่ระบุ — ใช้ Identifier โดยตรง)</option>
                                @if(isset($editConfig) && $editConfig->user_pk_col)
                                    <option value="{{ $editConfig->user_pk_col }}" selected>{{ $editConfig->user_pk_col }}</option>
                                @endif
                            </select>
                            {!! $arr !!}
                        </div>
                        <p class="{{ $hint }}">ระบุเมื่อ FK ใน junction table อ้างอิง PK (INT) ไม่ใช่ username โดยตรง</p>
                    </div>
                    <div>
                        <label class="{{ $lbl }}">ชื่อ-นามสกุล <span class="inline text-[10px] font-bold px-1.5 py-px bg-slate-100 text-slate-400 rounded ml-1">ไม่บังคับ</span></label>
                        <div class="relative">
                            <select id="field_user_name_col" class="{{ $sel }}">
                                <option value="">(ไม่ระบุ)</option>
                                @if(isset($editConfig) && $editConfig->user_name_col)
                                    <option value="{{ $editConfig->user_name_col }}" selected>{{ $editConfig->user_name_col }}</option>
                                @endif
                            </select>
                            {!! $arr !!}
                        </div>
                    </div>
                    <div>
                        <label class="{{ $lbl }}">อีเมล <span class="inline text-[10px] font-bold px-1.5 py-px bg-slate-100 text-slate-400 rounded ml-1">ไม่บังคับ</span></label>
                        <div class="relative">
                            <select id="field_user_email_col" class="{{ $sel }}">
                                <option value="">(ไม่ระบุ)</option>
                                @if(isset($editConfig) && $editConfig->user_email_col)
                                    <option value="{{ $editConfig->user_email_col }}" selected>{{ $editConfig->user_email_col }}</option>
                                @endif
                            </select>
                            {!! $arr !!}
                        </div>
                    </div>
                    <div>
                        <label class="{{ $lbl }}">แผนก <span class="inline text-[10px] font-bold px-1.5 py-px bg-slate-100 text-slate-400 rounded ml-1">ไม่บังคับ</span></label>
                        <div class="relative">
                            <select id="field_user_dept_col" class="{{ $sel }}">
                                <option value="">(ไม่ระบุ)</option>
                                @if(isset($editConfig) && $editConfig->user_dept_col)
                                    <option value="{{ $editConfig->user_dept_col }}" selected>{{ $editConfig->user_dept_col }}</option>
                                @endif
                            </select>
                            {!! $arr !!}
                        </div>
                    </div>
                    <div>
                        <label class="{{ $lbl }}">สถานะ Active <span class="inline text-[10px] font-bold px-1.5 py-px bg-slate-100 text-slate-400 rounded ml-1">ไม่บังคับ</span></label>
                        <div class="relative">
                            <select id="field_user_status_col" class="{{ $sel }}" onchange="wizToggleStatusVal()">
                                <option value="">(ไม่ระบุ)</option>
                                @if(isset($editConfig) && $editConfig->user_status_col)
                                    <option value="{{ $editConfig->user_status_col }}" selected>{{ $editConfig->user_status_col }}</option>
                                @endif
                            </select>
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
                                    @if(isset($editConfig) && $editConfig->perm_table)
                                        <option value="{{ $editConfig->perm_table }}" selected>{{ $editConfig->perm_table }}</option>
                                    @endif
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
                                <select id="field_perm_user_fk_col" class="{{ $sel }}">
                                    <option value="">— เลือกคอลัมน์ —</option>
                                    @if(isset($editConfig) && $editConfig->perm_user_fk_col)
                                        <option value="{{ $editConfig->perm_user_fk_col }}" selected>{{ $editConfig->perm_user_fk_col }}</option>
                                    @endif
                                </select>
                                {!! $arr !!}
                            </div>
                        </div>
                        <div>
                            <label class="{{ $lbl }}">Permission Value <span class="text-rose-500">*</span></label>
                            <div class="relative">
                                <select id="field_perm_value_col" class="{{ $sel }}">
                                    <option value="">— เลือกคอลัมน์ —</option>
                                    @if(isset($editConfig) && $editConfig->perm_value_col)
                                        <option value="{{ $editConfig->perm_value_col }}" selected>{{ $editConfig->perm_value_col }}</option>
                                    @endif
                                </select>
                                {!! $arr !!}
                            </div>
                        </div>
                        <div>
                            <label class="{{ $lbl }}">Label <span class="inline text-[10px] font-bold px-1.5 py-px bg-slate-100 text-slate-400 rounded ml-1">ไม่บังคับ</span></label>
                            <div class="relative">
                                <select id="field_perm_label_col" class="{{ $sel }}">
                                    <option value="">(ไม่ระบุ)</option>
                                    @if(isset($editConfig) && $editConfig->perm_label_col)
                                        <option value="{{ $editConfig->perm_label_col }}" selected>{{ $editConfig->perm_label_col }}</option>
                                    @endif
                                </select>
                                {!! $arr !!}
                            </div>
                        </div>
                        <div>
                            <label class="{{ $lbl }}">Group <span class="inline text-[10px] font-bold px-1.5 py-px bg-slate-100 text-slate-400 rounded ml-1">ไม่บังคับ</span></label>
                            <div class="relative">
                                <select id="field_perm_group_col" class="{{ $sel }}">
                                    <option value="">(ไม่ระบุ)</option>
                                    @if(isset($editConfig) && $editConfig->perm_group_col)
                                        <option value="{{ $editConfig->perm_group_col }}" selected>{{ $editConfig->perm_group_col }}</option>
                                    @endif
                                </select>
                                {!! $arr !!}
                            </div>
                        </div>
                    </div>

                    {{-- Composite Junction Columns --}}
                    <div id="perm-composite-section" class="mt-4 hidden">
                        <div class="flex items-center justify-between mb-2">
                            <div>
                                <p class="text-xs font-bold text-slate-600">คอลัมน์ FK เสริม <span class="text-[10px] font-normal text-slate-400">(Multi-Master Junction)</span></p>
                                <p class="text-[11px] text-slate-400 mt-0.5">เมื่อตาราง mapping อ้างอิงมากกว่า 1 master table — เพิ่มคอลัมน์ที่เหลือเพื่อสร้าง composite key</p>
                            </div>
                            <button type="button" class="{{ $btnO }}" onclick="wizAddCompositeCol()">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                เพิ่มคอลัมน์
                            </button>
                        </div>
                        <div id="composite-cols-list" class="space-y-2"></div>
                        <p id="composite-empty-hint" class="text-[11px] text-slate-400 italic mt-1">ตัวอย่าง: ระบบมีตาราง UserGrant(user_id, pg_id, s_id) → เพิ่ม s_id เป็น FK เสริม</p>
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
                <span class="text-xs text-slate-400 font-semibold">ขั้นที่ 4 จาก 6</span>
                <button class="{{ $btnP }}" onclick="wizNext(4)">
                    ถัดไป
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </button>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════
         STEP 5 — 2-Way Sync
    ══════════════════════════════════════════════════ --}}
    <div class="wiz-step hidden" data-step="5">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="flex items-start gap-4 px-6 py-5 border-b border-slate-100 bg-slate-50/50">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 bg-gradient-to-br from-violet-500 to-purple-600 shadow-lg shadow-violet-200/60">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-800">2-Way Sync <span class="text-xs font-normal text-slate-400 ml-1">(ไม่บังคับ)</span></h2>
                    <p class="text-xs text-slate-400 mt-0.5">เปิดให้ UCM สร้าง/ลบ Permission Definitions ในระบบปลายทางด้วย</p>
                </div>
            </div>
            <div class="px-6 py-6 space-y-6">

                {{-- Enable toggle --}}
                <div class="flex items-start gap-3 p-4 bg-violet-50 border border-violet-100 rounded-xl">
                    <input type="checkbox" id="enable_two_way" onchange="wizToggle2Way(this.checked)"
                           class="mt-0.5 w-4 h-4 text-violet-600 rounded border-slate-300 cursor-pointer">
                    <div>
                        <label for="enable_two_way" class="text-sm font-semibold text-slate-800 cursor-pointer">เปิดใช้งาน 2-Way Sync</label>
                        <p class="text-xs text-slate-500 mt-0.5">เมื่อ Admin เพิ่ม Permission ใน UCM → สร้าง record ใน DB ระบบปลายทาง<br>เมื่อ Admin ลบ Permission ใน UCM → ดำเนินการตาม Delete Mode ที่เลือก</p>
                    </div>
                </div>

                {{-- 2-Way fields (hidden until toggle enabled) --}}
                <div id="two-way-fields" class="hidden space-y-5">

                    {{-- Definition Table --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">ตาราง Permission Definitions <span class="text-rose-500">*</span></label>
                        <p class="text-xs text-slate-400 mb-2">ตารางที่เก็บนิยาม Permission (ต่างจาก junction table ที่เก็บ user↔permission mapping)</p>
                        <div class="flex gap-2">
                            <select id="field_perm_def_table" name="perm_def_table" onchange="wizLoadDefColumns()"
                                    class="flex-1 px-3 py-2 text-sm text-slate-800 bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-violet-500 transition">
                                <option value="">— เลือกตาราง —</option>
                                @if(isset($editConfig) && $editConfig->perm_def_table)
                                    <option value="{{ $editConfig->perm_def_table }}" selected>{{ $editConfig->perm_def_table }}</option>
                                @endif
                            </select>
                            <button type="button" onclick="wizLoadTables('perm_def_table', 'wizLoadDefColumns')"
                                    class="px-3 py-2 text-xs font-semibold text-violet-600 bg-violet-50 border border-violet-200 rounded-xl hover:bg-violet-100 transition whitespace-nowrap">
                                โหลดตาราง
                            </button>
                        </div>
                    </div>

                    {{-- Columns --}}
                    <div id="def-columns-wrap" class="{{ (isset($editConfig) && $editConfig->perm_def_table) ? '' : 'hidden' }} grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-600 mb-1.5">คอลัมน์ Key/Value <span class="text-rose-500">*</span></label>
                            <select id="field_perm_def_value_col" name="perm_def_value_col"
                                    class="w-full px-3 py-2 text-sm text-slate-800 bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-violet-500 transition">
                                <option value="">— เลือกคอลัมน์ —</option>
                                @if(isset($editConfig) && $editConfig->perm_def_value_col)
                                    <option value="{{ $editConfig->perm_def_value_col }}" selected>{{ $editConfig->perm_def_value_col }}</option>
                                @endif
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-600 mb-1.5">คอลัมน์ Primary Key</label>
                            <select id="field_perm_def_pk_col" name="perm_def_pk_col"
                                    class="w-full px-3 py-2 text-sm text-slate-800 bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-violet-500 transition">
                                <option value="">id (default)</option>
                                @if(isset($editConfig) && $editConfig->perm_def_pk_col)
                                    <option value="{{ $editConfig->perm_def_pk_col }}" selected>{{ $editConfig->perm_def_pk_col }}</option>
                                @endif
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-600 mb-1.5">คอลัมน์ Label <span class="text-slate-400 font-normal">(ไม่บังคับ)</span></label>
                            <select id="field_perm_def_label_col" name="perm_def_label_col"
                                    class="w-full px-3 py-2 text-sm text-slate-800 bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-violet-500 transition">
                                <option value="">(ไม่ระบุ)</option>
                                @if(isset($editConfig) && $editConfig->perm_def_label_col)
                                    <option value="{{ $editConfig->perm_def_label_col }}" selected>{{ $editConfig->perm_def_label_col }}</option>
                                @endif
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-600 mb-1.5">คอลัมน์ Group <span class="text-slate-400 font-normal">(ไม่บังคับ)</span></label>
                            <select id="field_perm_def_group_col" name="perm_def_group_col"
                                    class="w-full px-3 py-2 text-sm text-slate-800 bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-violet-500 transition">
                                <option value="">(ไม่ระบุ)</option>
                                @if(isset($editConfig) && $editConfig->perm_def_group_col)
                                    <option value="{{ $editConfig->perm_def_group_col }}" selected>{{ $editConfig->perm_def_group_col }}</option>
                                @endif
                            </select>
                        </div>
                    </div>

                    {{-- Delete Mode --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-3">Delete Mode — พฤติกรรมเมื่อ Admin ลบ Permission ใน UCM</label>
                        <div class="space-y-2">
                            <label class="flex items-start gap-3 p-3.5 border rounded-xl cursor-pointer transition hover:bg-slate-50 border-slate-200" id="dmode-card-detach_only">
                                <input type="radio" name="perm_delete_mode" value="detach_only" checked
                                       onchange="wizToggleDeleteMode('detach_only')"
                                       class="mt-0.5 w-4 h-4 text-violet-600">
                                <div>
                                    <div class="text-sm font-semibold text-slate-800">Detach Only</div>
                                    <div class="text-xs text-slate-500 mt-0.5">ลบเฉพาะใน UCM — ไม่แตะ record ในระบบปลายทาง (ปลอดภัยที่สุด)</div>
                                </div>
                            </label>
                            <label class="flex items-start gap-3 p-3.5 border rounded-xl cursor-pointer transition hover:bg-red-50 border-slate-200" id="dmode-card-hard">
                                <input type="radio" name="perm_delete_mode" value="hard"
                                       onchange="wizToggleDeleteMode('hard')"
                                       class="mt-0.5 w-4 h-4 text-violet-600">
                                <div>
                                    <div class="text-sm font-semibold text-slate-800">Hard Delete</div>
                                    <div class="text-xs text-slate-500 mt-0.5">DELETE FROM definition table — ลบถาวร ไม่สามารถกู้คืนได้</div>
                                </div>
                            </label>
                            <label class="flex items-start gap-3 p-3.5 border rounded-xl cursor-pointer transition hover:bg-amber-50 border-slate-200" id="dmode-card-soft">
                                <input type="radio" name="perm_delete_mode" value="soft"
                                       onchange="wizToggleDeleteMode('soft')"
                                       class="mt-0.5 w-4 h-4 text-violet-600">
                                <div>
                                    <div class="text-sm font-semibold text-slate-800">Soft Delete</div>
                                    <div class="text-xs text-slate-500 mt-0.5">UPDATE ด้วยค่า "ลบแล้ว" — record ยังอยู่ใน DB แต่ถูก mark ว่าลบ</div>
                                </div>
                            </label>
                        </div>
                    </div>

                    {{-- Soft delete fields --}}
                    <div id="soft-delete-fields" class="hidden grid grid-cols-1 sm:grid-cols-2 gap-4 p-4 bg-amber-50 border border-amber-100 rounded-xl">
                        <div>
                            <label class="block text-xs font-bold text-amber-700 mb-1.5">คอลัมน์ Soft Delete <span class="text-rose-500">*</span></label>
                            <p class="text-xs text-amber-600 mb-1.5">คอลัมน์ที่จะ UPDATE เช่น <code class="font-mono bg-amber-100 px-1 rounded">is_deleted</code>, <code class="font-mono bg-amber-100 px-1 rounded">deleted_at</code></p>
                            <input type="text" id="field_perm_def_soft_delete_col" name="perm_def_soft_delete_col"
                                   value="{{ isset($editConfig) ? ($editConfig->perm_def_soft_delete_col ?? '') : '' }}"
                                   placeholder="เช่น is_deleted"
                                   class="w-full px-3 py-2 text-sm font-mono text-slate-800 bg-white border border-amber-200 rounded-xl focus:outline-none focus:border-amber-500 transition">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-amber-700 mb-1.5">ค่าที่หมายถึง "ลบแล้ว" <span class="text-rose-500">*</span></label>
                            <p class="text-xs text-amber-600 mb-1.5">ค่าที่จะ SET เช่น <code class="font-mono bg-amber-100 px-1 rounded">1</code>, <code class="font-mono bg-amber-100 px-1 rounded">deleted</code></p>
                            <input type="text" id="field_perm_def_soft_delete_val" name="perm_def_soft_delete_val"
                                   value="{{ isset($editConfig) ? ($editConfig->perm_def_soft_delete_val ?? '1') : '1' }}"
                                   placeholder="เช่น 1 หรือ deleted"
                                   class="w-full px-3 py-2 text-sm font-mono text-slate-800 bg-white border border-amber-200 rounded-xl focus:outline-none focus:border-amber-500 transition">
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <div class="flex items-center justify-between mt-5">
            <button class="{{ $btnG }}" onclick="wizPrev(5)">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                ย้อนกลับ
            </button>
            <div class="flex items-center gap-3">
                <span class="text-xs text-slate-400 font-semibold">ขั้นที่ 5 จาก 6</span>
                <button class="{{ $btnP }}" onclick="wizNext(5)">
                    ถัดไป
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </button>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════
         STEP 6 — Confirm
    ══════════════════════════════════════════════════ --}}
    <div class="wiz-step hidden" data-step="6">
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
            <button class="{{ $btnG }}" onclick="wizPrev(6)">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                ย้อนกลับ
            </button>
            <div class="flex items-center gap-3">
                <span class="text-xs text-slate-400 font-semibold">ขั้นที่ 6 จาก 6</span>
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
    var EDIT_UPDATE_URL = @json(isset($editConfig) ? route('connectors.update', $editConfig->id) : null);
    var AI_AVAILABLE = @json($aiAvailable ?? false);
    var ROUTE_ANALYZE_SCHEMA = '{{ route("connectors.ajax.analyze-schema") }}';
    var ROUTE_ANALYZE_ZIP    = '{{ route("connectors.ajax.analyze-zip") }}';

    var currentStep    = 1;
    var tables         = [];
    var connTested     = false;
    var wizSuggestion  = null;

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
        if (from + 1 === 4) { wizApplyPermSuggestion(); }
        if (from + 1 === 6) { wizBuildSummary(); }
    };

    window.wizPrev = function (from) {
        showStep(from - 1);
    };

    function showStep(n) {
        currentStep = n;
        document.querySelectorAll('.wiz-step').forEach(function (el) {
            el.classList.toggle('hidden', parseInt(el.dataset.step) !== n);
        });
        for (var i = 1; i <= 6; i++) {
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
            db_driver:            radioVal('db_driver'),
            db_host:              val('db_host'),
            db_port:              val('db_port'),
            db_name:              val('db_name'),
            db_user:              val('db_user'),
            db_password:          document.getElementById('field_db_password')?.value ?? '',
            connector_config_id:  IS_EDIT ? EDIT_CONFIG_ID : null,
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
                var panel = document.getElementById('analysis-panel');
                if (panel) { panel.style.display = ''; }
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

    window.wizLoadTables = function (fieldId, onChangeFn, preselectVal) {
        wizFetchTablesInternal(function (tbls) {
            var sel = document.getElementById('field_' + fieldId);
            if (! sel) return;
            var current = (preselectVal !== undefined && preselectVal !== null) ? preselectVal : sel.value;
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
            { id: 'field_user_pk_col', nullable: true, preselect: EDIT_CONFIG?.user_pk_col },
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
        var compositeSection = document.getElementById('perm-composite-section');
        if (compositeSection) compositeSection.classList.toggle('hidden', mode !== 'junction');
    };

    // ── 2-Way Sync Toggle ──────────────────────────────────────────────────

    window.wizToggle2Way = function (enabled) {
        document.getElementById('two-way-fields').classList.toggle('hidden', ! enabled);
        if (enabled) {
            // ใช้ค่าปัจจุบันใน select หรือ EDIT_CONFIG เป็น preselect
            var preselect = val('perm_def_table') || (EDIT_CONFIG ? EDIT_CONFIG.perm_def_table || null : null);
            wizLoadTables('perm_def_table', 'wizLoadDefColumns', preselect);
        }
    };

    window.wizLoadDefColumns = function () {
        var table = val('perm_def_table');
        if (! table) return;
        var wrap = document.getElementById('def-columns-wrap');
        if (wrap) { wrap.classList.remove('hidden'); }
        loadColumnsFor(table, [
            { id: 'field_perm_def_value_col',   preselect: EDIT_CONFIG?.perm_def_value_col },
            { id: 'field_perm_def_pk_col',      nullable: true, preselect: EDIT_CONFIG?.perm_def_pk_col },
            { id: 'field_perm_def_label_col',   nullable: true, preselect: EDIT_CONFIG?.perm_def_label_col },
            { id: 'field_perm_def_group_col',   nullable: true, preselect: EDIT_CONFIG?.perm_def_group_col },
        ]);
    };

    window.wizToggleDeleteMode = function (mode) {
        ['detach_only', 'hard', 'soft'].forEach(function (m) {
            var card = document.getElementById('dmode-card-' + m);
            if (! card) return;
            if (m === mode) {
                card.classList.remove('border-slate-200');
                card.classList.add(m === 'hard' ? 'border-red-300' : m === 'soft' ? 'border-amber-300' : 'border-violet-300');
            } else {
                card.classList.remove('border-red-300', 'border-amber-300', 'border-violet-300');
                card.classList.add('border-slate-200');
            }
        });
        document.getElementById('soft-delete-fields').classList.toggle('hidden', mode !== 'soft');
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
            {
                title: '2-Way Sync',
                rows: (function() {
                    var enabled = document.getElementById('enable_two_way')?.checked;
                    if (! enabled) return [['สถานะ', 'ปิดใช้งาน']];
                    var modeLabels2 = { hard: 'Hard Delete', soft: 'Soft Delete', detach_only: 'Detach Only' };
                    var rows = [
                        ['สถานะ', 'เปิดใช้งาน'],
                        ['Definition Table', val('perm_def_table') || '-'],
                        ['Key Column', val('perm_def_value_col') || '-'],
                        ['Delete Mode', modeLabels2[radioVal('perm_delete_mode')] || 'Detach Only'],
                    ];
                    if (radioVal('perm_delete_mode') === 'soft') {
                        rows.push(['Soft Delete Column', val('perm_def_soft_delete_col') || '-']);
                        rows.push(['Soft Delete Value', val('perm_def_soft_delete_val') || '1']);
                    }
                    return rows;
                })(),
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
        var twoWayEnabled = document.getElementById('enable_two_way')?.checked;
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
            user_pk_col:            val('user_pk_col') || null,
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
            perm_composite_cols:    mode === 'junction' ? JSON.stringify(wizGetCompositeCols()) : null,
            manual_permissions:     mode === 'manual' ? JSON.stringify(getManualPerms()) : null,
            perm_def_table:           twoWayEnabled ? (val('perm_def_table') || (IS_EDIT ? EDIT_CONFIG?.perm_def_table || null : null)) : null,
            perm_def_value_col:       twoWayEnabled ? (val('perm_def_value_col') || (IS_EDIT ? EDIT_CONFIG?.perm_def_value_col || null : null)) : null,
            perm_def_pk_col:          twoWayEnabled ? (val('perm_def_pk_col') || (IS_EDIT ? EDIT_CONFIG?.perm_def_pk_col || null : null)) : null,
            perm_def_label_col:       twoWayEnabled ? (val('perm_def_label_col') || (IS_EDIT ? EDIT_CONFIG?.perm_def_label_col || null : null)) : null,
            perm_def_group_col:       twoWayEnabled ? (val('perm_def_group_col') || (IS_EDIT ? EDIT_CONFIG?.perm_def_group_col || null : null)) : null,
            perm_delete_mode:         twoWayEnabled ? (radioVal('perm_delete_mode') || (IS_EDIT ? EDIT_CONFIG?.perm_delete_mode || null : null) || 'detach_only') : null,
            perm_def_soft_delete_col: twoWayEnabled && radioVal('perm_delete_mode') === 'soft' ? (val('perm_def_soft_delete_col') || (IS_EDIT ? EDIT_CONFIG?.perm_def_soft_delete_col || null : null)) : null,
            perm_def_soft_delete_val: twoWayEnabled && radioVal('perm_delete_mode') === 'soft' ? (val('perm_def_soft_delete_val') || (IS_EDIT ? EDIT_CONFIG?.perm_def_soft_delete_val || null : null)) : null,
        });

        var submitUrl    = IS_EDIT ? EDIT_UPDATE_URL : '{{ route("connectors.store") }}';
        var submitMethod = IS_EDIT ? 'PUT' : 'POST';

        fetch(submitUrl, {
            method: submitMethod,
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

    // ── AI Schema Analysis ─────────────────────────────────────────────────

    window.wizToggleAI = function (checked) {
        var track  = document.getElementById('ai-toggle-track');
        var thumb  = document.getElementById('ai-toggle-thumb');
        var zipRow = document.getElementById('zip-upload-row');
        if (track) { track.style.backgroundColor = checked ? '#6366f1' : ''; track.classList.toggle('bg-indigo-500', checked); track.classList.toggle('bg-slate-200', ! checked); }
        if (thumb) { thumb.style.transform = checked ? 'translateX(16px)' : ''; }
        if (zipRow) { zipRow.style.display = checked ? '' : 'none'; }
    };

    window.wizHandleZipSelect = function (input) {
        var file = input.files[0];
        if (! file) { return; }
        document.getElementById('zip-placeholder').style.display = 'none';
        document.getElementById('zip-selected').style.display = '';
        document.getElementById('zip-filename').textContent = file.name + ' (' + (file.size / 1024 / 1024).toFixed(1) + ' MB)';
    };

    window.wizHandleZipDrop = function (e) {
        e.preventDefault();
        var file = e.dataTransfer.files[0];
        if (! file || ! file.name.endsWith('.zip')) { return; }
        var input = document.getElementById('source_zip_input');
        var dt    = new DataTransfer();
        dt.items.add(file);
        input.files = dt.files;
        wizHandleZipSelect(input);
    };

    window.wizClearZip = function (e) {
        e.stopPropagation();
        var input = document.getElementById('source_zip_input');
        input.value = '';
        document.getElementById('zip-placeholder').style.display = '';
        document.getElementById('zip-selected').style.display = 'none';
    };

    window.wizRunAnalysis = function () {
        var btn    = document.getElementById('run-analysis-btn');
        var result = document.getElementById('analysis-result');
        var useAI  = AI_AVAILABLE && document.getElementById('use_ai_toggle') && document.getElementById('use_ai_toggle').checked;
        var zipInput = document.getElementById('source_zip_input');
        var hasZip   = useAI && zipInput && zipInput.files.length > 0;

        btn.disabled = true;
        btn.innerHTML = '<svg style="width:.875rem;height:.875rem;animation:spin 1s linear infinite" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg> กำลังวิเคราะห์...';
        result.style.display = 'none';

        if (hasZip) {
            var formData = new FormData();
            formData.append('_token', CSRF);
            var cd = connData();
            Object.keys(cd).forEach(function (k) { formData.append(k, cd[k]); });
            formData.append('source_zip', zipInput.files[0]);

            fetch(ROUTE_ANALYZE_ZIP, { method: 'POST', body: formData })
                .then(function (r) { return r.json(); })
                .then(function (res) { wizRenderAnalysis(res, btn, result); })
                .catch(function (e) { wizRenderAnalysisError(e.message, btn, result); });
        } else {
            var payload = Object.assign({}, connData(), { use_ai: useAI ? 1 : 0 });
            post(ROUTE_ANALYZE_SCHEMA, payload, function (res) {
                wizRenderAnalysis(res, btn, result);
            });
        }
    };

    function wizRenderAnalysis(res, btn, result) {
        btn.disabled = false;
        btn.innerHTML = '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg> วิเคราะห์และแนะนำการตั้งค่า';
        result.style.display = '';

        if (! res.ok) {
            result.innerHTML = '<div class="rounded-lg p-3 bg-rose-50 border border-rose-200 text-rose-700 text-xs">' + escHtml(res.message || 'เกิดข้อผิดพลาด') + '</div>';
            return;
        }

        var suggestion = res.ai || res.rule;
        if (! suggestion) {
            result.innerHTML = '<div class="rounded-lg p-3 bg-amber-50 border border-amber-200 text-amber-700 text-xs">ไม่สามารถวิเคราะห์ได้</div>';
            return;
        }

        wizSuggestion = suggestion;
        var source   = res.ai ? ('AI (Claude' + (res.framework ? ' · ' + escHtml(res.framework) : '') + ')') : 'Rule-based';
        var ut       = suggestion.user_table || {};
        var perm     = suggestion.permission || {};
        var masters  = suggestion.master_tables || [];

        var html = '<div class="space-y-2.5">';

        // Source badge
        html += '<div class="flex items-center gap-1.5"><span class="text-[11px] font-semibold px-2 py-0.5 rounded-full ' + (res.ai ? 'bg-violet-100 text-violet-700' : 'bg-indigo-100 text-indigo-700') + '">' + source + '</span><span class="text-[11px] text-slate-400">วิเคราะห์เสร็จสิ้น</span></div>';

        // User Table suggestion
        if (ut.table) {
            html += '<div class="rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2.5">';
            html += '<div class="flex items-center justify-between mb-1.5">';
            html += '<span class="text-xs font-bold text-emerald-700">ตาราง Users: <code class="font-mono">' + escHtml(ut.table) + '</code></span>';
            html += '<span class="text-[11px] text-slate-500">' + (ut.confidence || 0) + '% มั่นใจ</span>';
            html += '</div>';
            if (ut.reasons && ut.reasons.length) {
                html += '<ul class="text-[11px] text-emerald-700 space-y-0.5 mb-2">' + ut.reasons.map(function (r) { return '<li>• ' + escHtml(r) + '</li>'; }).join('') + '</ul>';
            }
            html += '<button type="button" class="text-[11px] font-semibold px-2.5 py-1 rounded-md bg-emerald-600 text-white hover:bg-emerald-700 transition" onclick="wizApplyUserSuggestion()">ใช้การตั้งค่านี้ →</button>';
            html += '</div>';
        }

        // Permission suggestion
        if (perm.mode) {
            var modeLabel = perm.mode === 'junction' ? 'Junction Table' : perm.mode === 'column' ? 'Single Column' : 'Manual';
            html += '<div class="rounded-lg border border-blue-200 bg-blue-50 px-3 py-2.5">';
            html += '<div class="flex items-center justify-between mb-1.5">';
            html += '<span class="text-xs font-bold text-blue-700">Permission Mode: <code class="font-mono">' + escHtml(modeLabel) + '</code>' + (perm.table ? ' · <code class="font-mono">' + escHtml(perm.table) + '</code>' : '') + '</span>';
            html += '<span class="text-[11px] text-slate-500">' + (perm.confidence || 0) + '% มั่นใจ</span>';
            html += '</div>';
            if (perm.reasons && perm.reasons.length) {
                html += '<ul class="text-[11px] text-blue-700 space-y-0.5">' + perm.reasons.map(function (r) { return '<li>• ' + escHtml(r) + '</li>'; }).join('') + '</ul>';
            }
            html += '</div>';
        }

        // Master tables
        if (masters.length > 0) {
            html += '<div class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2">';
            html += '<p class="text-[11px] font-semibold text-slate-500 mb-1">Reference Tables ที่แนะนำ</p>';
            html += '<div class="flex flex-wrap gap-1">' + masters.map(function (m) { return '<span class="text-[10px] font-mono px-1.5 py-0.5 rounded bg-white border border-slate-200 text-slate-600">' + escHtml(m.table) + '</span>'; }).join('') + '</div>';
            html += '</div>';
        }

        html += '</div>';
        result.innerHTML = html;
    }

    function wizRenderAnalysisError(msg, btn, result) {
        btn.disabled = false;
        btn.innerHTML = '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg> วิเคราะห์และแนะนำการตั้งค่า';
        result.style.display = '';
        result.innerHTML = '<div class="rounded-lg p-3 bg-rose-50 border border-rose-200 text-rose-700 text-xs">' + escHtml(msg) + '</div>';
    }

    window.wizApplyUserSuggestion = function () {
        if (! wizSuggestion) { return; }
        var ut = wizSuggestion.user_table;
        if (! ut || ! ut.table) { return; }

        showStep(3);

        var doApply = function () {
            // Populate the user_table <select> with options (it starts empty)
            var sel = document.getElementById('field_user_table');
            if (! sel) { return; }
            sel.innerHTML = '<option value="">— เลือกตาราง —</option>';
            tables.forEach(function (t) {
                var opt = document.createElement('option');
                opt.value = t;
                opt.textContent = t;
                if (t === ut.table) { opt.selected = true; }
                sel.appendChild(opt);
            });

            // Fetch columns and pre-select each one via loadColumnsFor's preselect param
            var m = ut.mapping || {};
            loadColumnsFor(ut.table, [
                { id: 'field_user_identifier_col',                  preselect: m.identifier },
                { id: 'field_user_pk_col',     nullable: true,      preselect: m.pk_col },
                { id: 'field_user_name_col',   nullable: true,      preselect: m.name },
                { id: 'field_user_email_col',  nullable: true,      preselect: m.email },
                { id: 'field_user_dept_col',   nullable: true,      preselect: m.department },
                { id: 'field_user_status_col', nullable: true,      preselect: m.status },
            ]);

            if (m.status) { setTimeout(wizToggleStatusVal, 800); }
        };

        if (tables.length > 0) {
            doApply();
        } else {
            wizFetchTablesInternal(doApply);
        }
    };

    // Apply permission suggestion when step 4 loads (called from wizNext hook)
    function wizApplyPermSuggestion() {
        if (! wizSuggestion) { return; }
        var perm = wizSuggestion.permission;
        if (! perm || ! perm.mode) { return; }

        selectPermMode(perm.mode);
        wizPermModeChange(perm.mode);

        if (perm.mode === 'manual') { return; }

        // Determine which table to use for the perm_table select
        var tableToUse = perm.table || null;
        if (perm.mode === 'column' && ! tableToUse) {
            // Column mode: permission column lives on the user table itself
            tableToUse = (wizSuggestion.user_table || {}).table || null;
        }
        if (! tableToUse) { return; }

        // Populate the perm_table <select> with options
        var sel = document.getElementById('field_perm_table');
        if (sel) {
            sel.innerHTML = '<option value="">— เลือกตาราง —</option>';
            tables.forEach(function (t) {
                var opt = document.createElement('option');
                opt.value = t; opt.textContent = t;
                if (t === tableToUse) { opt.selected = true; }
                sel.appendChild(opt);
            });
        }

        if (perm.mode === 'junction') {
            loadColumnsFor(tableToUse, [
                { id: 'field_perm_user_fk_col',                    preselect: perm.user_fk_col },
                { id: 'field_perm_value_col',                       preselect: perm.value_col },
                { id: 'field_perm_label_col', nullable: true,       preselect: perm.label_col },
                { id: 'field_perm_group_col', nullable: true,       preselect: perm.group_col },
            ]);

            // Apply composite cols — delay until loadColumnsFor AJAX completes
            if (perm.composite_cols && perm.composite_cols.length > 0) {
                var list = document.getElementById('composite-cols-list');
                if (list) { list.innerHTML = ''; }
                var hint = document.getElementById('composite-empty-hint');
                if (hint) { hint.style.display = ''; }
                setTimeout(function () {
                    perm.composite_cols.forEach(function (cc) { wizAddCompositeCol(cc); });
                }, 600);
            }
        } else if (perm.mode === 'column') {
            // column: perm.column is the permission column on the user table
            loadColumnsFor(tableToUse, [
                { id: 'field_perm_value_col',                       preselect: perm.column },
                { id: 'field_perm_label_col', nullable: true },
                { id: 'field_perm_group_col', nullable: true },
            ]);
        }
    }

    // ── Composite Junction Columns ─────────────────────────────────────────

    window.wizAddCompositeCol = function (preset) {
        preset = preset || {};
        var list = document.getElementById('composite-cols-list');
        var hint = document.getElementById('composite-empty-hint');
        if (! list) { return; }

        // Build master table options from tables array fetched in step 2
        var masterOpts = '<option value="">(ไม่ระบุ)</option>';
        tables.forEach(function (t) {
            masterOpts += '<option value="' + escHtml(t) + '">' + escHtml(t) + '</option>';
        });

        var selectCls = 'w-full pl-3 pr-8 py-1.5 text-xs text-slate-800 bg-white border border-slate-200 rounded-lg appearance-none focus:outline-none focus:border-indigo-500';

        var row = document.createElement('div');
        row.className = 'composite-col-row flex items-start gap-2 p-2.5 bg-slate-50 rounded-lg border border-slate-200';
        row.innerHTML = [
            '<div class="flex-1 min-w-0">',
            '  <label class="block text-[11px] font-semibold text-slate-500 mb-1">คอลัมน์ใน Mapping Table <span class="text-rose-500">*</span></label>',
            '  <select class="composite-col-select ' + selectCls + '">',
            '    <option value="">— เลือกคอลัมน์ —</option>',
            '  </select>',
            '</div>',
            '<div class="flex-1 min-w-0">',
            '  <label class="block text-[11px] font-semibold text-slate-500 mb-1">Master Table <span class="text-[10px] font-normal">(ไม่บังคับ)</span></label>',
            '  <select class="composite-master-table ' + selectCls + '" onchange="wizLoadCompositeLabelCols(this)">',
            masterOpts,
            '  </select>',
            '</div>',
            '<div class="flex-1 min-w-0">',
            '  <label class="block text-[11px] font-semibold text-slate-500 mb-1">Label Column <span class="text-[10px] font-normal">(ไม่บังคับ)</span></label>',
            '  <select class="composite-master-label ' + selectCls + '">',
            '    <option value="">(ไม่ระบุ)</option>',
            '  </select>',
            '</div>',
            '<button type="button" class="mt-5 text-slate-400 hover:text-rose-500 transition flex-shrink-0" onclick="wizRemoveCompositeCol(this)">',
            '  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>',
            '</button>',
        ].join('');

        list.appendChild(row);
        if (hint) { hint.style.display = 'none'; }

        // Populate junction-column select from current perm_table columns
        var permTable = val('perm_table');
        if (permTable) {
            var colSelect    = row.querySelector('.composite-col-select');
            var existingCols = [];
            var existingSel  = document.getElementById('field_perm_value_col');
            if (existingSel) {
                for (var i = 0; i < existingSel.options.length; i++) {
                    if (existingSel.options[i].value) { existingCols.push(existingSel.options[i].value); }
                }
            }
            existingCols.forEach(function (c) {
                var opt = document.createElement('option');
                opt.value = c; opt.textContent = c;
                if (c === preset.col) { opt.selected = true; }
                colSelect.appendChild(opt);
            });
        }

        // Set preset master_table then load its columns for label select
        var masterSel = row.querySelector('.composite-master-table');
        if (preset.master_table) {
            masterSel.value = preset.master_table;
            wizLoadCompositeLabelCols(masterSel, preset.master_label_col);
        }
    };

    // Load columns for the label-column select of a composite row
    window.wizLoadCompositeLabelCols = function (masterSel, preselect) {
        var row      = masterSel.closest('.composite-col-row');
        var labelSel = row ? row.querySelector('.composite-master-label') : null;
        if (! labelSel) { return; }
        var tableName = masterSel.value;
        labelSel.innerHTML = '<option value="">(ไม่ระบุ)</option>';
        if (! tableName) { return; }
        var data = Object.assign(connData(), { table: tableName });
        post('{{ route("connectors.ajax.fetch-columns") }}', data, function (res) {
            if (! res.ok) { return; }
            var cols = res.columns || [];
            cols.forEach(function (c) {
                var opt = document.createElement('option');
                opt.value = c; opt.textContent = c;
                if (c === preselect) { opt.selected = true; }
                labelSel.appendChild(opt);
            });
        });
    };

    window.wizRemoveCompositeCol = function (btn) {
        var row = btn.closest('.composite-col-row');
        if (row) { row.remove(); }
        var list = document.getElementById('composite-cols-list');
        var hint = document.getElementById('composite-empty-hint');
        if (hint && list && list.children.length === 0) { hint.style.display = ''; }
    };

    function wizGetCompositeCols() {
        var rows   = document.querySelectorAll('.composite-col-row');
        var result = [];
        rows.forEach(function (row) {
            var col = row.querySelector('.composite-col-select') ? row.querySelector('.composite-col-select').value : '';
            if (! col) { return; }
            var entry = { col: col };
            var mt    = row.querySelector('.composite-master-table') ? row.querySelector('.composite-master-table').value : '';
            var ml    = row.querySelector('.composite-master-label') ? row.querySelector('.composite-master-label').value : '';
            if (mt) { entry.master_table = mt; }
            if (ml) { entry.master_label_col = ml; }
            result.push(entry);
        });

        return result;
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

            // Restore composite cols in edit mode
            if (pMode === 'junction' && EDIT_CONFIG.perm_composite_cols && EDIT_CONFIG.perm_composite_cols.length > 0) {
                setTimeout(function () {
                    EDIT_CONFIG.perm_composite_cols.forEach(function (cc) { wizAddCompositeCol(cc); });
                }, 800);
            }
        });
    }

    // ── Init 2-Way for edit mode ───────────────────────────────────────────
    if (IS_EDIT && EDIT_CONFIG && EDIT_CONFIG.perm_def_table) {
        var cb = document.getElementById('enable_two_way');
        if (cb) {
            cb.checked = true;
            wizToggle2Way(true);
            if (EDIT_CONFIG.perm_delete_mode) {
                var radio = document.querySelector('input[name="perm_delete_mode"][value="' + EDIT_CONFIG.perm_delete_mode + '"]');
                if (radio) { radio.checked = true; wizToggleDeleteMode(EDIT_CONFIG.perm_delete_mode); }
            }
            // Pre-populate soft delete text inputs (server-side value อาจถูก override ตรงนี้ถ้า JS ต้องการ)
            // ค่าถูก render server-side แล้วใน value=, ส่วนนี้เป็น safety net
            if (EDIT_CONFIG.perm_def_soft_delete_col) {
                var sdcEl = document.getElementById('field_perm_def_soft_delete_col');
                if (sdcEl && ! sdcEl.value) { sdcEl.value = EDIT_CONFIG.perm_def_soft_delete_col; }
            }
            if (EDIT_CONFIG.perm_def_soft_delete_val) {
                var sdvEl = document.getElementById('field_perm_def_soft_delete_val');
                if (sdvEl && ! sdvEl.value) { sdvEl.value = EDIT_CONFIG.perm_def_soft_delete_val; }
            }
        }
    }

})();
</script>

@endsection
