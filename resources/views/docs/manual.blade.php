@extends('layouts.app')

@section('title', 'คู่มือผู้ใช้งาน')
@section('header', 'คู่มือผู้ใช้งาน')

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}" class="text-slate-400 hover:text-slate-600 font-medium transition-colors whitespace-nowrap">Dashboard</a>
    <svg class="w-3.5 h-3.5 text-slate-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    <span class="font-semibold text-slate-800 truncate">คู่มือผู้ใช้งาน</span>
@endsection

@section('content')

@php
$sections = [
    ['id' => 'overview',          'label' => 'ภาพรวมระบบ'],
    ['id' => 'login',             'label' => 'การเข้าสู่ระบบ'],
    ['id' => 'dashboard',         'label' => 'Dashboard'],
    ['id' => 'users',             'label' => 'จัดการผู้ใช้'],
    ['id' => 'export-csv',        'label' => 'ส่งออก CSV'],
    ['id' => 'permissions',       'label' => 'จัดการสิทธิ์'],
    ['id' => 'systems',           'label' => 'ระบบที่เชื่อมต่อ'],
    ['id' => 'ref-data',          'label' => 'ข้อมูล Reference'],
    ['id' => 'twoway-sync',       'label' => '2-Way Sync'],
    ['id' => 'admin-levels',      'label' => 'สิทธิ์ Admin'],
    ['id' => 'ad-check',          'label' => 'ตรวจสอบ AD'],
    ['id' => 'sync',              'label' => 'การ Sync สิทธิ์'],
    ['id' => 'connector-wizard',  'label' => 'Connector Wizard'],
    ['id' => 'queue-monitor',         'label' => 'Queue Monitor'],
    ['id' => 'audit-log',             'label' => 'Audit Log'],
    ['id' => 'notification-channels', 'label' => 'Notification Channels'],
    ['id' => 'permission-matrix',     'label' => 'Permission Matrix'],
];
@endphp

