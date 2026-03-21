@extends('layouts.app')

@section('title', isset($editConfig) ? 'แก้ไข Connector' : 'Connector Wizard')

@section('content')

<style>
/* ── Base Elements ── */
.wiz-input {
    display: block; width: 100%;
    padding: 0.5rem 0.75rem;
    font-size: 0.875rem; line-height: 1.5;
    color: #1e293b;
    background: #fff;
    border: 1.5px solid #e2e8f0;
    border-radius: 0.5rem;
    outline: none;
    transition: border-color 0.15s, box-shadow 0.15s;
}
.wiz-input:focus { border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,.12); }
.wiz-input::placeholder { color: #94a3b8; }
textarea.wiz-input { resize: none; }

.wiz-select {
    display: block; width: 100%;
    padding: 0.5rem 2rem 0.5rem 0.75rem;
    font-size: 0.875rem; line-height: 1.5;
    color: #1e293b;
    background: #fff url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%2394a3b8' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e") no-repeat right 0.5rem center / 1.25rem;
    border: 1.5px solid #e2e8f0;
    border-radius: 0.5rem;
    outline: none;
    appearance: none;
    -webkit-appearance: none;
    transition: border-color 0.15s, box-shadow 0.15s;
}
.wiz-select:focus { border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,.12); }