<div class="flex gap-8 items-start">

    {{-- Sticky Table of Contents --}}
    <aside class="hidden xl:block w-56 flex-shrink-0 sticky top-24 self-start">
        <div class="bg-white rounded-2xl shadow-sm ring-1 ring-slate-100 p-4">
            <p class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-3">สารบัญ</p>
            <nav class="space-y-0.5" id="toc-nav">
                @foreach ($sections as $s)
                    <a href="#{{ $s['id'] }}"
                       class="toc-link flex items-center gap-2 px-3 py-2 rounded-xl text-sm text-slate-500 hover:text-slate-800 hover:bg-slate-50 transition-colors">
                        <span class="w-1.5 h-1.5 rounded-full bg-slate-300 flex-shrink-0"></span>
                        {{ $s['label'] }}
                    </a>
                @endforeach
            </nav>
        </div>
    </aside>

    {{-- Content --}}
    <div class="flex-1 min-w-0 space-y-6">

        {{-- Header card --}}
        <div class="relative overflow-hidden rounded-2xl p-7"
             style="background:linear-gradient(135deg,#4f46e5 0%,#7c3aed 50%,#2563eb 100%)">
            <div class="absolute inset-0 pointer-events-none"
                 style="background-image:linear-gradient(rgba(255,255,255,0.05) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,0.05) 1px,transparent 1px);background-size:28px 28px"></div>
            <div class="relative flex items-center gap-4">
                <div class="w-14 h-14 bg-white/15 rounded-2xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-white">คู่มือผู้ใช้งาน UCM</h1>
                    <p class="text-indigo-200 text-sm mt-1">User Centralized Management — Bangkok Flight Services</p>
                </div>
            </div>
        </div>

        {{-- ── ภาพรวมระบบ ── --}}
        <div id="overview" class="bg-white rounded-2xl shadow-sm ring-1 ring-slate-100 overflow-hidden">
            <div class="flex items-center gap-3 px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                <div class="w-8 h-8 bg-indigo-100 rounded-xl flex items-center justify-center">
                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h2 class="font-bold text-slate-800">ภาพรวมระบบ</h2>
            </div>
            <div class="px-6 py-5 space-y-4 text-sm text-slate-700 leading-relaxed">
                <p>
                    <strong class="text-slate-900">UCM (User Centralized Management)</strong> คือระบบบริหารจัดการสิทธิ์ผู้ใช้งานกลาง สำหรับ Bangkok Flight Services
                    โดยทำหน้าที่เป็นตัวกลางในการกำหนดสิทธิ์การเข้าถึงระบบต่าง ๆ ผ่าน Active Directory (AD) ขององค์กร
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-1">
                    <div class="bg-indigo-50 rounded-xl p-4 border border-indigo-100">
                        <div class="w-8 h-8 bg-indigo-600 rounded-xl flex items-center justify-center mb-2">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <div class="font-bold text-indigo-900 text-sm mb-1">ผู้ใช้จาก AD</div>
                        <p class="text-xs text-indigo-700">นำเข้าผู้ใช้จาก Active Directory โดยตรง ไม่ต้องสร้างบัญชีใหม่</p>
                    </div>
                    <div class="bg-emerald-50 rounded-xl p-4 border border-emerald-100">
                        <div class="w-8 h-8 bg-emerald-600 rounded-xl flex items-center justify-center mb-2">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                        <div class="font-bold text-emerald-900 text-sm mb-1">จัดการสิทธิ์</div>
                        <p class="text-xs text-emerald-700">กำหนด Permission Keys ต่อระบบและโมดูลได้ละเอียด รองรับทั้ง Multi-select และ Exclusive mode</p>
                    </div>
                    <div class="bg-sky-50 rounded-xl p-4 border border-sky-100">
                        <div class="w-8 h-8 bg-sky-600 rounded-xl flex items-center justify-center mb-2">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                        </div>
                        <div class="font-bold text-sky-900 text-sm mb-1">Sync อัตโนมัติ</div>
                        <p class="text-xs text-sky-700">ส่งสิทธิ์ไปยังระบบปลายทางผ่าน Queue Worker อัตโนมัติ</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── การเข้าสู่ระบบ ── --}}
        <div id="login" class="bg-white rounded-2xl shadow-sm ring-1 ring-slate-100 overflow-hidden">
            <div class="flex items-center gap-3 px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                <div class="w-8 h-8 bg-violet-100 rounded-xl flex items-center justify-center">
                    <svg class="w-4 h-4 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                    </svg>
                </div>
                <h2 class="font-bold text-slate-800">การเข้าสู่ระบบ</h2>
            </div>
            <div class="px-6 py-5 space-y-4 text-sm text-slate-700 leading-relaxed">
                <p>UCM ใช้บัญชี <strong class="text-slate-900">Active Directory (AD)</strong> ขององค์กรในการยืนยันตัวตน ไม่มีการสร้างรหัสผ่านแยกต่างหาก</p>
                <div class="space-y-2">
                    @foreach ([
                        ['step' => '1', 'text' => 'เปิด browser ไปที่ URL ของระบบ UCM'],
                        ['step' => '2', 'text' => 'กรอก <strong>AD Username</strong> เช่น <code class="font-mono bg-slate-100 px-1.5 py-0.5 rounded text-xs">firstname.lastname</code>'],
                        ['step' => '3', 'text' => 'กรอก <strong>รหัสผ่าน AD</strong> (รหัสผ่านเดียวกับที่ใช้ Login เครื่องคอมพิวเตอร์)'],
                        ['step' => '4', 'text' => 'กด <strong>เข้าสู่ระบบ</strong>'],
                    ] as $item)
                        <div class="flex items-start gap-3">
                            <span class="w-6 h-6 bg-indigo-600 text-white text-xs font-bold rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">{{ $item['step'] }}</span>
                            <p class="text-slate-700">{!! $item['text'] !!}</p>
                        </div>
                    @endforeach
                </div>
                <div class="flex items-start gap-3 p-3.5 bg-amber-50 border border-amber-200 rounded-xl text-xs text-amber-800">
                    <svg class="w-4 h-4 text-amber-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <span>หากเข้าสู่ระบบไม่ได้ กรุณาติดต่อฝ่าย IT เพื่อตรวจสอบสิทธิ์บัญชี AD ของคุณ</span>
                </div>
            </div>
        </div>

        {{-- ── Dashboard ── --}}
        <div id="dashboard" class="bg-white rounded-2xl shadow-sm ring-1 ring-slate-100 overflow-hidden">
            <div class="flex items-center gap-3 px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                <div class="w-8 h-8 bg-sky-100 rounded-xl flex items-center justify-center">
                    <svg class="w-4 h-4 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                </div>
                <h2 class="font-bold text-slate-800">Dashboard</h2>
            </div>
            <div class="px-6 py-5 space-y-4 text-sm text-slate-700 leading-relaxed">
                <p>หน้า Dashboard แสดงภาพรวมของระบบ UCM ทั้งหมด แบ่งเป็น 5 ส่วน:</p>
                <div class="space-y-3">
                    <div class="flex gap-3 p-3 bg-slate-50 rounded-xl border border-slate-100">
                        <div class="w-2 h-2 rounded-full bg-indigo-500 mt-1.5 flex-shrink-0"></div>
                        <div>
                            <div class="font-semibold text-slate-900 mb-0.5">Stat Cards (4 การ์ด)</div>
                            <p class="text-slate-600 text-xs">แสดงจำนวนระบบที่เชื่อมต่อ, ผู้ใช้งานทั้งหมด, จำนวน Sync สำเร็จและล้มเหลวในวันนี้</p>
                        </div>
                    </div>
                    <div class="flex gap-3 p-3 bg-slate-50 rounded-xl border border-slate-100">
                        <div class="w-2 h-2 rounded-full bg-violet-500 mt-1.5 flex-shrink-0"></div>
                        <div>
                            <div class="font-semibold text-slate-900 mb-0.5">Activity Charts (กราฟ 3 ชุด)</div>
                            <p class="text-slate-600 text-xs">กราฟ Audit Activity 7 วันล่าสุด (แยกตามหมวด), Sync Activity 7 วันล่าสุด (สำเร็จ/ล้มเหลว), และ Permissions per System (Top 8 ระบบ) — ขับเคลื่อนด้วย Chart.js</p>
                        </div>
                    </div>
                    <div class="flex gap-3 p-3 bg-slate-50 rounded-xl border border-slate-100">
                        <div class="w-2 h-2 rounded-full bg-sky-500 mt-1.5 flex-shrink-0"></div>
                        <div>
                            <div class="font-semibold text-slate-900 mb-0.5">ระบบที่เชื่อมต่อ</div>
                            <p class="text-slate-600 text-xs">รายการระบบทั้งหมดพร้อมสถานะ Active/Inactive และจำนวนสิทธิ์ที่กำหนด</p>
                        </div>
                    </div>
                    <div class="flex gap-3 p-3 bg-slate-50 rounded-xl border border-slate-100">
                        <div class="w-2 h-2 rounded-full bg-emerald-500 mt-1.5 flex-shrink-0"></div>
                        <div>
                            <div class="font-semibold text-slate-900 mb-0.5">กิจกรรมล่าสุด</div>
                            <p class="text-slate-600 text-xs">Timeline แสดงการ Sync สิทธิ์ 10 รายการล่าสุด พร้อมสถานะและข้อผิดพลาด (ถ้ามี)</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── จัดการผู้ใช้ ── --}}
        <div id="users" class="bg-white rounded-2xl shadow-sm ring-1 ring-slate-100 overflow-hidden">
            <div class="flex items-center gap-3 px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                <div class="w-8 h-8 bg-emerald-100 rounded-xl flex items-center justify-center">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <h2 class="font-bold text-slate-800">จัดการผู้ใช้</h2>
            </div>
            <div class="px-6 py-5 space-y-5 text-sm text-slate-700 leading-relaxed">
                <div>
                    <h3 class="font-bold text-slate-900 mb-2">นำเข้าผู้ใช้จาก AD</h3>
                    <div class="space-y-2">
                        @foreach ([
                            'กด <strong>นำเข้าจาก AD</strong> มุมขวาบน',
                            'เลือก Tab <strong>ค้นหาจาก AD</strong> แล้วพิมพ์ชื่อหรือ Username อย่างน้อย 2 ตัวอักษร',
                            'ติ๊กเลือกผู้ใช้ที่ต้องการ (หรือ "เลือกทั้งหมด") แล้วกด <strong>นำเข้า</strong>',
                            'ระบบจะสร้างบัญชีผู้ใช้และซิงค์ข้อมูล AD โดยอัตโนมัติ',
                        ] as $i => $text)
                            <div class="flex items-start gap-3">
                                <span class="w-5 h-5 bg-emerald-100 text-emerald-700 text-xs font-bold rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">{{ $i+1 }}</span>
                                <p>{!! $text !!}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="border-t border-slate-100 pt-4">
                    <h3 class="font-bold text-slate-900 mb-2">นำเข้าผู้ใช้จาก AD จำนวนมาก (Bulk Import)</h3>
                    <p class="text-slate-600 mb-2">เมื่อค้นหาและเลือกผู้ใช้หลายคนในหน้าต่าง "นำเข้าจาก AD" ระบบจะนำเข้าพร้อมกันทั้งหมด <strong>โดยไม่มีจำกัดจำนวน</strong> และดึง Permissions จากทุกระบบที่รองรับโดยอัตโนมัติ</p>
                    <div class="flex items-start gap-3 p-3 bg-amber-50 border border-amber-200 rounded-xl text-xs text-amber-800">
                        <svg class="w-4 h-4 text-amber-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                        <span>การนำเข้าจำนวนมาก (100+ คน) อาจใช้เวลาสักครู่ กรุณารอจนกว่าจะมีข้อความยืนยัน อย่าปิดหน้าต่างระหว่างนำเข้า</span>
                    </div>
                </div>
                <div class="border-t border-slate-100 pt-4">
                    <h3 class="font-bold text-slate-900 mb-2">นำเข้าจากระบบอื่น</h3>
                    <p class="text-slate-600">หากระบบที่เชื่อมต่อรองรับการดึงรายชื่อผู้ใช้ จะมี Tab <strong>"นำเข้าจากระบบ"</strong> เพิ่มขึ้นในหน้าต่างนำเข้า สามารถเลือกระบบแล้วดูรายชื่อผู้ใช้ที่ยังไม่ได้อยู่ใน UCM ได้</p>
                </div>
                <div class="border-t border-slate-100 pt-4">
                    <h3 class="font-bold text-slate-900 mb-2">ค้นหาผู้ใช้</h3>
                    <p class="text-slate-600">ใช้ช่องค้นหาด้านบนเพื่อกรองตามชื่อ, username หรือแผนก ผลลัพธ์จะแสดงจำนวนที่พบ พร้อมปุ่ม × เพื่อล้างการค้นหา</p>
                </div>
            </div>
        </div>

        {{-- ── ส่งออก CSV ── --}}
        <div id="export-csv" class="bg-white rounded-2xl shadow-sm ring-1 ring-slate-100 overflow-hidden">
            <div class="flex items-center gap-3 px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                <div class="w-8 h-8 bg-emerald-100 rounded-xl flex items-center justify-center">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                </div>
                <h2 class="font-bold text-slate-800">ส่งออกข้อมูล (Export CSV)</h2>
            </div>
            <div class="px-6 py-5 space-y-5 text-sm text-slate-700 leading-relaxed">
                <p>ฟีเจอร์ <strong>ส่งออก CSV</strong> ช่วยให้ดึงข้อมูลผู้ใช้พร้อม permissions ทุกระบบออกมาเป็นไฟล์ CSV สำหรับใช้งานใน Excel หรือเครื่องมืออื่น — <strong>ใช้ได้ทุกระดับ (ทั่วไป, L1, L2)</strong></p>

                {{-- ข้อมูลใน CSV --}}
                <div>
                    <h3 class="font-bold text-slate-900 mb-2">ข้อมูลที่ได้รับ</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        @foreach ([
                            ['label' => 'username',         'desc' => 'ชื่อผู้ใช้ในระบบ'],
                            ['label' => 'employee_number',  'desc' => 'รหัสพนักงาน'],
                            ['label' => 'name',             'desc' => 'ชื่อ-นามสกุล'],
                            ['label' => 'email',            'desc' => 'อีเมล'],
                            ['label' => 'department',       'desc' => 'แผนก'],
                            ['label' => 'title',            'desc' => 'ตำแหน่ง'],
                            ['label' => '[ชื่อระบบ]_permissions', 'desc' => 'Permission keys ของระบบนั้น (comma-separated)'],
                        ] as $col)
                        <div class="flex items-start gap-2 p-2.5 bg-slate-50 rounded-xl border border-slate-100">
                            <code class="text-xs font-mono text-indigo-600 bg-indigo-50 px-1.5 py-0.5 rounded flex-shrink-0 mt-0.5">{{ $col['label'] }}</code>
                            <span class="text-xs text-slate-600">{{ $col['desc'] }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- วิธีใช้ --}}
                <div class="border-t border-slate-100 pt-4">
                    <h3 class="font-bold text-slate-900 mb-3">วิธีการส่งออก</h3>
                    <div class="space-y-3">

                        <div class="flex gap-3 p-3 bg-emerald-50 rounded-xl border border-emerald-100">
                            <span class="w-6 h-6 bg-emerald-600 text-white text-xs font-bold rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">1</span>
                            <div>
                                <div class="font-semibold text-slate-800 text-xs mb-0.5">ส่งออกทั้งหมด</div>
                                <p class="text-xs text-slate-600">กดปุ่ม <strong>ส่งออก CSV ทั้งหมด</strong> ที่มุมขวาของหัวตาราง โดยไม่ต้องติ๊กเลือกใดๆ — ระบบจะส่งออกผู้ใช้ทุกคน</p>
                            </div>
                        </div>

                        <div class="flex gap-3 p-3 bg-emerald-50 rounded-xl border border-emerald-100">
                            <span class="w-6 h-6 bg-emerald-600 text-white text-xs font-bold rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">2</span>
                            <div>
                                <div class="font-semibold text-slate-800 text-xs mb-0.5">ส่งออกรายบุคคล หรือหลายคน</div>
                                <p class="text-xs text-slate-600">ติ๊ก checkbox ที่ column <strong>CSV</strong> ด้านซ้ายของแต่ละแถว — header ของตารางจะเปลี่ยนเป็นสีเขียวแสดงจำนวนที่เลือก จากนั้นกด <strong>ส่งออก N คน</strong></p>
                            </div>
                        </div>

                        <div class="flex gap-3 p-3 bg-emerald-50 rounded-xl border border-emerald-100">
                            <span class="w-6 h-6 bg-emerald-600 text-white text-xs font-bold rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">3</span>
                            <div>
                                <div class="font-semibold text-slate-800 text-xs mb-0.5">ส่งออกทุกหน้า (ข้ามหลาย page)</div>
                                <p class="text-xs text-slate-600">ติ๊ก checkbox ที่ header (เลือกทั้งหมดในหน้า) — แถบสีฟ้าจะโผล่พร้อมข้อความ <strong>"เลือกทั้งหมด N คนทุกหน้า"</strong> กดลิงก์นั้นเพื่อ export ข้ามทุก page โดยไม่ต้องเปิดแต่ละหน้า</p>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- หมายเหตุ --}}
                <div class="flex items-start gap-3 p-3.5 bg-blue-50 border border-blue-100 rounded-xl text-xs text-blue-800">
                    <svg class="w-4 h-4 text-blue-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                    <span>ไฟล์ CSV มี <strong>UTF-8 BOM</strong> เพื่อให้ Excel เปิดภาษาไทยได้ถูกต้องโดยไม่ต้องแปลง encoding หากใช้ Mac หรือ Google Sheets ก็รองรับได้เช่นกัน</span>
                </div>
            </div>
        </div>

        {{-- ── จัดการสิทธิ์ ── --}}
        <div id="permissions" class="bg-white rounded-2xl shadow-sm ring-1 ring-slate-100 overflow-hidden">
            <div class="flex items-center gap-3 px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                <div class="w-8 h-8 bg-rose-100 rounded-xl flex items-center justify-center">
                    <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <h2 class="font-bold text-slate-800">จัดการสิทธิ์ผู้ใช้</h2>
            </div>
            <div class="px-6 py-5 space-y-5 text-sm text-slate-700 leading-relaxed">
                <p>กดปุ่ม <strong>จัดการสิทธิ์</strong> ที่แถวของผู้ใช้ เพื่อเข้าหน้า Permission Matrix ของผู้ใช้คนนั้น</p>

                <div>
                    <h3 class="font-bold text-slate-900 mb-3">Permission Key System</h3>
                    <p class="text-slate-600 text-xs mb-3">UCM ใช้ระบบ <strong>Permission Key</strong> ที่ยืดหยุ่น — แต่ละระบบกำหนด Permission ของตนเองผ่านหน้า "ระบบที่เชื่อมต่อ" โดย Admin ระดับ 1 ขึ้นไป และ Permission เหล่านี้จะปรากฏเป็น Checkbox หรือ Radio Pill ให้กำหนดให้ผู้ใช้</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                        <div class="flex items-start gap-3 p-3 bg-indigo-50 rounded-xl border border-indigo-100">
                            <div class="w-7 h-7 bg-indigo-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <p class="font-semibold text-indigo-800 mb-0.5">Multi-select (Checkbox)</p>
                                <p class="text-indigo-700">กลุ่มสิทธิ์ที่ผู้ใช้เลือกได้หลาย Permission พร้อมกัน เช่น module ต่าง ๆ ในระบบ</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3 p-3 bg-violet-50 rounded-xl border border-violet-100">
                            <div class="w-7 h-7 bg-violet-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-3.5 h-3.5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                            </div>
                            <div>
                                <p class="font-semibold text-violet-800 mb-0.5">Exclusive (Radio Pill)</p>
                                <p class="text-violet-700">กลุ่มสิทธิ์ที่เลือกได้เพียง 1 ค่า เช่น Editable / Read Only / Denied สำหรับแต่ละโมดูลของระบบ Earth</p>
                            </div>
                        </div>
                    </div>
                    <p class="text-xs text-slate-500 mt-2">รูปแบบ (Checkbox หรือ Radio) ขึ้นอยู่กับวิธีที่แต่ละระบบกำหนด Permission ไว้ — Admin ระดับ 2 ตั้งค่าได้ในหน้า "ระบบที่เชื่อมต่อ"</p>
                </div>

                <div class="border-t border-slate-100 pt-4">
                    <h3 class="font-bold text-slate-900 mb-2">วิธีการกำหนดสิทธิ์</h3>
                    <div class="space-y-2">
                        @foreach ([
                            'เลือกระบบที่ต้องการกำหนดสิทธิ์ (แต่ละระบบแสดงเป็นส่วนแยก)',
                            'แต่ละกลุ่ม Permission แสดงเป็นแถว — ติ๊ก Checkbox (เลือกได้หลาย) หรือคลิก Radio Pill (เลือกได้ 1 ค่า) ตามรูปแบบที่ระบบกำหนด',
                            'กด <strong>บันทึกสิทธิ์ระบบนี้</strong> เพื่อส่งคำสั่ง Sync ไปยังระบบปลายทาง',
                            'สถานะ Sync (success/failed) จะแสดงทันทีหลังบันทึก',
                        ] as $i => $text)
                            <div class="flex items-start gap-3">
                                <span class="w-5 h-5 bg-slate-100 text-slate-600 text-xs font-bold rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">{{ $i+1 }}</span>
                                <p>{!! $text !!}</p>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="border-t border-slate-100 pt-4">
                    <h3 class="font-bold text-slate-900 mb-2">สัญลักษณ์ 2-Way Sync ในหน้าสิทธิ์</h3>
                    <p class="text-slate-600 mb-2">Permission ที่มี Badge สีส้ม <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-bold bg-orange-100 text-orange-700">2-way</span> หมายความว่าระบบนี้เปิด 2-way ON และการเพิ่มหรือลบ Permission จะ<strong>ส่งผลต่อระบบปลายทางทันที</strong> ด้วย</p>
                    <ul class="space-y-1 text-slate-600 text-xs list-disc list-inside">
                        <li>Badge จะแสดงเฉพาะเมื่อระบบนั้นเปิด <strong>2-way ON</strong> เท่านั้น (ดูหัวข้อ "2-Way Sync")</li>
                        <li>เมื่อ <strong>เพิ่ม Permission</strong> ระบบจะ Provision (สร้าง) ใน External System อัตโนมัติ</li>
                        <li>เมื่อ <strong>ลบ Permission</strong> จะมีกล่องยืนยันพร้อมคำเตือนว่าจะลบออกจากระบบปลายทางด้วย</li>
                        <li>เมื่อ <strong>แก้ไข</strong> label/key — บันทึกใน UCM เท่านั้น ไม่ rename ในระบบปลายทาง</li>
                    </ul>
                </div>

                <div class="border-t border-slate-100 pt-4">
                    <h3 class="font-bold text-slate-900 mb-2">ปุ่ม Discover — ดึงสิทธิ์จากระบบจริง</h3>
                    <p class="text-slate-600 mb-3">เมื่อระบบปลายทางเปลี่ยนสิทธิ์ของผู้ใช้โดยตรง (ไม่ผ่าน UCM) ระบบจะแสดง Badge <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-xs font-bold bg-amber-100 text-amber-700">Out of Sync</span> และปุ่ม <strong class="text-indigo-600">Discover</strong> จะปรากฏข้างๆ</p>
                    <div class="space-y-2 text-xs">
                        <div class="flex items-start gap-3 p-3 bg-indigo-50 rounded-xl border border-indigo-100">
                            <svg class="w-4 h-4 text-indigo-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            <div>
                                <strong class="text-indigo-800">กด Discover</strong>
                                <p class="text-slate-600 mt-0.5">UCM จะดึงสิทธิ์ปัจจุบันจากระบบปลายทาง แล้วบันทึกทับข้อมูลใน UCM ทันที — เหมาะเมื่อต้องการให้ UCM ตามระบบจริง</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3 p-3 bg-slate-50 rounded-xl border border-slate-100">
                            <svg class="w-4 h-4 text-slate-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <div>
                                <strong class="text-slate-700">กด บันทึกสิทธิ์ระบบนี้</strong>
                                <p class="text-slate-600 mt-0.5">UCM จะ Push สิทธิ์ที่ตั้งไว้ใน UCM กลับไปยังระบบปลายทาง — เหมาะเมื่อต้องการให้ระบบจริงตาม UCM</p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3 flex items-start gap-2 p-3 bg-amber-50 border border-amber-200 rounded-xl text-xs text-amber-800">
                        <svg class="w-3.5 h-3.5 text-amber-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                        <span>ปุ่ม Discover ปรากฏเฉพาะระบบที่มี Adapter และสถานะเป็น <strong>Out of Sync</strong> เท่านั้น — ต้องเป็น Admin ระดับ 1 ขึ้นไปจึงจะใช้งานได้</span>
                    </div>
                </div>

                <div class="border-t border-slate-100 pt-4">
                    <h3 class="font-bold text-slate-900 mb-2">สถานะบัญชีในระบบ</h3>
                    <p class="text-slate-600 mb-2">แต่ละระบบมีปุ่ม Toggle สำหรับเปิด/ปิดบัญชีของผู้ใช้ในระบบนั้น ๆ โดยเฉพาะ:</p>
                    <ul class="space-y-1 text-slate-600 text-xs list-disc list-inside">
                        <li><strong>เปิด (Active)</strong> — ผู้ใช้เข้าใช้งานระบบนั้นได้</li>
                        <li><strong>ปิด (Inactive)</strong> — บล็อกการเข้าถึงระบบนั้น โดยไม่ลบสิทธิ์</li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- ── ระบบที่เชื่อมต่อ ── --}}
        <div id="systems" class="bg-white rounded-2xl shadow-sm ring-1 ring-slate-100 overflow-hidden">
            <div class="flex items-center gap-3 px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                <div class="w-8 h-8 bg-amber-100 rounded-xl flex items-center justify-center">
                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2"/>
                    </svg>
                </div>
                <h2 class="font-bold text-slate-800">ระบบที่เชื่อมต่อ</h2>
            </div>
            <div class="px-6 py-5 space-y-4 text-sm text-slate-700 leading-relaxed">
                <p>หน้า <strong>ระบบที่เชื่อมต่อ</strong> แสดงระบบทั้งหมดที่ UCM รองรับ แต่ละระบบการ์ดจะแสดง:</p>
                <ul class="space-y-1 text-slate-600 text-xs list-disc list-inside">
                    <li>ชื่อระบบ, Slug, คำอธิบาย</li>
                    <li>จำนวน Permissions ที่กำหนดไว้</li>
                    <li>จำนวน Assignments (ผู้ใช้ที่ได้รับสิทธิ์)</li>
                    <li>สถานะ Active / Inactive</li>
                </ul>
                <div class="border-t border-slate-100 pt-3">
                    <h3 class="font-bold text-slate-900 mb-2">หน้ารายละเอียดระบบ</h3>
                    <ul class="space-y-1 text-slate-600 text-xs list-disc list-inside">
                        <li><strong>Discover Permissions</strong> — ดึง Permission groups จากระบบปลายทางโดยอัตโนมัติ (เฉพาะระบบที่รองรับ)</li>
                        <li>เพิ่ม/แก้ไข/ลบ Permission key ได้ด้วยตนเอง</li>
                        <li>จัดกลุ่ม Permission ตาม Group เพื่อแสดงใน Permission Matrix</li>
                    </ul>
                </div>
                <div class="border-t border-slate-100 pt-3">
                    <h3 class="font-bold text-slate-900 mb-2">Toggle 2-way ON/OFF ต่อระบบ</h3>
                    <p class="text-slate-600 text-xs mb-2">แต่ละระบบที่ Adapter รองรับ 2-way จะมีปุ่ม <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-xs font-semibold bg-amber-500 text-white">⇄ 2-way ON</span> / <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-xs font-semibold border border-slate-200 text-slate-600 bg-white">⇄ 2-way OFF</span> ที่ด้านบนของหน้ารายละเอียดระบบ — <strong class="text-amber-700">เฉพาะ Admin ระดับ 2</strong> เท่านั้นที่กดสลับได้</p>
                    <div class="overflow-hidden rounded-xl border border-slate-200 text-xs">
                        <div class="grid grid-cols-4 bg-slate-50 font-bold text-slate-500 px-3 py-2 border-b border-slate-200">
                            <div>Action</div><div>2-way ON</div><div>2-way OFF</div><div class="text-slate-400">หมายเหตุ</div>
                        </div>
                        @foreach ([
                            ['เพิ่ม Permission', 'UCM + ระบบภายนอก', 'UCM เท่านั้น', ''],
                            ['ลบ Permission', 'UCM + ระบบภายนอก', 'UCM เท่านั้น', 'มีกล่องยืนยันเมื่อ ON'],
                            ['แก้ไข label/key', 'UCM เท่านั้น', 'UCM เท่านั้น', 'ไม่ rename ในระบบภายนอก'],
                            ['Sync สิทธิ์ผู้ใช้', 'ส่งไประบบภายนอกเสมอ', 'ส่งไประบบภายนอกเสมอ', 'ไม่เกี่ยวกับ toggle นี้'],
                        ] as $row)
                        <div class="grid grid-cols-4 px-3 py-2 border-b border-slate-100 last:border-0">
                            <div class="font-medium text-slate-700">{{ $row[0] }}</div>
                            <div class="text-amber-700 font-medium">{{ $row[1] }}</div>
                            <div class="text-slate-500">{{ $row[2] }}</div>
                            <div class="text-slate-400">{{ $row[3] }}</div>
                        </div>
                        @endforeach
                    </div>
                    <p class="text-xs text-slate-400 mt-2 font-medium">* "Sync สิทธิ์ผู้ใช้" หมายถึงการกด "บันทึกสิทธิ์" ในหน้าจัดการผู้ใช้ — ทำงานผ่าน Queue Worker แยกต่างหากเสมอ</p>
                </div>
            </div>
        </div>

        {{-- ── ข้อมูล Reference (Managed Groups) ── --}}
        <div id="ref-data" class="bg-white rounded-2xl shadow-sm ring-1 ring-slate-100 overflow-hidden">
            <div class="flex items-center gap-3 px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                <div class="w-8 h-8 bg-purple-100 rounded-xl flex items-center justify-center">
                    <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582 4 8 4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/>
                    </svg>
                </div>
                <h2 class="font-bold text-slate-800">ข้อมูล Reference (Managed Groups)</h2>
            </div>
            <div class="px-6 py-5 space-y-4 text-sm text-slate-700 leading-relaxed">
                <p>บางระบบมีข้อมูล Master Data ที่ UCM สามารถจัดการได้โดยตรง ซึ่งเป็นข้อมูลที่ Permission ของระบบนั้นอ้างอิงถึง ปัจจุบันรองรับ 2 ระบบ:</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                    <div class="p-3.5 bg-indigo-50 rounded-xl border border-indigo-100 text-xs">
                        <div class="font-bold text-indigo-900 mb-1.5">Earth (FLIGHT OPS)</div>
                        <ul class="space-y-1 text-indigo-700">
                            <li><span class="font-semibold">PageGroup</span> — ตาราง <code class="font-mono bg-indigo-100 px-1 rounded">UserMgnt_PageGroup</code></li>
                            <li>แต่ละ PageGroup รองรับสิทธิ์ Editable / Read Only / Denied</li>
                            <li>Renaming PageGroup กระทบ remote_value ของ permission ที่อ้างอิง</li>
                        </ul>
                    </div>
                    <div class="p-3.5 bg-purple-50 rounded-xl border border-purple-100 text-xs">
                        <div class="font-bold text-purple-900 mb-1.5">EFiling</div>
                        <ul class="space-y-1 text-purple-700">
                            <li><span class="font-semibold">Department</span> — ตาราง <code class="font-mono bg-purple-100 px-1 rounded">departments</code></li>
                            <li><span class="font-semibold">Document Category</span> — ตาราง <code class="font-mono bg-purple-100 px-1 rounded">document_categories</code></li>
                        </ul>
                    </div>
                </div>

                <div class="flex items-start gap-3 p-3.5 bg-red-50 border border-red-200 rounded-xl text-xs text-red-800">
                    <svg class="w-4 h-4 text-red-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                    <div>
                        <strong>ข้อมูลเหล่านี้เขียนตรงไปยังฐานข้อมูลของระบบปลายทาง (Direct Write)</strong> — การเพิ่ม/แก้ไข/ลบมีผลทันที ไม่มีขั้นตอน Sync Queue ควรใช้ความระมัดระวัง
                        <br class="mt-1"><strong>⚠️ Earth — Rename PageGroup:</strong> การเปลี่ยนชื่อ PageGroup จะอัปเดตเฉพาะในฐานข้อมูล Earth เท่านั้น Permission ใน UCM ที่อ้างอิงชื่อเดิมจะ Out of Sync — ควร Discover Permissions ใหม่หลัง Rename
                    </div>
                </div>

                {{-- Admin level requirements --}}
                <div class="overflow-hidden rounded-xl border border-slate-200 text-xs">
                    <div class="grid grid-cols-3 bg-slate-50 font-bold text-slate-500 px-3 py-2 border-b border-slate-200">
                        <div>การกระทำ</div><div>ระดับที่ต้องการ</div><div class="text-slate-400">หมายเหตุ</div>
                    </div>
                    @foreach ([
                        ['เพิ่มรายการ', 'Admin ระดับ 1 ขึ้นไป', 'bg-indigo-50 text-indigo-700', 'กรอกชื่อแล้วกด +'],
                        ['แก้ไขรายการ', 'Admin ระดับ 2 เท่านั้น', 'bg-amber-50 text-amber-700', 'กดปุ่มดินสอ ✏️'],
                        ['ลบรายการ', 'Admin ระดับ 2 เท่านั้น', 'bg-amber-50 text-amber-700', 'มีกล่องยืนยันก่อนลบ'],
                    ] as [$action, $level, $cls, $note])
                    <div class="grid grid-cols-3 px-3 py-2 border-b border-slate-100 last:border-0">
                        <div class="font-medium text-slate-700">{{ $action }}</div>
                        <div class="{{ $cls }} font-semibold px-2 py-0.5 rounded-md w-fit">{{ $level }}</div>
                        <div class="text-slate-400">{{ $note }}</div>
                    </div>
                    @endforeach
                </div>

                <div>
                    <h3 class="font-bold text-slate-900 mb-3">วิธีเข้าถึง Managed Groups</h3>
                    <div class="space-y-2">
                        @foreach ([
                            'เข้าหน้า <strong>ระบบที่เชื่อมต่อ</strong> แล้วคลิกระบบที่ต้องการ (เช่น EFiling)',
                            'เลื่อนลงไปส่วน <strong>ข้อมูล Reference</strong> ที่อยู่ด้านล่างของหน้ารายละเอียดระบบ',
                            'เลือก Tab ที่ต้องการ เช่น <strong>Department</strong> หรือ <strong>Document Category</strong>',
                            'จะเห็นรายการทั้งหมดจากระบบปลายทาง พร้อมปุ่มแก้ไขและลบแต่ละรายการ',
                        ] as $i => $text)
                            <div class="flex items-start gap-3">
                                <span class="w-5 h-5 bg-purple-100 text-purple-700 text-xs font-bold rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">{{ $i+1 }}</span>
                                <p>{!! $text !!}</p>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="border-t border-slate-100 pt-4">
                    <h3 class="font-bold text-slate-900 mb-3">การจัดการรายการ</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div class="flex gap-3 p-3 bg-emerald-50 rounded-xl border border-emerald-100">
                            <div class="w-7 h-7 bg-emerald-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            </div>
                            <div>
                                <div class="font-semibold text-emerald-900 text-xs mb-0.5">เพิ่มรายการ</div>
                                <p class="text-emerald-700 text-xs">กรอกชื่อในช่อง "เพิ่มรายการใหม่" แล้วกดปุ่ม <strong>+</strong></p>
                            </div>
                        </div>
                        <div class="flex gap-3 p-3 bg-sky-50 rounded-xl border border-sky-100">
                            <div class="w-7 h-7 bg-sky-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-3.5 h-3.5 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </div>
                            <div>
                                <div class="font-semibold text-sky-900 text-xs mb-0.5">แก้ไขรายการ <span class="ml-1 text-[10px] font-bold text-amber-600 bg-amber-50 px-1.5 py-0.5 rounded ring-1 ring-amber-200/70">Admin L2</span></div>
                                <p class="text-sky-700 text-xs">กดปุ่มดินสอ ✏️ ที่รายการ แก้ไขชื่อ แล้วกด บันทึก</p>
                            </div>
                        </div>
                        <div class="flex gap-3 p-3 bg-red-50 rounded-xl border border-red-100">
                            <div class="w-7 h-7 bg-red-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-3.5 h-3.5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </div>
                            <div>
                                <div class="font-semibold text-red-900 text-xs mb-0.5">ลบรายการ <span class="ml-1 text-[10px] font-bold text-amber-600 bg-amber-50 px-1.5 py-0.5 rounded ring-1 ring-amber-200/70">Admin L2</span></div>
                                <p class="text-red-700 text-xs">กดปุ่มถังขยะ 🗑️ จะมีกล่องยืนยันก่อนลบ</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── 2-Way Sync ── --}}
        <div id="twoway-sync" class="bg-white rounded-2xl shadow-sm ring-1 ring-slate-100 overflow-hidden">
            <div class="flex items-center gap-3 px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                <div class="w-8 h-8 bg-orange-100 rounded-xl flex items-center justify-center">
                    <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                    </svg>
                </div>
                <h2 class="font-bold text-slate-800">2-Way Sync</h2>
            </div>
            <div class="px-6 py-5 space-y-5 text-sm text-slate-700 leading-relaxed">

                {{-- ภาพรวม --}}
                <p>UCM แยก Sync ออกเป็น 2 ระดับ ที่ทำงาน<strong>อิสระจากกัน</strong>:</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                    <div class="p-4 bg-indigo-50 rounded-xl border border-indigo-200">
                        <div class="font-bold text-indigo-900 mb-1.5">📋 Permission Definition Sync</div>
                        <p class="text-indigo-800">การเพิ่ม/ลบ <em>นิยาม</em>ของ Permission (เช่น PageGroup ใน Earth, Department ใน EFiling)<br>ควบคุมได้ด้วย <strong>Toggle 2-way ON/OFF</strong></p>
                    </div>
                    <div class="p-4 bg-emerald-50 rounded-xl border border-emerald-200">
                        <div class="font-bold text-emerald-900 mb-1.5">👤 User Permission Sync</div>
                        <p class="text-emerald-800">การส่งสิทธิ์ของผู้ใช้ไปยังระบบปลายทาง (กดบันทึกสิทธิ์ในหน้าผู้ใช้)<br><strong>ทำงานเสมอ</strong> ผ่าน Queue Worker ไม่เกี่ยวกับ Toggle</p>
                    </div>
                </div>

                {{-- Toggle --}}
                <div class="border-t border-slate-100 pt-4">
                    <h3 class="font-bold text-slate-900 mb-3">Toggle 2-way ON / OFF</h3>
                    <p class="text-slate-600 text-xs mb-3">ระบบที่ Adapter รองรับ (Earth, EFiling) จะมีปุ่มสลับที่หน้ารายละเอียดระบบ — <strong class="text-amber-700">เฉพาะ Admin ระดับ 2</strong> เท่านั้นที่กดสลับได้</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                        <div class="p-3.5 bg-amber-50 rounded-xl border border-amber-200">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold bg-amber-500 text-white">⇄ 2-way ON</span>
                            </div>
                            <ul class="space-y-1 text-amber-800">
                                <li>✅ เพิ่ม Permission → สร้างในระบบภายนอกด้วย</li>
                                <li>✅ ลบ Permission → ลบออกจากระบบภายนอกด้วย</li>
                                <li>➡️ แก้ไข label/key → UCM เท่านั้น</li>
                            </ul>
                        </div>
                        <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold border border-slate-300 text-slate-600 bg-white">⇄ 2-way OFF</span>
                            </div>
                            <ul class="space-y-1 text-slate-600">
                                <li>➡️ เพิ่ม Permission → UCM เท่านั้น</li>
                                <li>➡️ ลบ Permission → UCM เท่านั้น</li>
                                <li>➡️ แก้ไข label/key → UCM เท่านั้น</li>
                            </ul>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 mt-3 p-3 bg-blue-50 border border-blue-100 rounded-xl text-xs text-blue-800">
                        <svg class="w-4 h-4 text-blue-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                        <span><strong>Sync สิทธิ์ผู้ใช้</strong> (กดบันทึกสิทธิ์ในหน้าผู้ใช้) ทำงาน<strong>ทุกครั้ง</strong>ผ่าน Queue Worker โดยไม่เกี่ยวกับ Toggle นี้</span>
                    </div>
                </div>

                {{-- ระบบที่รองรับ --}}
                <div class="border-t border-slate-100 pt-4">
                    <h3 class="font-bold text-slate-900 mb-3">ระบบที่รองรับ 2-way</h3>
                    <div class="space-y-2 text-xs">
                        @foreach ([
                            ['Earth', 'ใช่', 'สร้าง PageGroup เมื่อเพิ่ม / ลบ PageGroup เมื่อลบ Permission สุดท้ายของ group (จัดการ PageGroup ได้ผ่าน Managed Groups ด้วย)', 'bg-emerald-50 border-emerald-200 text-emerald-800'],
                            ['EFiling', 'ใช่', 'สร้าง Department/Document Category เมื่อเพิ่ม / ลบเมื่อลบ (จัดการผ่าน Managed Groups ด้วย)', 'bg-emerald-50 border-emerald-200 text-emerald-800'],
                            ['Repair System', 'ไม่', 'ไม่มี permission definition table — Toggle จะไม่ปรากฏ', 'bg-slate-50 border-slate-200 text-slate-600'],
                            ['ระบบไม่มี Adapter', 'ไม่', 'บันทึกใน UCM เท่านั้น', 'bg-slate-50 border-slate-200 text-slate-600'],
                        ] as [$sys, $supported, $desc, $cls])
                        <div class="flex items-start gap-3 p-3 rounded-xl border {{ $cls }}">
                            <div class="w-24 font-bold flex-shrink-0">{{ $sys }}</div>
                            <div class="w-8 flex-shrink-0">{{ $supported === 'ใช่' ? '✅' : '—' }}</div>
                            <div>{{ $desc }}</div>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- สัญลักษณ์ --}}
                <div class="border-t border-slate-100 pt-4">
                    <h3 class="font-bold text-slate-900 mb-3">สัญลักษณ์ที่ควรรู้</h3>
                    <div class="space-y-2 text-xs">
                        <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl border border-slate-100">
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-bold bg-orange-100 text-orange-700 flex-shrink-0 whitespace-nowrap">2-way</span>
                            <p class="text-slate-700">Badge สีส้มบน Permission — ระบบนี้เปิด 2-way ON และการเพิ่ม/ลบมีผลต่อระบบปลายทาง</p>
                        </div>
                        <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl border border-slate-100">
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-bold bg-emerald-100 text-emerald-700 flex-shrink-0 whitespace-nowrap">remote</span>
                            <p class="text-slate-700">แหล่งข้อมูลสิทธิ์ผู้ใช้มาจากระบบปลายทางโดยตรง (Real-time)</p>
                        </div>
                        <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl border border-slate-100">
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-bold bg-slate-200 text-slate-600 flex-shrink-0 whitespace-nowrap">ucm</span>
                            <p class="text-slate-700">แหล่งข้อมูลสิทธิ์มาจาก UCM (ระบบปลายทางออฟไลน์หรือไม่มี Adapter)</p>
                        </div>
                        <div class="flex items-center gap-3 p-3 bg-amber-50 rounded-xl border border-amber-100">
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-bold bg-amber-100 text-amber-700 flex-shrink-0 whitespace-nowrap">Out of Sync</span>
                            <p class="text-slate-700">สิทธิ์ผู้ใช้ใน UCM ไม่ตรงกับระบบปลายทาง — กด <strong>"Discover"</strong> เพื่อดึงสิทธิ์จากระบบจริงมาเก็บใน UCM หรือกด "บันทึกสิทธิ์" เพื่อ Push สิทธิ์ UCM กลับไปยังระบบปลายทาง</p>
                        </div>
                    </div>
                </div>

                <div class="flex items-start gap-3 p-3.5 bg-red-50 border border-red-200 rounded-xl text-xs text-red-800">
                    <svg class="w-4 h-4 text-red-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                    <span>เมื่อ 2-way ON การ<strong>ลบ Permission</strong>จะแสดงกล่องยืนยันพร้อมรายละเอียดว่าจะเกิดอะไรขึ้นกับระบบภายนอก — อ่านให้ครบก่อนยืนยัน</span>
                </div>
            </div>
        </div>

        {{-- ── สิทธิ์ Admin ── --}}
        <div id="admin-levels" class="bg-white rounded-2xl shadow-sm ring-1 ring-slate-100 overflow-hidden">
            <div class="flex items-center gap-3 px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                <div class="w-8 h-8 bg-amber-100 rounded-xl flex items-center justify-center">
                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <h2 class="font-bold text-slate-800">สิทธิ์ Admin (Admin Levels)</h2>
            </div>
            <div class="px-6 py-5 space-y-5 text-sm text-slate-700 leading-relaxed">
                <p>UCM แบ่งระดับสิทธิ์ผู้ดูแลระบบออกเป็น <strong>3 ระดับ</strong> เพื่อควบคุมการเข้าถึงฟีเจอร์ที่มีความเสี่ยงสูง</p>

                {{-- Level cards --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div class="p-4 bg-slate-50 rounded-xl border border-slate-200">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                            <span class="font-bold text-slate-700 text-sm">ทั่วไป (0)</span>
                        </div>
                        <ul class="space-y-1 text-xs text-slate-600">
                            <li>✅ เข้าดูข้อมูลและ Permission ได้ (อ่านอย่างเดียว)</li>
                            <li>❌ แก้ไขสิทธิ์ผู้ใช้ไม่ได้</li>
                            <li>❌ แก้ไข / ลบ Permission ไม่ได้</li>
                            <li>❌ ตรวจสอบ / นำเข้าจาก AD ไม่ได้</li>
                            <li>❌ จัดการข้อมูล Reference ไม่ได้</li>
                            <li>❌ Toggle 2-way ไม่ได้</li>
                        </ul>
                    </div>
                    <div class="p-4 bg-indigo-50 rounded-xl border border-indigo-200">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                            <span class="font-bold text-indigo-800 text-sm">Admin ระดับ 1</span>
                        </div>
                        <ul class="space-y-1 text-xs text-indigo-700">
                            <li>✅ ทุกอย่างของผู้ใช้ทั่วไป</li>
                            <li>✅ <strong>แก้ไขสิทธิ์ผู้ใช้</strong> ในทุกระบบได้</li>
                            <li>✅ <strong>แก้ไข / ลบ Permission</strong> ในระบบได้</li>
                            <li>✅ <strong>ตรวจสอบ / นำเข้า</strong> ผู้ใช้จาก AD ได้</li>
                            <li>✅ <strong>Discover Permissions</strong> จากระบบภายนอกได้</li>
                            <li>✅ <strong>เพิ่ม</strong> Reference Data ได้</li>
                            <li>❌ แก้ไข / ลบ Reference Data ไม่ได้</li>
                            <li>❌ Toggle 2-way ไม่ได้</li>
                            <li>❌ จัดการสิทธิ์ Admin ไม่ได้</li>
                        </ul>
                    </div>
                    <div class="p-4 bg-amber-50 rounded-xl border border-amber-200">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                            <span class="font-bold text-amber-800 text-sm">Admin ระดับ 2</span>
                        </div>
                        <ul class="space-y-1 text-xs text-amber-700">
                            <li>✅ ทุกอย่างของ Admin ระดับ 1</li>
                            <li>✅ <strong>แก้ไข / ลบ</strong> Reference Data ได้</li>
                            <li>✅ Toggle 2-way ON/OFF ได้</li>
                            <li>✅ เปิด / ปิด Account ในระบบภายนอกได้</li>
                            <li>✅ <strong>จัดการสิทธิ์ Admin</strong> ของผู้ใช้อื่นได้</li>
                            <li>✅ <strong>สร้าง / แก้ไข / ลบ Notification Channel</strong> ได้</li>
                            <li>✅ <strong>Retry / Flush Failed Jobs</strong> ใน Queue Monitor ได้</li>
                            <li>⚠️ ไม่สามารถลดระดับตัวเองได้</li>
                        </ul>
                    </div>
                </div>

                {{-- How to manage --}}
                <div class="border-t border-slate-100 pt-4">
                    <h3 class="font-bold text-slate-900 mb-2">วิธีจัดการสิทธิ์ Admin</h3>
                    <div class="space-y-2">
                        @foreach ([
                            'เข้าเมนู <strong>ผู้ดูแลระบบ → จัดการสิทธิ์ Admin</strong> ในแถบนำทางด้านซ้าย (มองเห็นเฉพาะ Admin ระดับ 2)',
                            'หน้าจะแสดงรายชื่อผู้ใช้ทั้งหมด พร้อมระดับปัจจุบัน (ทั่วไป / L1 / L2)',
                            'กดปุ่ม <strong>ทั่วไป</strong>, <strong>L1</strong> หรือ <strong>L2</strong> ที่แถวผู้ใช้เพื่อเปลี่ยนระดับทันที',
                            'ผู้ใช้ที่เป็น Admin ระดับ 2 <strong>ไม่สามารถลดระดับตัวเองได้</strong> เพื่อป้องกันระบบไม่มีผู้ดูแล',
                        ] as $i => $text)
                            <div class="flex items-start gap-3">
                                <span class="w-5 h-5 bg-amber-100 text-amber-700 text-xs font-bold rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">{{ $i+1 }}</span>
                                <p>{!! $text !!}</p>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="flex items-start gap-3 p-3.5 bg-blue-50 border border-blue-100 rounded-xl text-xs text-blue-800">
                    <svg class="w-4 h-4 text-blue-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                    <span>หน้า "จัดการสิทธิ์ Admin" จะ<strong>ไม่ปรากฏ</strong>ในเมนูหากผู้ใช้ไม่ใช่ Admin ระดับ 2 และจะ return 403 หากเข้า URL โดยตรง</span>
                </div>
            </div>
        </div>

        {{-- ── ตรวจสอบ AD ── --}}
        <div id="ad-check" class="bg-white rounded-2xl shadow-sm ring-1 ring-slate-100 overflow-hidden">
            <div class="flex items-center gap-3 px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                <div class="w-8 h-8 bg-orange-100 rounded-xl flex items-center justify-center">
                    <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h2 class="font-bold text-slate-800">ตรวจสอบ AD</h2>
            </div>
            <div class="px-6 py-5 space-y-3 text-sm text-slate-700 leading-relaxed">
                <p>ฟีเจอร์ <strong>ตรวจสอบ AD</strong> ช่วยระบุผู้ใช้ใน UCM ที่บัญชี AD ถูกลบหรือ Disable ไปแล้ว</p>
                <div class="space-y-2">
                    @foreach ([
                        'กด <strong>ตรวจสอบ AD</strong> มุมขวาบนของหน้าจัดการผู้ใช้',
                        'กด <strong>เริ่มตรวจสอบ</strong> รอระบบตรวจสอบกับ AD',
                        'หากพบผู้ใช้ที่ไม่มีใน AD แล้ว จะแสดงรายชื่อพร้อม Badge <span class="text-red-600 font-semibold">ไม่พบใน AD</span>',
                        'ติ๊กเลือกผู้ใช้ที่ต้องการลบ แล้วกด <strong>ลบออกจาก UCM</strong>',
                    ] as $i => $text)
                        <div class="flex items-start gap-3">
                            <span class="w-5 h-5 bg-orange-100 text-orange-700 text-xs font-bold rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">{{ $i+1 }}</span>
                            <p>{!! $text !!}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- ── Sync ── --}}
        <div id="sync" class="bg-white rounded-2xl shadow-sm ring-1 ring-slate-100 overflow-hidden">
            <div class="flex items-center gap-3 px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                <div class="w-8 h-8 bg-teal-100 rounded-xl flex items-center justify-center">
                    <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                </div>
                <h2 class="font-bold text-slate-800">การ Sync สิทธิ์</h2>
            </div>
            <div class="px-6 py-5 space-y-4 text-sm text-slate-700 leading-relaxed">
                <p>เมื่อกด <strong>บันทึกสิทธิ์</strong> ระบบจะสร้าง Job ใน Queue และส่งไปประมวลผลโดย Queue Worker ซึ่งเชื่อมต่อไปยังฐานข้อมูลของระบบปลายทางโดยตรง</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="flex items-center gap-3 p-3 bg-emerald-50 rounded-xl border border-emerald-100">
                        <div class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        </div>
                        <div>
                            <div class="font-semibold text-emerald-900 text-xs">Sync Success</div>
                            <div class="text-emerald-700 text-xs">สิทธิ์ถูกส่งไปยังระบบปลายทางแล้ว</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 p-3 bg-red-50 rounded-xl border border-red-100">
                        <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-red-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                        </div>
                        <div>
                            <div class="font-semibold text-red-900 text-xs">Sync Failed</div>
                            <div class="text-red-700 text-xs">เกิดข้อผิดพลาด ดู error message ใน Dashboard</div>
                        </div>
                    </div>
                </div>
                <div class="flex items-start gap-3 p-3.5 bg-blue-50 border border-blue-200 rounded-xl text-xs text-blue-800">
                    <svg class="w-4 h-4 text-blue-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                    <span>หาก Sync Failed ให้แจ้ง IT พร้อม error message ที่แสดงใน Dashboard เพื่อตรวจสอบการเชื่อมต่อกับระบบปลายทาง</span>
                </div>
            </div>
        </div>

        {{-- ── Connector Wizard ── --}}
        <div id="connector-wizard" class="bg-white rounded-2xl shadow-sm ring-1 ring-slate-100 overflow-hidden">
            <div class="flex items-center gap-3 px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                <div class="w-8 h-8 bg-indigo-100 rounded-xl flex items-center justify-center">
                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="font-bold text-slate-800">Connector Wizard</h2>
                    <p class="text-xs text-slate-400 mt-0.5">เชื่อมต่อฐานข้อมูลระบบภายนอกเข้ากับ UCM โดยไม่ต้องเขียนโค้ด</p>
                </div>
                <span class="ml-auto text-[10px] font-bold bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full">Admin L2 เท่านั้น</span>
            </div>
            <div class="px-6 py-5 space-y-6 text-sm text-slate-700 leading-relaxed">

                {{-- คืออะไร --}}
                <div>
                    <p><strong class="text-slate-900">Connector Wizard</strong> คือเครื่องมือที่ช่วยให้ Admin ระดับ 2 สามารถเชื่อมต่อฐานข้อมูลของระบบภายนอก (เช่น ระบบซ่อมบำรุง, ระบบ HR, ระบบจัดการเอกสาร) เข้ากับ UCM ได้ <strong>โดยไม่ต้องเขียนโค้ด PHP</strong> เพียงกรอกข้อมูลผ่านหน้า Wizard แบบ Step-by-Step</p>

                    <div class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div class="bg-indigo-50 rounded-xl p-4 border border-indigo-100">
                            <div class="text-2xl mb-2">🔌</div>
                            <div class="font-bold text-indigo-900 text-sm mb-1">No-Code</div>
                            <p class="text-xs text-indigo-700">ไม่ต้องเขียน Adapter PHP ด้วยตนเอง ระบบสร้าง DynamicAdapter ให้อัตโนมัติ</p>
                        </div>
                        <div class="bg-emerald-50 rounded-xl p-4 border border-emerald-100">
                            <div class="text-2xl mb-2">🗄️</div>
                            <div class="font-bold text-emerald-900 text-sm mb-1">หลาย DB Driver</div>
                            <p class="text-xs text-emerald-700">รองรับ MySQL / MariaDB, PostgreSQL และ Microsoft SQL Server</p>
                        </div>
                        <div class="bg-sky-50 rounded-xl p-4 border border-sky-100">
                            <div class="text-2xl mb-2">⚡</div>
                            <div class="font-bold text-sky-900 text-sm mb-1">Sync อัตโนมัติ</div>
                            <p class="text-xs text-sky-700">หลังตั้งค่าแล้ว UCM จะ sync สิทธิ์ไปยังระบบปลายทางทันทีที่มีการเปลี่ยนแปลง</p>
                        </div>
                    </div>
                </div>

                {{-- วิธีเปิดใช้งาน --}}
                <div class="border-t border-slate-100 pt-5">
                    <h3 class="font-bold text-slate-900 mb-3">วิธีเข้าใช้งาน</h3>
                    <p class="text-slate-600 mb-3">ไปที่เมนู <strong>ผู้ดูแลระบบ → Connector Wizard</strong> ในแถบเมนูด้านซ้าย (ต้องเป็น Admin ระดับ 2 เท่านั้น)</p>
                    <div class="flex items-start gap-3 p-3.5 bg-amber-50 border border-amber-200 rounded-xl text-xs text-amber-800">
                        <svg class="w-4 h-4 text-amber-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                        <span>Connector Wizard ต้องการสิทธิ์เข้าถึงฐานข้อมูลโดยตรง กรุณาเตรียม <strong>Host, Port, ชื่อ Database, Username, Password</strong> ของระบบปลายทางไว้ก่อน</span>
                    </div>
                </div>

                {{-- 5 ขั้นตอน --}}
                <div class="border-t border-slate-100 pt-5">
                    <h3 class="font-bold text-slate-900 mb-4">5 ขั้นตอนของ Wizard</h3>
                    <div class="space-y-4">

                        {{-- Step 1 --}}
                        <div class="flex gap-4 p-4 bg-slate-50 rounded-xl border border-slate-100">
                            <div class="w-8 h-8 bg-indigo-600 text-white text-sm font-bold rounded-full flex items-center justify-center flex-shrink-0">1</div>
                            <div class="flex-1">
                                <p class="font-bold text-slate-900 mb-1">ข้อมูลระบบ</p>
                                <p class="text-slate-600 text-xs leading-relaxed">กรอกชื่อระบบ, Slug (รหัสระบบ), คำอธิบาย, สีประจำระบบ และ Emoji icon หรือเลือก "เชื่อมกับระบบที่มีอยู่แล้ว" หากต้องการเพิ่ม Connector ให้ระบบที่สร้างไว้ก่อนหน้านี้</p>
                                <div class="mt-2 text-xs text-indigo-700 bg-indigo-50 px-3 py-2 rounded-lg">
                                    <strong>Slug</strong> — ต้องเป็นตัวพิมพ์เล็ก ตัวเลข และ "-" เท่านั้น เช่น <code class="font-mono bg-white px-1 rounded">repair-system</code> ระบบจะสร้าง Slug ให้อัตโนมัติจากชื่อ
                                </div>
                            </div>
                        </div>

                        {{-- Step 2 --}}
                        <div class="flex gap-4 p-4 bg-slate-50 rounded-xl border border-slate-100">
                            <div class="w-8 h-8 bg-indigo-600 text-white text-sm font-bold rounded-full flex items-center justify-center flex-shrink-0">2</div>
                            <div class="flex-1">
                                <p class="font-bold text-slate-900 mb-1">การเชื่อมต่อฐานข้อมูล</p>
                                <p class="text-slate-600 text-xs leading-relaxed mb-2">เลือก Database Driver จากนั้นกรอก Host, Port, Database Name, Username และ Password</p>
                                <div class="grid grid-cols-3 gap-2 text-xs">
                                    @foreach (['🐬 MySQL / MariaDB' => '3306', '🐘 PostgreSQL' => '5432', '🪟 SQL Server' => '1433'] as $name => $port)
                                    <div class="bg-white border border-slate-200 rounded-lg p-2 text-center">
                                        <p class="font-semibold text-slate-800">{{ $name }}</p>
                                        <p class="text-slate-400 mt-0.5">Port: {{ $port }}</p>
                                    </div>
                                    @endforeach
                                </div>
                                <p class="mt-2 text-xs text-slate-600">กด <strong>"ทดสอบการเชื่อมต่อ"</strong> เพื่อตรวจสอบว่าเข้าถึงฐานข้อมูลได้ก่อนดำเนินการต่อ</p>
                            </div>
                        </div>

                        {{-- Step 3 --}}
                        <div class="flex gap-4 p-4 bg-slate-50 rounded-xl border border-slate-100">
                            <div class="w-8 h-8 bg-indigo-600 text-white text-sm font-bold rounded-full flex items-center justify-center flex-shrink-0">3</div>
                            <div class="flex-1">
                                <p class="font-bold text-slate-900 mb-1">ตาราง Users</p>
                                <p class="text-slate-600 text-xs leading-relaxed mb-2">เลือกตารางที่เก็บข้อมูลผู้ใช้ในระบบภายนอก จากนั้น map คอลัมน์:</p>
                                <div class="space-y-1.5 text-xs">
                                    @foreach ([
                                        ['col' => 'Identifier', 'req' => true, 'desc' => 'คอลัมน์ที่ตรงกับ UCM username หรือ employee_number เช่น user_code, login_name'],
                                        ['col' => 'ชื่อ-นามสกุล', 'req' => false, 'desc' => 'คอลัมน์ชื่อผู้ใช้สำหรับแสดงผล'],
                                        ['col' => 'อีเมล', 'req' => false, 'desc' => 'คอลัมน์อีเมล'],
                                        ['col' => 'แผนก', 'req' => false, 'desc' => 'คอลัมน์แผนกหรือหน่วยงาน'],
                                        ['col' => 'สถานะ Active', 'req' => false, 'desc' => 'คอลัมน์ที่บ่งบอกว่า user ยังใช้งานอยู่หรือไม่ พร้อมระบุค่าที่หมายถึง Active เช่น 1, Y, active'],
                                    ] as $f)
                                    <div class="flex items-start gap-2">
                                        <span class="inline-block px-1.5 py-0.5 rounded text-[10px] font-bold {{ $f['req'] ? 'bg-red-100 text-red-600' : 'bg-slate-100 text-slate-500' }} flex-shrink-0 mt-0.5">{{ $f['req'] ? 'บังคับ' : 'ไม่บังคับ' }}</span>
                                        <span><strong class="text-slate-800">{{ $f['col'] }}:</strong> {{ $f['desc'] }}</span>
                                    </div>
                                    @endforeach
                                </div>
                                <p class="mt-2 text-xs text-slate-600">กด <strong>"ดูตัวอย่างข้อมูล"</strong> เพื่อดู 10 แถวแรกจากตาราง ช่วยยืนยันว่า mapping ถูกต้อง</p>
                            </div>
                        </div>

                        {{-- Step 4 --}}
                        <div class="flex gap-4 p-4 bg-slate-50 rounded-xl border border-slate-100">
                            <div class="w-8 h-8 bg-indigo-600 text-white text-sm font-bold rounded-full flex items-center justify-center flex-shrink-0">4</div>
                            <div class="flex-1">
                                <p class="font-bold text-slate-900 mb-2">Permission Mode</p>
                                <p class="text-slate-600 text-xs mb-3">เลือกวิธีที่ระบบภายนอกจัดเก็บสิทธิ์ มี 3 รูปแบบ:</p>
                                <div class="space-y-3">
                                    <div class="bg-white border border-slate-200 rounded-xl p-3">
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="text-base">🔗</span>
                                            <p class="font-bold text-slate-800 text-xs">Junction Table</p>
                                            <span class="text-[10px] bg-indigo-100 text-indigo-700 px-1.5 py-0.5 rounded font-bold">แนะนำ</span>
                                        </div>
                                        <p class="text-xs text-slate-600">ตาราง mapping แยกต่างหาก เช่น <code class="font-mono bg-slate-100 px-1 rounded text-[10px]">user_roles(user_id, role_code)</code> เหมาะสำหรับระบบที่ผู้ใช้มีได้หลายสิทธิ์</p>
                                        <p class="text-xs text-slate-500 mt-1">→ ต้องระบุ: ตาราง, FK column ที่ชี้หาผู้ใช้, column ค่าสิทธิ์</p>
                                    </div>
                                    <div class="bg-white border border-slate-200 rounded-xl p-3">
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="text-base">📋</span>
                                            <p class="font-bold text-slate-800 text-xs">Single Column</p>
                                        </div>
                                        <p class="text-xs text-slate-600">คอลัมน์ <code class="font-mono bg-slate-100 px-1 rounded text-[10px]">role</code> อยู่บนตาราง users เดิม เหมาะสำหรับระบบที่ผู้ใช้มีได้แค่ 1 role</p>
                                        <p class="text-xs text-slate-500 mt-1">→ ต้องระบุ: ตาราง users เดิม, column ที่เก็บค่าสิทธิ์</p>
                                    </div>
                                    <div class="bg-white border border-slate-200 rounded-xl p-3">
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="text-base">✍️</span>
                                            <p class="font-bold text-slate-800 text-xs">Manual</p>
                                        </div>
                                        <p class="text-xs text-slate-600">ไม่มีตาราง permission ในระบบ — กำหนด permission list ด้วยตนเองใน UCM โดยไม่ sync ไปยังฐานข้อมูล</p>
                                        <p class="text-xs text-slate-500 mt-1">→ เหมาะสำหรับระบบที่ตรวจสิทธิ์ผ่าน UCM API โดยตรง</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Step 5 --}}
                        <div class="flex gap-4 p-4 bg-emerald-50 rounded-xl border border-emerald-100">
                            <div class="w-8 h-8 bg-emerald-600 text-white text-sm font-bold rounded-full flex items-center justify-center flex-shrink-0">5</div>
                            <div class="flex-1">
                                <p class="font-bold text-slate-900 mb-1">ยืนยันการสร้าง Connector</p>
                                <p class="text-slate-600 text-xs leading-relaxed">ตรวจสอบข้อมูลสรุปทั้งหมด — ชื่อระบบ, DB Connection, User Table Mapping และ Permission Mode จากนั้นกด <strong>"สร้าง Connector"</strong></p>
                                <div class="mt-2 text-xs text-emerald-700 bg-white border border-emerald-200 px-3 py-2 rounded-lg">
                                    หลังสร้างสำเร็จ ระบบจะสร้าง <strong>System</strong> ใหม่ใน UCM พร้อม <strong>DynamicAdapter</strong> และ redirect ไปยังหน้าจัดการระบบโดยอัตโนมัติ
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- หลังสร้างแล้วทำอะไรต่อ --}}
                <div class="border-t border-slate-100 pt-5">
                    <h3 class="font-bold text-slate-900 mb-3">หลังสร้าง Connector แล้ว ทำอะไรต่อ?</h3>
                    <div class="space-y-2">
                        @foreach ([
                            ['icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z', 'color' => 'indigo', 'title' => 'สร้าง Permissions', 'desc' => 'ไปที่หน้า "ระบบที่เชื่อมต่อ" เลือกระบบที่สร้างใหม่ กด "Discover Permissions" เพื่อดึง permissions จากฐานข้อมูลปลายทางมาใน UCM โดยอัตโนมัติ'],
                            ['icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', 'color' => 'emerald', 'title' => 'กำหนดสิทธิ์ผู้ใช้', 'desc' => 'ไปที่ "จัดการผู้ใช้" เลือกผู้ใช้ที่ต้องการ แล้ว assign permissions ของระบบใหม่ — UCM จะ sync ไปยังฐานข้อมูลปลายทางทันที'],
                            ['icon' => 'M13 10V3L4 14h7v7l9-11h-7z', 'color' => 'sky', 'title' => 'ทดสอบการเชื่อมต่อ', 'desc' => 'ในหน้าจัดการระบบ กด "ทดสอบการเชื่อมต่อ" เพื่อยืนยันว่า UCM สื่อสารกับฐานข้อมูลปลายทางได้สำเร็จ'],
                        ] as $item)
                        <div class="flex gap-3 p-3.5 bg-slate-50 rounded-xl border border-slate-100">
                            <div class="w-8 h-8 bg-{{ $item['color'] }}-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-{{ $item['color'] }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"/>
                                </svg>
                            </div>
                            <div>
                                <p class="font-semibold text-slate-900 text-xs mb-0.5">{{ $item['title'] }}</p>
                                <p class="text-xs text-slate-600">{{ $item['desc'] }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- แก้ไขและลบ --}}
                <div class="border-t border-slate-100 pt-5">
                    <h3 class="font-bold text-slate-900 mb-2">การแก้ไขและลบ Connector</h3>
                    <div class="space-y-2 text-sm text-slate-600">
                        <p><strong class="text-slate-800">แก้ไข:</strong> ไปที่หน้า Connector Wizard → กด "แก้ไข Connector" ข้าง Connector ที่ต้องการ → ปรับข้อมูลและกดบันทึก การแก้ไขจะมีผลกับ sync ครั้งถัดไปทันที</p>
                        <p><strong class="text-slate-800">ลบ:</strong> กด "ลบ" ในหน้ารายการ Connector — ระบบจะลบเฉพาะ Connector Config แต่ไม่ลบ System และข้อมูล Permissions ที่กำหนดไว้</p>
                    </div>
                </div>

                {{-- Security Note --}}
                <div class="flex items-start gap-3 p-3.5 bg-red-50 border border-red-200 rounded-xl text-xs text-red-800">
                    <svg class="w-4 h-4 text-red-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                    <div>
                        <strong class="block mb-0.5">ข้อควรระวังด้านความปลอดภัย</strong>
                        ควรใช้ DB User ที่มีสิทธิ์ขั้นต่ำที่จำเป็นเท่านั้น (เช่น SELECT บนตาราง users, INSERT/DELETE บนตาราง permissions) และหลีกเลี่ยงการใช้ root หรือ sa account ของฐานข้อมูลปลายทาง
                    </div>
                </div>

            </div>
        </div>

        {{-- ── Queue Monitor ── --}}
        <div id="queue-monitor" class="bg-white rounded-2xl shadow-sm ring-1 ring-slate-100 overflow-hidden">
            <div class="flex items-center gap-3 px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                <div class="w-8 h-8 bg-violet-100 rounded-xl flex items-center justify-center">
                    <svg class="w-4 h-4 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="font-bold text-slate-800">Queue Monitor</h2>
                    <p class="text-xs text-slate-400 mt-0.5">ตรวจสอบสถานะ Job และประวัติการ Sync</p>
                </div>
                <span class="ml-auto text-[10px] font-bold bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded-full">Admin L1 ขึ้นไป</span>
            </div>
            <div class="px-6 py-5 space-y-5 text-sm text-slate-700 leading-relaxed">

                <p>หน้า <strong>Queue Monitor</strong> ช่วยให้ Admin ตรวจสอบสถานะของ Sync Jobs และ Failed Jobs ได้แบบ Real-time เข้าถึงได้จากเมนู <strong>ผู้ดูแลระบบ → Queue Monitor</strong></p>

                {{-- Stat Cards --}}
                <div>
                    <h3 class="font-bold text-slate-900 mb-3">ข้อมูลที่แสดง</h3>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 text-xs">
                        @foreach ([
                            ['label' => 'Pending Jobs', 'color' => 'sky', 'desc' => 'Jobs ที่รอ Worker ประมวลผล'],
                            ['label' => 'Processing', 'color' => 'amber', 'desc' => 'Jobs ที่ Worker กำลังรันอยู่'],
                            ['label' => 'Failed Jobs', 'color' => 'red', 'desc' => 'Jobs ที่ล้มเหลวทั้งหมด (ทุก retry)'],
                            ['label' => 'Sync Success วันนี้', 'color' => 'emerald', 'desc' => 'จำนวน Sync ที่สำเร็จในวันนี้'],
                            ['label' => 'Sync Failed วันนี้', 'color' => 'rose', 'desc' => 'จำนวน Sync ที่ล้มเหลวในวันนี้'],
                            ['label' => 'Recent Activity', 'color' => 'violet', 'desc' => 'ประวัติ Sync 40 รายการล่าสุด พร้อม error message'],
                        ] as $item)
                        <div class="flex items-start gap-2 p-3 bg-slate-50 rounded-xl border border-slate-100">
                            <div class="w-2 h-2 rounded-full bg-{{ $item['color'] }}-500 mt-1 flex-shrink-0"></div>
                            <div>
                                <p class="font-semibold text-slate-800">{{ $item['label'] }}</p>
                                <p class="text-slate-500 mt-0.5">{{ $item['desc'] }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Failed Jobs Actions --}}
                <div class="border-t border-slate-100 pt-4">
                    <h3 class="font-bold text-slate-900 mb-3">การจัดการ Failed Jobs <span class="text-xs font-normal text-slate-400">(Admin ระดับ 2 เท่านั้น)</span></h3>
                    <div class="space-y-2 text-xs">
                        <div class="flex items-start gap-3 p-3 bg-indigo-50 rounded-xl border border-indigo-100">
                            <svg class="w-4 h-4 text-indigo-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            <div>
                                <p class="font-semibold text-indigo-800">Retry (ทีละ Job)</p>
                                <p class="text-slate-600 mt-0.5">กด <strong>Retry</strong> ที่แถว Failed Job ที่ต้องการ — ระบบจะส่ง Job กลับเข้า Queue ทันที เหมาะเมื่อแก้ไขปัญหาแล้วและต้องการลอง Sync ซ้ำ</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3 p-3 bg-amber-50 rounded-xl border border-amber-100">
                            <svg class="w-4 h-4 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            <div>
                                <p class="font-semibold text-amber-800">Retry All</p>
                                <p class="text-slate-600 mt-0.5">กด <strong>Retry ทั้งหมด</strong> เพื่อส่ง Failed Jobs ทั้งหมดกลับเข้า Queue พร้อมกัน ใช้เมื่อระบบปลายทาง Down ชั่วคราวแล้วกลับมาออนไลน์</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3 p-3 bg-slate-50 rounded-xl border border-slate-100">
                            <svg class="w-4 h-4 text-slate-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            <div>
                                <p class="font-semibold text-slate-700">ลบ Job (Forget / Flush)</p>
                                <p class="text-slate-600 mt-0.5">กด <strong>ลบ</strong> เพื่อลบ Failed Job ทีละรายการ หรือ <strong>ล้างทั้งหมด (Flush)</strong> เพื่อลบ Failed Jobs ทุกรายการออก — ใช้เมื่อ Job เก่าเกินไปหรือไม่สามารถ retry ได้แล้ว</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SyncLog detail --}}
                <div class="border-t border-slate-100 pt-4">
                    <h3 class="font-bold text-slate-900 mb-2">ประวัติ Sync (Recent Activity)</h3>
                    <p class="text-slate-600 text-xs mb-2">แสดง Sync Log 40 รายการล่าสุดพร้อมข้อมูล:</p>
                    <ul class="space-y-1 text-slate-600 text-xs list-disc list-inside">
                        <li>ผู้ใช้ที่ถูก Sync และระบบที่ Sync ไป</li>
                        <li>ผู้ที่สั่ง Sync (Performed By)</li>
                        <li>สถานะ (success / failed) และเวลาที่ทำ</li>
                        <li>Error message (กรณีล้มเหลว) เพื่อช่วยวินิจฉัยปัญหา</li>
                    </ul>
                </div>

                <div class="flex items-start gap-3 p-3.5 bg-blue-50 border border-blue-100 rounded-xl text-xs text-blue-800">
                    <svg class="w-4 h-4 text-blue-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                    <span>หาก Queue ค้างหรือ Worker หยุดทำงาน ให้รัน <code class="font-mono bg-blue-100 px-1 rounded">docker restart ucm-queue</code> (Docker) หรือ <code class="font-mono bg-blue-100 px-1 rounded">sudo supervisorctl restart ucm-queue:*</code> (Bare Server) เพื่อ restart Queue Worker</span>
                </div>
            </div>
        </div>

        {{-- ── Audit Log ── --}}
        <div id="audit-log" class="bg-white rounded-2xl shadow-sm ring-1 ring-slate-100 overflow-hidden">
            <div class="flex items-center gap-3 px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                <div class="w-8 h-8 bg-violet-100 rounded-xl flex items-center justify-center">
                    <svg class="w-4 h-4 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                    </svg>
                </div>
                <div>
                    <h2 class="font-bold text-slate-800">Audit Log</h2>
                    <p class="text-xs text-slate-400 mt-0.5">บันทึกการกระทำทั้งหมดในระบบ</p>
                </div>
                <span class="ml-auto text-[10px] font-bold bg-violet-100 text-violet-700 px-2 py-0.5 rounded-full">Admin / SQA / QA</span>
            </div>
            <div class="px-6 py-5 space-y-5 text-sm text-slate-700 leading-relaxed">

                <p>หน้า <strong>Audit Log</strong> รวบรวมบันทึกการกระทำทุกรายการในระบบ UCM ให้ผู้ดูแลและแผนกที่ได้รับสิทธิ์ตรวจสอบย้อนหลังได้ เข้าถึงได้จากเมนู <strong>Audit Log</strong> ในแถบนำทาง</p>

                {{-- สิทธิ์การเข้าถึง --}}
                <div>
                    <h3 class="font-bold text-slate-900 mb-3">สิทธิ์การเข้าถึง</h3>
                    <div class="grid sm:grid-cols-2 gap-3 text-xs">
                        @foreach ([
                            ['role' => 'Admin (ทุกระดับ)', 'color' => 'indigo', 'desc' => 'เข้าถึงและดูบันทึกทั้งหมดได้'],
                            ['role' => 'SQA / QA (Read-Only)', 'color' => 'violet', 'desc' => 'ดูได้เฉพาะ — ไม่สามารถแก้ไขหรือลบข้อมูลใดๆ ได้ กำหนดแผนกใน .env ด้วย UCM_AUDIT_DEPARTMENTS'],
                        ] as $item)
                        <div class="flex items-start gap-2 p-3 bg-slate-50 rounded-xl border border-slate-100">
                            <div class="w-2 h-2 rounded-full bg-{{ $item['color'] }}-500 mt-1 flex-shrink-0"></div>
                            <div>
                                <p class="font-semibold text-slate-800">{{ $item['role'] }}</p>
                                <p class="text-slate-500 mt-0.5">{{ $item['desc'] }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- ประเภทเหตุการณ์ --}}
                <div class="border-t border-slate-100 pt-4">
                    <h3 class="font-bold text-slate-900 mb-3">ประเภทเหตุการณ์ (Category)</h3>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 text-xs">
                        @foreach ([
                            ['label' => 'Auth', 'color' => 'sky', 'desc' => 'Login / Logout / Login Failed'],
                            ['label' => 'Users', 'color' => 'indigo', 'desc' => 'Import, อัปเดต, ลบผู้ใช้'],
                            ['label' => 'Permissions', 'color' => 'violet', 'desc' => 'เพิ่ม/ลบ/เปลี่ยน สิทธิ์ผู้ใช้'],
                            ['label' => 'Systems', 'color' => 'emerald', 'desc' => 'สร้าง/แก้ไข/ลบ ระบบและ Permission Definition'],
                            ['label' => 'Connectors', 'color' => 'amber', 'desc' => 'สร้าง/แก้ไข/ลบ Connector Wizard'],
                            ['label' => 'API', 'color' => 'rose', 'desc' => 'ออก Token / Revoke / User Login ผ่าน API'],
                        ] as $item)
                        <div class="flex items-start gap-2 p-3 bg-slate-50 rounded-xl border border-slate-100">
                            <div class="w-2 h-2 rounded-full bg-{{ $item['color'] }}-500 mt-1 flex-shrink-0"></div>
                            <div>
                                <p class="font-semibold text-slate-800">{{ $item['label'] }}</p>
                                <p class="text-slate-500 mt-0.5">{{ $item['desc'] }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- การกรองและค้นหา --}}
                <div class="border-t border-slate-100 pt-4">
                    <h3 class="font-bold text-slate-900 mb-3">การกรองและค้นหา</h3>
                    <div class="space-y-2 text-xs">
                        <div class="flex items-start gap-3 p-3 bg-indigo-50 rounded-xl border border-indigo-100">
                            <svg class="w-4 h-4 text-indigo-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a2 2 0 012-2z"/></svg>
                            <div>
                                <p class="font-semibold text-indigo-800">กรองตามประเภท</p>
                                <p class="text-slate-600 mt-0.5">คลิกแท็บ Auth / Users / Permissions / Systems / Connectors / API เพื่อกรองเฉพาะหมวดนั้น</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3 p-3 bg-slate-50 rounded-xl border border-slate-100">
                            <svg class="w-4 h-4 text-slate-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            <div>
                                <p class="font-semibold text-slate-700">ค้นหาข้อความ</p>
                                <p class="text-slate-600 mt-0.5">ค้นหาด้วย Username, คำอธิบาย หรือ Subject ของเหตุการณ์</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3 p-3 bg-slate-50 rounded-xl border border-slate-100">
                            <svg class="w-4 h-4 text-slate-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <div>
                                <p class="font-semibold text-slate-700">กรองตามวันที่ (Preset / DatePicker)</p>
                                <p class="text-slate-600 mt-0.5">
                                    เลือกจากปุ่ม Preset ได้แก่
                                    <strong>CD</strong> (วันนี้), <strong>PD</strong> (เมื่อวาน),
                                    <strong>CW</strong> (สัปดาห์นี้), <strong>PW</strong> (สัปดาห์ก่อน),
                                    <strong>CM</strong> (เดือนนี้), <strong>PM</strong> (เดือนก่อน) —
                                    หรือเลือกช่วงวันที่เองด้วย DatePicker
                                    <br class="my-1">
                                    <span class="text-slate-500">กดปุ่ม Preset ซ้ำเพื่อเลื่อนช่วงวันที่ไปข้างหน้า/ข้างหลังตามหน่วยของปุ่มนั้น</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ข้อมูลที่แสดง --}}
                <div class="border-t border-slate-100 pt-4">
                    <h3 class="font-bold text-slate-900 mb-3">ข้อมูลในแต่ละรายการ</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs border-collapse">
                            <thead>
                                <tr class="bg-slate-50">
                                    <th class="text-left px-3 py-2 font-semibold text-slate-600 rounded-tl-lg border border-slate-100">คอลัมน์</th>
                                    <th class="text-left px-3 py-2 font-semibold text-slate-600 rounded-tr-lg border border-slate-100">ความหมาย</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ([
                                    ['col' => 'ประเภท', 'desc' => 'หมวดหมู่เหตุการณ์ (Auth / Users / Permissions / Systems / Connectors / API)'],
                                    ['col' => 'เหตุการณ์', 'desc' => 'ชื่อ Event เช่น Login, Update Permissions, Create System'],
                                    ['col' => 'ผู้กระทำ', 'desc' => 'Username ของผู้ดำเนินการ (หรือ System หากเกิดจาก API)'],
                                    ['col' => 'เป้าหมาย', 'desc' => 'ข้อมูลที่ถูกกระทำ เช่น ชื่อผู้ใช้ ชื่อระบบ'],
                                    ['col' => 'รายละเอียด', 'desc' => 'คำอธิบายเหตุการณ์ พร้อม Metadata (JSON) ที่สามารถขยายดูได้'],
                                    ['col' => 'IP Address', 'desc' => 'IP ของผู้ดำเนินการ'],
                                    ['col' => 'เวลา', 'desc' => 'วันที่และเวลาที่เกิดเหตุการณ์'],
                                ] as $row)
                                <tr class="border border-slate-100">
                                    <td class="px-3 py-2 font-mono font-semibold text-slate-700 bg-slate-50/50 whitespace-nowrap">{{ $row['col'] }}</td>
                                    <td class="px-3 py-2 text-slate-600">{{ $row['desc'] }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="flex items-start gap-3 p-3.5 bg-amber-50 border border-amber-100 rounded-xl text-xs text-amber-800">
                    <svg class="w-4 h-4 text-amber-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                    <span>Audit Log เป็น <strong>Read-Only</strong> — ไม่มีการแก้ไขหรือลบข้อมูล ทุกรายการถูกบันทึกอย่างถาวรเพื่อวัตถุประสงค์ตรวจสอบ (Compliance)</span>
                </div>
            </div>
        </div>

        {{-- ── Notification Channels ── --}}
        <div id="notification-channels" class="bg-white rounded-2xl shadow-sm ring-1 ring-slate-100 overflow-hidden">
            <div class="flex items-center gap-3 px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                <div class="w-8 h-8 bg-emerald-100 rounded-xl flex items-center justify-center">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                </div>
                <h2 class="font-bold text-slate-800">Notification Channels</h2>
                <span class="ml-auto text-[10px] font-bold bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full">Admin L2 เท่านั้น</span>
            </div>
            <div class="px-6 py-5 space-y-5 text-sm text-slate-700 leading-relaxed">
                <p>ระบบ Notification ช่วยให้ Admin รับการแจ้งเตือนอัตโนมัติเมื่อเกิด event สำคัญในระบบ UCM เช่น การเปลี่ยนสิทธิ์ผู้ใช้ รองรับ 2 ช่องทาง:</p>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="p-4 bg-sky-50 rounded-xl border border-sky-100">
                        <div class="flex items-center gap-2 mb-2">
                            <div class="w-6 h-6 bg-sky-600 rounded-lg flex items-center justify-center">
                                <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            </div>
                            <span class="font-semibold text-sky-900 text-xs">Email</span>
                        </div>
                        <p class="text-xs text-sky-800">ส่งอีเมลหาผู้รับหลายคน (คั่นด้วย comma) พร้อม subject <code class="font-mono bg-sky-100 px-1 rounded">[UCM] {คำอธิบาย}</code> และรายละเอียด event ในเนื้อหา</p>
                    </div>
                    <div class="p-4 bg-violet-50 rounded-xl border border-violet-100">
                        <div class="flex items-center gap-2 mb-2">
                            <div class="w-6 h-6 bg-violet-600 rounded-lg flex items-center justify-center">
                                <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                            </div>
                            <span class="font-semibold text-violet-900 text-xs">Webhook</span>
                        </div>
                        <p class="text-xs text-violet-800">ส่ง HTTP POST พร้อม JSON payload ไปยัง URL ที่กำหนด รองรับ HMAC-SHA256 signature ผ่าน header <code class="font-mono bg-violet-100 px-1 rounded">X-UCM-Signature</code></p>
                    </div>
                </div>

                <div class="border-t border-slate-100 pt-4">
                    <h3 class="font-bold text-slate-900 mb-3">การสร้าง Notification Channel</h3>
                    <div class="space-y-2">
                        @foreach ([
                            'เมนู <strong>Notifications</strong> ใน Sidebar (หมวด Admin)',
                            'กด <strong>+ เพิ่ม Channel</strong> มุมขวาบน',
                            'กรอกชื่อ Channel และเลือกประเภท (Email / Webhook)',
                            'เลือก <strong>Events</strong> ที่ต้องการรับแจ้งเตือน (สามารถเลือก <code class="font-mono bg-slate-100 px-1 rounded text-xs">*</code> เพื่อรับทุก event)',
                            'กรอกข้อมูลการส่ง (อีเมลผู้รับ หรือ Webhook URL + Secret)',
                            'กด <strong>บันทึก</strong>',
                        ] as $i => $text)
                            <div class="flex items-start gap-3">
                                <span class="w-5 h-5 bg-emerald-100 text-emerald-700 text-xs font-bold rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">{{ $i+1 }}</span>
                                <p class="text-slate-700">{!! $text !!}</p>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="border-t border-slate-100 pt-4">
                    <h3 class="font-bold text-slate-900 mb-3">Events ที่รองรับ</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs border-collapse">
                            <thead>
                                <tr class="bg-slate-50">
                                    <th class="text-left px-3 py-2 font-semibold text-slate-600 border border-slate-100">Event Key</th>
                                    <th class="text-left px-3 py-2 font-semibold text-slate-600 border border-slate-100">เมื่อไหร่</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ([
                                    ['event' => 'permissions_updated',  'desc' => 'เมื่อมีการเปลี่ยนแปลงสิทธิ์ผู้ใช้ (ผ่านหน้า Manage Permissions)'],
                                    ['event' => 'user_imported',        'desc' => 'เมื่อนำเข้าผู้ใช้รายคนจาก Active Directory'],
                                    ['event' => 'user_bulk_imported',   'desc' => 'เมื่อนำเข้าผู้ใช้แบบ Bulk จาก Active Directory'],
                                    ['event' => 'user_removed',         'desc' => 'เมื่อลบผู้ใช้ออกจากระบบ UCM'],
                                    ['event' => 'admin_level_updated',  'desc' => 'เมื่อเปลี่ยนระดับ Admin ของผู้ใช้'],
                                    ['event' => 'system_created',       'desc' => 'เมื่อเพิ่มระบบที่เชื่อมต่อใหม่'],
                                    ['event' => 'system_updated',       'desc' => 'เมื่อแก้ไขข้อมูลระบบที่เชื่อมต่อ'],
                                    ['event' => 'system_deleted',       'desc' => 'เมื่อลบระบบที่เชื่อมต่อ'],
                                    ['event' => 'login_failed',         'desc' => 'เมื่อมีการ Login ล้มเหลว (รหัสผ่านผิด หรือแผนกไม่มีสิทธิ์)'],
                                    ['event' => '*',                    'desc' => 'Wildcard — รับแจ้งเตือนทุก event'],
                                ] as $row)
                                <tr class="border border-slate-100">
                                    <td class="px-3 py-2 font-mono font-semibold text-indigo-700 bg-slate-50/50 whitespace-nowrap">{{ $row['event'] }}</td>
                                    <td class="px-3 py-2 text-slate-600">{{ $row['desc'] }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="border-t border-slate-100 pt-4">
                    <h3 class="font-bold text-slate-900 mb-3">Webhook Payload Format</h3>
                    <div class="bg-slate-900 rounded-xl p-4 font-mono text-xs text-slate-300 overflow-x-auto">
<pre>{
  "event": "permissions_updated",
  "payload": {
    "user_id": 42,
    "username": "john.doe",
    "description": "อัปเดตสิทธิ์ john.doe",
    ...
  },
  "timestamp": "2026-03-22T10:00:00+07:00",
  "source": "UCM"
}</pre>
                    </div>
                    <p class="text-xs text-slate-500 mt-2">Header <code class="font-mono bg-slate-100 px-1 rounded">X-UCM-Signature</code> = HMAC-SHA256 ของ JSON body (มีเฉพาะเมื่อตั้งค่า Secret)</p>
                </div>
            </div>
        </div>

        {{-- ── Permission Matrix ── --}}
        <div id="permission-matrix" class="bg-white rounded-2xl shadow-sm ring-1 ring-slate-100 overflow-hidden">
            <div class="flex items-center gap-3 px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                <div class="w-8 h-8 bg-indigo-100 rounded-xl flex items-center justify-center">
                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18M10 3v18M14 3v18M3 6a3 3 0 013-3h12a3 3 0 013 3v12a3 3 0 01-3 3H6a3 3 0 01-3-3V6z"/>
                    </svg>
                </div>
                <h2 class="font-bold text-slate-800">Permission Matrix Report</h2>
                <span class="ml-auto text-xs font-semibold text-indigo-700 bg-indigo-100 px-2.5 py-1 rounded-full">Admin Only</span>
            </div>
            <div class="px-6 py-5 space-y-5 text-sm text-slate-700 leading-relaxed">
                <p>รายงาน Permission Matrix แสดงสิทธิ์ของผู้ใช้ทุกคนในรูปแบบตารางครอสแทบ — แถวคือ User, คอลัมน์คือ Permission แต่ละรายการของแต่ละระบบ ช่วยให้เห็น "ใครมีสิทธิ์อะไรในระบบไหน" ในมุมมองเดียว</p>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    @foreach ([
                        ['label' => 'Sticky Headers', 'desc' => 'หัวตาราง (ระบบ + สิทธิ์) และคอลัมน์ Username ยึดอยู่กับที่ขณะ Scroll', 'color' => 'indigo'],
                        ['label' => 'System Color Bands', 'desc' => 'แต่ละระบบมีสีแยกกัน ทำให้อ่านง่ายและไม่สับสน', 'color' => 'violet'],
                        ['label' => 'CSV Export', 'desc' => 'ส่งออกตารางทั้งหมดเป็นไฟล์ CSV (UTF-8 BOM) พร้อมใช้ใน Excel', 'color' => 'emerald'],
                    ] as $feat)
                        <div class="p-3 bg-{{ $feat['color'] }}-50 rounded-xl border border-{{ $feat['color'] }}-100">
                            <div class="font-semibold text-{{ $feat['color'] }}-900 text-xs mb-1">{{ $feat['label'] }}</div>
                            <p class="text-xs text-{{ $feat['color'] }}-800">{{ $feat['desc'] }}</p>
                        </div>
                    @endforeach
                </div>

                <div class="border-t border-slate-100 pt-4">
                    <h3 class="font-bold text-slate-900 mb-3">การใช้งาน</h3>
                    <div class="space-y-2">
                        @foreach ([
                            'เมนู <strong>Permission Matrix</strong> ใน Sidebar (หมวด Admin)',
                            'เลือก <strong>ระบบ</strong> ที่ต้องการดู (หรือเว้นว่างเพื่อดูทุกระบบ)',
                            'พิมพ์ในช่อง <strong>ค้นหา</strong> เพื่อกรองตามชื่อหรือ Username',
                            'ตารางแสดง ✓ ในช่องที่ผู้ใช้มีสิทธิ์ ส่วนช่องว่างหมายถึงไม่มีสิทธิ์',
                            'กด <strong>Export CSV</strong> เพื่อดาวน์โหลดตารางทั้งหมดเป็นไฟล์ CSV',
                        ] as $i => $text)
                            <div class="flex items-start gap-3">
                                <span class="w-5 h-5 bg-indigo-100 text-indigo-700 text-xs font-bold rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">{{ $i+1 }}</span>
                                <p class="text-slate-700">{!! $text !!}</p>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="border-t border-slate-100 pt-4">
                    <h3 class="font-bold text-slate-900 mb-3">คอลัมน์ในตาราง</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs border-collapse">
                            <thead>
                                <tr class="bg-slate-50">
                                    <th class="text-left px-3 py-2 font-semibold text-slate-600 border border-slate-100">คอลัมน์</th>
                                    <th class="text-left px-3 py-2 font-semibold text-slate-600 border border-slate-100">ความหมาย</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ([
                                    ['col' => 'Username', 'desc' => 'ชื่อผู้ใช้ใน AD'],
                                    ['col' => 'ชื่อ-นามสกุล', 'desc' => 'ชื่อเต็มของผู้ใช้'],
                                    ['col' => 'แผนก', 'desc' => 'แผนกที่สังกัด (จาก AD)'],
                                    ['col' => 'ตำแหน่ง', 'desc' => 'ตำแหน่งงาน (จาก AD)'],
                                    ['col' => '{ระบบ} — {สิทธิ์}', 'desc' => '✓ = มีสิทธิ์, ว่าง = ไม่มีสิทธิ์'],
                                    ['col' => 'Total', 'desc' => 'จำนวนสิทธิ์รวมทุกระบบของผู้ใช้คนนั้น'],
                                ] as $row)
                                <tr class="border border-slate-100">
                                    <td class="px-3 py-2 font-mono font-semibold text-slate-700 bg-slate-50/50 whitespace-nowrap">{{ $row['col'] }}</td>
                                    <td class="px-3 py-2 text-slate-600">{{ $row['desc'] }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="flex items-start gap-3 p-3.5 bg-sky-50 border border-sky-100 rounded-xl text-xs text-sky-800">
                    <svg class="w-4 h-4 text-sky-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>ตารางรองรับผู้ใช้จำนวนมาก (Pagination 50 คน/หน้า) — ใช้ตัวกรองระบบและค้นหาเพื่อจำกัดผลลัพธ์ก่อน Export</span>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- Smooth scroll highlight active TOC item --}}
<script>
(function () {
    var links = document.querySelectorAll('.toc-link');
    var ids   = Array.from(links).map(function (l) { return l.getAttribute('href').slice(1); });

    function setActive(active) {
        links.forEach(function (l) {
            var isCur = l.getAttribute('href') === '#' + active;
            l.className = 'toc-link flex items-center gap-2 px-3 py-2 rounded-xl text-sm transition-colors ' +
                (isCur ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-slate-500 hover:text-slate-800 hover:bg-slate-50');
            l.querySelector('span').className = 'w-1.5 h-1.5 rounded-full flex-shrink-0 ' +
                (isCur ? 'bg-indigo-500' : 'bg-slate-300');
        });
    }

    function onScroll() {
        // Use getBoundingClientRect so the position is always relative to the viewport,
        // not the offsetParent — works correctly inside flex/grid containers.
        var threshold = 140; // px from top of viewport to "activate" the section
        var active    = ids[0];
        ids.forEach(function (id) {
            var el = document.getElementById(id);
            if (el && el.getBoundingClientRect().top <= threshold) active = id;
        });
        setActive(active);
    }

    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
})();
</script>

@endsection