.wiz-label {
    display: block;
    font-size: 0.8125rem;
    font-weight: 600;
    color: #475569;
    margin-bottom: 0.375rem;
    letter-spacing: 0.01em;
}
.wiz-hint { font-size: 0.75rem; color: #94a3b8; margin-top: 0.3rem; }
.wiz-req { color: #f43f5e; margin-left: 2px; }

/* ── Buttons ── */
.btn-primary {
    display: inline-flex; align-items: center; gap: 0.4rem;
    padding: 0.6rem 1.4rem;
    font-size: 0.875rem; font-weight: 600;
    color: #fff;
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    border: none; border-radius: 0.6rem;
    cursor: pointer;
    transition: opacity .15s, transform .1s, box-shadow .15s;
    box-shadow: 0 2px 8px rgba(99,102,241,.35);
}
.btn-primary:hover { opacity: .93; box-shadow: 0 4px 14px rgba(99,102,241,.4); }
.btn-primary:active { transform: scale(.98); }
.btn-primary:disabled { opacity: .5; cursor: not-allowed; transform: none; }

.btn-ghost {
    display: inline-flex; align-items: center; gap: 0.4rem;
    padding: 0.6rem 1.2rem;
    font-size: 0.875rem; font-weight: 500;
    color: #64748b;
    background: transparent;
    border: 1.5px solid #e2e8f0;
    border-radius: 0.6rem;
    cursor: pointer;
    transition: background .15s, color .15s, border-color .15s;
}
.btn-ghost:hover { background: #f8fafc; color: #334155; border-color: #cbd5e1; }

.btn-outline-indigo {
    display: inline-flex; align-items: center; gap: 0.4rem;
    padding: 0.5rem 1rem;
    font-size: 0.8125rem; font-weight: 600;
    color: #6366f1;
    background: rgba(99,102,241,.05);
    border: 1.5px solid rgba(99,102,241,.25);
    border-radius: 0.5rem;
    cursor: pointer;
    transition: background .15s, border-color .15s;
    white-space: nowrap;
}
.btn-outline-indigo:hover { background: rgba(99,102,241,.1); border-color: rgba(99,102,241,.5); }

/* ── Step Indicator ── */
.step-wrap { display: flex; align-items: flex-start; }
.step-item { display: flex; flex-direction: column; align-items: center; gap: .35rem; flex-shrink: 0; }
.step-circle {
    width: 2.25rem; height: 2.25rem; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: .8125rem; font-weight: 700;
    border: 2px solid #e2e8f0;
    background: #fff; color: #94a3b8;
    transition: all .25s;
    position: relative; z-index: 1;
}
.step-circle.done {
    background: #6366f1; border-color: #6366f1; color: #fff;
}
.step-circle.active {
    background: #6366f1; border-color: #6366f1; color: #fff;
    box-shadow: 0 0 0 4px rgba(99,102,241,.2);
}
.step-label { font-size: .6875rem; font-weight: 600; color: #94a3b8; transition: color .25s; white-space: nowrap; }
.step-label.done, .step-label.active { color: #6366f1; }
.step-line { flex: 1; height: 2px; background: #e2e8f0; margin: 0 .4rem; margin-top: -1.1rem; transition: background .25s; }
.step-line.done { background: #6366f1; }

/* ── Step Card ── */
.step-card {
    background: #fff;
    border: 1px solid #e8edf4;
    border-radius: 1rem;
    box-shadow: 0 1px 3px rgba(0,0,0,.04), 0 4px 16px rgba(0,0,0,.04);
    overflow: hidden;
}
.step-card-header {
    padding: 1.25rem 1.5rem 1rem;
    border-bottom: 1px solid #f1f5f9;
    display: flex; align-items: flex-start; gap: 1rem;
}
.step-icon-wrap {
    width: 2.5rem; height: 2.5rem; border-radius: .75rem; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    box-shadow: 0 3px 10px rgba(99,102,241,.3);
}
.step-icon-wrap svg { width: 1.1rem; height: 1.1rem; color: #fff; stroke: #fff; }
.step-card-body { padding: 1.5rem; }

/* ── Radio Cards ── */
.radio-card {
    display: flex; align-items: flex-start; gap: .875rem;
    padding: .875rem 1rem;
    border: 1.5px solid #e2e8f0;
    border-radius: .75rem;
    cursor: pointer;
    transition: border-color .15s, background .15s;
    background: #fff;
}
.radio-card:hover { border-color: #c7d2fe; background: #fafafe; }
.radio-card input[type=radio] { margin-top: .15rem; accent-color: #6366f1; flex-shrink: 0; }
.radio-card.selected { border-color: #6366f1; background: rgba(99,102,241,.04); }
.radio-card-icon {
    width: 2rem; height: 2rem; border-radius: .5rem; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
    font-size: 1rem;
    background: #f1f5f9;
}
.radio-card.selected .radio-card-icon { background: rgba(99,102,241,.12); }

/* ── DB Driver Cards ── */
.driver-card {
    flex: 1; display: flex; flex-direction: column; align-items: center; gap: .35rem;
    padding: .75rem .5rem;
    border: 1.5px solid #e2e8f0;
    border-radius: .75rem;
    cursor: pointer;
    transition: border-color .15s, background .15s;
    background: #fff;
    text-align: center;
}
.driver-card:hover { border-color: #c7d2fe; background: #fafafe; }
.driver-card input[type=radio] { display: none; }
.driver-card.selected { border-color: #6366f1; background: rgba(99,102,241,.05); }
.driver-card-icon { font-size: 1.5rem; line-height: 1; }
.driver-card-label { font-size: .75rem; font-weight: 600; color: #64748b; }
.driver-card.selected .driver-card-label { color: #6366f1; }

/* ── Field Group ── */
.field-group { display: grid; gap: 1rem; }
.field-group.cols-2 { grid-template-columns: 1fr 1fr; }
.field-group.cols-3-1 { grid-template-columns: 2fr 1fr; }

/* ── Inline Badge ── */
.badge-req { display: inline-block; font-size: .625rem; font-weight: 700; padding: .1rem .35rem; background: #fef2f2; color: #f43f5e; border-radius: .25rem; vertical-align: middle; margin-left: .3rem; }
.badge-opt { display: inline-block; font-size: .625rem; font-weight: 700; padding: .1rem .35rem; background: #f1f5f9; color: #94a3b8; border-radius: .25rem; vertical-align: middle; margin-left: .3rem; }

/* ── Section Divider ── */
.section-sep { border: none; border-top: 1px solid #f1f5f9; margin: 1.25rem 0; }

/* ── Alert ── */
#wiz-alert {
    display: none; padding: .875rem 1rem;
    border-radius: .75rem; font-size: .8125rem; font-weight: 500;
    margin-bottom: 1rem;
    animation: slideDown .2s ease;
}
@keyframes slideDown { from { opacity:0; transform:translateY(-6px); } to { opacity:1; transform:translateY(0); } }

/* ── Connection Test Result ── */
#conn-result {
    display: none;
    padding: .75rem 1rem; border-radius: .625rem;
    font-size: .8125rem; font-weight: 500;
    margin-top: .75rem;
}

/* ── Preview Table ── */
.preview-table { width: 100%; border-collapse: collapse; font-size: .75rem; }
.preview-table th { padding: .5rem .75rem; text-align: left; font-weight: 700; font-size: .6875rem; color: #64748b; background: #f8fafc; text-transform: uppercase; letter-spacing: .04em; }
.preview-table td { padding: .5rem .75rem; color: #334155; border-top: 1px solid #f1f5f9; }
.preview-table tr:hover td { background: #fafafe; }

/* ── Summary ── */
.summary-group { margin-bottom: 1.5rem; }
.summary-group-title { font-size: .6875rem; font-weight: 800; text-transform: uppercase; letter-spacing: .08em; color: #94a3b8; margin-bottom: .625rem; }
.summary-row { display: flex; align-items: center; justify-content: space-between; padding: .5rem 0; border-bottom: 1px solid #f1f5f9; }
.summary-row:last-child { border-bottom: none; }
.summary-key { font-size: .8125rem; color: #64748b; font-weight: 500; }
.summary-val { font-size: .75rem; color: #1e293b; font-family: ui-monospace, monospace; background: #f1f5f9; padding: .2rem .5rem; border-radius: .375rem; max-width: 55%; word-break: break-all; text-align: right; }

/* ── Nav Bar ── */
.wiz-nav { display: flex; align-items: center; justify-content: space-between; margin-top: 1.25rem; }
.step-counter { font-size: .75rem; color: #94a3b8; font-weight: 600; }

/* ── Identifier Match Preview ── */
.match-badge { display: inline-flex; align-items: center; gap: .3rem; font-size: .75rem; font-weight: 600; padding: .25rem .625rem; border-radius: 9999px; }
.match-badge.username { background: rgba(99,102,241,.1); color: #6366f1; }
.match-badge.employee { background: rgba(245,158,11,.1); color: #d97706; }

/* ── Manual Perm Row ── */
.mp-row { display: flex; gap: .5rem; align-items: center; background: #f8fafc; padding: .5rem .625rem; border-radius: .5rem; border: 1px solid #e8edf4; }
.mp-row .wiz-input { background: #fff; }

/* ── Responsive ── */
@media (max-width: 600px) {
    .field-group.cols-2 { grid-template-columns: 1fr; }
    .field-group.cols-3-1 { grid-template-columns: 1fr; }
    .step-label { display: none; }
}
</style>

<div class="max-w-2xl mx-auto py-6 px-4" style="padding-bottom:3rem">

    {{-- ── Page Header ── --}}
    <div class="flex items-center gap-3 mb-7">
        <a href="{{ route('connectors.index') }}"
           class="w-9 h-9 rounded-xl border border-slate-200 bg-white flex items-center justify-center text-slate-400 hover:text-slate-700 hover:border-slate-300 transition shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-indigo-500 bg-indigo-50 px-2 py-0.5 rounded-md uppercase tracking-wider">
                    {{ isset($editConfig) ? 'แก้ไข' : 'สร้างใหม่' }}
                </span>
                <h1 class="text-xl font-extrabold text-slate-800">Connector Wizard</h1>
            </div>
            <p class="text-xs text-slate-400 mt-0.5 font-medium">เชื่อมต่อฐานข้อมูลระบบภายนอกเข้ากับ UCM — ไม่ต้องเขียนโค้ด</p>
        </div>
    </div>

    {{-- ── Step Indicator ── --}}
    <div class="step-wrap mb-8" id="step-indicator">
        @php
            $steps = [
                ['label' => 'ข้อมูลระบบ', 'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'],
                ['label' => 'เชื่อมต่อ DB', 'icon' => 'M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4'],
                ['label' => 'ตาราง Users', 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
                ['label' => 'Permissions', 'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'],
                ['label' => 'ยืนยัน', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4'],
            ];
        @endphp

        @foreach($steps as $i => $step)
        <div class="step-item">
            <div class="step-circle {{ $i === 0 ? 'active' : '' }}" data-step-circle="{{ $i + 1 }}">
                {{-- Number (shown when pending/active) --}}
                <span data-step-num="{{ $i + 1 }}" {{ $i === 0 ? '' : '' }}>{{ $i + 1 }}</span>
                {{-- Check (shown when done) --}}
                <svg class="hidden w-3.5 h-3.5" data-step-check="{{ $i + 1 }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <span class="step-label {{ $i === 0 ? 'active' : '' }}" data-step-label="{{ $i + 1 }}">{{ $step['label'] }}</span>
        </div>
        @if(! $loop->last)
        <div class="step-line" data-step-line="{{ $i + 1 }}"></div>
        @endif
        @endforeach
    </div>

    {{-- ── Alert ── --}}
    <div id="wiz-alert"></div>

    {{-- ══════════════════════════════════════════════════
         STEP 1 — System Info
    ══════════════════════════════════════════════════ --}}
    <div class="wiz-step" data-step="1">
        <div class="step-card">
            <div class="step-card-header">
                <div class="step-icon-wrap">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-800">ข้อมูลระบบ</h2>
                    <p class="text-xs text-slate-400 mt-0.5">กำหนดชื่อและรายละเอียดของระบบที่ต้องการเชื่อมต่อ</p>
                </div>
            </div>
            <div class="step-card-body">

                @if(isset($editConfig))
                    <input type="hidden" id="field_system_id" value="{{ $editConfig->system_id }}">
                    <div style="display:flex;align-items:center;gap:.625rem;padding:.875rem 1rem;background:rgba(99,102,241,.06);border:1.5px solid rgba(99,102,241,.2);border-radius:.75rem;margin-bottom:1.25rem;">
                        <div style="width:2rem;height:2rem;background:linear-gradient(135deg,#6366f1,#8b5cf6);border-radius:.5rem;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="white" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-indigo-700">แก้ไข Connector</p>
                            <p class="text-xs text-indigo-500 mt-0.5">ระบบ: <strong>{{ $editConfig->system->name }}</strong></p>
                        </div>
                    </div>
                @else

                    {{-- เชื่อมกับระบบเดิม --}}
                    <div class="mb-5">
                        <label class="wiz-label">
                            เชื่อมกับระบบที่มีอยู่แล้ว
                            <span class="badge-opt">ไม่บังคับ</span>
                        </label>
                        <select id="field_system_id" class="wiz-select">
                            <option value="">— สร้างระบบใหม่ —</option>
                            @foreach(\App\Models\System::orderBy('name')->get() as $sys)
                            <option value="{{ $sys->id }}">{{ $sys->name }} ({{ $sys->slug }})</option>
                            @endforeach
                        </select>
                        <p class="wiz-hint">เลือกเฉพาะถ้าต้องการเพิ่ม Connector ให้ระบบที่มีอยู่แล้ว</p>
                    </div>

                    <hr class="section-sep">

                    {{-- ข้อมูลระบบใหม่ --}}
                    <div id="new-system-fields">
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-4">ข้อมูลระบบใหม่</p>

                        <div class="field-group cols-2 mb-4">
                            <div>
                                <label class="wiz-label">ชื่อระบบ <span class="wiz-req">*</span></label>
                                <input type="text" id="field_system_name" class="wiz-input" placeholder="เช่น ระบบซ่อมบำรุง">
                            </div>
                            <div>
                                <label class="wiz-label">Slug <span class="wiz-req">*</span></label>
                                <input type="text" id="field_system_slug" class="wiz-input" style="font-family:ui-monospace,monospace;font-size:.8125rem" placeholder="repair-system">
                                <p class="wiz-hint">ตัวพิมพ์เล็ก, ตัวเลข และ "-" เท่านั้น</p>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="wiz-label">คำอธิบาย <span class="badge-opt">ไม่บังคับ</span></label>
                            <textarea id="field_system_description" class="wiz-input" rows="2" placeholder="คำอธิบายสั้นๆ ของระบบนี้"></textarea>
                        </div>

                        <div class="field-group cols-2">
                            <div>
                                <label class="wiz-label">สีประจำระบบ</label>
                                <div style="display:flex;gap:.5rem;align-items:center;">
                                    <input type="color" id="field_system_color" value="#6366f1"
                                           style="width:2.5rem;height:2.5rem;border:1.5px solid #e2e8f0;border-radius:.5rem;padding:2px;cursor:pointer;background:#fff;">
                                    <input type="text" id="field_system_color_text" value="#6366f1" class="wiz-input"
                                           style="font-family:ui-monospace,monospace;font-size:.8125rem" placeholder="#6366f1">
                                </div>
                            </div>
                            <div>
                                <label class="wiz-label">ไอคอน (Emoji) <span class="badge-opt">ไม่บังคับ</span></label>
                                <input type="text" id="field_system_icon" class="wiz-input" style="font-size:1.5rem;text-align:center;" placeholder="🔧" maxlength="5">
                            </div>
                        </div>
                    </div>

                @endif
            </div>
        </div>
        <div class="wiz-nav">
            <span class="step-counter">ขั้นที่ 1 จาก 5</span>
            <button class="btn-primary" onclick="wizNext(1)">
                ถัดไป
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </button>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════
         STEP 2 — Database Connection
    ══════════════════════════════════════════════════ --}}
    <div class="wiz-step hidden" data-step="2">
        <div class="step-card">
            <div class="step-card-header">
                <div class="step-icon-wrap">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-800">การเชื่อมต่อฐานข้อมูล</h2>
                    <p class="text-xs text-slate-400 mt-0.5">กรอกข้อมูล credentials สำหรับเชื่อมต่อ DB ของระบบภายนอก</p>
                </div>
            </div>
            <div class="step-card-body">

                {{-- Driver --}}
                <div class="mb-5">
                    <label class="wiz-label">Database Driver <span class="wiz-req">*</span></label>
                    <div style="display:flex;gap:.625rem;">
                        @foreach(['mysql' => ['MySQL / MariaDB', '🐬'], 'pgsql' => ['PostgreSQL', '🐘'], 'sqlsrv' => ['SQL Server', '🪟']] as $val => [$lbl, $emoji])
                        <label class="driver-card {{ $val === 'mysql' ? 'selected' : '' }}" id="driver-card-{{ $val }}" onclick="selectDriver('{{ $val }}')">
                            <input type="radio" name="db_driver" value="{{ $val }}" {{ $val === 'mysql' ? 'checked' : '' }} onchange="wizDriverChange(this.value)">
                            <div class="driver-card-icon">{{ $emoji }}</div>
                            <div class="driver-card-label">{{ $lbl }}</div>
                        </label>
                        @endforeach
                    </div>
                </div>

                <hr class="section-sep">

                {{-- Host & Port --}}
                <div class="field-group cols-3-1 mb-4">
                    <div>
                        <label class="wiz-label">Host <span class="wiz-req">*</span></label>
                        <input type="text" id="field_db_host" class="wiz-input" style="font-family:ui-monospace,monospace;font-size:.8125rem"
                               placeholder="192.168.1.100" value="{{ $editConfig->db_host ?? '' }}">
                    </div>
                    <div>
                        <label class="wiz-label">Port <span class="wiz-req">*</span></label>
                        <input type="number" id="field_db_port" class="wiz-input" style="font-family:ui-monospace,monospace;font-size:.8125rem"
                               value="{{ $editConfig->db_port ?? 3306 }}" min="1" max="65535">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="wiz-label">Database Name <span class="wiz-req">*</span></label>
                    <input type="text" id="field_db_name" class="wiz-input" style="font-family:ui-monospace,monospace;font-size:.8125rem"
                           placeholder="myapp_db" value="{{ $editConfig->db_name ?? '' }}">
                </div>

                <div class="field-group cols-2">
                    <div>
                        <label class="wiz-label">Username <span class="wiz-req">*</span></label>
                        <input type="text" id="field_db_user" class="wiz-input" style="font-family:ui-monospace,monospace;font-size:.8125rem"
                               placeholder="db_readonly" value="{{ $editConfig->db_user ?? '' }}" autocomplete="off">
                    </div>
                    <div>
                        <label class="wiz-label">Password <span class="badge-opt">{{ isset($editConfig) ? 'เว้นว่าง = ไม่เปลี่ยน' : 'ถ้ามี' }}</span></label>
                        <input type="password" id="field_db_password" class="wiz-input" style="font-family:ui-monospace,monospace;font-size:.8125rem"
                               placeholder="{{ isset($editConfig) ? '••••••••' : 'password' }}" autocomplete="new-password">
                    </div>
                </div>

                <hr class="section-sep">

                {{-- Test Connection --}}
                <button class="btn-outline-indigo w-full justify-center py-2.5" onclick="wizTestConnection()" id="test-conn-btn">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    ทดสอบการเชื่อมต่อ
                </button>
                <div id="conn-result"></div>
            </div>
        </div>
        <div class="wiz-nav">
            <button class="btn-ghost" onclick="wizPrev(2)">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                ย้อนกลับ
            </button>
            <div style="display:flex;align-items:center;gap:.75rem;">
                <span class="step-counter">ขั้นที่ 2 จาก 5</span>
                <button class="btn-primary" id="step2-next" onclick="wizNext(2)">
                    ถัดไป
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════
         STEP 3 — User Table
    ══════════════════════════════════════════════════ --}}
    <div class="wiz-step hidden" data-step="3">
        <div class="step-card">
            <div class="step-card-header">
                <div class="step-icon-wrap">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-800">ตาราง Users</h2>
                    <p class="text-xs text-slate-400 mt-0.5">กำหนดตารางและคอลัมน์ที่เก็บข้อมูลผู้ใช้ในระบบภายนอก</p>
                </div>
            </div>
            <div class="step-card-body">

                {{-- Select table --}}
                <div class="mb-5">
                    <label class="wiz-label">ตาราง Users <span class="wiz-req">*</span></label>
                    <div style="display:flex;gap:.5rem;">
                        <select id="field_user_table" class="wiz-select" style="flex:1" onchange="wizLoadUserColumns()">
                            <option value="">— เลือกตาราง —</option>
                        </select>
                        <button class="btn-outline-indigo" onclick="wizLoadTables('user_table','wizLoadUserColumns')" style="flex-shrink:0;padding:.5rem .75rem;" title="โหลดรายการตาราง">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                        </button>
                    </div>
                    <p class="wiz-hint">กดปุ่ม ⟳ เพื่อโหลดรายการตาราง (ต้องผ่านขั้นตอนที่ 2 แล้ว)</p>
                </div>

                {{-- UCM Identifier --}}
                <div style="background:#f8fafc;border:1.5px solid #e8edf4;border-radius:.75rem;padding:.875rem 1rem;margin-bottom:1.25rem;">
                    <p class="text-xs font-bold text-slate-600 mb-3">UCM จะจับคู่ผู้ใช้โดยใช้:</p>
                    <div style="display:flex;gap:.75rem;">
                        <label class="radio-card flex-1" id="ucm-id-card-username" onclick="selectUcmId('username')">
                            <input type="radio" name="user_ucm_identifier" value="username" checked>
                            <div class="radio-card-icon">👤</div>
                            <div>
                                <p style="font-size:.8125rem;font-weight:700;color:#334155;">Username (LDAP)</p>
                                <p style="font-size:.6875rem;color:#94a3b8;margin-top:.125rem;">ใช้ username จาก Active Directory</p>
                            </div>
                        </label>
                        <label class="radio-card flex-1" id="ucm-id-card-employee_number" onclick="selectUcmId('employee_number')">
                            <input type="radio" name="user_ucm_identifier" value="employee_number">
                            <div class="radio-card-icon">🪪</div>
                            <div>
                                <p style="font-size:.8125rem;font-weight:700;color:#334155;">Employee Number</p>
                                <p style="font-size:.6875rem;color:#94a3b8;margin-top:.125rem;">ใช้รหัสพนักงาน</p>
                            </div>
                        </label>
                    </div>
                </div>

                <hr class="section-sep">

                {{-- Column mapping --}}
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-4">Mapping คอลัมน์</p>
                <div class="field-group cols-2 mb-4">
                    <div>
                        <label class="wiz-label">คอลัมน์ Identifier <span class="wiz-req">*</span></label>
                        <select id="field_user_identifier_col" class="wiz-select">
                            <option value="">— เลือกคอลัมน์ —</option>
                        </select>
                        <p class="wiz-hint">ตรงกับ UCM username / employee_number</p>
                    </div>
                    <div>
                        <label class="wiz-label">คอลัมน์ ชื่อ-นามสกุล <span class="badge-opt">ไม่บังคับ</span></label>
                        <select id="field_user_name_col" class="wiz-select">
                            <option value="">(ไม่ระบุ)</option>
                        </select>
                    </div>
                    <div>
                        <label class="wiz-label">คอลัมน์ อีเมล <span class="badge-opt">ไม่บังคับ</span></label>
                        <select id="field_user_email_col" class="wiz-select">
                            <option value="">(ไม่ระบุ)</option>
                        </select>
                    </div>
                    <div>
                        <label class="wiz-label">คอลัมน์ แผนก <span class="badge-opt">ไม่บังคับ</span></label>
                        <select id="field_user_dept_col" class="wiz-select">
                            <option value="">(ไม่ระบุ)</option>
                        </select>
                    </div>
                    <div>
                        <label class="wiz-label">คอลัมน์ สถานะ Active <span class="badge-opt">ไม่บังคับ</span></label>
                        <select id="field_user_status_col" class="wiz-select" onchange="wizToggleStatusVal()">
                            <option value="">(ไม่ระบุ)</option>
                        </select>
                    </div>
                    <div id="status-val-wrap" class="hidden">
                        <label class="wiz-label">ค่าที่หมายถึง "Active"</label>
                        <input type="text" id="field_user_status_active_val" class="wiz-input" placeholder="เช่น 1, Y, active, true">
                    </div>
                </div>

                {{-- Preview --}}
                <button class="btn-outline-indigo w-full justify-center" style="font-size:.8125rem;" onclick="wizPreviewUsers()">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    ดูตัวอย่างข้อมูล 10 รายการแรก
                </button>
                <div id="user-preview" class="hidden" style="margin-top:.75rem;border:1px solid #e8edf4;border-radius:.625rem;overflow:hidden;overflow-x:auto;">
                    <table class="preview-table">
                        <thead><tr id="user-preview-head"></tr></thead>
                        <tbody id="user-preview-body"></tbody>
                    </table>
                </div>

            </div>
        </div>
        <div class="wiz-nav">
            <button class="btn-ghost" onclick="wizPrev(3)">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                ย้อนกลับ
            </button>
            <div style="display:flex;align-items:center;gap:.75rem;">
                <span class="step-counter">ขั้นที่ 3 จาก 5</span>
                <button class="btn-primary" onclick="wizNext(3)">
                    ถัดไป
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════
         STEP 4 — Permissions
    ══════════════════════════════════════════════════ --}}
    <div class="wiz-step hidden" data-step="4">
        <div class="step-card">
            <div class="step-card-header">
                <div class="step-icon-wrap">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-800">Permission Mode</h2>
                    <p class="text-xs text-slate-400 mt-0.5">เลือกวิธีที่ระบบภายนอกจัดเก็บสิทธิ์ของผู้ใช้</p>
                </div>
            </div>
            <div class="step-card-body">

                {{-- Mode Selection --}}
                <div style="display:flex;flex-direction:column;gap:.625rem;margin-bottom:1.5rem;">
                    <label class="radio-card selected" id="perm-card-junction" onclick="selectPermMode('junction')">
                        <input type="radio" name="permission_mode" value="junction" checked onchange="wizPermModeChange(this.value)">
                        <div class="radio-card-icon">🔗</div>
                        <div style="flex:1">
                            <p style="font-size:.875rem;font-weight:700;color:#1e293b;">Junction Table</p>
                            <p style="font-size:.75rem;color:#64748b;margin-top:.2rem;">ตาราง mapping แยกต่างหาก เช่น <code style="font-size:.7rem;background:#f1f5f9;padding:.1rem .3rem;border-radius:.25rem;">user_roles(user_id, role)</code></p>
                        </div>
                        <div style="flex-shrink:0;width:1.25rem;height:1.25rem;border-radius:50%;border:2px solid #e2e8f0;background:#fff;display:flex;align-items:center;justify-content:center;" id="perm-check-junction">
                            <div style="width:.5rem;height:.5rem;border-radius:50%;background:#6366f1;display:none;" id="perm-dot-junction"></div>
                        </div>
                    </label>
                    <label class="radio-card" id="perm-card-column" onclick="selectPermMode('column')">
                        <input type="radio" name="permission_mode" value="column" onchange="wizPermModeChange(this.value)">
                        <div class="radio-card-icon">📋</div>
                        <div style="flex:1">
                            <p style="font-size:.875rem;font-weight:700;color:#1e293b;">Single Column</p>
                            <p style="font-size:.75rem;color:#64748b;margin-top:.2rem;">คอลัมน์ <code style="font-size:.7rem;background:#f1f5f9;padding:.1rem .3rem;border-radius:.25rem;">role</code> หรือ <code style="font-size:.7rem;background:#f1f5f9;padding:.1rem .3rem;border-radius:.25rem;">permission</code> อยู่บนตาราง users เดิม</p>
                        </div>
                        <div style="flex-shrink:0;width:1.25rem;height:1.25rem;border-radius:50%;border:2px solid #e2e8f0;background:#fff;display:flex;align-items:center;justify-content:center;" id="perm-check-column">
                            <div style="width:.5rem;height:.5rem;border-radius:50%;background:#6366f1;display:none;" id="perm-dot-column"></div>
                        </div>
                    </label>
                    <label class="radio-card" id="perm-card-manual" onclick="selectPermMode('manual')">
                        <input type="radio" name="permission_mode" value="manual" onchange="wizPermModeChange(this.value)">
                        <div class="radio-card-icon">✍️</div>
                        <div style="flex:1">
                            <p style="font-size:.875rem;font-weight:700;color:#1e293b;">กำหนดด้วยตนเอง</p>
                            <p style="font-size:.75rem;color:#64748b;margin-top:.2rem;">ไม่มีตาราง permission ในระบบ — กำหนด permission list เองใน UCM</p>
                        </div>
                        <div style="flex-shrink:0;width:1.25rem;height:1.25rem;border-radius:50%;border:2px solid #e2e8f0;background:#fff;display:flex;align-items:center;justify-content:center;" id="perm-check-manual">
                            <div style="width:.5rem;height:.5rem;border-radius:50%;background:#6366f1;display:none;" id="perm-dot-manual"></div>
                        </div>
                    </label>
                </div>

                {{-- Junction / Column DB Fields --}}
                <div id="perm-db-fields">
                    <hr class="section-sep">
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-4">Mapping คอลัมน์</p>

                    <div class="mb-4">
                        <label class="wiz-label">ตาราง Permissions <span class="wiz-req">*</span></label>
                        <div style="display:flex;gap:.5rem;">
                            <select id="field_perm_table" class="wiz-select" style="flex:1" onchange="wizLoadPermColumns()">
                                <option value="">— เลือกตาราง —</option>
                            </select>
                            <button class="btn-outline-indigo" onclick="wizLoadTables('perm_table','wizLoadPermColumns')" style="flex-shrink:0;padding:.5rem .75rem;" title="โหลดรายการตาราง">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                            </button>
                        </div>
                        <p class="wiz-hint" id="perm-table-hint">ตาราง junction ที่เก็บความสัมพันธ์ user ↔ permission</p>
                    </div>

                    <div class="field-group cols-2">
                        <div id="perm-user-fk-wrap">
                            <label class="wiz-label">FK ชี้ไปยัง User <span class="wiz-req">*</span></label>
                            <select id="field_perm_user_fk_col" class="wiz-select">
                                <option value="">— เลือกคอลัมน์ —</option>
                            </select>
                        </div>
                        <div>
                            <label class="wiz-label">Permission Value <span class="wiz-req">*</span></label>
                            <select id="field_perm_value_col" class="wiz-select">
                                <option value="">— เลือกคอลัมน์ —</option>
                            </select>
                        </div>
                        <div>
                            <label class="wiz-label">Label <span class="badge-opt">ไม่บังคับ</span></label>
                            <select id="field_perm_label_col" class="wiz-select">
                                <option value="">(ไม่ระบุ)</option>
                            </select>
                        </div>
                        <div>
                            <label class="wiz-label">Group <span class="badge-opt">ไม่บังคับ</span></label>
                            <select id="field_perm_group_col" class="wiz-select">
                                <option value="">(ไม่ระบุ)</option>
                            </select>
                        </div>
                    </div>

                    <button class="btn-outline-indigo w-full justify-center mt-4" style="font-size:.8125rem;" onclick="wizPreviewPermissions()">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        ดูตัวอย่าง Permissions (20 รายการ)
                    </button>
                    <div id="perm-preview" class="hidden" style="margin-top:.75rem;border:1px solid #e8edf4;border-radius:.625rem;overflow:hidden;overflow-x:auto;">
                        <table class="preview-table">
                            <thead><tr id="perm-preview-head"></tr></thead>
                            <tbody id="perm-preview-body"></tbody>
                        </table>
                    </div>
                </div>

                {{-- Manual Fields --}}
                <div id="perm-manual-fields" class="hidden">
                    <hr class="section-sep">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:.875rem;">
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">รายการ Permissions</p>
                        <button class="btn-outline-indigo" style="font-size:.75rem;padding:.3rem .75rem;" onclick="wizAddManualPerm()">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            เพิ่ม Permission
                        </button>
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr 7rem auto;gap:.375rem;margin-bottom:.375rem;padding:0 .625rem;">
                        <p style="font-size:.6875rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.05em;">Key</p>
                        <p style="font-size:.6875rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.05em;">Label</p>
                        <p style="font-size:.6875rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.05em;">Group</p>
                        <div></div>
                    </div>
                    <div id="manual-perm-list" style="display:flex;flex-direction:column;gap:.375rem;"></div>
                    <p id="manual-empty" class="text-xs text-slate-400 text-center py-4">ยังไม่มี permission — กด "เพิ่ม Permission" เพื่อเริ่มต้น</p>
                </div>

            </div>
        </div>
        <div class="wiz-nav">
            <button class="btn-ghost" onclick="wizPrev(4)">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                ย้อนกลับ
            </button>
            <div style="display:flex;align-items:center;gap:.75rem;">
                <span class="step-counter">ขั้นที่ 4 จาก 5</span>
                <button class="btn-primary" onclick="wizNext(4)">
                    ถัดไป
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════
         STEP 5 — Confirm
    ══════════════════════════════════════════════════ --}}
    <div class="wiz-step hidden" data-step="5">
        <div class="step-card">
            <div class="step-card-header">
                <div class="step-icon-wrap" style="background:linear-gradient(135deg,#10b981,#059669);">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-800">ยืนยันการตั้งค่า</h2>
                    <p class="text-xs text-slate-400 mt-0.5">ตรวจสอบข้อมูลก่อนสร้าง Connector</p>
                </div>
            </div>
            <div class="step-card-body">
                <div id="confirm-summary"></div>
            </div>
        </div>
        <div class="wiz-nav">
            <button class="btn-ghost" onclick="wizPrev(5)">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                ย้อนกลับ
            </button>
            <div style="display:flex;align-items:center;gap:.75rem;">
                <span class="step-counter">ขั้นที่ 5 จาก 5</span>
                <button class="btn-primary" id="submit-btn" onclick="wizSubmit()" style="background:linear-gradient(135deg,#10b981,#059669);box-shadow:0 2px 8px rgba(16,185,129,.35);">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
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

    // ── UI State Helpers ───────────────────────────────────────────────────

    window.selectDriver = function (drv) {
        document.querySelectorAll('.driver-card').forEach(function (c) { c.classList.remove('selected'); });
        var card = document.getElementById('driver-card-' + drv);
        if (card) card.classList.add('selected');
        var radio = document.querySelector('input[name="db_driver"][value="' + drv + '"]');
        if (radio) { radio.checked = true; wizDriverChange(drv); }
    };

    window.selectUcmId = function (val) {
        ['username', 'employee_number'].forEach(function (v) {
            var c = document.getElementById('ucm-id-card-' + v);
            if (c) c.classList.toggle('selected', v === val);
        });
        var radio = document.querySelector('input[name="user_ucm_identifier"][value="' + val + '"]');
        if (radio) radio.checked = true;
    };

    window.selectPermMode = function (mode) {
        ['junction', 'column', 'manual'].forEach(function (m) {
            var card = document.getElementById('perm-card-' + m);
            var dot  = document.getElementById('perm-dot-' + m);
            var chk  = document.getElementById('perm-check-' + m);
            if (card) card.classList.toggle('selected', m === mode);
            if (dot)  dot.style.display  = m === mode ? 'block' : 'none';
            if (chk)  chk.style.borderColor = m === mode ? '#6366f1' : '#e2e8f0';
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
        // Update indicator
        for (var i = 1; i <= 5; i++) {
            var circle = document.querySelector('[data-step-circle="' + i + '"]');
            var label  = document.querySelector('[data-step-label="' + i + '"]');
            var line   = document.querySelector('[data-step-line="' + (i) + '"]');
            var numEl  = document.querySelector('[data-step-num="' + i + '"]');
            var chkEl  = document.querySelector('[data-step-check="' + i + '"]');

            if (circle) {
                circle.classList.remove('done', 'active');
                if (i < n) {
                    circle.classList.add('done');
                    numEl && numEl.classList.add('hidden');
                    chkEl && chkEl.classList.remove('hidden');
                } else if (i === n) {
                    circle.classList.add('active');
                    numEl && numEl.classList.remove('hidden');
                    chkEl && chkEl.classList.add('hidden');
                } else {
                    numEl && numEl.classList.remove('hidden');
                    chkEl && chkEl.classList.add('hidden');
                }
            }
            if (label) {
                label.classList.remove('done', 'active');
                if (i < n)      label.classList.add('done');
                else if (i ===n) label.classList.add('active');
            }
            if (line) {
                line.classList.toggle('done', i < n);
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
                return '<th>' + escHtml(c) + '</th>';
            }).join('');
            body.innerHTML = res.rows.map(function (row) {
                return '<tr>' + cols.map(function (c) {
                    return '<td>' + escHtml(String(row[c] ?? '')) + '</td>';
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
                return '<th>' + escHtml(c) + '</th>';
            }).join('');
            body.innerHTML = res.rows.map(function (row) {
                return '<tr>' + cols.map(function (c) {
                    return '<td>' + escHtml(String(row[c] ?? '')) + '</td>';
                }).join('') + '</tr>';
            }).join('');
            container.style.display = 'block';
        });
    };

    // ── Manual Permissions ─────────────────────────────────────────────────

    var manualPerms = EDIT_CONFIG?.manual_permissions || [];

    window.wizAddManualPerm = function (perm) {
        perm = perm || { key: '', label: '', group: '' };
        var list = document.getElementById('manual-perm-list');
        var empty = document.getElementById('manual-empty');
        if (empty) empty.style.display = 'none';
        var idx = list.children.length;
        var row = document.createElement('div');
        row.className = 'mp-row';
        row.style.cssText = 'display:grid;grid-template-columns:1fr 1fr 7rem auto;gap:.375rem;align-items:center;';
        row.innerHTML = [
            '<input type="text" placeholder="key (เช่น admin)" value="' + escAttr(perm.key) + '" class="wiz-input" style="font-family:ui-monospace,monospace;font-size:.8rem;padding:.375rem .5rem;" data-mp="key" data-idx="' + idx + '">',
            '<input type="text" placeholder="label (ชื่อที่แสดง)" value="' + escAttr(perm.label) + '" class="wiz-input" style="font-size:.8rem;padding:.375rem .5rem;" data-mp="label" data-idx="' + idx + '">',
            '<input type="text" placeholder="group" value="' + escAttr(perm.group || '') + '" class="wiz-input" style="font-size:.8rem;padding:.375rem .5rem;" data-mp="group" data-idx="' + idx + '">',
            '<button type="button" onclick="removeManualPerm(this)" style="width:1.75rem;height:1.75rem;border-radius:.375rem;background:#fef2f2;border:1px solid #fecaca;color:#f43f5e;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:1rem;line-height:1;flex-shrink:0;">×</button>',
        ].join('');
        list.appendChild(row);
    };

    window.removeManualPerm = function (btn) {
        btn.closest('.mp-row').remove();
        var list = document.getElementById('manual-perm-list');
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
            return '<div class="summary-group">'
                + '<p class="summary-group-title">' + escHtml(sec.title) + '</p>'
                + sec.rows.map(function (r) {
                    return '<div class="summary-row"><span class="summary-key">' + escHtml(r[0]) + '</span><span class="summary-val">' + escHtml(r[1]) + '</span></div>';
                }).join('')
                + '</div>';
        }).join('');

        document.getElementById('confirm-summary').innerHTML = html;
    };

    // ── Submit ─────────────────────────────────────────────────────────────

    window.wizSubmit = function () {
        var btn = document.getElementById('submit-btn');
        btn.disabled = true;
        btn.innerHTML = '<svg style="width:1rem;height:1rem;animation:spin 1s linear infinite" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg> กำลังสร้าง...';

        var mode = getPermMode();
        var payload = Object.assign(connData(), {
            system_id:            IS_EDIT ? EDIT_CONFIG.system_id : (val('system_id') || null),
            system_name:          val('system_name'),
            system_slug:          val('system_slug'),
            system_description:   val('system_description'),
            system_color:         document.getElementById('field_system_color')?.value || '#6366f1',
            system_icon:          val('system_icon'),
            user_table:           val('user_table'),
            user_ucm_identifier:  radioVal('user_ucm_identifier'),
            user_identifier_col:  val('user_identifier_col'),
            user_name_col:        val('user_name_col') || null,
            user_email_col:       val('user_email_col') || null,
            user_dept_col:        val('user_dept_col') || null,
            user_status_col:      val('user_status_col') || null,
            user_status_active_val: val('user_status_active_val') || null,
            permission_mode:      mode,
            perm_table:           mode !== 'manual' ? val('perm_table') : null,
            perm_user_fk_col:     mode === 'junction' ? val('perm_user_fk_col') : null,
            perm_value_col:       mode !== 'manual' ? val('perm_value_col') : null,
            perm_label_col:       mode !== 'manual' ? (val('perm_label_col') || null) : null,
            perm_group_col:       mode !== 'manual' ? (val('perm_group_col') || null) : null,
            manual_permissions:   mode === 'manual' ? JSON.stringify(getManualPerms()) : null,
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
                btn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>' + ('{{ isset($editConfig) ? 'บันทึกการเปลี่ยนแปลง' : 'สร้าง Connector' }}');
                showErr(res.message || 'เกิดข้อผิดพลาด กรุณาลองใหม่');
            }
        }).catch(function (e) {
            btn.disabled = false;
            btn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>' + ('{{ isset($editConfig) ? 'บันทึกการเปลี่ยนแปลง' : 'สร้าง Connector' }}');
            showErr(e.message);
        });
    };

    // ── Utilities ──────────────────────────────────────────────────────────

    function escHtml(s) {
        return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }
    function escAttr(s) {
        return String(s || '').replace(/"/g, '&quot;');
    }

    // ── CSS Animation ──────────────────────────────────────────────────────
    var spinStyle = document.createElement('style');
    spinStyle.textContent = '@keyframes spin { from { transform:rotate(0deg); } to { transform:rotate(360deg); } }';
    document.head.appendChild(spinStyle);

    // ── Init: Perm mode visual state ───────────────────────────────────────
    (function initPermDots() {
        var dot = document.getElementById('perm-dot-junction');
        var chk = document.getElementById('perm-check-junction');
        if (dot) dot.style.display = 'block';
        if (chk) chk.style.borderColor = '#6366f1';
    })();

    // ── Init for Edit Mode ─────────────────────────────────────────────────

    if (IS_EDIT && EDIT_CONFIG) {
        // Pre-fill radio buttons
        selectDriver(EDIT_CONFIG.db_driver || 'mysql');

        var ucmIdEl = EDIT_CONFIG.user_ucm_identifier || 'username';
        selectUcmId(ucmIdEl);

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
