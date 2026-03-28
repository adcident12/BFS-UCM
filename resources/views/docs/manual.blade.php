@extends('layouts.docs')
@section('doc-title', 'คู่มือผู้ใช้งาน')

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
    ['id' => 'master-tables',     'label' => '↳ Master Data Tables'],
    ['id' => 'twoway-sync',       'label' => '2-Way Sync'],
    ['id' => 'admin-levels',      'label' => 'สิทธิ์ Admin'],
    ['id' => 'ucm-access',        'label' => 'สิทธิ์ระบบ UCM'],
    ['id' => 'ad-check',          'label' => 'ตรวจสอบ AD'],
    ['id' => 'sync',              'label' => 'การ Sync สิทธิ์'],
    ['id' => 'connector-wizard',  'label' => 'Connector Wizard'],
    ['id' => 'queue-monitor',         'label' => 'Queue Monitor'],
    ['id' => 'audit-log',             'label' => 'Audit Log'],
    ['id' => 'notification-channels', 'label' => 'Notification Channels'],
    ['id' => 'permission-center',     'label' => 'Permission Center'],
    ['id' => 'permission-matrix',     'label' => 'Permission Matrix'],
    ['id' => 'share-links',           'label' => 'Share Links'],
    ['id' => 'permission-timeline',   'label' => 'Permission Timeline'],
    ['id' => 'inactive-users',        'label' => 'ผู้ใช้ไม่ได้ใช้งาน'],
    ['id' => 'health-check',          'label' => 'ทดสอบการเชื่อมต่อ'],
];
@endphp

@push('doc-page-toc')
@foreach($sections as $s)
<a href="#{{ $s['id'] }}" class="toc-item">{{ $s['label'] }}</a>
@endforeach
@endpush

@section('content')
<div class="space-y-6">

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
                    <div class="flex gap-3 p-3 bg-red-50 rounded-xl border border-red-100">
                        <div class="w-2 h-2 rounded-full bg-red-500 mt-1.5 flex-shrink-0"></div>
                        <div>
                            <div class="font-semibold text-slate-900 mb-0.5">Login ล้มเหลวล่าสุด</div>
                            <p class="text-slate-600 text-xs">Widget แสดง 10 รายการ Login ล้มเหลวล่าสุด พร้อม Username, สาเหตุ (รหัสผ่านผิด / แผนกไม่มีสิทธิ์), IP Address และเวลา — ปรากฏเฉพาะเมื่อมีข้อมูล (เฉพาะ Admin)</p>
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
                    <li>ปุ่ม <strong>ทดสอบ</strong> — ทดสอบการเชื่อมต่อระบบปลายทางทันที (เฉพาะ Admin ระดับ 1 ขึ้นไป)</li>
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
                <p>UCM รองรับการจัดการ <strong>Master Data</strong> ของระบบปลายทางโดยตรง — เพิ่ม/แก้ไข/ลบข้อมูลใน UI โดยไม่ต้องเปิด DB โดยตรง มี 2 รูปแบบ:</p>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                    {{-- Custom Adapter --}}
                    <div class="p-3.5 bg-indigo-50 rounded-xl border border-indigo-100 text-xs space-y-2">
                        <div class="font-bold text-indigo-900 flex items-center gap-2">
                            <span class="px-2 py-0.5 bg-indigo-200 text-indigo-800 rounded-md text-[10px]">Custom Adapter</span>
                            Earth / EFiling
                        </div>
                        <ul class="space-y-1 text-indigo-700">
                            <li><span class="font-semibold">Earth</span> — PageGroup (<code class="font-mono bg-indigo-100 px-1 rounded">UserMgnt_PageGroup</code>)</li>
                            <li><span class="font-semibold">EFiling</span> — Department, Document Category</li>
                            <li class="text-indigo-500">ตั้งค่าถาวรในโค้ด Adapter ไม่ปรับได้จาก UI</li>
                        </ul>
                    </div>
                    {{-- Dynamic Adapter --}}
                    <div class="p-3.5 bg-cyan-50 rounded-xl border border-cyan-200 text-xs space-y-2">
                        <div class="font-bold text-cyan-900 flex items-center gap-2">
                            <span class="px-2 py-0.5 bg-cyan-200 text-cyan-800 rounded-md text-[10px]">Dynamic Adapter</span>
                            No-Code Connector
                        </div>
                        <ul class="space-y-1 text-cyan-800">
                            <li>กำหนดตาราง Master Data ได้หลายตาราง ผ่าน <strong>Connector Wizard Step 6</strong></li>
                            <li>ตั้งค่าได้เอง: label, PK, label column, extra fields</li>
                            <li>เลือก Delete Mode <strong>Hard</strong> หรือ <strong>Soft</strong> แต่ละตาราง</li>
                            <li class="text-cyan-600">→ ดูรายละเอียดที่ <a href="#master-tables" class="underline">Master Data Tables</a></li>
                        </ul>
                    </div>
                </div>

                <div class="flex items-start gap-3 p-3.5 bg-red-50 border border-red-200 rounded-xl text-xs text-red-800">
                    <svg class="w-4 h-4 text-red-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                    <div>
                        <strong>ข้อมูลเหล่านี้เขียนตรงไปยังฐานข้อมูลของระบบปลายทาง (Direct Write)</strong> — การเพิ่ม/แก้ไข/ลบมีผลทันที ไม่มีขั้นตอน Sync Queue
                        <br class="mt-1"><strong>⚠️ Earth — Rename PageGroup:</strong> การเปลี่ยนชื่อ PageGroup จะอัปเดตเฉพาะใน DB Earth เท่านั้น Permission ใน UCM ที่อ้างอิงชื่อเดิมจะ Out of Sync — ควร Discover Permissions ใหม่หลัง Rename
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
                        ['ลบรายการ', 'Admin ระดับ 2 เท่านั้น', 'bg-amber-50 text-amber-700', 'มีกล่องยืนยัน — ข้อความต่างกัน Hard/Soft'],
                        ['Discover (รีเฟรชข้อมูล)', 'Admin ระดับ 1 ขึ้นไป', 'bg-cyan-50 text-cyan-700', 'ปุ่ม Discover ใน card header'],
                    ] as [$action, $level, $cls, $note])
                    <div class="grid grid-cols-3 px-3 py-2 border-b border-slate-100 last:border-0">
                        <div class="font-medium text-slate-700">{{ $action }}</div>
                        <div class="{{ $cls }} font-semibold px-2 py-0.5 rounded-md w-fit text-[11px]">{{ $level }}</div>
                        <div class="text-slate-400">{{ $note }}</div>
                    </div>
                    @endforeach
                </div>

                {{-- Delete Mode Badges --}}
                <div class="border-t border-slate-100 pt-4">
                    <h3 class="font-bold text-slate-900 mb-3">Badge Delete Mode</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                        <div class="flex items-start gap-3 p-3 bg-red-50 rounded-xl border border-red-200">
                            <span class="text-[10px] font-bold text-red-500 bg-red-100 border border-red-300 px-2 py-1 rounded flex-shrink-0">Hard</span>
                            <div class="text-red-800">
                                <p class="font-semibold mb-0.5">Hard Delete</p>
                                <p>ลบข้อมูลถาวรออกจาก DB (<code class="font-mono bg-white px-1 rounded border border-red-200">DELETE FROM table</code>) — <strong>ไม่สามารถกู้คืนได้</strong> กล่องยืนยันจะแจ้งเตือน</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3 p-3 bg-amber-50 rounded-xl border border-amber-200">
                            <span class="text-[10px] font-bold text-amber-600 bg-amber-100 border border-amber-300 px-2 py-1 rounded flex-shrink-0">Soft</span>
                            <div class="text-amber-800">
                                <p class="font-semibold mb-0.5">Soft Delete</p>
                                <p>อัปเดต Flag ในคอลัมน์ที่กำหนด (<code class="font-mono bg-white px-1 rounded border border-amber-200">UPDATE SET is_deleted = 1</code>) — ข้อมูลยังอยู่ใน DB แต่ UCM จะไม่แสดง สามารถกู้คืนได้โดยตรงใน DB</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <h3 class="font-bold text-slate-900 mb-3">วิธีเข้าถึง Managed Groups</h3>
                    <div class="space-y-2">
                        @foreach ([
                            'เข้าหน้า <strong>ระบบที่เชื่อมต่อ</strong> แล้วคลิกระบบที่ต้องการ (เช่น EFiling หรือระบบที่สร้างด้วย No-Code Connector)',
                            'เลื่อนลงไปส่วน <strong>ข้อมูล Reference</strong> ที่อยู่ด้านล่างของหน้ารายละเอียดระบบ',
                            'แต่ละ Card คือหนึ่ง Master Table — ดู Badge <strong class="text-red-600">Hard</strong>/<strong class="text-amber-600">Soft</strong> ที่มุมขวาบน Card เพื่อทราบ Delete Mode',
                            'กด <strong class="text-cyan-600">Discover</strong> เพื่อโหลดข้อมูลล่าสุดจาก DB ปลายทาง (ข้อมูลจะ reload ใน Card ทันที)',
                            'จะเห็นรายการทั้งหมดจากระบบปลายทาง พร้อมปุ่มแก้ไขและลบแต่ละรายการ',
                        ] as $i => $text)
                            <div class="flex items-start gap-3">
                                <span class="w-5 h-5 bg-purple-100 text-purple-700 text-xs font-bold rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">{{ $i+1 }}</span>
                                <p class="text-sm">{!! $text !!}</p>
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
                                <p class="text-emerald-700 text-xs">กรอกชื่อ (และ extra fields ที่กำหนดไว้) แล้วกดปุ่ม <strong>+</strong></p>
                            </div>
                        </div>
                        <div class="flex gap-3 p-3 bg-sky-50 rounded-xl border border-sky-100">
                            <div class="w-7 h-7 bg-sky-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-3.5 h-3.5 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </div>
                            <div>
                                <div class="font-semibold text-sky-900 text-xs mb-0.5">แก้ไขรายการ <span class="ml-1 text-[10px] font-bold text-amber-600 bg-amber-50 px-1.5 py-0.5 rounded ring-1 ring-amber-200/70">Admin L2</span></div>
                                <p class="text-sky-700 text-xs">กดปุ่มดินสอ ✏️ ที่รายการ แก้ไขชื่อ/extra fields แล้วกด บันทึก</p>
                            </div>
                        </div>
                        <div class="flex gap-3 p-3 bg-red-50 rounded-xl border border-red-100">
                            <div class="w-7 h-7 bg-red-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-3.5 h-3.5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </div>
                            <div>
                                <div class="font-semibold text-red-900 text-xs mb-0.5">ลบรายการ <span class="ml-1 text-[10px] font-bold text-amber-600 bg-amber-50 px-1.5 py-0.5 rounded ring-1 ring-amber-200/70">Admin L2</span></div>
                                <p class="text-red-700 text-xs">กดปุ่มถังขยะ 🗑️ กล่องยืนยันบอก Hard/Soft ก่อนดำเนินการ</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Master Data Tables (No-Code) ── --}}
        <div id="master-tables" class="bg-white rounded-2xl shadow-sm ring-1 ring-slate-100 overflow-hidden">
            <div class="flex items-center gap-3 px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                <div class="w-8 h-8 bg-cyan-100 rounded-xl flex items-center justify-center">
                    <svg class="w-4 h-4 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
                    </svg>
                </div>
                <div>
                    <h2 class="font-bold text-slate-800">Master Data Tables <span class="text-xs font-normal text-slate-400 ml-1">(No-Code Connector)</span></h2>
                    <p class="text-xs text-slate-400 mt-0.5">ฟีเจอร์สำหรับระบบที่สร้างด้วย Connector Wizard — ไม่ต้องเขียนโค้ด Adapter เพิ่ม</p>
                </div>
            </div>
            <div class="px-6 py-5 space-y-5 text-sm text-slate-700 leading-relaxed">

                <p>No-Code Connector (Dynamic Adapter) สามารถกำหนด <strong>ตาราง Reference Data</strong> ที่ต้องการให้ UCM จัดการ CRUD ได้โดยตรง ตั้งค่าได้ผ่าน <strong>Step 6 ใน Connector Wizard</strong> — รองรับหลายตาราง แต่ละตารางแยกอิสระกัน</p>

                <div class="flex items-start gap-3 p-3.5 bg-blue-50 border border-blue-200 rounded-xl text-xs text-blue-800">
                    <svg class="w-4 h-4 text-blue-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <p>ขั้นตอนนี้เป็น <strong>ไม่บังคับ (Optional)</strong> — ระบบที่ไม่มี Reference Table สามารถข้าม Step 6 ได้เลย</p>
                </div>

                {{-- Config fields --}}
                <div>
                    <h3 class="font-bold text-slate-900 mb-3">การตั้งค่าแต่ละตาราง (Step 6)</h3>
                    <div class="overflow-hidden rounded-xl border border-slate-200 text-xs">
                        <div class="grid grid-cols-3 bg-slate-50 font-bold text-slate-500 px-3 py-2 border-b border-slate-200">
                            <div>ฟิลด์</div><div>คำอธิบาย</div><div>ตัวอย่าง</div>
                        </div>
                        @foreach ([
                            ['ชื่อที่แสดงใน UCM', 'Label ที่แสดงเป็นหัว Card ใน UI', 'Department'],
                            ['ตาราง', 'ชื่อตารางใน DB ปลายทาง', 'departments'],
                            ['Primary Key Column', 'คอลัมน์ PK ของตาราง', 'id'],
                            ['Label Column', 'คอลัมน์ที่แสดงเป็นชื่อรายการ', 'name'],
                            ['Extra Columns', 'คอลัมน์เพิ่มเติม (ไม่บังคับ) พร้อม Label, Type, Required', 'code (text), sort_order (number)'],
                            ['Delete Mode', 'Hard = ลบถาวร, Soft = set flag', 'soft'],
                            ['Soft Delete Column', 'คอลัมน์สำหรับ flag (เฉพาะ Soft)', 'is_deleted'],
                            ['Soft Delete Value', 'ค่าที่หมายถึง "ถูกลบ"', '1'],
                        ] as [$f, $d, $ex])
                        <div class="grid grid-cols-3 px-3 py-2.5 border-b border-slate-100 last:border-0">
                            <div class="font-semibold text-slate-700">{{ $f }}</div>
                            <div class="text-slate-500">{{ $d }}</div>
                            <div class="font-mono text-indigo-700 bg-indigo-50 px-1.5 py-0.5 rounded w-fit text-[11px]">{{ $ex }}</div>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Delete Mode detail --}}
                <div>
                    <h3 class="font-bold text-slate-900 mb-3">Delete Mode — Hard vs Soft</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                        <div class="rounded-xl border border-red-200 overflow-hidden">
                            <div class="flex items-center gap-2 px-3 py-2 bg-red-50 border-b border-red-200">
                                <span class="text-[10px] font-bold text-red-500 bg-white border border-red-300 px-2 py-0.5 rounded">Hard Delete</span>
                                <span class="text-red-700 font-semibold">ลบถาวร</span>
                            </div>
                            <div class="p-3 bg-white space-y-1.5 text-slate-600">
                                <p>✅ เหมาะกับข้อมูลที่ไม่มี FK อ้างอิง หรือต้องการล้างข้อมูลจริง</p>
                                <p>⚠️ ไม่สามารถกู้คืนผ่าน UCM ได้ ต้องกู้จาก DB backup โดยตรง</p>
                                <div class="mt-2 p-2 bg-slate-50 rounded border border-slate-200">
                                    <p class="font-mono text-[11px] text-slate-700">DELETE FROM departments WHERE id = ?</p>
                                </div>
                            </div>
                        </div>
                        <div class="rounded-xl border border-amber-200 overflow-hidden">
                            <div class="flex items-center gap-2 px-3 py-2 bg-amber-50 border-b border-amber-200">
                                <span class="text-[10px] font-bold text-amber-600 bg-white border border-amber-300 px-2 py-0.5 rounded">Soft Delete</span>
                                <span class="text-amber-700 font-semibold">ซ่อนข้อมูล</span>
                            </div>
                            <div class="p-3 bg-white space-y-1.5 text-slate-600">
                                <p>✅ ข้อมูลยังอยู่ใน DB — UCM กรองออกจากการแสดงผล</p>
                                <p>✅ เหมาะกับข้อมูลที่มี FK อ้างอิงจากตารางอื่น</p>
                                <div class="mt-2 p-2 bg-slate-50 rounded border border-slate-200">
                                    <p class="font-mono text-[11px] text-slate-700">UPDATE departments SET is_deleted = 1 WHERE id = ?</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Discover button --}}
                <div class="border-t border-slate-100 pt-4">
                    <h3 class="font-bold text-slate-900 mb-3">ปุ่ม Discover</h3>
                    <p class="text-xs text-slate-600 mb-3">แต่ละ Master Table Card มีปุ่ม <strong class="text-cyan-600">Discover</strong> แยกต่างหาก (ต่างจากปุ่ม "Discover Permissions" ที่ header ของหน้า)</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 space-y-1.5">
                            <p class="font-semibold text-slate-800">Discover (ใน card หัว Master Table)</p>
                            <ul class="space-y-1 text-slate-600 list-disc list-inside">
                                <li>โหลดข้อมูลล่าสุดจาก DB ปลายทาง ของตารางนั้นๆ</li>
                                <li>แสดงจำนวนรายการที่พบ (Log ไว้ใน Audit)</li>
                                <li>ข้อมูลใน Card จะ refresh ทันทีหลังกด Reload</li>
                            </ul>
                        </div>
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 space-y-1.5">
                            <p class="font-semibold text-slate-800">Discover Permissions (header ของหน้า System)</p>
                            <ul class="space-y-1 text-slate-600 list-disc list-inside">
                                <li>ดึง permission values ใหม่จากตาราง Permission ของระบบ</li>
                                <li>สร้าง Permission ใน UCM อัตโนมัติ</li>
                                <li>ไม่เกี่ยวกับ Master Data</li>
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- DB user permissions required --}}
                <div class="flex items-start gap-3 p-3.5 bg-amber-50 border border-amber-200 rounded-xl text-xs text-amber-800">
                    <svg class="w-4 h-4 text-amber-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                    <div>
                        <strong class="block mb-1">สิทธิ์ DB User ที่จำเป็น</strong>
                        DB User ที่กำหนดใน Connector ต้องมีสิทธิ์ <code class="font-mono bg-white px-1 rounded border border-amber-300">SELECT</code>, <code class="font-mono bg-white px-1 rounded border border-amber-300">INSERT</code>, <code class="font-mono bg-white px-1 rounded border border-amber-300">UPDATE</code> บนตาราง Master Table ที่กำหนดไว้ สำหรับ Hard Delete ต้องมีสิทธิ์ <code class="font-mono bg-white px-1 rounded border border-amber-300">DELETE</code> ด้วย
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
                            ['Earth', 'ใช่', 'Hard Delete', 'สร้าง PageGroup เมื่อเพิ่ม / ลบ PageGroup เมื่อลบ Permission สุดท้ายของ group (จัดการ PageGroup ได้ผ่าน Managed Groups ด้วย)', 'bg-emerald-50 border-emerald-200 text-emerald-800'],
                            ['EFiling', 'ใช่', 'Hard Delete', 'สร้าง Department/Document Category เมื่อเพิ่ม / ลบเมื่อลบ (จัดการผ่าน Managed Groups ด้วย)', 'bg-emerald-50 border-emerald-200 text-emerald-800'],
                            ['DynamicAdapter (Connector Wizard)', 'ทางเลือก', 'ตาม Wizard', 'เปิดใช้ได้ใน Step 5 ของ Wizard — รองรับ Hard Delete, Soft Delete และ Detach Only ตามที่ตั้งค่า', 'bg-orange-50 border-orange-200 text-orange-800'],
                            ['Repair System', 'ไม่', '—', 'ไม่มี permission definition table — Toggle จะไม่ปรากฏ', 'bg-slate-50 border-slate-200 text-slate-600'],
                            ['ระบบไม่มี Adapter', 'ไม่', '—', 'บันทึกใน UCM เท่านั้น', 'bg-slate-50 border-slate-200 text-slate-600'],
                        ] as [$sys, $supported, $deleteMode, $desc, $cls])
                        <div class="flex items-start gap-3 p-3 rounded-xl border {{ $cls }}">
                            <div class="w-40 font-bold flex-shrink-0">{{ $sys }}</div>
                            <div class="w-16 flex-shrink-0">{{ $supported === 'ใช่' || $supported === 'ทางเลือก' ? '✅ ' . $supported : '—' }}</div>
                            <div class="w-24 flex-shrink-0 font-mono text-[10px]">{{ $deleteMode }}</div>
                            <div>{{ $desc }}</div>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Delete Mode --}}
                <div class="border-t border-slate-100 pt-4">
                    <h3 class="font-bold text-slate-900 mb-3">Permission Delete Mode</h3>
                    <p class="text-xs text-slate-600 mb-3">กำหนดพฤติกรรมเมื่อ Admin ลบ Permission Definition ใน UCM (เฉพาะระบบที่เปิด 2-Way ON)</p>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                        <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold bg-slate-200 text-slate-700">Detach Only</span>
                                <span class="text-[10px] text-slate-400 font-medium">ค่าเริ่มต้น</span>
                            </div>
                            <p class="text-slate-600 leading-relaxed">ลบออกจาก UCM เท่านั้น — ข้อมูลในฐานข้อมูลปลายทางไม่ถูกแตะต้อง</p>
                        </div>
                        <div class="p-3.5 bg-red-50 rounded-xl border border-red-200">
                            <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold bg-red-100 text-red-700 mb-2">Hard Delete</span>
                            <p class="text-red-800 leading-relaxed">ลบแถวออกจากตาราง Permission Definition ในฐานข้อมูลปลายทางถาวร</p>
                        </div>
                        <div class="p-3.5 bg-amber-50 rounded-xl border border-amber-200">
                            <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-700 mb-2">Soft Delete</span>
                            <p class="text-amber-800 leading-relaxed">อัปเดตคอลัมน์ที่ระบุ (เช่น <code class="font-mono bg-white px-0.5 rounded">is_active = 0</code>) แทนการลบจริง</p>
                        </div>
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
                            <li>✅ ดู <strong>Audit Log</strong> ได้ (อ่านอย่างเดียว)</li>
                            <li>✅ ดู <strong>Permission Matrix</strong> ได้ (อ่านอย่างเดียว)</li>
                            <li>✅ <strong>Queue Monitor</strong> (ดูและจัดการ Failed Jobs) ได้ <span class="text-indigo-500">(ค่าเริ่มต้น)</span></li>
                            <li>❌ แก้ไข / ลบ Reference Data ไม่ได้</li>
                            <li>❌ Toggle 2-way ไม่ได้</li>
                            <li>❌ Connector Wizard / Notifications ไม่ได้ <span class="text-indigo-500">(ค่าเริ่มต้น)</span></li>
                            <li>❌ จัดการสิทธิ์ Admin / UCM Access ไม่ได้</li>
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
                            <li>✅ <strong>Connector Wizard</strong> (เชื่อมต่อฐานข้อมูลภายนอก) ได้</li>
                            <li>✅ <strong>ปรับสิทธิ์ระบบ UCM</strong> (UCM Access Control) ได้</li>
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
                    <span>ระดับสิทธิ์ที่แสดงในตารางนี้คือ <strong>ค่าเริ่มต้น</strong> — Admin L2 สามารถปรับค่าขั้นต่ำของแต่ละฟีเจอร์ได้ผ่านหน้า <strong>สิทธิ์ระบบ UCM</strong> (ดูหัวข้อถัดไป) รวมถึงมอบสิทธิ์พิเศษให้ผู้ใช้รายบุคคลโดยไม่ต้องเลื่อนระดับ Admin</span>
                </div>
            </div>
        </div>

        {{-- ── สิทธิ์ระบบ UCM ── --}}
        <div id="ucm-access" class="bg-white rounded-2xl shadow-sm ring-1 ring-slate-100 overflow-hidden">
            <div class="flex items-center gap-3 px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                <div class="w-8 h-8 bg-indigo-100 rounded-xl flex items-center justify-center">
                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="font-bold text-slate-800">สิทธิ์ระบบ UCM (UCM Access Control)</h2>
                    <p class="text-xs text-slate-400 mt-0.5">ควบคุมการเข้าถึงฟีเจอร์ภายใน UCM แบบ Fine-Grained</p>
                </div>
                <span class="ml-auto text-[10px] font-bold bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full">Admin L2 เท่านั้น</span>
            </div>
            <div class="px-6 py-5 space-y-5 text-sm text-slate-700 leading-relaxed">

                <p>ระบบ <strong>UCM Access Control</strong> ช่วยให้ผู้ดูแลระดับ 2 กำหนดว่าผู้ใช้แต่ละระดับ (L0/L1/L2) สามารถเข้าถึงฟีเจอร์ใดของ UCM ได้บ้าง และสามารถมอบสิทธิ์พิเศษให้ผู้ใช้รายบุคคลได้โดยไม่ต้องเลื่อนระดับ Admin</p>

                <div class="grid sm:grid-cols-2 gap-3 text-xs">
                    <div class="p-4 bg-indigo-50 rounded-xl border border-indigo-100">
                        <p class="font-semibold text-indigo-900 mb-1">ปรับ Min Level ต่อ Feature</p>
                        <p class="text-indigo-800">เปลี่ยน Level ขั้นต่ำที่ต้องการสำหรับแต่ละฟีเจอร์ เช่น เปิดให้ L1 เข้า Queue Monitor หรือล็อก Connector Wizard ไว้ที่ L2 เท่านั้น</p>
                    </div>
                    <div class="p-4 bg-violet-50 rounded-xl border border-violet-100">
                        <p class="font-semibold text-violet-900 mb-1">Individual Grant</p>
                        <p class="text-violet-800">มอบสิทธิ์เฉพาะฟีเจอร์ให้ผู้ใช้รายบุคคล โดยไม่ต้องเพิ่ม Admin Level — เหมาะสำหรับการเปิดสิทธิ์ชั่วคราวหรือตามหน้าที่</p>
                    </div>
                </div>

                {{-- ฟีเจอร์ที่ควบคุมได้ --}}
                <div class="border-t border-slate-100 pt-4">
                    <h3 class="font-bold text-slate-900 mb-3">ฟีเจอร์ที่ควบคุมได้</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs border-collapse">
                            <thead>
                                <tr class="bg-slate-50">
                                    <th class="text-left px-3 py-2 font-semibold text-slate-600 border border-slate-100">Feature Key</th>
                                    <th class="text-left px-3 py-2 font-semibold text-slate-600 border border-slate-100">ชื่อ</th>
                                    <th class="text-left px-3 py-2 font-semibold text-slate-600 border border-slate-100">Default Level</th>
                                    <th class="text-left px-3 py-2 font-semibold text-slate-600 border border-slate-100">หมวด</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ([
                                    ['key' => 'dashboard',           'name' => 'Dashboard',                       'level' => 'L0', 'group' => 'ทั่วไป'],
                                    ['key' => 'user_list',           'name' => 'ดูรายชื่อผู้ใช้',                  'level' => 'L0', 'group' => 'ทั่วไป'],
                                    ['key' => 'user_detail',         'name' => 'ดูรายละเอียดผู้ใช้',              'level' => 'L0', 'group' => 'ทั่วไป'],
                                    ['key' => 'system_list',         'name' => 'ดูรายชื่อระบบที่เชื่อมต่อ',       'level' => 'L0', 'group' => 'ทั่วไป'],
                                    ['key' => 'user_import_ldap',    'name' => 'Import ผู้ใช้จาก LDAP/AD',        'level' => 'L1', 'group' => 'ผู้ใช้'],
                                    ['key' => 'user_info_edit',      'name' => 'แก้ไขข้อมูลผู้ใช้',               'level' => 'L2', 'group' => 'ผู้ใช้'],
                                    ['key' => 'user_remove',         'name' => 'ลบ/ถอนผู้ใช้ออกจากระบบ',          'level' => 'L2', 'group' => 'ผู้ใช้'],
                                    ['key' => 'user_inactive_report','name' => 'รายงาน Inactive Users',            'level' => 'L1', 'group' => 'ผู้ใช้'],
                                    ['key' => 'permission_timeline', 'name' => 'Permission Timeline ของผู้ใช้',    'level' => 'L1', 'group' => 'ผู้ใช้'],
                                    ['key' => 'permission_update',   'name' => 'แก้ไขสิทธิ์ผู้ใช้ในระบบ',         'level' => 'L1', 'group' => 'สิทธิ์'],
                                    ['key' => 'permission_matrix',   'name' => 'Permission Matrix Report',         'level' => 'L1', 'group' => 'สิทธิ์'],
                                    ['key' => 'permission_center',   'name' => 'จัดการ Permissions (Permission Center)', 'level' => 'L1', 'group' => 'สิทธิ์'],
                                    ['key' => 'audit_log',           'name' => 'Audit Log',                        'level' => 'L1', 'group' => 'รายงาน'],
                                    ['key' => 'admin_levels',        'name' => 'จัดการสิทธิ์ Admin',               'level' => 'L2', 'group' => 'ผู้ดูแลระบบ'],
                                    ['key' => 'ucm_access',          'name' => 'จัดการสิทธิ์ระบบ UCM',             'level' => 'L2', 'group' => 'ผู้ดูแลระบบ'],
                                    ['key' => 'queue_monitor',       'name' => 'Queue Monitor',                    'level' => 'L1', 'group' => 'ผู้ดูแลระบบ'],
                                    ['key' => 'connector_wizard',    'name' => 'Connector Wizard',                 'level' => 'L2', 'group' => 'ผู้ดูแลระบบ'],
                                    ['key' => 'notifications',       'name' => 'Notification Channels',            'level' => 'L2', 'group' => 'ผู้ดูแลระบบ'],
                                    ['key' => 'system_create_edit',  'name' => 'เพิ่ม/แก้ไขระบบที่เชื่อมต่อ',     'level' => 'L2', 'group' => 'ผู้ดูแลระบบ'],
                                ] as $row)
                                <tr class="border border-slate-100">
                                    <td class="px-3 py-2 font-mono font-semibold text-indigo-700 bg-slate-50/50 whitespace-nowrap">{{ $row['key'] }}</td>
                                    <td class="px-3 py-2 text-slate-700">{{ $row['name'] }}</td>
                                    <td class="px-3 py-2 whitespace-nowrap">
                                        <span class="text-[10px] font-bold px-1.5 py-0.5 rounded
                                            {{ match($row['level']) {
                                                'L0' => 'bg-emerald-100 text-emerald-700',
                                                'L1' => 'bg-indigo-100 text-indigo-700',
                                                'L2' => 'bg-amber-100 text-amber-700',
                                                default => 'bg-slate-100 text-slate-600',
                                            } }}">{{ $row['level'] }}</span>
                                    </td>
                                    <td class="px-3 py-2 text-slate-500">{{ $row['group'] }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <p class="text-xs text-slate-500 mt-2">ฟีเจอร์ที่ <code class="font-mono bg-slate-100 px-1 rounded">lockable = false</code> ไม่สามารถลด Level ลงต่ำกว่าค่าเดิมได้ (เพื่อป้องกันการล็อคตัวเอง)</p>
                </div>

                {{-- วิธีใช้งาน --}}
                <div class="border-t border-slate-100 pt-4">
                    <h3 class="font-bold text-slate-900 mb-3">วิธีใช้งาน</h3>
                    <div class="space-y-2">
                        @foreach ([
                            'เข้าเมนู <strong>ผู้ดูแลระบบ → สิทธิ์ระบบ UCM</strong> ในแถบนำทางด้านซ้าย (มองเห็นเฉพาะ Admin ระดับ 2)',
                            'หน้าจะแสดงรายการฟีเจอร์ทั้งหมดพร้อม Level ขั้นต่ำปัจจุบัน และรายชื่อ Individual Grants',
                            'กดปุ่ม <strong>L0</strong>, <strong>L1</strong> หรือ <strong>L2</strong> ที่แถวฟีเจอร์เพื่อเปลี่ยน Min Level ทันที',
                            'กด <strong>+ ให้สิทธิ์พิเศษ</strong> แล้วค้นหาผู้ใช้ที่ต้องการมอบ Individual Grant',
                            'กด <strong>ถอน</strong> ที่ Individual Grant เพื่อลบสิทธิ์พิเศษของผู้ใช้คนนั้น',
                        ] as $i => $text)
                            <div class="flex items-start gap-3">
                                <span class="w-5 h-5 bg-indigo-100 text-indigo-700 text-xs font-bold rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">{{ $i+1 }}</span>
                                <p>{!! $text !!}</p>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="flex items-start gap-3 p-3.5 bg-blue-50 border border-blue-100 rounded-xl text-xs text-blue-800">
                    <svg class="w-4 h-4 text-blue-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                    <span>การเปลี่ยน Level จะมีผลทันทีสำหรับ request ถัดไป — ค่าถูก Cache ไว้ 5 นาที การเปลี่ยน Level จะล้าง Cache ให้อัตโนมัติ</span>
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

                {{-- TOC ภายใน --}}
                <div class="flex flex-wrap gap-2 text-[11px]">
                    @foreach ([
                        ['href' => '#wiz-intro',        'label' => 'คืออะไร'],
                        ['href' => '#wiz-prereq',       'label' => 'ก่อนเริ่ม'],
                        ['href' => '#wiz-analyze',      'label' => 'วิเคราะห์อัตโนมัติ (AI)'],
                        ['href' => '#wiz-steps',        'label' => '7 ขั้นตอน'],
                        ['href' => '#wiz-multi-table',  'label' => 'Multi-Table JOIN'],
                        ['href' => '#wiz-perm-mode',         'label' => 'เลือก Permission Mode'],
                        ['href' => '#wiz-mixed-mode',        'label' => 'Mixed Mode'],
                        ['href' => '#wiz-boolean-matrix',    'label' => 'Boolean Matrix'],
                        ['href' => '#wiz-group-inheritance', 'label' => 'Group Inheritance'],
                        ['href' => '#wiz-advanced-junction', 'label' => 'Advanced Junction Options'],
                        ['href' => '#wiz-composite',         'label' => 'Composite Junction'],
                        ['href' => '#wiz-2way',              'label' => '2-Way Sync & Delete Mode'],
                        ['href' => '#wiz-example-gov-hr',    'label' => '📋 ตัวอย่าง: ระบบ HR ภาครัฐ'],
                        ['href' => '#wiz-after',             'label' => 'หลังสร้างแล้ว'],
                    ] as $t)
                    <a href="{{ $t['href'] }}" class="px-2.5 py-1 bg-indigo-50 text-indigo-700 rounded-lg hover:bg-indigo-100 transition-colors font-medium">{{ $t['label'] }}</a>
                    @endforeach
                </div>

                {{-- คืออะไร --}}
                <div id="wiz-intro">
                    <p><strong class="text-slate-900">Connector Wizard</strong> คือเครื่องมือที่ช่วยให้ Admin ระดับ 2 สามารถเชื่อมต่อฐานข้อมูลของระบบภายนอก (เช่น ระบบซ่อมบำรุง, ระบบ HR, ระบบจัดการเอกสาร) เข้ากับ UCM ได้ <strong>โดยไม่ต้องเขียนโค้ด PHP</strong> เพียงกรอกข้อมูลผ่านหน้า Wizard แบบ Step-by-Step</p>

                    <div class="mt-4 grid grid-cols-2 sm:grid-cols-4 gap-3">
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
                        <div class="bg-violet-50 rounded-xl p-4 border border-violet-100">
                            <div class="text-2xl mb-2">🤖</div>
                            <div class="font-bold text-violet-900 text-sm mb-1">AI Analysis</div>
                            <p class="text-xs text-violet-700">วิเคราะห์โครงสร้าง DB และ Source Code ด้วย AI แนะนำการตั้งค่าอัตโนมัติ</p>
                        </div>
                    </div>
                </div>

                {{-- ก่อนเริ่ม --}}
                <div id="wiz-prereq" class="border-t border-slate-100 pt-5">
                    <h3 class="font-bold text-slate-900 mb-3">ก่อนเริ่ม: สิ่งที่ต้องเตรียม</h3>
                    <p class="text-slate-600 mb-3">ไปที่เมนู <strong>ผู้ดูแลระบบ → Connector Wizard</strong> ในแถบเมนูด้านซ้าย (ต้องเป็น Admin ระดับ 2 เท่านั้น)</p>
                    <div class="flex items-start gap-3 p-3.5 bg-amber-50 border border-amber-200 rounded-xl text-xs text-amber-800">
                        <svg class="w-4 h-4 text-amber-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                        <span>Connector Wizard ต้องการสิทธิ์เข้าถึงฐานข้อมูลโดยตรง กรุณาเตรียม <strong>Host, Port, ชื่อ Database, Username, Password</strong> ของระบบปลายทางไว้ก่อน</span>
                    </div>
                </div>

                {{-- วิเคราะห์อัตโนมัติ (AI) --}}
                <div id="wiz-analyze" class="border-t border-slate-100 pt-5">
                    <h3 class="font-bold text-slate-900 mb-1">วิเคราะห์โครงสร้างฐานข้อมูลอัตโนมัติ</h3>
                    <p class="text-xs text-slate-500 mb-4">ก่อนกรอก Step 3–4 ด้วยตนเอง ลองใช้ฟีเจอร์วิเคราะห์อัตโนมัติเพื่อให้ระบบแนะนำการตั้งค่าได้ทันที</p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5">
                        {{-- วิเคราะห์จาก DB Connection --}}
                        <div class="bg-slate-50 rounded-xl border border-slate-200 p-4">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="text-lg">🔍</span>
                                <p class="font-bold text-slate-800 text-sm">วิเคราะห์จาก DB Connection</p>
                            </div>
                            <p class="text-xs text-slate-600 mb-2">ปุ่ม <strong>"วิเคราะห์ Schema อัตโนมัติ"</strong> ใน Step 2 จะ introspect ฐานข้อมูลปลายทางโดยตรง ดึงโครงสร้างตาราง คอลัมน์ FK และตัวอย่างข้อมูล แล้วส่งให้ AI หรือ Rule-Based engine วิเคราะห์</p>
                            <ul class="text-xs text-slate-500 space-y-1 list-disc list-inside">
                                <li>ไม่ต้องอัปโหลดไฟล์ใดๆ</li>
                                <li>ทำงานได้ทันทีหลังผ่าน Test Connection</li>
                                <li>ดึง row count และ sample data (PII ถูก REDACT อัตโนมัติ)</li>
                            </ul>
                        </div>
                        {{-- วิเคราะห์จาก Source Code ZIP --}}
                        <div class="bg-violet-50 rounded-xl border border-violet-200 p-4">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="text-lg">📦</span>
                                <p class="font-bold text-violet-800 text-sm">วิเคราะห์จาก Source Code ZIP</p>
                                <span class="text-[10px] font-bold bg-violet-100 text-violet-700 px-1.5 py-0.5 rounded">ต้องการ API Key</span>
                            </div>
                            <p class="text-xs text-slate-600 mb-2">อัปโหลดไฟล์ <strong>.zip</strong> ที่บีบอัด source code ของระบบภายนอก ระบบจะสแกนหาไฟล์ migration, model, schema และตรวจจับ framework อัตโนมัติ จากนั้นส่ง context ให้ Claude API วิเคราะห์</p>
                            <ul class="text-xs text-slate-500 space-y-1 list-disc list-inside">
                                <li>รองรับ 20+ frameworks: Laravel, Django, Rails, Spring, NestJS ฯลฯ</li>
                                <li>รองรับ Legacy PHP (ไม่มี framework) และ Plain SQL</li>
                                <li>ข้อมูลลับ (password, token) ถูก redact ก่อนส่ง AI</li>
                                <li>ขนาดไฟล์สูงสุด: 100 MB (uncompressed), 512 KB ต่อไฟล์</li>
                            </ul>
                        </div>
                    </div>

                    {{-- ระดับความแม่นยำ --}}
                    <div class="rounded-xl border border-slate-200 overflow-hidden mb-4">
                        <div class="px-4 py-2.5 bg-slate-50 border-b border-slate-200">
                            <p class="font-bold text-slate-800 text-xs">ระดับความแม่นยำของการวิเคราะห์</p>
                        </div>
                        <div class="divide-y divide-slate-100">
                            @foreach ([
                                ['mode' => 'AI + Source Code', 'color' => 'violet', 'pct' => '90–98%', 'desc' => 'ส่ง schema + source files ให้ Claude API วิเคราะห์ — แม่นยำที่สุด เหมาะกับระบบที่มีชื่อตารางไม่ตรงมาตรฐาน'],
                                ['mode' => 'AI เท่านั้น', 'color' => 'indigo', 'pct' => '80–92%', 'desc' => 'ส่ง schema introspect ให้ Claude API วิเคราะห์ — ดีมากสำหรับ DB ที่มี FK constraints ครบ'],
                                ['mode' => 'Rule-Based', 'color' => 'sky', 'pct' => '65–85%', 'desc' => 'ใช้ heuristics และ pattern matching — ไม่ต้องการ API Key ทำงานเร็ว เหมาะกับระบบที่ชื่อตารางเป็นมาตรฐาน'],
                            ] as $row)
                            <div class="flex items-center gap-3 px-4 py-3 text-xs">
                                <span class="inline-block w-24 flex-shrink-0 text-center px-2 py-1 rounded-lg font-bold bg-{{ $row['color'] }}-100 text-{{ $row['color'] }}-800">{{ $row['mode'] }}</span>
                                <span class="font-bold text-slate-700 w-16 flex-shrink-0">{{ $row['pct'] }}</span>
                                <span class="text-slate-500">{{ $row['desc'] }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="flex items-start gap-3 p-3.5 bg-amber-50 border border-amber-200 rounded-xl text-xs text-amber-800">
                        <svg class="w-4 h-4 text-amber-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                        <span>ผลที่ได้จาก AI เป็นเพียง <strong>ข้อเสนอแนะ</strong> — กดปุ่ม <strong>"ใช้การตั้งค่านี้"</strong> เพื่อ autofill ค่าลง Wizard แล้วตรวจสอบความถูกต้องก่อนกด "ถัดไป" ทุกครั้ง ค่า confidence ต่ำกว่า 60% แสดงว่าระบบไม่แน่ใจ ควรตรวจสอบด้วยตนเอง</span>
                    </div>
                </div>

                {{-- 7 ขั้นตอน --}}
                <div id="wiz-steps" class="border-t border-slate-100 pt-5">
                    <h3 class="font-bold text-slate-900 mb-4">7 ขั้นตอนของ Wizard</h3>
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
                                <div class="mt-2 flex items-start gap-2 p-2.5 bg-violet-50 border border-violet-200 rounded-lg text-xs text-violet-800">
                                    <span class="text-base leading-none">🤖</span>
                                    <span>หลัง Test Connection สำเร็จ กด <strong>"วิเคราะห์ Schema อัตโนมัติ"</strong> เพื่อให้ AI หรือ Rule-Based engine แนะนำการตั้งค่า Step 3–4 ให้อัตโนมัติ หรืออัปโหลด <strong>ZIP source code</strong> เพื่อความแม่นยำสูงขึ้น → <a href="#wiz-analyze" class="underline">รายละเอียด</a></span>
                                </div>
                            </div>
                        </div>

                        {{-- Step 3 --}}
                        <div class="flex gap-4 p-4 bg-slate-50 rounded-xl border border-slate-100">
                            <div class="w-8 h-8 bg-indigo-600 text-white text-sm font-bold rounded-full flex items-center justify-center flex-shrink-0">3</div>
                            <div class="flex-1">
                                <p class="font-bold text-slate-900 mb-1">ตาราง Users</p>
                                <p class="text-slate-600 text-xs leading-relaxed mb-2">เลือกตารางหลักที่เก็บข้อมูลผู้ใช้ จากนั้น map คอลัมน์ที่จำเป็น:</p>
                                <div class="space-y-1.5 text-xs mb-3">
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

                                {{-- Multi-table JOIN hint --}}
                                <div class="flex items-start gap-2 p-2.5 bg-indigo-50 border border-indigo-200 rounded-xl text-xs text-indigo-800 mb-2">
                                    <span class="text-base leading-none flex-shrink-0">🔀</span>
                                    <span><strong>ข้อมูลผู้ใช้กระจายหลายตาราง?</strong> กด <strong>"+ เพิ่ม JOIN"</strong> เพื่อเชื่อมตารางเพิ่มเติม (LEFT / INNER / RIGHT JOIN) — แต่ละแถว JOIN ต้องระบุ Alias และ ON clause → <a href="#wiz-multi-table" class="underline font-semibold">รายละเอียด Multi-Table JOIN</a></span>
                                </div>

                                <p class="text-xs text-slate-600">กด <strong>"ดูตัวอย่างข้อมูล"</strong> เพื่อดู 10 แถวแรก ช่วยยืนยันว่า mapping และ JOIN ถูกต้อง</p>
                            </div>
                        </div>

                        {{-- Step 4 --}}
                        <div class="flex gap-4 p-4 bg-slate-50 rounded-xl border border-slate-100">
                            <div class="w-8 h-8 bg-indigo-600 text-white text-sm font-bold rounded-full flex items-center justify-center flex-shrink-0">4</div>
                            <div class="flex-1">
                                <p class="font-bold text-slate-900 mb-2">Permission Mode</p>
                                <p class="text-slate-600 text-xs mb-3">เลือกวิธีที่ระบบภายนอกจัดเก็บสิทธิ์ มี 10 รูปแบบ:</p>
                                <div class="space-y-3">
                                    <div class="bg-white border border-slate-200 rounded-xl p-3">
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="text-base">🔗</span>
                                            <p class="font-bold text-slate-800 text-xs">Junction Table</p>
                                            <span class="text-[10px] bg-indigo-100 text-indigo-700 px-1.5 py-0.5 rounded font-bold">แนะนำ</span>
                                        </div>
                                        <p class="text-xs text-slate-600">ตาราง mapping แยกต่างหาก เช่น <code class="font-mono bg-slate-100 px-1 rounded text-[10px]">user_roles(user_id, role_code)</code> เหมาะสำหรับระบบที่ผู้ใช้มีได้หลายสิทธิ์</p>
                                        <p class="text-xs text-slate-500 mt-1">→ ต้องระบุ: ตาราง, FK column ที่ชี้หาผู้ใช้, column ค่าสิทธิ์</p>
                                        <p class="text-xs text-violet-600 mt-1">→ รองรับ <strong>Composite Junction</strong> และ <strong>Advanced Options</strong> (Soft-Delete, Time-Bound, Insert Meta) → <a href="#wiz-composite" class="underline">Composite</a> · <a href="#wiz-advanced-junction" class="underline">Advanced Options</a></p>
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
                                            <span class="text-base">🔀</span>
                                            <p class="font-bold text-slate-800 text-xs">Junction + Column (Mixed)</p>
                                            <span class="text-[10px] bg-rose-100 text-rose-700 px-1.5 py-0.5 rounded font-bold">ขั้นสูง</span>
                                        </div>
                                        <p class="text-xs text-slate-600">ใช้ทั้ง Junction Table <strong>และ</strong> Column บนตาราง user พร้อมกัน — เหมาะสำหรับระบบที่มี role หลัก (column) และ permission เพิ่มเติม (junction) แยกกัน</p>
                                        <p class="text-xs text-slate-500 mt-1">→ ต้องระบุ: ตาราง junction + ตาราง/คอลัมน์ฝั่ง column พร้อมตัวเลือกที่เป็นไปได้</p>
                                        <p class="text-xs text-rose-600 mt-1">→ สิทธิ์ฝั่ง column มี prefix <code class="font-mono bg-rose-50 px-1 rounded">col:</code> → <a href="#wiz-mixed-mode" class="underline">รายละเอียด Mixed Mode</a></p>
                                    </div>
                                    <div class="bg-white border border-emerald-200 rounded-xl p-3">
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="text-base">☑️</span>
                                            <p class="font-bold text-slate-800 text-xs">Boolean Matrix</p>
                                            <span class="text-[10px] bg-emerald-100 text-emerald-700 px-1.5 py-0.5 rounded font-bold">ใหม่</span>
                                        </div>
                                        <p class="text-xs text-slate-600">สิทธิ์เป็นคอลัมน์ TINYINT บนตาราง users โดยตรง เช่น <code class="font-mono bg-slate-100 px-1 rounded text-[10px]">can_view=1, can_edit=0</code></p>
                                        <p class="text-xs text-slate-500 mt-1">→ ต้องระบุ: รายชื่อคอลัมน์ที่เป็น permission flag (กรอกทีละบรรทัดในช่อง textarea)</p>
                                        <p class="text-xs text-emerald-600 mt-1">→ <a href="#wiz-boolean-matrix" class="underline">ดูตัวอย่างและการตั้งค่า</a></p>
                                    </div>
                                    <div class="bg-white border border-sky-200 rounded-xl p-3">
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="text-base">👥</span>
                                            <p class="font-bold text-slate-800 text-xs">Group Inheritance</p>
                                            <span class="text-[10px] bg-sky-100 text-sky-700 px-1.5 py-0.5 rounded font-bold">ใหม่</span>
                                        </div>
                                        <p class="text-xs text-slate-600">สิทธิ์ถ่ายทอดผ่านกลุ่ม 3 ชั้น — users → via table (membership) → perm table (permissions)</p>
                                        <p class="text-xs text-slate-500 mt-1">→ ต้องระบุ: Via Table + FK→User + FK→Group, Perm Table + FK→Group + Value Column</p>
                                        <p class="text-xs text-sky-600 mt-1">→ <a href="#wiz-group-inheritance" class="underline">ดูตัวอย่างและการตั้งค่า</a></p>
                                    </div>
                                    <div class="bg-white border border-slate-200 rounded-xl p-3">
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="text-base">✍️</span>
                                            <p class="font-bold text-slate-800 text-xs">Manual</p>
                                        </div>
                                        <p class="text-xs text-slate-600">ไม่มีตาราง permission ในระบบ — กำหนด permission list ด้วยตนเองใน UCM โดยไม่ sync ไปยังฐานข้อมูล</p>
                                        <p class="text-xs text-slate-500 mt-1">→ เหมาะสำหรับระบบที่ตรวจสิทธิ์ผ่าน UCM API โดยตรง</p>
                                    </div>
                                    <div class="bg-white border border-purple-200 rounded-xl p-3">
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="text-base">🗂️</span>
                                            <p class="font-bold text-slate-800 text-xs">JSON Column</p>
                                            <span class="text-[10px] bg-purple-100 text-purple-700 px-1.5 py-0.5 rounded font-bold">ใหม่</span>
                                        </div>
                                        <p class="text-xs text-slate-600">สิทธิ์เก็บเป็น JSON array ใน 1 column บนตาราง users เช่น <code class="font-mono bg-slate-100 px-1 rounded text-[10px]">permissions = '["read","write","approve"]'</code></p>
                                        <p class="text-xs text-slate-500 mt-1">→ ต้องระบุ: column ที่เก็บ JSON array, รายการ permissions (หรือ auto-discover จาก DB)</p>
                                        <p class="text-xs text-purple-600 mt-1">→ เหมาะกับ modern systems ที่ใช้ JSON field แทนตาราง junction</p>
                                    </div>
                                    <div class="bg-white border border-teal-200 rounded-xl p-3">
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="text-base">📝</span>
                                            <p class="font-bold text-slate-800 text-xs">Delimited Column</p>
                                            <span class="text-[10px] bg-teal-100 text-teal-700 px-1.5 py-0.5 rounded font-bold">ใหม่</span>
                                        </div>
                                        <p class="text-xs text-slate-600">สิทธิ์เก็บเป็น string คั่นด้วยตัวคั่น เช่น <code class="font-mono bg-slate-100 px-1 rounded text-[10px]">role = 'admin,editor,viewer'</code> หรือ <code class="font-mono bg-slate-100 px-1 rounded text-[10px]">'HR|Finance'</code></p>
                                        <p class="text-xs text-slate-500 mt-1">→ ต้องระบุ: column ที่เก็บ string, ตัวคั่น (<code class="font-mono bg-slate-100 px-1 rounded text-[10px]">,</code> <code class="font-mono bg-slate-100 px-1 rounded text-[10px]">|</code> <code class="font-mono bg-slate-100 px-1 rounded text-[10px]">;</code> หรือ space), รายการ permissions</p>
                                        <p class="text-xs text-teal-600 mt-1">→ เหมาะกับ legacy systems ที่ใช้ string field สำหรับ multi-role</p>
                                    </div>
                                    <div class="bg-white border border-orange-200 rounded-xl p-3">
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="text-base">🔢</span>
                                            <p class="font-bold text-slate-800 text-xs">Bitmask</p>
                                            <span class="text-[10px] bg-orange-100 text-orange-700 px-1.5 py-0.5 rounded font-bold">ใหม่</span>
                                        </div>
                                        <p class="text-xs text-slate-600">สิทธิ์เก็บเป็น integer ที่ encode หลาย permission ด้วย bitwise flags เช่น <code class="font-mono bg-slate-100 px-1 rounded text-[10px]">perms_flag = 7</code> หมายถึง bit 1=read, 2=write, 4=admin ทั้งหมดเปิดอยู่</p>
                                        <p class="text-xs text-slate-500 mt-1">→ ต้องระบุ: column integer บน user table, Bit Map (bit value → permission key) เช่น <code class="font-mono bg-slate-100 px-1 rounded text-[10px]">{"1":"read","2":"write","4":"admin"}</code></p>
                                        <p class="text-xs text-orange-600 mt-1">→ เหมาะกับ ERP หรือระบบ C-origin ที่ใช้ bitwise flags</p>
                                    </div>
                                    <div class="bg-white border border-rose-200 rounded-xl p-3">
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="text-base">🌳</span>
                                            <p class="font-bold text-slate-800 text-xs">Multi-Level Hierarchy</p>
                                            <span class="text-[10px] bg-rose-100 text-rose-700 px-1.5 py-0.5 rounded font-bold">ใหม่</span>
                                        </div>
                                        <p class="text-xs text-slate-600">RBAC หลายชั้น — users → roles → parent roles → permissions โดยใช้ self-reference FK บนตาราง roles เช่น <code class="font-mono bg-slate-100 px-1 rounded text-[10px]">roles(id, name, parent_id)</code></p>
                                        <p class="text-xs text-slate-500 mt-1">→ ต้องระบุ: Membership Table (user↔role), Roles Table (รวม self-ref FK), Role-Permission Table — รองรับ Recursive CTE (MySQL 8+) และ iterative fallback (MySQL 5.7)</p>
                                        <p class="text-xs text-rose-600 mt-1">→ เหมาะกับ RBAC ที่มี role hierarchy ลึก 3+ ชั้น</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Step 5 --}}
                        <div class="flex gap-4 p-4 bg-orange-50 rounded-xl border border-orange-100">
                            <div class="w-8 h-8 bg-orange-500 text-white text-sm font-bold rounded-full flex items-center justify-center flex-shrink-0">5</div>
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    <p class="font-bold text-slate-900">2-Way Permission Sync</p>
                                    <span class="text-[10px] font-bold bg-orange-100 text-orange-700 px-2 py-0.5 rounded-full">ทางเลือก</span>
                                </div>
                                <p class="text-slate-600 text-xs leading-relaxed mb-3">เปิดใช้ <strong>2-Way Sync</strong> เพื่อให้ UCM สร้าง/ลบ Permission Definition ในฐานข้อมูลปลายทางโดยอัตโนมัติเมื่อ Admin เพิ่มหรือลบ Permission ใน UCM</p>

                                {{-- Enable toggle --}}
                                <div class="bg-white rounded-xl border border-orange-200 p-3 mb-3 text-xs">
                                    <p class="font-semibold text-slate-800 mb-2">การเปิดใช้งาน 2-Way Sync</p>
                                    <div class="space-y-1.5 text-slate-600">
                                        <div class="flex items-start gap-2">
                                            <span class="inline-block w-3 h-3 rounded-full bg-orange-400 mt-0.5 flex-shrink-0"></span>
                                            <p>สลับ Toggle <strong>"เปิด 2-Way Sync"</strong> ให้เป็น ON</p>
                                        </div>
                                        <div class="flex items-start gap-2">
                                            <span class="inline-block w-3 h-3 rounded-full bg-orange-400 mt-0.5 flex-shrink-0"></span>
                                            <p>เลือก <strong>ตาราง Permission Definition</strong> — ตารางในระบบปลายทางที่เก็บนิยามของสิทธิ์ เช่น <code class="font-mono bg-slate-100 px-1 rounded">roles</code>, <code class="font-mono bg-slate-100 px-1 rounded">page_groups</code></p>
                                        </div>
                                        <div class="flex items-start gap-2">
                                            <span class="inline-block w-3 h-3 rounded-full bg-orange-400 mt-0.5 flex-shrink-0"></span>
                                            <p>Map คอลัมน์: <strong>Value (บังคับ)</strong>, Primary Key, Label, Group (ไม่บังคับ)</p>
                                        </div>
                                    </div>
                                </div>

                                {{-- Delete Mode --}}
                                <div class="bg-white rounded-xl border border-orange-200 p-3 text-xs">
                                    <p class="font-semibold text-slate-800 mb-2">Delete Mode — พฤติกรรมเมื่อลบ Permission</p>
                                    <div class="space-y-2">
                                        <div class="flex items-start gap-2 p-2 bg-slate-50 rounded-lg border border-slate-100">
                                            <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold bg-slate-200 text-slate-700 flex-shrink-0 mt-0.5">Detach Only</span>
                                            <p class="text-slate-600">ลบ Permission จาก UCM เท่านั้น — ไม่แตะข้อมูลในฐานข้อมูลปลายทาง</p>
                                        </div>
                                        <div class="flex items-start gap-2 p-2 bg-red-50 rounded-lg border border-red-100">
                                            <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold bg-red-100 text-red-700 flex-shrink-0 mt-0.5">Hard Delete</span>
                                            <p class="text-slate-600">ลบแถวออกจากตาราง Permission Definition ในฐานข้อมูลปลายทางถาวร</p>
                                        </div>
                                        <div class="flex items-start gap-2 p-2 bg-amber-50 rounded-lg border border-amber-100">
                                            <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-700 flex-shrink-0 mt-0.5">Soft Delete</span>
                                            <p class="text-slate-600">อัปเดตคอลัมน์ flag (เช่น <code class="font-mono bg-white px-1 rounded border border-slate-200">is_active = 0</code>) แทนการลบจริง</p>
                                        </div>
                                    </div>
                                    <p class="mt-2 text-slate-500">→ ดูตัวอย่างแต่ละ mode อย่างละเอียดได้ที่ <a href="#wiz-2way" class="text-indigo-600 underline">2-Way Sync &amp; Delete Mode</a></p>
                                </div>
                            </div>
                        </div>

                        {{-- Step 6 --}}
                        <div class="flex gap-4 p-4 bg-teal-50 rounded-xl border border-teal-100">
                            <div class="w-8 h-8 bg-teal-600 text-white text-sm font-bold rounded-full flex items-center justify-center flex-shrink-0">6</div>
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    <p class="font-bold text-slate-900">Master Data Tables</p>
                                    <span class="text-[10px] font-bold bg-teal-100 text-teal-700 px-2 py-0.5 rounded-full">ทางเลือก</span>
                                </div>
                                <p class="text-slate-600 text-xs leading-relaxed mb-3">กำหนดตาราง "ข้อมูลหลัก" ที่ Admin ต้องการจัดการ (เพิ่ม/แก้ไข/ลบ) ผ่าน UCM โดยตรง เช่น ตารางแผนก ประเภทเอกสาร หรือ Role Definition ที่ไม่ได้เชื่อมกับ 2-Way Sync</p>

                                {{-- เมื่อไหร่ควรใช้ --}}
                                <div class="bg-white rounded-xl border border-teal-200 p-3 mb-3 text-xs">
                                    <p class="font-semibold text-slate-800 mb-2">เมื่อไหร่ควรตั้งค่า Step นี้?</p>
                                    <div class="space-y-1.5 text-slate-600">
                                        <div class="flex items-start gap-2">
                                            <span class="inline-block w-3 h-3 rounded-full bg-teal-400 mt-0.5 flex-shrink-0"></span>
                                            <p>มีตาราง lookup/reference ที่ต้องเพิ่ม/แก้ไขข้อมูลบ่อย เช่น ตารางแผนก, หมวดหมู่, ประเภทคำร้อง</p>
                                        </div>
                                        <div class="flex items-start gap-2">
                                            <span class="inline-block w-3 h-3 rounded-full bg-teal-400 mt-0.5 flex-shrink-0"></span>
                                            <p>ต้องการให้ Admin จัดการข้อมูลผ่าน UCM โดยไม่ต้องเข้าถึงฐานข้อมูลโดยตรง</p>
                                        </div>
                                        <div class="flex items-start gap-2">
                                            <span class="inline-block w-3 h-3 rounded-full bg-teal-400 mt-0.5 flex-shrink-0"></span>
                                            <p>บางตารางใช้ Soft Delete (ซ่อนแทนลบถาวร) บางตารางใช้ Hard Delete — ตั้งค่าได้แยกต่อตาราง</p>
                                        </div>
                                    </div>
                                </div>

                                {{-- การตั้งค่า --}}
                                <div class="bg-white rounded-xl border border-teal-200 p-3 mb-3 text-xs">
                                    <p class="font-semibold text-slate-800 mb-2">วิธีเพิ่ม Master Table</p>
                                    <div class="space-y-1.5 text-slate-600">
                                        <div class="flex items-start gap-2">
                                            <span class="w-4 h-4 bg-teal-600 text-white text-[9px] font-bold rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">1</span>
                                            <p>กด <strong>"+ เพิ่มตาราง"</strong> — การ์ดใหม่จะปรากฏขึ้น</p>
                                        </div>
                                        <div class="flex items-start gap-2">
                                            <span class="w-4 h-4 bg-teal-600 text-white text-[9px] font-bold rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">2</span>
                                            <p>กรอก <strong>ชื่อกลุ่ม (Label)</strong> — ชื่อที่จะแสดงใน UCM เช่น "แผนก", "ประเภทเอกสาร"</p>
                                        </div>
                                        <div class="flex items-start gap-2">
                                            <span class="w-4 h-4 bg-teal-600 text-white text-[9px] font-bold rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">3</span>
                                            <p>เลือก <strong>ตาราง</strong> จาก dropdown (ดึงจาก DB ปลายทาง)</p>
                                        </div>
                                        <div class="flex items-start gap-2">
                                            <span class="w-4 h-4 bg-teal-600 text-white text-[9px] font-bold rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">4</span>
                                            <p>เลือก <strong>Primary Key Column</strong> และ <strong>Label Column</strong> (คอลัมน์ชื่อ/ค่าหลักที่แสดง)</p>
                                        </div>
                                        <div class="flex items-start gap-2">
                                            <span class="w-4 h-4 bg-teal-600 text-white text-[9px] font-bold rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">5</span>
                                            <p>เลือก <strong>Delete Mode</strong>: <span class="text-red-600 font-bold">Hard Delete</span> (ลบแถวถาวร) หรือ <span class="text-amber-600 font-bold">Soft Delete</span> (อัปเดต flag)</p>
                                        </div>
                                        <div class="flex items-start gap-2">
                                            <span class="w-4 h-4 bg-teal-600 text-white text-[9px] font-bold rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">6</span>
                                            <p>หาก Soft Delete: ระบุ <strong>Soft Delete Column</strong> (เช่น <code class="font-mono bg-slate-100 px-1 rounded">is_deleted</code>) และ <strong>ค่าที่หมายถึงลบ</strong> (เช่น <code class="font-mono bg-slate-100 px-1 rounded">1</code>)</p>
                                        </div>
                                        <div class="flex items-start gap-2">
                                            <span class="w-4 h-4 bg-teal-600 text-white text-[9px] font-bold rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">7</span>
                                            <p>(ไม่บังคับ) กด <strong>"+ เพิ่มคอลัมน์เสริม"</strong> เพื่อเพิ่มฟิลด์อื่น เช่น คำอธิบาย, รหัส, สถานะ — กำหนดชื่อ label, ประเภทข้อมูล และว่า required หรือไม่</p>
                                        </div>
                                    </div>
                                </div>

                                {{-- Delete Mode comparison --}}
                                <div class="grid grid-cols-2 gap-2 text-xs mb-3">
                                    <div class="p-2.5 bg-red-50 border border-red-200 rounded-xl">
                                        <p class="font-bold text-red-700 mb-1 flex items-center gap-1"><span class="text-[10px] font-bold text-red-500 bg-red-100 border border-red-200 px-1 py-0.5 rounded">Hard</span> Hard Delete</p>
                                        <p class="text-red-800"><code class="font-mono bg-white border border-red-200 px-1 rounded text-[10px]">DELETE FROM table WHERE pk = ?</code></p>
                                        <p class="text-slate-500 mt-1">ลบแถวออกจากฐานข้อมูลถาวร ไม่สามารถกู้คืนได้ เหมาะกับข้อมูลที่ไม่ต้องการ audit trail</p>
                                    </div>
                                    <div class="p-2.5 bg-amber-50 border border-amber-200 rounded-xl">
                                        <p class="font-bold text-amber-700 mb-1 flex items-center gap-1"><span class="text-[10px] font-bold text-amber-600 bg-amber-50 border border-amber-200 px-1 py-0.5 rounded">Soft</span> Soft Delete</p>
                                        <p class="text-amber-800"><code class="font-mono bg-white border border-amber-200 px-1 rounded text-[10px]">UPDATE table SET col = val WHERE pk = ?</code></p>
                                        <p class="text-slate-500 mt-1">ซ่อนแถวโดยการ update flag เก็บข้อมูลไว้ เหมาะกับระบบที่มี FK หรือต้องการ audit</p>
                                    </div>
                                </div>

                                <div class="flex items-start gap-2 p-2.5 bg-blue-50 border border-blue-200 rounded-xl text-xs text-blue-800">
                                    <svg class="w-4 h-4 text-blue-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                                    <span>สามารถเพิ่มได้หลายตาราง — แต่ละตารางมีการตั้งค่า Delete Mode และคอลัมน์เสริมเป็นอิสระจากกัน หากไม่ต้องการฟีเจอร์นี้ ข้ามขั้นตอน 6 ได้เลย</span>
                                </div>
                            </div>
                        </div>

                        {{-- Step 7 --}}
                        <div class="flex gap-4 p-4 bg-emerald-50 rounded-xl border border-emerald-100">
                            <div class="w-8 h-8 bg-emerald-600 text-white text-sm font-bold rounded-full flex items-center justify-center flex-shrink-0">7</div>
                            <div class="flex-1">
                                <p class="font-bold text-slate-900 mb-1">ยืนยันการสร้าง Connector</p>
                                <p class="text-slate-600 text-xs leading-relaxed">ตรวจสอบข้อมูลสรุปทั้งหมด — ชื่อระบบ, DB Connection, User Table Mapping, Permission Mode, 2-Way Sync config และ Master Data Tables จากนั้นกด <strong>"สร้าง Connector"</strong></p>
                                <div class="mt-2 text-xs text-emerald-700 bg-white border border-emerald-200 px-3 py-2 rounded-lg">
                                    หลังสร้างสำเร็จ ระบบจะสร้าง <strong>System</strong> ใหม่ใน UCM พร้อม <strong>DynamicAdapter</strong> และ redirect ไปยังหน้าจัดการระบบโดยอัตโนมัติ
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- เลือก Permission Mode --}}
                <div id="wiz-perm-mode" class="border-t border-slate-100 pt-5">
                    <h3 class="font-bold text-slate-900 mb-1">เลือก Permission Mode (Step 4)</h3>
                    <p class="text-xs text-slate-500 mb-4">เปรียบเทียบ 10 รูปแบบก่อนตัดสินใจ จากนั้นดูตัวอย่างโครงสร้าง DB และการตั้งค่าของแต่ละ Mode</p>

                    {{-- ตารางเปรียบเทียบ --}}
                    <div class="rounded-2xl border border-slate-200 overflow-hidden mb-6">
                        <div class="px-5 py-3 bg-slate-50 border-b border-slate-200">
                            <p class="font-bold text-slate-800 text-sm">สรุปเปรียบเทียบ 10 Permission Mode</p>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-xs">
                                <thead class="bg-slate-50 border-b border-slate-200">
                                    <tr>
                                        <th class="text-left px-4 py-2.5 font-semibold text-slate-700 whitespace-nowrap">ประเด็น</th>
                                        <th class="text-left px-4 py-2.5 font-semibold text-indigo-700 whitespace-nowrap">🔗 Junction</th>
                                        <th class="text-left px-4 py-2.5 font-semibold text-violet-700 whitespace-nowrap">📋 Single Column</th>
                                        <th class="text-left px-4 py-2.5 font-semibold text-rose-700 whitespace-nowrap">🔀 Mixed</th>
                                        <th class="text-left px-4 py-2.5 font-semibold text-emerald-700 whitespace-nowrap">☑️ Boolean Matrix</th>
                                        <th class="text-left px-4 py-2.5 font-semibold text-sky-700 whitespace-nowrap">👥 Group Inheritance</th>
                                        <th class="text-left px-4 py-2.5 font-semibold text-amber-700 whitespace-nowrap">✍️ Manual</th>
                                        <th class="text-left px-4 py-2.5 font-semibold text-purple-700 whitespace-nowrap">🗂️ JSON Column</th>
                                        <th class="text-left px-4 py-2.5 font-semibold text-teal-700 whitespace-nowrap">📝 Delimited Column</th>
                                        <th class="text-left px-4 py-2.5 font-semibold text-orange-700 whitespace-nowrap">🔢 Bitmask</th>
                                        <th class="text-left px-4 py-2.5 font-semibold text-rose-800 whitespace-nowrap">🌳 Multi-Level Hierarchy</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach ([
                                        ['label' => 'ตาราง permission',    'j' => 'ตารางแยกต่างหาก',         'sc' => 'คอลัมน์บนตาราง users',      'mx' => 'ทั้ง junction + column',       'bm' => 'คอลัมน์ TINYINT บน users',    'gi' => '3 ตาราง: users→via→perms',   'm' => 'ไม่มี',              'jc' => 'คอลัมน์ JSON array บน users',          'dc' => 'คอลัมน์ string บน users',          'bk' => 'คอลัมน์ integer บน users',       'ml' => '3 ตาราง: membership→roles(parent)→role_perms'],
                                        ['label' => 'หลาย perm ต่อ user', 'j' => '✅ ได้',                  'sc' => '❌ ได้แค่ 1 ค่า',           'mx' => '✅ junction หลาย + column 1', 'bm' => '✅ แต่ละ column = 1 perm',    'gi' => '✅ ตาม groups ที่ join',       'm' => '✅ ได้',             'jc' => '✅ ได้ (หลาย items ใน array)',         'dc' => '✅ ได้ (หลาย items ใน string)',     'bk' => '✅ ได้ (หลาย bits)',              'ml' => '✅ ได้ (ตาม role chain)'],
                                        ['label' => 'UCM sync ไปปลายทาง', 'j' => 'INSERT / DELETE แถว',     'sc' => 'UPDATE คอลัมน์',            'mx' => 'INSERT/DELETE + UPDATE col',   'bm' => 'UPDATE col=1/0 บน users',     'gi' => 'INSERT/DELETE ใน via table',  'm' => 'ไม่ sync (pull จาก API)', 'jc' => 'UPDATE col = json_encode($perms)',     'dc' => 'UPDATE col = implode($delimiter)',  'bk' => 'UPDATE col = sum of bits',        'ml' => 'INSERT/DELETE ใน membership table'],
                                        ['label' => 'Discover Permissions','j' => '✅ ดึง value อัตโนมัติ', 'sc' => '✅ ดึง value อัตโนมัติ',   'mx' => '✅ ทั้งสองฝั่ง',              'bm' => '✅ จากชื่อ column ที่กรอก',   'gi' => '✅ ดึง value จาก perm table', 'm' => '❌ กรอก JSON เอง',  'jc' => '✅ scan DB หรือกรอกเอง',              'dc' => '✅ scan DB หรือกรอกเอง',           'bk' => '✅ จาก Bit Map ที่กรอก',         'ml' => '✅ ดึง roles จาก roles table'],
                                        ['label' => 'Key format ใน UCM',   'j' => 'ค่าดิบ เช่น ADMIN',       'sc' => 'ค่าดิบ เช่น ADMIN',         'mx' => 'col:ADMIN (ฝั่ง column)',      'bm' => 'ชื่อคอลัมน์ เช่น can_view',  'gi' => 'ชื่อ group/role เช่น VIEWER', 'm' => 'ค่าดิบตาม JSON',    'jc' => 'ค่าดิบใน array เช่น read',            'dc' => 'ค่าดิบแต่ละ item เช่น admin',     'bk' => 'permission key จาก Bit Map',     'ml' => 'ชื่อ role เช่น MANAGER'],
                                        ['label' => 'เหมาะกับ',            'j' => 'RBAC / multi-role',       'sc' => 'single-role เรียบง่าย',     'mx' => 'มีทั้ง role + extra perms',   'bm' => 'flag column เช่น can_view',   'gi' => 'RBAC 3 ชั้น: user→group→perm','m' => 'ตรวจสิทธิ์ผ่าน API','jc' => 'modern systems ที่ใช้ JSON field',     'dc' => 'legacy systems ที่ใช้ string field','bk' => 'ERP / C-origin (bitwise flags)',  'ml' => 'RBAC recursive hierarchy 3+ ชั้น'],
                                    ] as $row)
                                    <tr class="hover:bg-slate-50">
                                        <td class="px-4 py-2.5 font-medium text-slate-700 whitespace-nowrap">{{ $row['label'] }}</td>
                                        <td class="px-4 py-2.5 text-slate-600">{{ $row['j'] }}</td>
                                        <td class="px-4 py-2.5 text-slate-600">{{ $row['sc'] }}</td>
                                        <td class="px-4 py-2.5 text-rose-700 font-medium">{{ $row['mx'] }}</td>
                                        <td class="px-4 py-2.5 text-emerald-700 font-medium">{{ $row['bm'] }}</td>
                                        <td class="px-4 py-2.5 text-sky-700 font-medium">{{ $row['gi'] }}</td>
                                        <td class="px-4 py-2.5 text-slate-600">{{ $row['m'] }}</td>
                                        <td class="px-4 py-2.5 text-purple-700 font-medium">{{ $row['jc'] }}</td>
                                        <td class="px-4 py-2.5 text-teal-700 font-medium">{{ $row['dc'] }}</td>
                                        <td class="px-4 py-2.5 text-orange-700 font-medium">{{ $row['bk'] }}</td>
                                        <td class="px-4 py-2.5 text-rose-800 font-medium">{{ $row['ml'] }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- ── Example 1: Junction Table ── --}}
                    <div class="mb-4 rounded-2xl border border-indigo-200 overflow-hidden">
                        <div class="flex items-center gap-3 px-5 py-3 bg-indigo-50 border-b border-indigo-200">
                            <span class="text-xl">🔗</span>
                            <div>
                                <p class="font-bold text-indigo-900 text-sm">Junction Table — ระบบซ่อมบำรุง (MySQL)</p>
                                <p class="text-xs text-indigo-600">ผู้ใช้มีได้หลาย role พร้อมกัน เก็บในตารางแยกต่างหาก</p>
                            </div>
                            <span class="ml-auto text-[10px] font-bold bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded-full">แนะนำ</span>
                        </div>
                        <div class="px-5 py-4 bg-white text-xs space-y-3">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div class="bg-slate-50 rounded-xl p-3 border border-slate-200 font-mono text-[11px] leading-relaxed">
                                    <p class="font-bold text-slate-500 mb-1 not-italic font-sans">ตาราง <code>employees</code></p>
                                    <p><span class="text-sky-600">emp_code</span>    VARCHAR(20) PK</p>
                                    <p><span class="text-slate-500">full_name</span>  VARCHAR(100)</p>
                                    <p><span class="text-slate-500">email</span>      VARCHAR(100)</p>
                                    <p><span class="text-slate-500">department</span> VARCHAR(50)</p>
                                    <p><span class="text-slate-500">is_active</span>  TINYINT (1=ใช้งาน)</p>
                                </div>
                                <div class="bg-slate-50 rounded-xl p-3 border border-slate-200 font-mono text-[11px] leading-relaxed">
                                    <p class="font-bold text-slate-500 mb-1 not-italic font-sans">ตาราง <code>employee_roles</code></p>
                                    <p><span class="text-sky-600">emp_code</span>    VARCHAR(20) FK→employees</p>
                                    <p><span class="text-indigo-600">role_code</span>  VARCHAR(50) <span class="text-slate-400">(ค่าสิทธิ์)</span></p>
                                    <p><span class="text-slate-500">role_name</span>  VARCHAR(100)</p>
                                    <p><span class="text-slate-500">role_group</span> VARCHAR(50)</p>
                                </div>
                            </div>
                            <div class="flex gap-3 p-3 bg-indigo-50 rounded-xl border border-indigo-200">
                                <div class="w-6 h-6 bg-indigo-600 text-white text-[10px] font-bold rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">4</div>
                                <div>
                                    <p class="font-semibold text-indigo-900 mb-1">Permission Mode → Junction Table</p>
                                    <div class="space-y-0.5 text-slate-700">
                                        <p><span class="text-slate-400">ตาราง Permission:</span> <code class="bg-white border border-indigo-200 px-1 rounded">employee_roles</code></p>
                                        <p><span class="text-slate-400">FK Column:</span> <code class="bg-white border border-indigo-200 px-1 rounded">emp_code</code></p>
                                        <p><span class="text-slate-400">Value Column:</span> <code class="bg-white border border-indigo-200 px-1 rounded">role_code</code> <span class="text-slate-400 text-[10px]">— ค่าที่ sync: TECH_LEAD, INSPECTOR</span></p>
                                        <p><span class="text-slate-400">Label Column:</span> <code class="bg-white border border-indigo-200 px-1 rounded">role_name</code></p>
                                        <p><span class="text-slate-400">Group Column:</span> <code class="bg-white border border-indigo-200 px-1 rounded">role_group</code></p>
                                    </div>
                                    <p class="mt-1.5 text-slate-600">Step 3 → ตาราง Users: <code class="bg-white border border-slate-200 px-1 rounded">employees</code>, Identifier: <code class="bg-white border border-slate-200 px-1 rounded">emp_code</code></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ── Example 2: Single Column ── --}}
                    <div class="mb-4 rounded-2xl border border-violet-200 overflow-hidden">
                        <div class="flex items-center gap-3 px-5 py-3 bg-violet-50 border-b border-violet-200">
                            <span class="text-xl">📋</span>
                            <div>
                                <p class="font-bold text-violet-900 text-sm">Single Column — ระบบ HR (PostgreSQL)</p>
                                <p class="text-xs text-violet-600">ผู้ใช้มีได้เพียง 1 role เก็บเป็นคอลัมน์บนตาราง users โดยตรง</p>
                            </div>
                        </div>
                        <div class="px-5 py-4 bg-white text-xs space-y-3">
                            <div class="bg-slate-50 rounded-xl p-3 border border-slate-200 font-mono text-[11px] leading-relaxed">
                                <p class="font-bold text-slate-500 mb-1 not-italic font-sans">ตาราง <code>staff</code></p>
                                <p><span class="text-sky-600">username</span>      VARCHAR(50) UNIQUE</p>
                                <p><span class="text-slate-500">display_name</span> VARCHAR(100)</p>
                                <p><span class="text-indigo-600">access_level</span> VARCHAR(20) <span class="text-slate-400">— 'ADMIN' | 'MANAGER' | 'VIEWER'</span></p>
                                <p><span class="text-slate-500">department</span>   VARCHAR(50)</p>
                                <p><span class="text-slate-500">active</span>       BOOLEAN (TRUE=ใช้งาน)</p>
                            </div>
                            <div class="flex gap-3 p-3 bg-violet-50 rounded-xl border border-violet-200">
                                <div class="w-6 h-6 bg-violet-600 text-white text-[10px] font-bold rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">4</div>
                                <div>
                                    <p class="font-semibold text-violet-900 mb-1">Permission Mode → Single Column</p>
                                    <div class="space-y-0.5 text-slate-700">
                                        <p><span class="text-slate-400">ตาราง Permission:</span> <code class="bg-white border border-violet-200 px-1 rounded">staff</code> <span class="text-slate-400 text-[10px]">— ตารางเดียวกับ Users</span></p>
                                        <p><span class="text-slate-400">FK Column:</span> <code class="bg-white border border-violet-200 px-1 rounded">username</code></p>
                                        <p><span class="text-slate-400">Value Column:</span> <code class="bg-white border border-violet-200 px-1 rounded">access_level</code> <span class="text-slate-400 text-[10px]">— ค่า: ADMIN, MANAGER, VIEWER</span></p>
                                    </div>
                                    <div class="mt-2 p-2 bg-white rounded-lg border border-violet-100 text-slate-600">
                                        <strong>UCM sync:</strong> อัปเดต <code class="bg-violet-50 px-1 rounded">staff.access_level</code> ของผู้ใช้ให้ตรงกับ permission ที่ assigned ใน UCM
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ── Example 3: Boolean Matrix ── --}}
                    <div class="mb-4 rounded-2xl border border-emerald-200 overflow-hidden" id="wiz-boolean-matrix">
                        <div class="flex items-center gap-3 px-5 py-3 bg-emerald-50 border-b border-emerald-200">
                            <span class="text-xl">☑️</span>
                            <div>
                                <p class="font-bold text-emerald-900 text-sm">Boolean Matrix — ระบบขนส่งสินค้า (MySQL)</p>
                                <p class="text-xs text-emerald-700">สิทธิ์เป็น TINYINT columns บนตาราง users โดยตรง — ไม่มีตาราง junction แยก</p>
                            </div>
                        </div>
                        <div class="px-5 py-4 bg-white text-xs space-y-3">
                            <div class="bg-slate-50 rounded-xl p-3 border border-slate-200 font-mono text-[11px] leading-relaxed">
                                <p class="font-bold text-slate-500 mb-1 not-italic font-sans">ตาราง <code>cargo_users</code></p>
                                <p><span class="text-sky-600">username</span>             VARCHAR(50) PK</p>
                                <p><span class="text-slate-500">full_name</span>           VARCHAR(100)</p>
                                <p><span class="text-emerald-600">can_view_manifest</span>  TINYINT(1) <span class="text-slate-400">DEFAULT 0</span></p>
                                <p><span class="text-emerald-600">can_edit_manifest</span>  TINYINT(1) <span class="text-slate-400">DEFAULT 0</span></p>
                                <p><span class="text-emerald-600">can_approve_manifest</span> TINYINT(1) <span class="text-slate-400">DEFAULT 0</span></p>
                                <p><span class="text-emerald-600">can_export_report</span>  TINYINT(1) <span class="text-slate-400">DEFAULT 0</span></p>
                                <p><span class="text-emerald-600">can_manage_users</span>   TINYINT(1) <span class="text-slate-400">DEFAULT 0</span></p>
                            </div>
                            <div class="flex gap-3 p-3 bg-emerald-50 rounded-xl border border-emerald-200">
                                <div class="w-6 h-6 bg-emerald-600 text-white text-[10px] font-bold rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">4</div>
                                <div>
                                    <p class="font-semibold text-emerald-900 mb-2">Permission Mode → Boolean Matrix</p>
                                    <div class="space-y-0.5 text-slate-700 mb-2">
                                        <p><span class="text-slate-400">ตาราง Users:</span> <code class="bg-white border border-emerald-200 px-1 rounded">cargo_users</code></p>
                                        <p><span class="text-slate-400">Identifier:</span> <code class="bg-white border border-emerald-200 px-1 rounded">username</code></p>
                                    </div>
                                    <p class="font-semibold text-emerald-800 mb-1">รายการคอลัมน์ (กรอกในช่อง textarea ทีละบรรทัด)</p>
                                    <div class="bg-slate-900 rounded-lg px-3 py-2 font-mono text-[11px] text-emerald-300 leading-relaxed">
                                        <p>can_view_manifest</p>
                                        <p>can_edit_manifest</p>
                                        <p>can_approve_manifest</p>
                                        <p>can_export_report</p>
                                        <p>can_manage_users</p>
                                    </div>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div class="bg-emerald-50 rounded-lg p-2.5 border border-emerald-200">
                                    <p class="font-semibold text-emerald-800 mb-1">เมื่อ Assign สิทธิ์</p>
                                    <p class="text-slate-600">UCM รัน <code class="bg-white px-1 rounded">UPDATE cargo_users SET can_view_manifest=1, can_edit_manifest=1, can_approve_manifest=0, ... WHERE username=?</code> — อัปเดตทุก column ในครั้งเดียว</p>
                                </div>
                                <div class="bg-slate-50 rounded-lg p-2.5 border border-slate-200">
                                    <p class="font-semibold text-slate-700 mb-1">Permissions ที่แสดงใน UCM</p>
                                    <p class="text-slate-600">Permission key = ชื่อคอลัมน์โดยตรง เช่น <code class="bg-white px-1 rounded">can_view_manifest</code>, <code class="bg-white px-1 rounded">can_manage_users</code></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ── Example 4: Group Inheritance ── --}}
                    <div class="mb-4 rounded-2xl border border-sky-200 overflow-hidden" id="wiz-group-inheritance">
                        <div class="flex items-center gap-3 px-5 py-3 bg-sky-50 border-b border-sky-200">
                            <span class="text-xl">👥</span>
                            <div>
                                <p class="font-bold text-sky-900 text-sm">Group Inheritance — ระบบ HR (MySQL)</p>
                                <p class="text-xs text-sky-700">สิทธิ์ถ่ายทอดผ่านกลุ่ม — users → employee_roles (via) → role_permissions (perms)</p>
                            </div>
                        </div>
                        <div class="px-5 py-4 bg-white text-xs space-y-3">
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <div class="bg-slate-50 rounded-xl p-3 border border-slate-200 font-mono text-[11px] leading-relaxed">
                                    <p class="font-bold text-slate-500 mb-1 not-italic font-sans">ตาราง <code>employees</code></p>
                                    <p><span class="text-sky-600">username</span>   VARCHAR(50)</p>
                                    <p><span class="text-slate-500">full_name</span> VARCHAR(100)</p>
                                </div>
                                <div class="bg-sky-50 rounded-xl p-3 border border-sky-200 font-mono text-[11px] leading-relaxed">
                                    <p class="font-bold text-sky-700 mb-1 not-italic font-sans">ตาราง <code>employee_roles</code> <span class="text-[10px] font-bold text-sky-600 not-italic">(via)</span></p>
                                    <p><span class="text-sky-600">username</span>   VARCHAR(50) FK→employees</p>
                                    <p><span class="text-violet-600">role_code</span> VARCHAR(50) FK→role_permissions</p>
                                </div>
                                <div class="bg-slate-50 rounded-xl p-3 border border-slate-200 font-mono text-[11px] leading-relaxed">
                                    <p class="font-bold text-slate-500 mb-1 not-italic font-sans">ตาราง <code>role_permissions</code></p>
                                    <p><span class="text-violet-600">role_code</span>  VARCHAR(50)</p>
                                    <p><span class="text-indigo-600">perm_code</span>  VARCHAR(50) <span class="text-slate-400">(ค่าสิทธิ์)</span></p>
                                </div>
                            </div>
                            <div class="flex gap-3 p-3 bg-sky-50 rounded-xl border border-sky-200">
                                <div class="w-6 h-6 bg-sky-600 text-white text-[10px] font-bold rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">4</div>
                                <div>
                                    <p class="font-semibold text-sky-900 mb-2">Permission Mode → Group Inheritance</p>
                                    <div class="space-y-0.5 text-slate-700">
                                        <p><span class="text-slate-400">ตาราง Users:</span> <code class="bg-white border border-sky-200 px-1 rounded">employees</code> &nbsp; Identifier: <code class="bg-white border border-sky-200 px-1 rounded">username</code></p>
                                        <p><span class="text-slate-400">Via Table:</span> <code class="bg-white border border-sky-200 px-1 rounded">employee_roles</code> <span class="text-slate-400 text-[10px]">— ตาราง membership user ↔ group</span></p>
                                        <p><span class="text-slate-400">FK → User ใน Via:</span> <code class="bg-white border border-sky-200 px-1 rounded">username</code></p>
                                        <p><span class="text-slate-400">FK → Group ใน Via:</span> <code class="bg-white border border-sky-200 px-1 rounded">role_code</code></p>
                                        <p><span class="text-slate-400">Perm Table:</span> <code class="bg-white border border-sky-200 px-1 rounded">role_permissions</code> <span class="text-slate-400 text-[10px]">— ตาราง group ↔ permission</span></p>
                                        <p><span class="text-slate-400">FK → Group ใน Perm:</span> <code class="bg-white border border-sky-200 px-1 rounded">role_code</code></p>
                                        <p><span class="text-slate-400">Permission Value Col:</span> <code class="bg-white border border-sky-200 px-1 rounded">perm_code</code></p>
                                    </div>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div class="bg-sky-50 rounded-lg p-2.5 border border-sky-200">
                                    <p class="font-semibold text-sky-800 mb-1">เมื่อ Assign permission</p>
                                    <p class="text-slate-600">UCM จะ<strong>จัดการ group membership</strong> ใน via table (<code class="bg-white px-1 rounded">employee_roles</code>) — INSERT/DELETE แถว group ที่จำเป็นเพื่อให้ user ได้รับ permissions ที่ต้องการ</p>
                                </div>
                                <div class="bg-slate-50 rounded-lg p-2.5 border border-slate-200">
                                    <p class="font-semibold text-slate-700 mb-1">Permission key ใน UCM</p>
                                    <p class="text-slate-600">UCM จะ discover group/role ที่มีอยู่ใน <code class="bg-white px-1 rounded">role_permissions</code> และแสดงเป็น assignable permissions เช่น <code class="bg-white px-1 rounded">VIEWER</code>, <code class="bg-white px-1 rounded">ADMIN</code></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ── Example 5: Manual ── --}}
                    <div class="rounded-2xl border border-amber-200 overflow-hidden">
                        <div class="flex items-center gap-3 px-5 py-3 bg-amber-50 border-b border-amber-200">
                            <span class="text-xl">✍️</span>
                            <div>
                                <p class="font-bold text-amber-900 text-sm">Manual — Reporting Portal (MySQL)</p>
                                <p class="text-xs text-amber-700">ไม่มีตาราง permission ในฐานข้อมูล ตรวจสอบสิทธิ์ผ่าน UCM API โดยตรง</p>
                            </div>
                        </div>
                        <div class="px-5 py-4 bg-white text-xs space-y-3">
                            <div class="bg-slate-50 rounded-xl p-3 border border-slate-200 font-mono text-[11px] leading-relaxed">
                                <p class="font-bold text-slate-500 mb-1 not-italic font-sans">ตาราง <code>portal_users</code></p>
                                <p><span class="text-sky-600">login_name</span> VARCHAR(50) UNIQUE</p>
                                <p><span class="text-slate-500">full_name</span>  VARCHAR(100)</p>
                                <p><span class="text-slate-500">dept_code</span>  VARCHAR(20)</p>
                                <p><span class="text-amber-600 text-[10px]">-- ไม่มีตาราง permission -- ระบบดึงสิทธิ์จาก UCM API --</span></p>
                            </div>
                            <div class="flex gap-3 p-3 bg-amber-50 rounded-xl border border-amber-200">
                                <div class="w-6 h-6 bg-amber-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">4</div>
                                <div class="w-full">
                                    <p class="font-semibold text-amber-900 mb-2">Permission Mode → Manual (กรอก JSON)</p>
                                    <div class="bg-slate-900 rounded-xl p-3 font-mono text-[11px] text-emerald-300 leading-relaxed overflow-x-auto">
                                        <p>[</p>
                                        <p>&nbsp;&nbsp;{"{"}"key": "VIEW_REPORT",&nbsp;&nbsp;"label": "ดูรายงาน",&nbsp;&nbsp;&nbsp;"group": "รายงาน"{"}"} ,</p>
                                        <p>&nbsp;&nbsp;{"{"}"key": "EXPORT_DATA",&nbsp;"label": "ส่งออกข้อมูล","group": "รายงาน"{"}"} ,</p>
                                        <p>&nbsp;&nbsp;{"{"}"key": "MANAGE_DASH",&nbsp;&nbsp;"label": "จัดการ Dashboard","group": "ผู้ดูแล"{"}"}</p>
                                        <p>]</p>
                                    </div>
                                    <div class="mt-2 p-2 bg-amber-100 rounded-lg border border-amber-200 text-amber-800">
                                        UCM <strong>ไม่ส่ง</strong> ค่าเหล่านี้ไปยังฐานข้อมูลปลายทาง — Reporting Portal ต้อง query UCM API เองเพื่อตรวจสอบสิทธิ์
                                    </div>
                                </div>
                            </div>
                            <div class="bg-slate-50 rounded-xl p-3 border border-slate-200">
                                <p class="font-semibold text-slate-700 mb-2">ตัวอย่าง API call จาก Reporting Portal</p>
                                <div class="bg-slate-900 rounded-xl p-3 font-mono text-[11px] text-sky-300 leading-relaxed overflow-x-auto">
                                    <p>GET /api/users/{"{"}username{"}"}/permissions?system=reporting-portal</p>
                                    <p>Authorization: Bearer &lt;UCM_API_TOKEN&gt;</p>
                                    <br/>
                                    <p><span class="text-slate-400">// Response</span></p>
                                    <p>{"{"} "permissions": ["VIEW_REPORT", "EXPORT_DATA"] {"}"}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ── Multi-Table JOIN ── --}}
                <div id="wiz-multi-table" class="border-t border-slate-100 pt-5">
                    <h3 class="font-bold text-slate-900 mb-1">Multi-Table JOIN (Step 3)</h3>
                    <p class="text-xs text-slate-500 mb-4">ใช้เมื่อข้อมูลผู้ใช้กระจายอยู่ในหลายตาราง เช่น ตาราง <code class="font-mono bg-slate-100 px-1 rounded">employees</code> JOIN กับ <code class="font-mono bg-slate-100 px-1 rounded">employee_profiles</code> — ระบบจะ build <code class="font-mono bg-slate-100 px-1 rounded">FROM … LEFT JOIN …</code> ให้อัตโนมัติ</p>

                    {{-- อธิบาย concept --}}
                    <div class="bg-slate-50 rounded-xl border border-slate-200 p-4 mb-4 text-xs">
                        <p class="font-semibold text-slate-800 mb-2">หลักการทำงาน</p>
                        <div class="space-y-2 text-slate-600">
                            <div class="flex items-start gap-2">
                                <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-100 text-indigo-700 flex-shrink-0 mt-0.5">ตารางหลัก (แถวแรก)</span>
                                <p>ตารางที่อยู่ใน <code class="font-mono bg-white px-1 rounded border border-slate-200">FROM</code> clause — คอลัมน์ Identifier, PK, ชื่อ, Email, แผนก, สถานะ ดึงจากตารางนี้เป็นหลัก</p>
                            </div>
                            <div class="flex items-start gap-2">
                                <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold bg-violet-100 text-violet-700 flex-shrink-0 mt-0.5">ตาราง JOIN (แถวถัดไป)</span>
                                <p>ตารางที่ต้องการนำข้อมูลเพิ่มเติมมาจาก — แต่ละแถวต้องระบุ <strong>Alias</strong>, <strong>JOIN Type</strong> และ <strong>ON clause</strong></p>
                            </div>
                        </div>
                        <div class="mt-3 bg-slate-900 text-emerald-300 font-mono rounded-xl px-4 py-3 text-[11px] leading-relaxed overflow-x-auto">
                            <p class="text-slate-400">-- SQL ที่ระบบสร้างให้อัตโนมัติ (ตัวอย่าง 2 ตาราง) --</p>
                            <p>SELECT emp.*, ep.photo_url, ep.phone</p>
                            <p>FROM <span class="text-sky-300">employees</span> AS emp</p>
                            <p>LEFT JOIN <span class="text-violet-300">employee_profiles</span> AS ep</p>
                            <p>&nbsp;&nbsp;ON emp.emp_code = ep.emp_code</p>
                        </div>
                    </div>

                    {{-- วิธีเพิ่ม JOIN --}}
                    <div class="bg-indigo-50 border border-indigo-200 rounded-xl p-4 text-xs mb-4">
                        <p class="font-semibold text-indigo-900 mb-2">วิธีเพิ่มตาราง JOIN ใน Wizard</p>
                        <ol class="space-y-2 text-slate-700 list-decimal list-inside">
                            <li>ใน Step 3 เลือก <strong>ตารางหลัก</strong> จาก dropdown ตามปกติ กรอก Alias (ไม่บังคับ — ค่า default คือชื่อตาราง)</li>
                            <li>กด <strong>"+ เพิ่ม JOIN"</strong> ด้านล่างรายการตาราง — การ์ด JOIN ใหม่จะปรากฏขึ้น</li>
                            <li>เลือก <strong>ตาราง</strong> ที่ต้องการ JOIN จาก dropdown</li>
                            <li>กรอก <strong>Alias</strong> สำหรับตาราง JOIN (บังคับ เมื่อมีหลายตาราง) เช่น <code class="font-mono bg-white px-1 rounded border border-slate-200">ep</code></li>
                            <li>เลือก <strong>JOIN Type</strong>: LEFT JOIN (ค่า default), INNER JOIN หรือ RIGHT JOIN</li>
                            <li>ระบุ <strong>ON clause</strong>: คอลัมน์จากตารางหลัก (local) และ คอลัมน์จากตาราง JOIN (remote) ที่ใช้เชื่อมกัน</li>
                            <li>ทำซ้ำได้หลายครั้งหากต้องการ JOIN มากกว่า 2 ตาราง</li>
                        </ol>
                        <div class="mt-3 flex items-start gap-2 p-2 bg-amber-50 border border-amber-200 rounded-lg text-amber-800">
                            <svg class="w-3.5 h-3.5 text-amber-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            <p>ON clause ของทุกแถว JOIN <strong>บังคับ</strong> — ถ้าไม่ระบุ Wizard จะไม่ยอมให้ไป Step ถัดไป</p>
                        </div>
                    </div>

                    {{-- ตัวอย่าง --}}
                    <div class="rounded-2xl border border-indigo-200 overflow-hidden mb-4">
                        <div class="flex items-center gap-3 px-5 py-3 bg-indigo-50 border-b border-indigo-200">
                            <span class="text-xl">🔀</span>
                            <div>
                                <p class="font-bold text-indigo-900 text-sm">ตัวอย่าง: employees JOIN employee_profiles (MySQL)</p>
                                <p class="text-xs text-indigo-600">ชื่อ-อีเมลอยู่ที่ employees, เบอร์โทร-รูปอยู่ที่ employee_profiles</p>
                            </div>
                        </div>
                        <div class="px-5 py-4 bg-white text-xs space-y-3">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div class="bg-slate-50 rounded-xl p-3 border border-slate-200 font-mono text-[11px] leading-relaxed">
                                    <p class="font-bold text-slate-500 mb-1 not-italic font-sans">ตาราง <code>employees</code></p>
                                    <p><span class="text-sky-600">emp_code</span>   VARCHAR(20) PK</p>
                                    <p><span class="text-slate-500">full_name</span> VARCHAR(100)</p>
                                    <p><span class="text-slate-500">email</span>     VARCHAR(100)</p>
                                    <p><span class="text-slate-500">is_active</span> TINYINT</p>
                                </div>
                                <div class="bg-slate-50 rounded-xl p-3 border border-slate-200 font-mono text-[11px] leading-relaxed">
                                    <p class="font-bold text-slate-500 mb-1 not-italic font-sans">ตาราง <code>employee_profiles</code></p>
                                    <p><span class="text-sky-600">emp_code</span>   VARCHAR(20) FK→employees</p>
                                    <p><span class="text-slate-500">phone</span>     VARCHAR(20)</p>
                                    <p><span class="text-slate-500">photo_url</span> VARCHAR(255)</p>
                                    <p><span class="text-slate-500">dept_code</span> VARCHAR(50)</p>
                                </div>
                            </div>
                            <div class="flex gap-3 p-3 bg-indigo-50 rounded-xl border border-indigo-200">
                                <div class="w-6 h-6 bg-indigo-600 text-white text-[10px] font-bold rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">3</div>
                                <div class="space-y-1.5 text-slate-700 w-full">
                                    <p class="font-semibold text-indigo-900 mb-1">การตั้งค่า Step 3</p>
                                    <div class="bg-white rounded-lg border border-indigo-100 overflow-hidden">
                                        <table class="w-full text-[11px]">
                                            <tbody class="divide-y divide-slate-100">
                                                <tr class="bg-indigo-50/50">
                                                    <td class="px-3 py-1.5 font-bold text-indigo-700 w-28">แถวหลัก</td>
                                                    <td class="px-3 py-1.5"><span class="text-slate-500">ตาราง:</span> <code class="bg-indigo-50 px-1 rounded">employees</code> &nbsp; <span class="text-slate-500">Alias:</span> <code class="bg-indigo-50 px-1 rounded">emp</code></td>
                                                </tr>
                                                <tr>
                                                    <td class="px-3 py-1.5 font-bold text-violet-700">JOIN แถว 1</td>
                                                    <td class="px-3 py-1.5"><span class="text-slate-500">ตาราง:</span> <code class="bg-violet-50 px-1 rounded">employee_profiles</code> &nbsp; <span class="text-slate-500">Alias:</span> <code class="bg-violet-50 px-1 rounded">ep</code> &nbsp; <span class="text-slate-500">Type:</span> LEFT JOIN</td>
                                                </tr>
                                                <tr>
                                                    <td class="px-3 py-1.5 font-semibold text-slate-500 pl-8">ON clause</td>
                                                    <td class="px-3 py-1.5"><span class="text-slate-500">Local col:</span> <code class="bg-slate-100 px-1 rounded">emp_code</code> &nbsp; <span class="text-slate-500">Remote col:</span> <code class="bg-slate-100 px-1 rounded">emp_code</code></td>
                                                </tr>
                                                <tr class="bg-slate-50/50">
                                                    <td class="px-3 py-1.5 font-semibold text-slate-600" colspan="2">Identifier: <code class="bg-slate-100 px-1 rounded">emp_code</code> &nbsp;|&nbsp; ชื่อ: <code class="bg-slate-100 px-1 rounded">full_name</code> &nbsp;|&nbsp; Email: <code class="bg-slate-100 px-1 rounded">email</code> &nbsp;|&nbsp; แผนก: <code class="bg-slate-100 px-1 rounded">dept_code</code> &nbsp;|&nbsp; สถานะ: <code class="bg-slate-100 px-1 rounded">is_active</code></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-start gap-3 p-3.5 bg-sky-50 border border-sky-200 rounded-xl text-xs text-sky-800">
                        <svg class="w-4 h-4 text-sky-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <div class="space-y-1">
                            <p><strong>INSERT / UPDATE</strong> จะเขียนเฉพาะ <strong>ตารางหลัก</strong> เสมอ — JOIN table เป็น read-only ใน Wizard (SQL ไม่รองรับ UPDATE หลายตาราง ผ่าน JOIN โดยตรง)</p>
                            <p>ปุ่ม <strong>"ดูตัวอย่างข้อมูล"</strong> ใน Step 3 จะรัน SELECT ที่มี JOIN จริง ช่วยยืนยันว่า ON clause ถูกต้องก่อนบันทึก</p>
                        </div>
                    </div>
                </div>

                {{-- ── Mixed Mode ── --}}
                <div id="wiz-mixed-mode" class="border-t border-slate-100 pt-5">
                    <h3 class="font-bold text-slate-900 mb-1">Junction + Column (Mixed Mode) (Step 4)</h3>
                    <p class="text-xs text-slate-500 mb-4">ใช้เมื่อระบบภายนอกเก็บสิทธิ์ <strong>สองระดับพร้อมกัน</strong> — ตาราง junction สำหรับสิทธิ์หลัก (multi-row) และคอลัมน์บนตาราง user สำหรับ role หลัก (single value)</p>

                    {{-- ตัวอย่างสถานการณ์ --}}
                    <div class="bg-rose-50 rounded-xl border border-rose-200 p-4 mb-4 text-xs">
                        <p class="font-semibold text-rose-900 mb-2">ตัวอย่างสถานการณ์ที่เหมาะกับ Mixed Mode</p>
                        <div class="bg-slate-900 rounded-xl p-3 font-mono text-[11px] text-emerald-300 leading-relaxed overflow-x-auto mb-2">
                            <p class="text-slate-400">-- ตาราง users (role หลักอยู่ที่คอลัมน์ primary_role) --</p>
                            <p>CREATE TABLE users (</p>
                            <p>&nbsp;&nbsp;user_id      INT PK,</p>
                            <p>&nbsp;&nbsp;username     VARCHAR(50),</p>
                            <p>&nbsp;&nbsp;<span class="text-rose-300">primary_role VARCHAR(20)</span>  <span class="text-slate-400">-- 'ADMIN' | 'MANAGER' | 'VIEWER'</span></p>
                            <p>);</p>
                            <br/>
                            <p class="text-slate-400">-- ตาราง user_extra_perms (สิทธิ์เสริม — junction) --</p>
                            <p>CREATE TABLE user_extra_perms (</p>
                            <p>&nbsp;&nbsp;user_id  INT FK→users,</p>
                            <p>&nbsp;&nbsp;perm_key VARCHAR(50)  <span class="text-slate-400">-- 'EXPORT_PDF', 'MANAGE_USERS'</span></p>
                            <p>);</p>
                        </div>
                        <p class="text-slate-600">ในกรณีนี้ <code class="font-mono bg-white px-1 rounded border border-rose-200">primary_role</code> ใช้ <strong>Single Column</strong> แต่ <code class="font-mono bg-white px-1 rounded border border-rose-200">user_extra_perms</code> ใช้ <strong>Junction</strong> — Mixed Mode รองรับทั้งคู่พร้อมกัน</p>
                    </div>

                    {{-- วิธีตั้งค่า --}}
                    <div class="bg-rose-50 border border-rose-200 rounded-xl p-4 text-xs mb-4">
                        <p class="font-semibold text-rose-900 mb-2">วิธีตั้งค่า Mixed Mode ใน Wizard</p>
                        <ol class="space-y-2 text-slate-700 list-decimal list-inside">
                            <li>ใน Step 4 เลือก permission card <strong>"Junction + Column (Mixed)"</strong></li>
                            <li>กรอก <strong>ฝั่ง Junction</strong> ตามปกติ: ตาราง junction, User FK column, Value column (รองรับ Composite ด้วย)</li>
                            <li>กรอก <strong>ฝั่ง Column</strong>:
                                <ul class="list-disc list-inside ml-4 mt-1 space-y-0.5">
                                    <li><strong>ตาราง</strong> ที่มีคอลัมน์ role (ปกติคือตาราง users หลัก)</li>
                                    <li><strong>Identifier column</strong> ในตาราง column-side ที่ตรงกับ user identifier</li>
                                    <li><strong>Value column</strong> คอลัมน์ที่เก็บค่า role เช่น <code class="font-mono bg-white px-1 rounded border border-rose-200">primary_role</code></li>
                                    <li><strong>ตัวเลือกที่เป็นไปได้</strong> (Col Options) — เพิ่มแต่ละค่าที่ valid ด้วยปุ่ม <strong>"+ เพิ่มตัวเลือก"</strong> เช่น ADMIN, MANAGER, VIEWER</li>
                                </ul>
                            </li>
                        </ol>
                    </div>

                    {{-- prefix col: --}}
                    <div class="rounded-xl border border-slate-200 overflow-hidden mb-4">
                        <div class="px-4 py-2.5 bg-slate-50 border-b border-slate-200">
                            <p class="font-bold text-slate-800 text-xs">รูปแบบ Permission Key ใน UCM สำหรับ Mixed Mode</p>
                        </div>
                        <div class="px-4 py-3 text-xs space-y-2">
                            <p class="text-slate-600">UCM จะ<strong>แยก</strong> permission ฝั่ง junction และฝั่ง column ออกจากกันด้วย prefix <code class="font-mono bg-rose-100 text-rose-700 px-1 rounded">col:</code>:</p>
                            <div class="bg-slate-900 text-emerald-400 font-mono rounded-lg px-4 py-3 text-[11px] leading-relaxed">
                                <p class="text-slate-400"># Permission Keys ที่แสดงใน UCM</p>
                                <p>EXPORT_PDF      <span class="text-slate-400">← junction side (ค่าดิบจาก user_extra_perms.perm_key)</span></p>
                                <p>MANAGE_USERS    <span class="text-slate-400">← junction side</span></p>
                                <p><span class="text-rose-400">col:</span>ADMIN        <span class="text-slate-400">← column side (prefix "col:" + ค่า primary_role)</span></p>
                                <p><span class="text-rose-400">col:</span>MANAGER      <span class="text-slate-400">← column side</span></p>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-2">
                                <div class="bg-indigo-50 rounded-lg p-2.5 border border-indigo-200">
                                    <p class="font-semibold text-indigo-800 mb-1">เมื่อ Assign จาก UCM</p>
                                    <p class="text-slate-600">Assign <code class="bg-white px-1 rounded">EXPORT_PDF</code> → INSERT แถวใน junction<br>Assign <code class="bg-white px-1 rounded">col:ADMIN</code> → UPDATE <code class="bg-white px-1 rounded">primary_role = 'ADMIN'</code></p>
                                </div>
                                <div class="bg-rose-50 rounded-lg p-2.5 border border-rose-200">
                                    <p class="font-semibold text-rose-800 mb-1">เมื่อ Revoke จาก UCM</p>
                                    <p class="text-slate-600">Revoke <code class="bg-white px-1 rounded">EXPORT_PDF</code> → DELETE แถวจาก junction<br>Revoke <code class="bg-white px-1 rounded">col:ADMIN</code> → UPDATE <code class="bg-white px-1 rounded">primary_role = NULL</code></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-start gap-3 p-3.5 bg-amber-50 border border-amber-200 rounded-xl text-xs text-amber-800">
                        <svg class="w-4 h-4 text-amber-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                        <div class="space-y-1">
                            <p>ผู้ใช้ assign <code class="bg-amber-100 px-1 rounded">col:</code> ได้แค่ <strong>1 ค่าต่อครั้ง</strong> เพราะเป็น single column — assign ค่าใหม่จะ <strong>แทนที่</strong> ค่าเดิม</p>
                            <p>ต้องเพิ่ม Col Options อย่างน้อย 1 ค่า ก่อนจะบันทึก Wizard ได้</p>
                        </div>
                    </div>
                </div>

                {{-- ── Advanced Junction Options ── --}}
                <div id="wiz-advanced-junction" class="border-t border-slate-100 pt-5">
                    <h3 class="font-bold text-slate-900 mb-1">Advanced Junction Options (Step 4)</h3>
                    <p class="text-xs text-slate-500 mb-4">ตัวเลือกเสริมสำหรับ <strong>Junction</strong> และ <strong>Mixed</strong> mode — เปิดใช้งานได้ผ่าน Checkbox ใน Step 4 รองรับ 3 รูปแบบ</p>

                    {{-- Soft-Delete --}}
                    <div class="mb-4 rounded-2xl border border-amber-200 overflow-hidden">
                        <div class="flex items-center gap-3 px-5 py-3 bg-amber-50 border-b border-amber-200">
                            <span class="text-xl">🗑️</span>
                            <div>
                                <p class="font-bold text-amber-900 text-sm">Soft-Delete Junction</p>
                                <p class="text-xs text-amber-700">แทนการ DELETE แถว — อัปเดต active flag column แทน เพื่อเก็บ audit trail</p>
                            </div>
                        </div>
                        <div class="px-5 py-4 bg-white text-xs space-y-3">
                            <div class="bg-slate-50 rounded-xl p-3 border border-slate-200 font-mono text-[11px] leading-relaxed">
                                <p class="font-bold text-slate-500 mb-1 not-italic font-sans">ตาราง <code>staff_access</code> (junction ที่มี soft-delete)</p>
                                <p><span class="text-sky-600">username</span>    VARCHAR(50) FK→staff</p>
                                <p><span class="text-indigo-600">access_code</span> VARCHAR(50)</p>
                                <p><span class="text-amber-600">is_active</span>   TINYINT(1) <span class="text-slate-400">-- 1=active, 0=revoked</span></p>
                            </div>
                            <div class="bg-amber-50 rounded-xl p-3 border border-amber-200">
                                <p class="font-semibold text-amber-900 mb-2">ฟิลด์ที่ต้องกรอกเมื่อเปิด "ใช้ Soft-Delete"</p>
                                <div class="space-y-1 text-slate-700">
                                    <p><span class="text-slate-400 w-36 inline-block">Active Column:</span> <code class="bg-white border border-amber-200 px-1 rounded">is_active</code> — ชื่อคอลัมน์ flag</p>
                                    <p><span class="text-slate-400 w-36 inline-block">Active Value:</span> <code class="bg-white border border-amber-200 px-1 rounded">1</code> — ค่าที่หมายถึง "มีสิทธิ์"</p>
                                    <p><span class="text-slate-400 w-36 inline-block">Inactive Value:</span> <code class="bg-white border border-amber-200 px-1 rounded">0</code> — ค่าที่หมายถึง "ถูกยกเลิก"</p>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div class="bg-emerald-50 rounded-lg p-2.5 border border-emerald-200">
                                    <p class="font-semibold text-emerald-800 mb-1">เมื่อ Assign</p>
                                    <p class="text-slate-600">ถ้ามีแถวอยู่แล้ว → <code class="bg-white px-1 rounded">UPDATE … SET is_active=1</code><br>ถ้ายังไม่มี → INSERT แถวใหม่พร้อม <code class="bg-white px-1 rounded">is_active=1</code></p>
                                </div>
                                <div class="bg-rose-50 rounded-lg p-2.5 border border-rose-200">
                                    <p class="font-semibold text-rose-800 mb-1">เมื่อ Revoke</p>
                                    <p class="text-slate-600"><code class="bg-white px-1 rounded">UPDATE … SET is_active=0</code> — <strong>ไม่ลบแถว</strong> ประวัติยังคงอยู่</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Time-Bound --}}
                    <div class="mb-4 rounded-2xl border border-cyan-200 overflow-hidden">
                        <div class="flex items-center gap-3 px-5 py-3 bg-cyan-50 border-b border-cyan-200">
                            <span class="text-xl">⏰</span>
                            <div>
                                <p class="font-bold text-cyan-900 text-sm">Time-Bounded Junction</p>
                                <p class="text-xs text-cyan-700">กรองเฉพาะแถวที่ยังไม่หมดอายุ — ใช้คอลัมน์ <code class="font-mono bg-white px-1 rounded">valid_from</code> / <code class="font-mono bg-white px-1 rounded">valid_to</code></p>
                            </div>
                        </div>
                        <div class="px-5 py-4 bg-white text-xs space-y-3">
                            <div class="bg-slate-50 rounded-xl p-3 border border-slate-200 font-mono text-[11px] leading-relaxed">
                                <p class="font-bold text-slate-500 mb-1 not-italic font-sans">ตาราง <code>user_roles</code> (junction ที่มีวันหมดอายุ)</p>
                                <p><span class="text-sky-600">username</span>   VARCHAR(50) FK→users</p>
                                <p><span class="text-indigo-600">role_code</span>  VARCHAR(50)</p>
                                <p><span class="text-cyan-600">valid_from</span>  DATE</p>
                                <p><span class="text-cyan-600">valid_to</span>    DATE</p>
                            </div>
                            <div class="bg-cyan-50 rounded-xl p-3 border border-cyan-200">
                                <p class="font-semibold text-cyan-900 mb-2">ฟิลด์ที่ต้องกรอกเมื่อเปิด "มีวันหมดอายุ"</p>
                                <div class="space-y-1 text-slate-700">
                                    <p><span class="text-slate-400 w-36 inline-block">Valid From Column:</span> <code class="bg-white border border-cyan-200 px-1 rounded">valid_from</code> — คอลัมน์วันเริ่มต้น (ไม่บังคับ)</p>
                                    <p><span class="text-slate-400 w-36 inline-block">Valid To Column:</span> <code class="bg-white border border-cyan-200 px-1 rounded">valid_to</code> — คอลัมน์วันสิ้นสุด (ไม่บังคับ)</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-2 p-3 bg-sky-50 border border-sky-200 rounded-lg text-sky-800">
                                <svg class="w-4 h-4 text-sky-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <p>เมื่อดึง permissions ระบบจะเพิ่ม <code class="bg-white px-1 rounded">WHERE NOW() BETWEEN valid_from AND valid_to</code> อัตโนมัติ — แถวที่หมดอายุจะไม่ถูกนับ</p>
                            </div>
                        </div>
                    </div>

                    {{-- Insert Metadata --}}
                    <div class="rounded-2xl border border-violet-200 overflow-hidden">
                        <div class="flex items-center gap-3 px-5 py-3 bg-violet-50 border-b border-violet-200">
                            <span class="text-xl">📝</span>
                            <div>
                                <p class="font-bold text-violet-900 text-sm">Insert Metadata</p>
                                <p class="text-xs text-violet-700">เพิ่มคอลัมน์ metadata อัตโนมัติเมื่อ INSERT แถวใหม่ เช่น ใครอนุมัติ + เวลาที่อนุมัติ</p>
                            </div>
                        </div>
                        <div class="px-5 py-4 bg-white text-xs space-y-3">
                            <div class="bg-slate-50 rounded-xl p-3 border border-slate-200 font-mono text-[11px] leading-relaxed">
                                <p class="font-bold text-slate-500 mb-1 not-italic font-sans">ตาราง <code>user_grants</code> (junction ที่มี metadata)</p>
                                <p><span class="text-sky-600">username</span>    VARCHAR(50) FK→users</p>
                                <p><span class="text-indigo-600">grant_code</span>  VARCHAR(50)</p>
                                <p><span class="text-violet-600">granted_by</span>  VARCHAR(50) <span class="text-slate-400">-- username ของ admin</span></p>
                                <p><span class="text-violet-600">granted_at</span>  DATETIME <span class="text-slate-400">-- เวลาที่ grant</span></p>
                            </div>
                            <div class="bg-violet-50 rounded-xl p-3 border border-violet-200">
                                <p class="font-semibold text-violet-900 mb-2">วิธีตั้งค่า — เพิ่มแถว Column → Token</p>
                                <div class="overflow-x-auto">
                                    <table class="w-full text-[11px] border border-violet-200 rounded-lg overflow-hidden">
                                        <thead class="bg-violet-100">
                                            <tr>
                                                <th class="px-3 py-1.5 text-left font-semibold text-violet-800">ชื่อคอลัมน์ใน DB</th>
                                                <th class="px-3 py-1.5 text-left font-semibold text-violet-800">Token / ค่า</th>
                                                <th class="px-3 py-1.5 text-left font-semibold text-violet-800">ผลลัพธ์</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-violet-100 bg-white">
                                            <tr>
                                                <td class="px-3 py-1.5 font-mono text-violet-700">granted_by</td>
                                                <td class="px-3 py-1.5 font-mono text-slate-700">__ucm_admin__</td>
                                                <td class="px-3 py-1.5 text-slate-600">username ของ Admin ที่กำลัง sync</td>
                                            </tr>
                                            <tr>
                                                <td class="px-3 py-1.5 font-mono text-violet-700">granted_at</td>
                                                <td class="px-3 py-1.5 font-mono text-slate-700">__now__</td>
                                                <td class="px-3 py-1.5 text-slate-600">timestamp ปัจจุบัน (Y-m-d H:i:s)</td>
                                            </tr>
                                            <tr>
                                                <td class="px-3 py-1.5 font-mono text-violet-700">source_system</td>
                                                <td class="px-3 py-1.5 font-mono text-slate-700">UCM</td>
                                                <td class="px-3 py-1.5 text-slate-600">literal string "UCM" (ค่าคงที่)</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="flex items-start gap-2 p-3 bg-amber-50 border border-amber-200 rounded-lg text-amber-800">
                                <svg class="w-4 h-4 text-amber-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                <p>Metadata ถูกเพิ่มเฉพาะตอน <strong>INSERT แถวใหม่</strong> เท่านั้น — เมื่อ revoke (DELETE) หรือ reactivate แถวเดิม คอลัมน์เหล่านี้จะไม่ถูกแตะ</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Composite Junction --}}
                <div id="wiz-composite" class="border-t border-slate-100 pt-5">
                    <h3 class="font-bold text-slate-900 mb-1">Composite Junction Mode (Step 4)</h3>
                    <p class="text-xs text-slate-500 mb-4">ใช้เมื่อตาราง junction มี Foreign Key มากกว่า 2 ตัว เช่น ระบบที่สิทธิ์ผูกกับทั้ง Role และ Site พร้อมกัน</p>

                    {{-- อธิบาย composite --}}
                    <div class="bg-slate-50 rounded-xl border border-slate-200 p-4 mb-4 text-xs">
                        <p class="font-semibold text-slate-800 mb-2">ตัวอย่าง: ตาราง <code class="font-mono bg-white px-1.5 py-0.5 rounded border border-slate-200">UserGrant(user_id, pg_id, site_id)</code></p>
                        <div class="space-y-1.5 text-slate-600">
                            <div class="flex items-start gap-2">
                                <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold bg-slate-200 text-slate-700 flex-shrink-0">user_fk_col</span>
                                <p><code class="font-mono bg-white px-1 rounded border border-slate-200">user_id</code> — FK ที่ชี้ไปยัง users table</p>
                            </div>
                            <div class="flex items-start gap-2">
                                <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-100 text-indigo-700 flex-shrink-0">value_col</span>
                                <p><code class="font-mono bg-white px-1 rounded border border-slate-200">pg_id</code> — FK แรกที่ไม่ใช่ user FK = ค่าหลักของสิทธิ์ อ้างอิง <code class="font-mono bg-white px-1 rounded border border-slate-200">PageGroups</code></p>
                            </div>
                            <div class="flex items-start gap-2">
                                <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold bg-violet-100 text-violet-700 flex-shrink-0">composite_cols</span>
                                <p><code class="font-mono bg-white px-1 rounded border border-slate-200">site_id</code> → Master Table: <code class="font-mono bg-white px-1 rounded border border-slate-200">Sites</code> — FK เสริมที่ต้องระบุเพิ่ม</p>
                            </div>
                        </div>
                    </div>

                    {{-- Key format --}}
                    <div class="rounded-xl border border-slate-200 overflow-hidden mb-4">
                        <div class="px-4 py-2.5 bg-slate-50 border-b border-slate-200">
                            <p class="font-bold text-slate-800 text-xs">รูปแบบ Permission Key ใน UCM</p>
                        </div>
                        <div class="px-4 py-3 text-xs space-y-2">
                            <p class="text-slate-600">เมื่อใช้ Composite Junction UCM จะสร้าง Permission key เป็น <strong>composite key</strong> โดยใช้ <code class="font-mono bg-slate-100 px-1 rounded">:</code> เป็นตัวคั่น:</p>
                            <div class="bg-slate-900 text-emerald-400 font-mono rounded-lg px-4 py-3 text-[11px] leading-relaxed">
                                <p class="text-slate-400"># value_col = pg_id, composite_cols = [site_id]</p>
                                <p>"5:3"  &nbsp; <span class="text-slate-400">→ pg_id=5, site_id=3</span></p>
                                <p>"12:1" &nbsp; <span class="text-slate-400">→ pg_id=12, site_id=1</span></p>
                            </div>
                            <p class="text-slate-500">ใน UCM จะแสดง Label ที่ดึงมาจาก Master Table แทน key ดิบ เช่น "Report (Bangkok Branch)"</p>
                        </div>
                    </div>

                    {{-- วิธีเพิ่ม composite col --}}
                    <div class="bg-violet-50 border border-violet-200 rounded-xl p-4 text-xs mb-4">
                        <p class="font-semibold text-violet-900 mb-2">วิธีเพิ่ม Composite Column ใน Wizard</p>
                        <ol class="space-y-1.5 text-slate-700 list-decimal list-inside">
                            <li>เลือก <strong>Permission Mode = Junction Table</strong></li>
                            <li>กรอก Perm Table, User FK, Value Column ตามปกติ</li>
                            <li>กด <strong>"+ เพิ่มคอลัมน์เสริม"</strong></li>
                            <li>เลือก <strong>Column</strong> (FK เสริมในตาราง junction) จาก dropdown</li>
                            <li>เลือก <strong>Master Table</strong> ที่ FK นั้นอ้างอิง จาก dropdown</li>
                            <li>เลือก <strong>Label Column</strong> บน Master Table เพื่อแสดงชื่อที่อ่านได้</li>
                        </ol>
                    </div>

                    <div class="flex items-start gap-3 p-3.5 bg-sky-50 border border-sky-200 rounded-xl text-xs text-sky-800">
                        <svg class="w-4 h-4 text-sky-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>AI Analysis สามารถตรวจพบ Composite Junction ได้อัตโนมัติ — หาก AI แนะนำมา กด <strong>"ใช้การตั้งค่านี้"</strong> แล้วระบบจะสร้าง composite rows ให้ทันที</span>
                    </div>
                </div>

                {{-- 2-Way Sync & Delete Mode --}}
                <div id="wiz-2way" class="border-t border-slate-100 pt-5">
                    <h3 class="font-bold text-slate-900 mb-1">2-Way Sync &amp; Delete Mode (Step 5)</h3>
                    <p class="text-xs text-slate-500 mb-4">เปิด 2-Way Sync เมื่อต้องการให้ UCM สร้าง/ลบ Permission Definition ในฐานข้อมูลปลายทางโดยอัตโนมัติ — เลือก Delete Mode ตามนโยบายของระบบ</p>

                    {{-- Enable toggle guide --}}
                    <div class="bg-orange-50 border border-orange-200 rounded-xl p-4 text-xs mb-4">
                        <p class="font-semibold text-slate-800 mb-2">การเปิดใช้งาน 2-Way Sync</p>
                        <div class="space-y-1.5 text-slate-600">
                            <div class="flex items-start gap-2">
                                <span class="inline-block w-3 h-3 rounded-full bg-orange-400 mt-0.5 flex-shrink-0"></span>
                                <p>สลับ Toggle <strong>"เปิด 2-Way Sync"</strong> ให้เป็น ON</p>
                            </div>
                            <div class="flex items-start gap-2">
                                <span class="inline-block w-3 h-3 rounded-full bg-orange-400 mt-0.5 flex-shrink-0"></span>
                                <p>เลือก <strong>ตาราง Permission Definition</strong> — ตารางในระบบปลายทางที่เก็บนิยามของสิทธิ์ เช่น <code class="font-mono bg-white px-1 rounded border border-slate-200">roles</code></p>
                            </div>
                            <div class="flex items-start gap-2">
                                <span class="inline-block w-3 h-3 rounded-full bg-orange-400 mt-0.5 flex-shrink-0"></span>
                                <p>Map คอลัมน์: <strong>Value (บังคับ)</strong>, Primary Key, Label, Group (ไม่บังคับ)</p>
                            </div>
                            <div class="flex items-start gap-2">
                                <span class="inline-block w-3 h-3 rounded-full bg-orange-400 mt-0.5 flex-shrink-0"></span>
                                <p>เลือก <strong>Delete Mode</strong> — พฤติกรรมเมื่อ Admin ลบ Permission ใน UCM (ดูตัวอย่างด้านล่าง)</p>
                            </div>
                        </div>
                        <p class="mt-2 text-slate-500">หากปิด 2-Way Sync: UCM ลบ permission ออกจากตัวเองเท่านั้น (Detach Only) — ไม่แตะฐานข้อมูลปลายทาง</p>
                    </div>

                    {{-- Delete Mode Examples --}}
                    <div class="space-y-4">

                        {{-- Detach Only --}}
                        <div class="rounded-2xl border border-slate-200 overflow-hidden">
                            <div class="flex items-center gap-2 px-4 py-2.5 bg-slate-100 border-b border-slate-200">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-200 text-slate-700">Detach Only</span>
                                <span class="text-xs text-slate-500">— ลบเฉพาะใน UCM ไม่แตะ remote DB</span>
                            </div>
                            <div class="px-4 py-3 text-xs space-y-2 bg-white">
                                <p class="text-slate-600"><strong>สถานการณ์:</strong> ทีม DBA จัดการตาราง <code class="bg-slate-100 px-1 rounded">roles</code> โดยตรง UCM แค่ "รู้จัก" permission เหล่านั้นเพื่อ assign ให้ user</p>
                                <div class="bg-slate-50 rounded-lg p-2.5 border border-slate-200 space-y-1">
                                    <p class="text-slate-600"><span class="text-slate-400">ตาราง Perm Def:</span> <code class="bg-white border border-slate-200 px-1 rounded">roles</code></p>
                                    <p class="text-slate-600"><span class="text-slate-400">Value Column:</span> <code class="bg-white border border-slate-200 px-1 rounded">role_code</code></p>
                                    <p class="text-slate-600"><span class="text-slate-400">Delete Mode:</span> <span class="font-semibold">Detach Only</span></p>
                                </div>
                                <div class="flex gap-4 text-[11px]">
                                    <div class="flex-1 bg-indigo-50 rounded-lg p-2 border border-indigo-100">
                                        <p class="font-semibold text-indigo-800 mb-1">Admin <span class="text-emerald-700">เพิ่ม</span> permission</p>
                                        <p class="text-indigo-700">UCM INSERT แถวใหม่ใน <code class="bg-white px-1 rounded">roles</code> ของ remote DB</p>
                                    </div>
                                    <div class="flex-1 bg-slate-50 rounded-lg p-2 border border-slate-200">
                                        <p class="font-semibold text-slate-700 mb-1">Admin <span class="text-red-600">ลบ</span> permission</p>
                                        <p class="text-slate-600">ลบออกจาก UCM เท่านั้น — <code class="bg-white px-1 rounded">roles</code> ใน remote DB <strong>ยังอยู่ครบ</strong></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Hard Delete --}}
                        <div class="rounded-2xl border border-red-200 overflow-hidden">
                            <div class="flex items-center gap-2 px-4 py-2.5 bg-red-50 border-b border-red-200">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-red-100 text-red-700">Hard Delete</span>
                                <span class="text-xs text-red-600">— ลบถาวรทั้งใน UCM และ remote DB</span>
                            </div>
                            <div class="px-4 py-3 text-xs space-y-2 bg-white">
                                <p class="text-slate-600"><strong>สถานการณ์:</strong> ระบบ Document Management มีตาราง <code class="bg-slate-100 px-1 rounded">document_categories</code> — ต้องการให้ UCM เป็น single source of truth</p>
                                <div class="bg-slate-50 rounded-lg p-2.5 border border-slate-200 space-y-1">
                                    <p class="text-slate-600"><span class="text-slate-400">ตาราง Perm Def:</span> <code class="bg-white border border-slate-200 px-1 rounded">document_categories</code></p>
                                    <p class="text-slate-600"><span class="text-slate-400">Value Column:</span> <code class="bg-white border border-slate-200 px-1 rounded">category_code</code></p>
                                    <p class="text-slate-600"><span class="text-slate-400">PK Column:</span> <code class="bg-white border border-slate-200 px-1 rounded">id</code></p>
                                    <p class="text-slate-600"><span class="text-slate-400">Label Column:</span> <code class="bg-white border border-slate-200 px-1 rounded">category_name</code></p>
                                    <p class="text-slate-600"><span class="text-slate-400">Delete Mode:</span> <span class="font-semibold text-red-700">Hard Delete</span></p>
                                </div>
                                <div class="flex gap-4 text-[11px]">
                                    <div class="flex-1 bg-indigo-50 rounded-lg p-2 border border-indigo-100">
                                        <p class="font-semibold text-indigo-800 mb-1">Admin <span class="text-emerald-700">เพิ่ม</span> permission</p>
                                        <p class="text-indigo-700">UCM INSERT แถวใหม่ใน <code class="bg-white px-1 rounded">document_categories</code></p>
                                    </div>
                                    <div class="flex-1 bg-red-50 rounded-lg p-2 border border-red-200">
                                        <p class="font-semibold text-red-800 mb-1">Admin <span class="text-red-600">ลบ</span> permission</p>
                                        <p class="text-red-700"><code class="bg-white px-1 rounded">DELETE FROM document_categories WHERE category_code = ?</code> — ลบถาวร</p>
                                    </div>
                                </div>
                                <div class="flex items-start gap-2 p-2 bg-red-50 border border-red-200 rounded-lg text-red-800">
                                    <svg class="w-3.5 h-3.5 text-red-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                    <span>ข้อมูลที่ลบด้วย Hard Delete <strong>ไม่สามารถกู้คืนได้</strong> ควรใช้เฉพาะเมื่อไม่มี foreign key ชี้มาที่แถวนั้น</span>
                                </div>
                            </div>
                        </div>

                        {{-- Soft Delete --}}
                        <div class="rounded-2xl border border-amber-200 overflow-hidden">
                            <div class="flex items-center gap-2 px-4 py-2.5 bg-amber-50 border-b border-amber-200">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-700">Soft Delete</span>
                                <span class="text-xs text-amber-700">— ซ่อนใน remote DB โดยอัปเดตคอลัมน์ flag</span>
                            </div>
                            <div class="px-4 py-3 text-xs space-y-2 bg-white">
                                <p class="text-slate-600"><strong>สถานการณ์:</strong> ระบบ ERP มีตาราง <code class="bg-slate-100 px-1 rounded">access_rights</code> ที่ต้องการ Audit Trail — แทนที่จะลบแถว จะ set <code class="bg-slate-100 px-1 rounded">is_active = 0</code></p>
                                <div class="bg-slate-50 rounded-lg p-2.5 border border-slate-200 space-y-1">
                                    <p class="text-slate-600"><span class="text-slate-400">ตาราง Perm Def:</span> <code class="bg-white border border-slate-200 px-1 rounded">access_rights</code></p>
                                    <p class="text-slate-600"><span class="text-slate-400">Value Column:</span> <code class="bg-white border border-slate-200 px-1 rounded">right_code</code></p>
                                    <p class="text-slate-600"><span class="text-slate-400">Delete Mode:</span> <span class="font-semibold text-amber-700">Soft Delete</span></p>
                                    <p class="text-slate-600"><span class="text-slate-400">Soft Delete Column:</span> <code class="bg-white border border-slate-200 px-1 rounded">is_active</code></p>
                                    <p class="text-slate-600"><span class="text-slate-400">Soft Delete Value:</span> <code class="bg-white border border-slate-200 px-1 rounded">0</code></p>
                                </div>
                                <div class="flex gap-4 text-[11px]">
                                    <div class="flex-1 bg-indigo-50 rounded-lg p-2 border border-indigo-100">
                                        <p class="font-semibold text-indigo-800 mb-1">Admin <span class="text-emerald-700">เพิ่ม</span> permission</p>
                                        <p class="text-indigo-700">INSERT แถวใหม่ใน <code class="bg-white px-1 rounded">access_rights</code> โดยตั้ง <code class="bg-white px-1 rounded">is_active = 1</code></p>
                                    </div>
                                    <div class="flex-1 bg-amber-50 rounded-lg p-2 border border-amber-200">
                                        <p class="font-semibold text-amber-800 mb-1">Admin <span class="text-red-600">ลบ</span> permission</p>
                                        <p class="text-amber-700"><code class="bg-white px-1 rounded">UPDATE access_rights SET is_active = 0 WHERE right_code = ?</code> — แถวยังอยู่</p>
                                    </div>
                                </div>
                                <div class="flex items-start gap-2 p-2 bg-amber-50 border border-amber-200 rounded-lg text-amber-800">
                                    <svg class="w-3.5 h-3.5 text-amber-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                                    <span>ระบบปลายทางต้องกรอง <code class="bg-white px-1 rounded">WHERE is_active = 1</code> ในทุก query ที่ดึง permission เอง</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- หลังสร้างแล้วทำอะไรต่อ --}}
                {{-- ── ตัวอย่าง: ระบบ HR ภาครัฐ (Junction Table) ── --}}
                <div id="wiz-example-gov-hr" class="border-t border-slate-100 pt-5">
                    <div class="flex items-center gap-3 mb-1">
                        <h3 class="font-bold text-slate-900 text-base">ตัวอย่างการตั้งค่าแบบครบถ้วน</h3>
                        <span class="text-[11px] font-bold bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded-full">Junction Table Mode</span>
                    </div>
                    <p class="text-xs text-slate-500 mb-5">เชื่อมต่อ <strong class="text-slate-700">ระบบ HR ภาครัฐ</strong> (<code class="font-mono text-slate-700">gov-hr</code>) เข้ากับ UCM ทีละขั้นตอน ตั้งแต่ Step 1 ถึง Step 7 โดยใช้ข้อมูลจริงจากฐานข้อมูล <code class="font-mono text-slate-700">hr_system_db</code> พร้อม 2-Way Sync และ Department Code Mapping</p>

                    {{-- โครงสร้างฐานข้อมูลจริง --}}
                    <div class="mb-6 rounded-2xl border border-slate-200 overflow-hidden">
                        <div class="px-5 py-3 bg-slate-800 flex items-center gap-2">
                            <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582 4-8 4m16 0c0 2.21-3.582 4-8 4"/></svg>
                            <p class="font-bold text-white text-sm">โครงสร้างฐานข้อมูล <code class="font-mono text-emerald-300">hr_system_db</code></p>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 divide-y sm:divide-y-0 sm:divide-x divide-slate-200">
                            @foreach ([
                                [
                                    'table' => 'employees',
                                    'desc'  => 'ตารางพนักงาน (User Table หลัก)',
                                    'color' => 'indigo',
                                    'cols'  => [
                                        ['name' => 'id',         'type' => 'INT PK AUTO'],
                                        ['name' => 'emp_code',   'type' => 'VARCHAR(20) UNI — รหัสพนักงาน'],
                                        ['name' => 'full_name',  'type' => 'VARCHAR(100) NOT NULL'],
                                        ['name' => 'email',      'type' => 'VARCHAR(100) NULL'],
                                        ['name' => 'dept_code',  'type' => 'VARCHAR(20) NULL'],
                                        ['name' => 'status',     'type' => 'TINYINT (1=ปฏิบัติงาน, 0=ลาออก)'],
                                        ['name' => 'created_at', 'type' => 'TIMESTAMP'],
                                    ],
                                ],
                                [
                                    'table' => 'employee_permissions',
                                    'desc'  => 'Junction Table (สิทธิ์พนักงาน)',
                                    'color' => 'violet',
                                    'cols'  => [
                                        ['name' => 'id',         'type' => 'INT PK AUTO'],
                                        ['name' => 'emp_id',     'type' => 'INT FK → employees.id'],
                                        ['name' => 'perm_code',  'type' => 'VARCHAR(60)'],
                                        ['name' => 'granted_at', 'type' => 'TIMESTAMP DEFAULT NOW()'],
                                        ['name' => 'granted_by', 'type' => 'VARCHAR(60) NULL'],
                                    ],
                                ],
                                [
                                    'table' => 'system_permissions',
                                    'desc'  => 'Permission Definition (2-Way Sync)',
                                    'color' => 'orange',
                                    'cols'  => [
                                        ['name' => 'id',            'type' => 'INT PK AUTO'],
                                        ['name' => 'perm_code',     'type' => 'VARCHAR(60) UNI'],
                                        ['name' => 'perm_label',    'type' => 'VARCHAR(120) NULL'],
                                        ['name' => 'perm_category', 'type' => 'VARCHAR(50) NULL'],
                                        ['name' => 'is_active',     'type' => 'TINYINT DEFAULT 1'],
                                        ['name' => 'deleted_at',    'type' => 'TIMESTAMP NULL — soft-delete'],
                                    ],
                                ],
                                [
                                    'table' => 'departments',
                                    'desc'  => 'Master Data (แผนก)',
                                    'color' => 'teal',
                                    'cols'  => [
                                        ['name' => 'id',          'type' => 'INT PK AUTO'],
                                        ['name' => 'dept_code',   'type' => 'VARCHAR(20) UNI'],
                                        ['name' => 'dept_name',   'type' => 'VARCHAR(100) NOT NULL'],
                                        ['name' => 'parent_code', 'type' => 'VARCHAR(20) NULL — FK หน่วยงานแม่'],
                                        ['name' => 'is_active',   'type' => 'TINYINT DEFAULT 1 (0=soft-delete)'],
                                    ],
                                ],
                            ] as $tbl)
                            <div class="p-4">
                                <div class="flex items-center gap-1.5 mb-2">
                                    <span class="w-2 h-2 rounded-full bg-{{ $tbl['color'] }}-500 flex-shrink-0"></span>
                                    <code class="text-xs font-bold font-mono text-{{ $tbl['color'] }}-700">{{ $tbl['table'] }}</code>
                                </div>
                                <p class="text-[10px] text-slate-500 mb-2">{{ $tbl['desc'] }}</p>
                                <div class="space-y-0.5">
                                    @foreach ($tbl['cols'] as $c)
                                    <div class="flex gap-1.5 text-[10px]">
                                        <code class="font-mono text-slate-800 font-semibold flex-shrink-0">{{ $c['name'] }}</code>
                                        <span class="text-slate-400">{{ $c['type'] }}</span>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endforeach
                        </div>
                        {{-- perm_code จริงในระบบ --}}
                        <div class="px-5 py-3 border-t border-slate-200 bg-slate-50">
                            <p class="text-[10px] font-semibold text-slate-600 mb-1.5">perm_code จริงใน system_permissions (17 รายการ):</p>
                            <div class="flex flex-wrap gap-1.5">
                                @foreach ([
                                    'admin.superuser',
                                    'budget.approve','budget.view','salary.edit','salary.view',
                                    'report.export','report.view',
                                    'leave.approve','leave.view','personnel.manage','personnel.view',
                                    'system.manage','system.monitor',
                                    'pax.view_flight',
                                    'procurement.approve','procurement.create',
                                    'qa.audit',
                                ] as $p)
                                <code class="text-[10px] font-mono font-bold bg-violet-100 text-violet-800 px-2 py-0.5 rounded">{{ $p }}</code>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Step-by-Step --}}
                    <div class="space-y-5">

                        {{-- Step 1 --}}
                        <div class="rounded-2xl border border-slate-200 overflow-hidden">
                            <div class="flex items-center gap-3 px-5 py-3 bg-indigo-600">
                                <div class="w-7 h-7 bg-white/20 text-white text-sm font-bold rounded-full flex items-center justify-center flex-shrink-0">1</div>
                                <div>
                                    <p class="font-bold text-white text-sm">Step 1 — ข้อมูลระบบ</p>
                                    <p class="text-indigo-200 text-[11px]">กรอกชื่อและรายละเอียดระบบ HR</p>
                                </div>
                            </div>
                            <div class="p-5 space-y-3 text-xs">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    @foreach ([
                                        ['field' => 'ชื่อระบบ',       'val' => 'ระบบ HR ภาครัฐ',  'req' => true],
                                        ['field' => 'Slug',            'val' => 'gov-hr',           'req' => true,  'hint' => 'ระบบสร้างให้อัตโนมัติ หรือพิมพ์เอง'],
                                        ['field' => 'คำอธิบาย',       'val' => 'ระบบบริหารทรัพยากรบุคคลภาครัฐ', 'req' => false],
                                        ['field' => 'สีประจำระบบ',    'val' => 'Indigo (#4f46e5)', 'req' => false],
                                        ['field' => 'Emoji Icon',      'val' => '👤',               'req' => false],
                                        ['field' => 'เชื่อมกับระบบที่มีอยู่', 'val' => 'ไม่เลือก (สร้างระบบใหม่)', 'req' => false],
                                    ] as $f)
                                    <div class="flex items-start gap-2 p-2.5 bg-slate-50 rounded-xl border border-slate-100">
                                        <div class="flex-1 min-w-0">
                                            <p class="font-semibold text-slate-600 text-[10px] mb-0.5">
                                                {{ $f['field'] }}
                                                @if($f['req'])<span class="text-rose-500">*</span>@endif
                                            </p>
                                            <p class="font-mono text-slate-900 font-bold truncate">{{ $f['val'] }}</p>
                                            @if(isset($f['hint']))<p class="text-slate-400 text-[10px] mt-0.5">{{ $f['hint'] }}</p>@endif
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                <div class="flex items-start gap-2 p-3 bg-indigo-50 border border-indigo-200 rounded-xl text-[11px] text-indigo-800">
                                    <svg class="w-3.5 h-3.5 text-indigo-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                                    <span>Slug <code class="font-mono bg-white px-1 rounded border border-indigo-200">gov-hr</code> จะถูกใช้เป็น identifier ของระบบนี้ในทุก URL และ log — เปลี่ยนภายหลังไม่ได้</span>
                                </div>
                                <p class="text-slate-500">กด <strong class="text-slate-700">ถัดไป →</strong> เพื่อไป Step 2</p>
                            </div>
                        </div>

                        {{-- Step 2 --}}
                        <div class="rounded-2xl border border-slate-200 overflow-hidden">
                            <div class="flex items-center gap-3 px-5 py-3 bg-slate-700">
                                <div class="w-7 h-7 bg-white/20 text-white text-sm font-bold rounded-full flex items-center justify-center flex-shrink-0">2</div>
                                <div>
                                    <p class="font-bold text-white text-sm">Step 2 — การเชื่อมต่อฐานข้อมูล</p>
                                    <p class="text-slate-300 text-[11px]">กรอก connection string ไปยัง hr_system_db</p>
                                </div>
                            </div>
                            <div class="p-5 space-y-3 text-xs">
                                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                                    @foreach ([
                                        ['field' => 'Database Driver', 'val' => '🐬 MySQL / MariaDB'],
                                        ['field' => 'Host',            'val' => 'ucm-db'],
                                        ['field' => 'Port',            'val' => '3306'],
                                        ['field' => 'Database Name',   'val' => 'hr_system_db'],
                                        ['field' => 'Username',        'val' => 'root'],
                                        ['field' => 'Password',        'val' => 'ucm_root_password'],
                                    ] as $f)
                                    <div class="p-2.5 bg-slate-50 rounded-xl border border-slate-100">
                                        <p class="font-semibold text-slate-500 text-[10px] mb-0.5">{{ $f['field'] }}</p>
                                        <p class="font-mono text-slate-900 font-bold">{{ $f['val'] }}</p>
                                    </div>
                                    @endforeach
                                </div>

                                {{-- SQL สิทธิ์ขั้นต่ำ --}}
                                <div class="rounded-xl border border-slate-200 overflow-hidden">
                                    <div class="px-3 py-2 bg-slate-100 border-b border-slate-200">
                                        <p class="text-[10px] font-bold text-slate-600">สิทธิ์ขั้นต่ำที่ DB User ต้องการ (SQL ตัวอย่าง)</p>
                                    </div>
                                    <pre class="px-4 py-3 text-[10px] font-mono text-slate-700 bg-slate-800 text-emerald-300 overflow-x-auto leading-relaxed">CREATE USER 'hr_ucm_user'@'%' IDENTIFIED BY 'StrongP@ssword!';
GRANT SELECT, INSERT, UPDATE ON hr_system_db.employees TO 'hr_ucm_user'@'%';
GRANT SELECT, INSERT, DELETE ON hr_system_db.employee_permissions TO 'hr_ucm_user'@'%';
GRANT SELECT, INSERT, UPDATE ON hr_system_db.system_permissions TO 'hr_ucm_user'@'%';
GRANT SELECT, INSERT, UPDATE, DELETE ON hr_system_db.departments TO 'hr_ucm_user'@'%';
FLUSH PRIVILEGES;</pre>
                                </div>

                                <div class="space-y-2">
                                    <div class="flex items-start gap-2 p-3 bg-emerald-50 border border-emerald-200 rounded-xl text-[11px] text-emerald-800">
                                        <svg class="w-3.5 h-3.5 text-emerald-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <span>กด <strong>"ทดสอบการเชื่อมต่อ"</strong> ก่อนทำขั้นตอนถัดไปเสมอ หากขึ้น "เชื่อมต่อสำเร็จ" แสดงว่า credentials ถูกต้องและ UCM เข้าถึง hr_system_db ได้</span>
                                    </div>
                                    <div class="flex items-start gap-2 p-3 bg-violet-50 border border-violet-200 rounded-xl text-[11px] text-violet-800">
                                        <span class="text-base leading-none flex-shrink-0">🤖</span>
                                        <span>หลัง Test Connection สำเร็จ ลองกด <strong>"วิเคราะห์ Schema อัตโนมัติ"</strong> — ระบบจะ introspect โครงสร้างตารางและแนะนำการตั้งค่า Step 3–4 ได้ทันที ตรวจสอบผลแล้วกด "ใช้การตั้งค่านี้" เพื่อ autofill</span>
                                    </div>
                                </div>
                                <p class="text-slate-500">กด <strong class="text-slate-700">ถัดไป →</strong> เพื่อไป Step 3</p>
                            </div>
                        </div>

                        {{-- Step 3 --}}
                        <div class="rounded-2xl border border-slate-200 overflow-hidden">
                            <div class="flex items-center gap-3 px-5 py-3 bg-sky-600">
                                <div class="w-7 h-7 bg-white/20 text-white text-sm font-bold rounded-full flex items-center justify-center flex-shrink-0">3</div>
                                <div>
                                    <p class="font-bold text-white text-sm">Step 3 — ตาราง Users</p>
                                    <p class="text-sky-100 text-[11px]">แมปตาราง employees กับ UCM</p>
                                </div>
                            </div>
                            <div class="p-5 space-y-4 text-xs">

                                {{-- User Table + UCM Identifier --}}
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div class="p-3 bg-sky-50 border border-sky-200 rounded-xl">
                                        <p class="font-semibold text-sky-700 text-[10px] mb-1">ตาราง Users หลัก <span class="text-rose-500">*</span></p>
                                        <code class="font-mono font-bold text-slate-900">employees</code>
                                        <p class="text-slate-500 text-[10px] mt-1">เลือกจาก dropdown ที่ดึงจาก hr_system_db</p>
                                    </div>
                                    <div class="p-3 bg-sky-50 border border-sky-200 rounded-xl">
                                        <p class="font-semibold text-sky-700 text-[10px] mb-1">UCM ใช้อะไรระบุตัวตน <span class="text-rose-500">*</span></p>
                                        <code class="font-mono font-bold text-slate-900">Username (จาก AD)</code>
                                        <p class="text-slate-500 text-[10px] mt-1">พนักงานล็อกอินด้วย AD username → UCM จะ match กับ emp_code</p>
                                    </div>
                                </div>

                                {{-- Column Mapping --}}
                                <div class="rounded-xl border border-slate-200 overflow-hidden">
                                    <div class="px-4 py-2.5 bg-slate-50 border-b border-slate-200">
                                        <p class="font-bold text-slate-700 text-[10px]">การ map คอลัมน์ (Column Mapping)</p>
                                    </div>
                                    <div class="divide-y divide-slate-100">
                                        @foreach ([
                                            ['field' => 'Identifier Column', 'req' => true,  'col' => 'emp_code', 'note' => 'emp_code ตรงกับ AD username ของพนักงาน ใช้เพื่อ match กับ UCM User'],
                                            ['field' => 'Primary Key',       'req' => false, 'col' => 'id',       'note' => 'PK (INT) สำหรับ JOIN ภายใน — ระบุเพราะ Identifier (emp_code) ไม่ใช่ PK'],
                                            ['field' => 'ชื่อ-นามสกุล',     'req' => false, 'col' => 'full_name', 'note' => 'ใช้แสดงชื่อพนักงานใน UCM เมื่อ sync'],
                                            ['field' => 'อีเมล',            'req' => false, 'col' => 'email',     'note' => 'sync อีเมลจาก UCM เข้า employees ด้วย'],
                                            ['field' => 'แผนก',             'req' => false, 'col' => 'dept_code', 'note' => 'ใช้ Dept Mapping แปลงชื่อแผนก UCM → รหัส เช่น "Finance" → FIN'],
                                            ['field' => 'สถานะ Active',     'req' => false, 'col' => 'status',    'note' => 'ค่า Active = 1 / Inactive = 0 ตามที่ระบุด้านล่าง'],
                                        ] as $f)
                                        <div class="flex items-start gap-3 px-4 py-2.5 hover:bg-slate-50">
                                            <span class="w-16 flex-shrink-0 text-center text-[9px] font-bold px-1 py-0.5 rounded mt-0.5 {{ $f['req'] ? 'bg-rose-100 text-rose-600' : 'bg-slate-100 text-slate-500' }}">{{ $f['req'] ? 'บังคับ' : 'ไม่บังคับ' }}</span>
                                            <div class="w-32 flex-shrink-0">
                                                <p class="font-semibold text-slate-700">{{ $f['field'] }}</p>
                                            </div>
                                            <div class="flex-1 flex items-center gap-2">
                                                <code class="font-mono font-bold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded border border-indigo-100">{{ $f['col'] }}</code>
                                                <span class="text-slate-500">{{ $f['note'] }}</span>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>

                                {{-- Dept Code Mapping --}}
                                <div class="rounded-xl border border-indigo-200 overflow-hidden">
                                    <div class="px-4 py-2.5 bg-indigo-50 border-b border-indigo-200 flex items-center gap-2">
                                        <span class="text-sm">🗂️</span>
                                        <p class="font-bold text-indigo-800 text-[10px]">Department Code Mapping (จะปรากฏหลังเลือก dept_code)</p>
                                    </div>
                                    <div class="p-4 text-xs space-y-3">
                                        <p class="text-slate-600">UCM เก็บชื่อแผนกจาก AD เป็น <strong>ชื่อเต็ม</strong> แต่ <code class="font-mono bg-slate-100 px-1 rounded">employees.dept_code</code> เก็บเป็น <strong>รหัสย่อ</strong> — ต้องตั้งค่า mapping ให้ตรงกัน (ข้อมูลจริงจาก dept_map ใน Connector Config)</p>
                                        <div class="rounded-xl border border-slate-200 overflow-hidden">
                                            <div class="px-3 py-2 bg-slate-50 border-b border-slate-200 grid grid-cols-2 gap-4">
                                                <p class="text-[10px] font-bold text-slate-600">ชื่อแผนกใน UCM (จาก AD)</p>
                                                <p class="text-[10px] font-bold text-slate-600">dept_code ในตาราง employees</p>
                                            </div>
                                            @foreach ([
                                                ['ucm' => 'Finance',                 'ext' => 'FIN'],
                                                ['ucm' => 'HR Management',           'ext' => 'HR'],
                                                ['ucm' => 'HR Development',          'ext' => 'HRD'],
                                                ['ucm' => 'Systems Development and IT', 'ext' => 'IT'],
                                                ['ucm' => 'Procurement',             'ext' => 'PROC'],
                                                ['ucm' => 'Quality Assurance',       'ext' => 'QA'],
                                                ['ucm' => 'Legal and Compliance',    'ext' => 'LEGAL'],
                                                ['ucm' => 'Ramp Operations',         'ext' => 'RAMP'],
                                                ['ucm' => 'Passenger Services',      'ext' => 'PAX'],
                                                ['ucm' => 'Executive Office',        'ext' => 'EXEC'],
                                                ['ucm' => 'Corporate Communication', 'ext' => 'CORP'],
                                                ['ucm' => 'Facility Service',        'ext' => 'FACILITY'],
                                            ] as $d)
                                            <div class="border-b border-slate-100 last:border-0 grid grid-cols-2 gap-4 px-3 py-1.5">
                                                <span class="text-slate-700">{{ $d['ucm'] }}</span>
                                                <code class="font-mono font-bold text-violet-700">{{ $d['ext'] }}</code>
                                            </div>
                                            @endforeach
                                        </div>
                                        <div class="flex items-start gap-2 p-2.5 bg-amber-50 border border-amber-200 rounded-xl text-[11px] text-amber-800">
                                            <svg class="w-3.5 h-3.5 text-amber-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                            <span>กด <strong>"โหลดแผนกจาก UCM"</strong> เพื่อดึงรายชื่อแผนกทั้งหมดมาเป็นแถวอัตโนมัติ แล้วกรอกรหัสแผนกฝั่งขวา หรือกด "+ เพิ่มแถวด้วยตนเอง" หากต้องการเพิ่มทีละแถว</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-start gap-2 p-3 bg-emerald-50 border border-emerald-200 rounded-xl text-[11px] text-emerald-800">
                                    <svg class="w-3.5 h-3.5 text-emerald-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span>กด <strong>"ดูตัวอย่างข้อมูล 10 รายการแรก"</strong> เพื่อยืนยันว่าตาราง mapping ถูกต้อง ควรเห็น emp_code, full_name, dept_code ปรากฏในตัวอย่าง</span>
                                </div>
                                <p class="text-slate-500">กด <strong class="text-slate-700">ถัดไป →</strong> เพื่อไป Step 4</p>
                            </div>
                        </div>

                        {{-- Step 4 --}}
                        <div class="rounded-2xl border border-slate-200 overflow-hidden">
                            <div class="flex items-center gap-3 px-5 py-3 bg-violet-700">
                                <div class="w-7 h-7 bg-white/20 text-white text-sm font-bold rounded-full flex items-center justify-center flex-shrink-0">4</div>
                                <div>
                                    <p class="font-bold text-white text-sm">Step 4 — Permission Mode</p>
                                    <p class="text-violet-200 text-[11px]">เลือก 🔗 Junction Table และตั้งค่า employee_permissions</p>
                                </div>
                            </div>
                            <div class="p-5 space-y-4 text-xs">

                                {{-- Mode selection --}}
                                <div class="flex items-center gap-3 p-3.5 bg-violet-50 border-2 border-violet-400 rounded-xl">
                                    <div class="w-8 h-8 bg-violet-600 rounded-xl flex items-center justify-center flex-shrink-0">
                                        <span class="text-base">🔗</span>
                                    </div>
                                    <div>
                                        <p class="font-bold text-violet-900">เลือก "Junction Table" (แนะนำ)</p>
                                        <p class="text-violet-700 text-[11px]">เหมาะที่สุด เพราะ tb_emp_permission เป็นตาราง mapping แยกต่างหาก พนักงาน 1 คนมีหลาย permission ได้</p>
                                    </div>
                                </div>

                                {{-- Junction table config --}}
                                <div class="rounded-xl border border-slate-200 overflow-hidden">
                                    <div class="px-4 py-2.5 bg-slate-50 border-b border-slate-200">
                                        <p class="font-bold text-slate-700 text-[10px]">ค่าที่ต้องกรอกสำหรับ Junction Table</p>
                                    </div>
                                    <div class="divide-y divide-slate-100">
                                        @foreach ([
                                            ['field' => 'ตาราง Junction',            'req' => true,  'val' => 'employee_permissions', 'note' => 'ตาราง junction ที่เชื่อม employees กับสิทธิ์'],
                                            ['field' => 'User FK Column',            'req' => true,  'val' => 'emp_id',              'note' => 'FK ที่ชี้ไปยัง employees.id'],
                                            ['field' => 'Permission Value Column',   'req' => true,  'val' => 'perm_code',           'note' => 'คอลัมน์ที่เก็บค่าสิทธิ์ เช่น personnel.view, budget.approve'],
                                            ['field' => 'Permission Label Column',   'req' => false, 'val' => '(ว่าง)',              'note' => 'employee_permissions ไม่มีคอลัมน์ label — ข้ามได้ (label อยู่ใน system_permissions)'],
                                            ['field' => 'Permission Group Column',   'req' => false, 'val' => '(ว่าง)',              'note' => 'ไม่มีคอลัมน์ group ใน junction table — ข้ามได้'],
                                        ] as $f)
                                        <div class="flex items-start gap-3 px-4 py-2.5 hover:bg-slate-50">
                                            <span class="w-16 flex-shrink-0 text-center text-[9px] font-bold px-1 py-0.5 rounded mt-0.5 {{ $f['req'] ? 'bg-rose-100 text-rose-600' : 'bg-slate-100 text-slate-500' }}">{{ $f['req'] ? 'บังคับ' : 'ไม่บังคับ' }}</span>
                                            <div class="w-40 flex-shrink-0">
                                                <p class="font-semibold text-slate-700">{{ $f['field'] }}</p>
                                            </div>
                                            <div class="flex-1 flex items-center gap-2">
                                                <code class="font-mono font-bold text-violet-700 bg-violet-50 px-2 py-0.5 rounded border border-violet-100 whitespace-nowrap">{{ $f['val'] }}</code>
                                                <span class="text-slate-500">{{ $f['note'] }}</span>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>

                                {{-- Junction Enhancements --}}
                                <div class="rounded-xl border border-violet-200 overflow-hidden">
                                    <div class="px-4 py-2.5 bg-violet-50 border-b border-violet-200 flex items-center gap-2">
                                        <span class="text-sm">⚙️</span>
                                        <p class="font-bold text-violet-800 text-[10px]">Junction Enhancements (optional) — ขยายหัวข้อนี้ใน wizard เพื่อตั้งค่าเพิ่มเติม</p>
                                    </div>
                                    <div class="p-4 space-y-3">

                                        {{-- Insert Meta --}}
                                        <div class="p-3 bg-white border border-slate-200 rounded-xl">
                                            <p class="font-semibold text-slate-800 mb-1.5">Insert Metadata Columns <span class="text-[10px] text-slate-400">(ไม่บังคับ)</span></p>
                                            <p class="text-slate-600 mb-2">เมื่อ UCM INSERT แถวใน <code class="font-mono bg-slate-100 px-1 rounded">employee_permissions</code> จะ inject ค่า metadata อัตโนมัติ:</p>
                                            <div class="rounded-lg border border-slate-200 overflow-hidden">
                                                <div class="grid grid-cols-2 px-3 py-1.5 bg-slate-50 border-b border-slate-200 text-[10px] font-bold text-slate-600">
                                                    <span>Column ใน employee_permissions</span><span>Token ที่กรอกใน Wizard</span>
                                                </div>
                                                @foreach ([
                                                    ['col' => 'granted_at', 'token' => '__now__',       'desc' => '→ timestamp ปัจจุบันตอน sync'],
                                                    ['col' => 'granted_by', 'token' => '__ucm_admin__', 'desc' => '→ username ของ Admin ที่กด Sync'],
                                                ] as $m)
                                                <div class="grid grid-cols-2 gap-2 px-3 py-2 border-b border-slate-100 last:border-0 items-center">
                                                    <code class="font-mono font-bold text-violet-700">{{ $m['col'] }}</code>
                                                    <div class="flex items-center gap-1.5">
                                                        <code class="font-mono font-bold text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-200">{{ $m['token'] }}</code>
                                                        <span class="text-slate-500 text-[10px]">{{ $m['desc'] }}</span>
                                                    </div>
                                                </div>
                                                @endforeach
                                            </div>
                                            <p class="text-slate-500 text-[10px] mt-1.5">เปิดใช้งานโดยเช็ก <strong>"เปิดใช้ Insert Metadata"</strong> แล้วกด <strong>"+ เพิ่มคอลัมน์"</strong></p>
                                        </div>

                                        {{-- Available tokens --}}
                                        <div class="p-3 bg-white border border-slate-200 rounded-xl">
                                            <p class="font-semibold text-slate-800 mb-2">Token ที่ใช้ได้ทั้งหมด</p>
                                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-1.5">
                                                @foreach ([
                                                    ['tok' => '{{date}}',         'desc' => 'วันที่ปัจจุบัน YYYY-MM-DD'],
                                                    ['tok' => '{{datetime}}',     'desc' => 'วันและเวลาปัจจุบัน'],
                                                    ['tok' => '{{username}}',     'desc' => 'username ของ Admin'],
                                                    ['tok' => '{{user_emp_id}}',  'desc' => 'PK ของ employee ที่ sync'],
                                                    ['tok' => '{{user_name}}',    'desc' => 'ชื่อพนักงานที่ sync'],
                                                    ['tok' => '{{literal:xxx}}',  'desc' => 'ค่าคงที่ เช่น {{literal:UCM}}'],
                                                ] as $t)
                                                <div class="flex items-start gap-1.5 p-1.5 bg-slate-50 rounded-lg border border-slate-100">
                                                    <code class="font-mono text-[9px] font-bold text-emerald-700 bg-emerald-50 px-1 rounded whitespace-nowrap">{{ $t['tok'] }}</code>
                                                    <span class="text-[9px] text-slate-500">{{ $t['desc'] }}</span>
                                                </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Preview permissions --}}
                                <div class="flex items-start gap-2 p-3 bg-emerald-50 border border-emerald-200 rounded-xl text-[11px] text-emerald-800">
                                    <svg class="w-3.5 h-3.5 text-emerald-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <span>กด <strong>"ดูตัวอย่างสิทธิ์"</strong> เพื่อยืนยันว่า query ดึง permission_code ได้ถูกต้อง ควรเห็นค่าเช่น HR_VIEW, PAYROLL_VIEW ปรากฏขึ้น</span>
                                </div>
                                <p class="text-slate-500">กด <strong class="text-slate-700">ถัดไป →</strong> เพื่อไป Step 5</p>
                            </div>
                        </div>

                        {{-- Step 5 --}}
                        <div class="rounded-2xl border border-slate-200 overflow-hidden">
                            <div class="flex items-center gap-3 px-5 py-3 bg-orange-500">
                                <div class="w-7 h-7 bg-white/20 text-white text-sm font-bold rounded-full flex items-center justify-center flex-shrink-0">5</div>
                                <div>
                                    <p class="font-bold text-white text-sm">Step 5 — 2-Way Permission Sync</p>
                                    <p class="text-orange-100 text-[11px]">เปิดให้ UCM บริหาร tb_permission_master โดยอัตโนมัติ</p>
                                </div>
                            </div>
                            <div class="p-5 space-y-4 text-xs">
                                <div class="flex items-start gap-2 p-3 bg-orange-50 border border-orange-200 rounded-xl text-[11px] text-orange-800">
                                    <svg class="w-3.5 h-3.5 text-orange-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                                    <span>สลับ Toggle <strong>"เปิด 2-Way Sync"</strong> ให้เป็น ON เพื่อให้ UCM สร้าง/อัปเดต/ลบ Permission Definition ใน <code class="font-mono bg-orange-50 px-1 rounded border border-orange-200">system_permissions</code> โดยอัตโนมัติเมื่อ Admin จัดการสิทธิ์ใน UCM</span>
                                </div>

                                <div class="rounded-xl border border-slate-200 overflow-hidden">
                                    <div class="px-4 py-2.5 bg-slate-50 border-b border-slate-200">
                                        <p class="font-bold text-slate-700 text-[10px]">การตั้งค่า Permission Definition Table</p>
                                    </div>
                                    <div class="divide-y divide-slate-100">
                                        @foreach ([
                                            ['field' => 'Permission Definition Table', 'req' => true,  'val' => 'system_permissions', 'note' => 'ตารางที่เก็บนิยามสิทธิ์ทั้งหมดของระบบ HR'],
                                            ['field' => 'Value Column',               'req' => true,  'val' => 'perm_code',          'note' => 'ตรงกับ perm_code ใน employee_permissions'],
                                            ['field' => 'Primary Key Column',         'req' => false, 'val' => 'id',                 'note' => 'PK ของตาราง — ใช้สำหรับ UPDATE/DELETE'],
                                            ['field' => 'Label Column',               'req' => false, 'val' => 'perm_label',         'note' => 'ชื่อที่อ่านได้ เช่น "ดูข้อมูลบุคลากร"'],
                                            ['field' => 'Group Column',               'req' => false, 'val' => 'perm_category',      'note' => 'หมวดหมู่สิทธิ์ เช่น HR, Finance, IT'],
                                        ] as $f)
                                        <div class="flex items-start gap-3 px-4 py-2.5 hover:bg-slate-50">
                                            <span class="w-16 flex-shrink-0 text-center text-[9px] font-bold px-1 py-0.5 rounded mt-0.5 {{ $f['req'] ? 'bg-rose-100 text-rose-600' : 'bg-slate-100 text-slate-500' }}">{{ $f['req'] ? 'บังคับ' : 'ไม่บังคับ' }}</span>
                                            <div class="w-44 flex-shrink-0">
                                                <p class="font-semibold text-slate-700">{{ $f['field'] }}</p>
                                            </div>
                                            <div class="flex-1 flex items-center gap-2">
                                                <code class="font-mono font-bold text-orange-700 bg-orange-50 px-2 py-0.5 rounded border border-orange-100 whitespace-nowrap">{{ $f['val'] }}</code>
                                                <span class="text-slate-500">{{ $f['note'] }}</span>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>

                                {{-- Delete Mode --}}
                                <div class="rounded-xl border border-amber-200 overflow-hidden">
                                    <div class="px-4 py-2.5 bg-amber-50 border-b border-amber-200 flex items-center gap-2">
                                        <span class="text-sm">🗑️</span>
                                        <p class="font-bold text-amber-800 text-[10px]">Delete Mode — เลือก "Soft Delete" (เหมาะกับระบบ HR ที่มี deleted_at)</p>
                                    </div>
                                    <div class="p-4 space-y-3">
                                        <div class="flex items-center gap-3">
                                            <div class="grid grid-cols-3 gap-2 flex-1">
                                                <div class="p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-center opacity-50">
                                                    <p class="text-[10px] font-bold text-slate-600">Detach Only</p>
                                                    <p class="text-[10px] text-slate-500 mt-0.5">ไม่แตะ DB ปลายทาง</p>
                                                </div>
                                                <div class="p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-center opacity-50">
                                                    <p class="text-[10px] font-bold text-red-600">Hard Delete</p>
                                                    <p class="text-[10px] text-slate-500 mt-0.5">DELETE ถาวร</p>
                                                </div>
                                                <div class="p-2.5 bg-amber-50 border-2 border-amber-400 rounded-xl text-center">
                                                    <p class="text-[10px] font-bold text-amber-700">✓ Soft Delete</p>
                                                    <p class="text-[10px] text-amber-600 mt-0.5">อัปเดต flag</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                            <div class="p-2.5 bg-amber-50 border border-amber-200 rounded-xl">
                                                <p class="text-[10px] font-semibold text-amber-700 mb-0.5">Soft Delete Column</p>
                                                <code class="font-mono font-bold text-slate-900">deleted_at</code>
                                                <p class="text-slate-500 text-[10px] mt-0.5">คอลัมน์ DATETIME ที่บันทึกเวลาที่ soft-delete</p>
                                            </div>
                                            <div class="p-2.5 bg-amber-50 border border-amber-200 rounded-xl">
                                                <p class="text-[10px] font-semibold text-amber-700 mb-0.5">Soft Delete Value</p>
                                                <code class="font-mono font-bold text-slate-900">(ว่าง — ไม่ต้องระบุ)</code>
                                                <p class="text-slate-500 text-[10px] mt-0.5">ถ้าใช้ DATETIME ให้เว้นว่างไว้ UCM จะ set เป็น timestamp ปัจจุบัน และ filter ด้วย <code class="font-mono">IS NULL</code></p>
                                            </div>
                                        </div>
                                        <div class="flex items-start gap-2 p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-[10px] text-slate-600">
                                            <span class="font-bold text-slate-700 flex-shrink-0">ผลที่ได้:</span>
                                            <div class="space-y-0.5 font-mono">
                                                <p><span class="text-emerald-600">-- เมื่อ Admin เพิ่มสิทธิ์ใหม่:</span></p>
                                                <p class="text-slate-800">INSERT INTO system_permissions (perm_code, perm_label, perm_category) VALUES (?, ?, ?)</p>
                                                <p class="mt-1.5"><span class="text-amber-600">-- เมื่อ Admin ลบสิทธิ์:</span></p>
                                                <p class="text-slate-800">UPDATE system_permissions SET deleted_at = NOW() WHERE id = ?</p>
                                                <p class="mt-1.5"><span class="text-sky-600">-- Discover กรอง soft-deleted ออก:</span></p>
                                                <p class="text-slate-800">SELECT * FROM system_permissions WHERE deleted_at IS NULL</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <p class="text-slate-500">กด <strong class="text-slate-700">ถัดไป →</strong> เพื่อไป Step 6</p>
                            </div>
                        </div>

                        {{-- Step 6 --}}
                        <div class="rounded-2xl border border-slate-200 overflow-hidden">
                            <div class="flex items-center gap-3 px-5 py-3 bg-teal-600">
                                <div class="w-7 h-7 bg-white/20 text-white text-sm font-bold rounded-full flex items-center justify-center flex-shrink-0">6</div>
                                <div>
                                    <p class="font-bold text-white text-sm">Step 6 — Master Data Tables</p>
                                    <p class="text-teal-100 text-[11px]">ลงทะเบียน departments ให้ Admin จัดการผ่าน UCM ได้</p>
                                </div>
                            </div>
                            <div class="p-5 space-y-4 text-xs">
                                <p class="text-slate-600">กด <strong>"+ เพิ่มตาราง"</strong> เพื่อลงทะเบียน <code class="font-mono bg-slate-100 px-1 rounded">departments</code> เป็น Master Data Table ที่ Admin สามารถเพิ่ม/แก้ไข/ลบแผนกได้จากหน้า UCM โดยตรง</p>

                                <div class="rounded-xl border border-slate-200 overflow-hidden">
                                    <div class="px-4 py-2.5 bg-teal-50 border-b border-teal-200">
                                        <p class="font-bold text-teal-800 text-[10px]">การตั้งค่า Master Table: departments</p>
                                    </div>
                                    <div class="divide-y divide-slate-100">
                                        @foreach ([
                                            ['field' => 'ชื่อกลุ่ม (Label)',    'val' => 'แผนก',           'note' => 'ชื่อที่แสดงใน UCM ใต้แท็บ "ข้อมูล Reference"'],
                                            ['field' => 'ตาราง',               'val' => 'departments',     'note' => 'เลือกจาก dropdown'],
                                            ['field' => 'Primary Key Column',   'val' => 'id',             'note' => 'PK สำหรับ UPDATE/DELETE'],
                                            ['field' => 'Label Column',         'val' => 'dept_name',      'note' => 'คอลัมน์ชื่อแผนกที่แสดงในรายการ'],
                                            ['field' => 'Delete Mode',          'val' => 'Soft Delete',    'note' => 'ใช้ is_active = 0 แทนการลบถาวร'],
                                            ['field' => 'Soft Delete Column',   'val' => 'is_active',      'note' => 'flag ที่บ่งบอกสถานะแผนก'],
                                            ['field' => 'Soft Delete Value',    'val' => '0',              'note' => 'ค่าที่หมายถึง "ปิดใช้งาน/ลบแล้ว" (ตรงข้ามกับ Active = 1)'],
                                            ['field' => 'Extra Column: dept_code',  'val' => 'dept_code',  'note' => 'คอลัมน์รหัสแผนก (required) — เพิ่มผ่าน "+ เพิ่มคอลัมน์เสริม"'],
                                            ['field' => 'Extra Column: parent_code','val' => 'parent_code','note' => 'รหัสหน่วยงานแม่ (ไม่บังคับ) — FK ชี้หา dept_code ของหน่วยงานแม่'],
                                        ] as $f)
                                        <div class="flex items-start gap-3 px-4 py-2 hover:bg-slate-50">
                                            <div class="w-40 flex-shrink-0 font-semibold text-slate-700">{{ $f['field'] }}</div>
                                            <code class="font-mono font-bold text-teal-700 bg-teal-50 px-2 py-0.5 rounded border border-teal-100 whitespace-nowrap">{{ $f['val'] }}</code>
                                            <span class="flex-1 text-slate-500">{{ $f['note'] }}</span>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="flex items-start gap-2 p-3 bg-teal-50 border border-teal-200 rounded-xl text-[11px] text-teal-800">
                                    <svg class="w-3.5 h-3.5 text-teal-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                                    <span>ระบบ hr_system_db มีเพียง <code class="font-mono bg-white px-1 rounded border border-teal-200">departments</code> เป็น Master Data — ข้ามการเพิ่มตารางอื่นได้เลย Step 6 ไม่บังคับ</span>
                                </div>
                                <p class="text-slate-500">กด <strong class="text-slate-700">ถัดไป →</strong> เพื่อไป Step 7</p>
                            </div>
                        </div>

                        {{-- Step 7 --}}
                        <div class="rounded-2xl border border-emerald-200 overflow-hidden">
                            <div class="flex items-center gap-3 px-5 py-3 bg-emerald-600">
                                <div class="w-7 h-7 bg-white/20 text-white text-sm font-bold rounded-full flex items-center justify-center flex-shrink-0">7</div>
                                <div>
                                    <p class="font-bold text-white text-sm">Step 7 — ยืนยันการสร้าง Connector</p>
                                    <p class="text-emerald-100 text-[11px]">ตรวจสอบสรุปทั้งหมดก่อนกด "สร้าง"</p>
                                </div>
                            </div>
                            <div class="p-5 space-y-4 text-xs">
                                <p class="text-slate-600">ใน Step 7 ระบบจะแสดงสรุปการตั้งค่าทั้งหมด ตรวจสอบให้ครบก่อนกด <strong>"สร้าง Connector"</strong></p>

                                {{-- Summary checklist --}}
                                <div class="rounded-xl border border-emerald-200 overflow-hidden">
                                    <div class="px-4 py-2.5 bg-emerald-50 border-b border-emerald-200">
                                        <p class="font-bold text-emerald-800 text-[10px]">Checklist ก่อนกด "สร้าง Connector"</p>
                                    </div>
                                    <div class="divide-y divide-slate-100">
                                        @foreach ([
                                            ['ok' => true,  'item' => 'ชื่อระบบ',              'val' => 'ระบบ HR ภาครัฐ (gov-hr)'],
                                            ['ok' => true,  'item' => 'DB Connection',          'val' => 'MySQL — ucm-db:3306/hr_system_db ✓ เชื่อมต่อสำเร็จ'],
                                            ['ok' => true,  'item' => 'User Table',             'val' => 'employees — Identifier: emp_code, PK: id, Name: full_name, Dept: dept_code, Status: status (Active=1)'],
                                            ['ok' => true,  'item' => 'Dept Mapping',           'val' => '12 รายการ (Finance → FIN, Systems Development and IT → IT, ...)'],
                                            ['ok' => true,  'item' => 'Permission Mode',        'val' => 'Junction Table — employee_permissions (emp_id → perm_code)'],
                                            ['ok' => true,  'item' => 'Insert Metadata',        'val' => 'granted_at = __now__, granted_by = __ucm_admin__'],
                                            ['ok' => true,  'item' => '2-Way Sync',             'val' => 'ON — system_permissions (perm_code/perm_label/perm_category), Soft Delete: deleted_at IS NULL'],
                                            ['ok' => true,  'item' => 'Master Data',            'val' => 'departments — Label: dept_name, PK: id, Soft Delete: is_active = 0, Extra: dept_code, parent_code'],
                                        ] as $c)
                                        <div class="flex items-center gap-3 px-4 py-2.5 hover:bg-slate-50">
                                            <svg class="w-4 h-4 {{ $c['ok'] ? 'text-emerald-500' : 'text-slate-300' }} flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                            <div class="w-32 flex-shrink-0 font-semibold text-slate-700">{{ $c['item'] }}</div>
                                            <span class="text-slate-600">{{ $c['val'] }}</span>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="flex justify-center">
                                    <div class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 text-white rounded-xl font-bold text-sm shadow-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        สร้าง Connector
                                    </div>
                                </div>

                                <div class="flex items-start gap-2 p-3 bg-emerald-50 border border-emerald-200 rounded-xl text-[11px] text-emerald-800">
                                    <svg class="w-3.5 h-3.5 text-emerald-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span>หลังกด "สร้าง Connector" สำเร็จ ระบบจะสร้าง System <strong>gov-hr</strong> พร้อม DynamicAdapter และ redirect ไปยังหน้าจัดการระบบ — จากนั้นกด <strong>"Discover Permissions"</strong> เพื่อดึง permission_code ทั้งหมดจาก tb_permission_master เข้า UCM ทันที</span>
                                </div>
                            </div>
                        </div>

                        {{-- ขั้นตอนหลังสร้าง --}}
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5 space-y-3">
                            <h4 class="font-bold text-slate-900">ขั้นตอนหลังสร้าง Connector สำเร็จ</h4>
                            <div class="space-y-2">
                                @foreach ([
                                    ['n' => '1', 'color' => 'indigo',  'title' => 'Discover Permissions',      'desc' => 'ไปที่ ระบบที่เชื่อมต่อ → gov-hr → กด "Discover Permissions" — UCM จะดึง permission_code ทั้งหมดจาก tb_permission_master ที่ไม่ถูก soft-delete มาสร้างเป็น Permission list ใน UCM'],
                                    ['n' => '2', 'color' => 'violet',  'title' => 'Assign สิทธิ์ให้ผู้ใช้',   'desc' => 'ไปที่ จัดการผู้ใช้ → เลือกพนักงาน → เลือกสิทธิ์ระบบ HR → กด "บันทึกสิทธิ์ระบบนี้" — UCM จะ INSERT แถวใน tb_emp_permission พร้อม assigned_date และ assigned_by อัตโนมัติ'],
                                    ['n' => '3', 'color' => 'emerald', 'title' => 'ทดสอบ Health Check',        'desc' => 'กด "ทดสอบการเชื่อมต่อ" ในหน้าจัดการระบบ เพื่อยืนยันว่า UCM ยังสื่อสารกับ hr_db ได้สำเร็จ'],
                                    ['n' => '4', 'color' => 'sky',     'title' => 'ตรวจสอบ Out of Sync',       'desc' => 'ในหน้า User Profile หากพบ badge "Out of Sync" แสดงว่าสิทธิ์ใน UCM ไม่ตรงกับ tb_emp_permission — กด "Discover" เพื่อดึงจากระบบจริง หรือ "Sync" เพื่อเขียน UCM → ระบบจริง'],
                                    ['n' => '5', 'color' => 'teal',    'title' => 'จัดการ Master Data',         'desc' => 'ไปที่ ระบบที่เชื่อมต่อ → gov-hr → แท็บ "ข้อมูล Reference" → "แผนก" เพื่อเพิ่ม/แก้ไข/ลบข้อมูลในตาราง departments โดยตรง'],
                                ] as $s)
                                <div class="flex items-start gap-3 p-3 bg-white rounded-xl border border-slate-100">
                                    <div class="w-6 h-6 bg-{{ $s['color'] }}-100 text-{{ $s['color'] }}-700 text-[11px] font-bold rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">{{ $s['n'] }}</div>
                                    <div>
                                        <p class="font-semibold text-slate-800">{{ $s['title'] }}</p>
                                        <p class="text-slate-500 text-[11px] mt-0.5">{{ $s['desc'] }}</p>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>

                    </div>
                </div>

                <div id="wiz-after" class="border-t border-slate-100 pt-5">
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
                    <h3 class="font-bold text-slate-900 mb-3">การจัดการ Failed Jobs <span class="text-xs font-normal text-slate-400">(ผู้มีสิทธิ์ Queue Monitor)</span></h3>
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
                            ['label' => 'Auth',        'color' => 'sky',    'desc' => 'Login / Logout / Login Failed'],
                            ['label' => 'Users',       'color' => 'indigo', 'desc' => 'Import, อัปเดต, ลบผู้ใช้'],
                            ['label' => 'Permissions', 'color' => 'violet', 'desc' => 'เพิ่ม/ลบ/เปลี่ยน สิทธิ์ผู้ใช้ และ Account Status'],
                            ['label' => 'Systems',     'color' => 'emerald','desc' => 'สร้าง/แก้ไข/ลบ ระบบ, Permission Definition และข้อมูล Reference'],
                            ['label' => 'Connectors',  'color' => 'orange', 'desc' => 'สร้าง/แก้ไข/ลบ Connector Wizard'],
                            ['label' => 'Notifications','color' => 'teal',  'desc' => 'สร้าง/แก้ไข/ลบ Notification Channel'],
                            ['label' => 'UCM Access',  'color' => 'purple', 'desc' => 'เปลี่ยน Feature Level / มอบ/ถอน Individual Grant'],
                            ['label' => 'Queue',       'color' => 'amber',  'desc' => 'Retry / Delete / Flush Failed Jobs'],
                            ['label' => 'API',         'color' => 'rose',   'desc' => 'ออก Token / Revoke / User Login ผ่าน API'],
                            ['label' => 'Share Links', 'color' => 'cyan',   'desc' => 'สร้าง / ยกเลิก / Reactivate / เปิดดู Share Link'],
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
                                <p class="text-slate-600 mt-0.5">คลิกแท็บ Auth / Users / Permissions / Systems / Connectors / Notifications / UCM Access / Queue / API / Share Links เพื่อกรองเฉพาะหมวดนั้น</p>
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
                                    ['col' => 'ประเภท', 'desc' => 'หมวดหมู่เหตุการณ์ (Auth / Users / Permissions / Systems / Connectors / Notifications / UCM Access / Queue / API / Share Links)'],
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
                                    <th class="text-left px-3 py-2 font-semibold text-slate-600 border border-slate-100">หมวด</th>
                                    <th class="text-left px-3 py-2 font-semibold text-slate-600 border border-slate-100">เมื่อไหร่</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ([
                                    // Users
                                    ['event' => 'permissions_updated',    'group' => 'Users', 'desc' => 'เมื่อมีการเปลี่ยนแปลงสิทธิ์ผู้ใช้ (ผ่านหน้า Manage Permissions)'],
                                    ['event' => 'permissions_discovered', 'group' => 'Users', 'desc' => 'เมื่อ Discover สิทธิ์ผู้ใช้จากระบบปลายทาง (2-Way Sync)'],
                                    ['event' => 'account_status_changed', 'group' => 'Users', 'desc' => 'เมื่อเปิด/ปิด Account ของผู้ใช้ในระบบภายนอก'],
                                    ['event' => 'user_imported',          'group' => 'Users', 'desc' => 'เมื่อนำเข้าผู้ใช้รายคนจาก Active Directory'],
                                    ['event' => 'user_bulk_imported',     'group' => 'Users', 'desc' => 'เมื่อนำเข้าผู้ใช้แบบ Bulk จาก Active Directory'],
                                    ['event' => 'user_removed',           'group' => 'Users', 'desc' => 'เมื่อลบผู้ใช้ออกจากระบบ UCM'],
                                    ['event' => 'admin_level_updated',    'group' => 'Users', 'desc' => 'เมื่อเปลี่ยนระดับ Admin ของผู้ใช้'],
                                    // Systems
                                    ['event' => 'system_created',         'group' => 'Systems', 'desc' => 'เมื่อเพิ่มระบบที่เชื่อมต่อใหม่'],
                                    ['event' => 'system_updated',         'group' => 'Systems', 'desc' => 'เมื่อแก้ไขข้อมูลระบบที่เชื่อมต่อ'],
                                    ['event' => 'system_deleted',         'group' => 'Systems', 'desc' => 'เมื่อลบระบบที่เชื่อมต่อ'],
                                    ['event' => 'system_2way_toggled',    'group' => 'Systems', 'desc' => 'เมื่อเปิด/ปิด 2-Way Permission Sync ของระบบ'],
                                    // Permission Definitions
                                    ['event' => 'perm_def_created',       'group' => 'Permissions', 'desc' => 'เมื่อเพิ่ม Permission Definition ใหม่'],
                                    ['event' => 'perm_def_updated',       'group' => 'Permissions', 'desc' => 'เมื่อแก้ไข Permission Definition'],
                                    ['event' => 'perm_def_deleted',       'group' => 'Permissions', 'desc' => 'เมื่อลบ Permission Definition'],
                                    ['event' => 'perm_def_discovered',    'group' => 'Permissions', 'desc' => 'เมื่อ Discover Permission จากระบบปลายทางผ่าน Permission Center'],
                                    // Reference Data
                                    ['event' => 'group_record_created',   'group' => 'Reference', 'desc' => 'เมื่อเพิ่ม Reference Data (Master Data) ของระบบ'],
                                    ['event' => 'group_record_updated',   'group' => 'Reference', 'desc' => 'เมื่อแก้ไข Reference Data'],
                                    ['event' => 'group_record_deleted',   'group' => 'Reference', 'desc' => 'เมื่อลบ Reference Data'],
                                    ['event' => 'group_records_discovered','group' => 'Reference', 'desc' => 'เมื่อ Discover Reference Data จากระบบปลายทาง'],
                                    // Connectors
                                    ['event' => 'connector_created',      'group' => 'Connectors', 'desc' => 'เมื่อสร้าง Connector Wizard ใหม่'],
                                    ['event' => 'connector_updated',      'group' => 'Connectors', 'desc' => 'เมื่อแก้ไข Connector Wizard'],
                                    ['event' => 'connector_deleted',      'group' => 'Connectors', 'desc' => 'เมื่อลบ Connector Wizard'],
                                    // UCM Access Control
                                    ['event' => 'feature_level_updated',  'group' => 'UCM Access', 'desc' => 'เมื่อมีการเปลี่ยน Min Level ของ Feature ใน UCM'],
                                    ['event' => 'feature_grant_created',  'group' => 'UCM Access', 'desc' => 'เมื่อมอบสิทธิ์พิเศษ (Individual Grant) ให้ผู้ใช้'],
                                    ['event' => 'feature_grant_deleted',  'group' => 'UCM Access', 'desc' => 'เมื่อถอนสิทธิ์พิเศษ (Individual Grant) จากผู้ใช้'],
                                    // Notifications
                                    ['event' => 'notification_channel_created','group' => 'Notifications', 'desc' => 'เมื่อสร้าง Notification Channel ใหม่'],
                                    ['event' => 'notification_channel_updated','group' => 'Notifications', 'desc' => 'เมื่อแก้ไข Notification Channel'],
                                    ['event' => 'notification_channel_deleted','group' => 'Notifications', 'desc' => 'เมื่อลบ Notification Channel'],
                                    // Queue Monitor
                                    ['event' => 'queue_job_retried',      'group' => 'Queue', 'desc' => 'เมื่อ Retry Failed Job รายการเดียว'],
                                    ['event' => 'queue_all_retried',      'group' => 'Queue', 'desc' => 'เมื่อ Retry Failed Jobs ทั้งหมด'],
                                    ['event' => 'queue_job_deleted',      'group' => 'Queue', 'desc' => 'เมื่อลบ Failed Job รายการเดียว'],
                                    ['event' => 'queue_flushed',          'group' => 'Queue', 'desc' => 'เมื่อ Flush Failed Jobs ทั้งหมด'],
                                    // Security / API
                                    ['event' => 'login_failed',           'group' => 'Security', 'desc' => 'เมื่อมีการ Login ล้มเหลว (รหัสผ่านผิด หรือแผนกไม่มีสิทธิ์)'],
                                    ['event' => 'api_token_issued',       'group' => 'Security', 'desc' => 'เมื่อมีการออก API Token (admin credentials ผ่าน /api/auth/token)'],
                                    // Share Links
                                    ['event' => 'share_link_created',     'group' => 'Share Links', 'desc' => 'เมื่อสร้าง Share Link สำหรับ Permission Matrix'],
                                    ['event' => 'share_link_revoked',     'group' => 'Share Links', 'desc' => 'เมื่อยกเลิก (Revoke) Share Link'],
                                    // Wildcard
                                    ['event' => '*',                      'group' => '',            'desc' => 'Wildcard — รับแจ้งเตือนทุก event'],
                                ] as $row)
                                <tr class="border border-slate-100">
                                    <td class="px-3 py-2 font-mono font-semibold text-indigo-700 bg-slate-50/50 whitespace-nowrap">{{ $row['event'] }}</td>
                                    <td class="px-3 py-2 whitespace-nowrap">
                                        @if($row['group'])
                                            <span class="text-[10px] font-bold px-1.5 py-0.5 rounded
                                                {{ match($row['group']) {
                                                    'Users'         => 'bg-indigo-100 text-indigo-700',
                                                    'Systems'       => 'bg-emerald-100 text-emerald-700',
                                                    'Permissions'   => 'bg-violet-100 text-violet-700',
                                                    'Reference'     => 'bg-teal-100 text-teal-700',
                                                    'Connectors'    => 'bg-orange-100 text-orange-700',
                                                    'UCM Access'    => 'bg-purple-100 text-purple-700',
                                                    'Notifications' => 'bg-pink-100 text-pink-700',
                                                    'Queue'         => 'bg-amber-100 text-amber-700',
                                                    'Security'      => 'bg-rose-100 text-rose-700',
                                                    'Share Links'   => 'bg-cyan-100 text-cyan-700',
                                                    default         => 'bg-slate-100 text-slate-600',
                                                } }}">{{ $row['group'] }}</span>
                                        @endif
                                    </td>
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
    "username": "john.doe",
    "name": "John Doe",
    "system": "repair-system",
    "permissions": "view_report, edit_order",
    "performed_by": "admin",
    "description": "อัปเดตสิทธิ์ John Doe ในระบบ repair-system"
  },
  "timestamp": "2026-03-22T10:00:00+07:00",
  "source": "UCM"
}</pre>
                    </div>
                    <p class="text-xs text-slate-500 mt-2">Header <code class="font-mono bg-slate-100 px-1 rounded">X-UCM-Signature</code> = HMAC-SHA256 ของ JSON body (มีเฉพาะเมื่อตั้งค่า Secret)</p>
                </div>
            </div>
        </div>

        {{-- ── Permission Center ── --}}
        <div id="permission-center" class="bg-white rounded-2xl shadow-sm ring-1 ring-slate-100 overflow-hidden">
            <div class="flex items-center gap-3 px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                <div class="w-8 h-8 bg-violet-100 rounded-xl flex items-center justify-center">
                    <svg class="w-4 h-4 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 8h6m-6 4h6"/>
                    </svg>
                </div>
                <div>
                    <h2 class="font-bold text-slate-800">Permission Center (จัดการ Permissions)</h2>
                    <p class="text-xs text-slate-400 mt-0.5">จัดการนิยาม Permission ของทุกระบบในที่เดียว</p>
                </div>
                <span class="ml-auto text-[10px] font-bold bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded-full">Admin L1+</span>
            </div>
            <div class="px-6 py-5 space-y-5 text-sm text-slate-700 leading-relaxed">

                <p><strong>Permission Center</strong> เป็นหน้าจัดการ Permission Definition แบบรวมศูนย์ — สามารถดู เพิ่ม แก้ไข และลบ Permission ของทุกระบบที่เชื่อมต่อได้ในหน้าเดียว โดยไม่ต้องเข้าไปแต่ละระบบแยกกัน</p>

                <div class="grid sm:grid-cols-2 gap-3 text-xs">
                    <div class="p-4 bg-indigo-50 rounded-xl border border-indigo-100">
                        <p class="font-semibold text-indigo-900 mb-1">ต่างจากหน้าจัดการสิทธิ์ผู้ใช้อย่างไร?</p>
                        <ul class="text-indigo-800 space-y-1">
                            <li>• <strong>Permission Center</strong> — จัดการ <em>นิยาม</em> (Permission Definition) ว่ามี Permission อะไรในระบบบ้าง</li>
                            <li>• <strong>หน้าผู้ใช้ (Users → Profile)</strong> — มอบ/ถอน Permission ที่กำหนดไว้แล้วให้ผู้ใช้คนนั้น</li>
                        </ul>
                    </div>
                    <div class="p-4 bg-violet-50 rounded-xl border border-violet-100">
                        <p class="font-semibold text-violet-900 mb-1">ฟีเจอร์หลัก</p>
                        <ul class="text-violet-800 space-y-1">
                            <li>• Sidebar เลือกระบบ พร้อมค้นหาชื่อระบบ</li>
                            <li>• ดู Permission ทั้งหมด พร้อมจำนวน Assignments</li>
                            <li>• เพิ่ม/แก้ไข/ลบ Permission Definition</li>
                            <li>• Discover Permissions จากระบบปลายทาง</li>
                            <li>• ลิงก์ไป Permission Matrix Report โดยตรง</li>
                        </ul>
                    </div>
                </div>

                {{-- วิธีใช้งาน --}}
                <div class="border-t border-slate-100 pt-4">
                    <h3 class="font-bold text-slate-900 mb-3">วิธีใช้งาน</h3>
                    <div class="space-y-2">
                        @foreach ([
                            'เข้าเมนู <strong>จัดการ Permissions</strong> ในแถบนำทางด้านซ้าย',
                            'เลือกระบบจาก Sidebar ทางซ้าย (ค้นหาชื่อระบบได้)',
                            'เนื้อหาทางขวาจะแสดง Permission ทั้งหมดของระบบนั้น พร้อมจำนวนผู้ใช้ที่ได้รับแต่ละสิทธิ์',
                            'กด <strong>+ เพิ่ม Permission</strong> เพื่อนิยาม Permission ใหม่',
                            'กด <strong>แก้ไข</strong> เพื่อปรับ Display Name หรือ <code class="font-mono bg-slate-100 px-1 rounded text-xs">permission_key</code>',
                            'กด <strong>ลบ</strong> เพื่อลบ Permission (จะถามยืนยัน)',
                            'กด <strong>Discover</strong> เพื่อดึง Permission จากระบบปลายทางอัตโนมัติ',
                        ] as $i => $text)
                            <div class="flex items-start gap-3">
                                <span class="w-5 h-5 bg-violet-100 text-violet-700 text-xs font-bold rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">{{ $i+1 }}</span>
                                <p>{!! $text !!}</p>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="flex items-start gap-3 p-3.5 bg-amber-50 border border-amber-100 rounded-xl text-xs text-amber-800">
                    <svg class="w-4 h-4 text-amber-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                    <span>การลบ Permission จะลบ Assignment ของผู้ใช้ทุกคนสำหรับ Permission นั้นด้วย — กระบวนการนี้ย้อนกลับไม่ได้</span>
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
                <span class="ml-auto text-xs font-semibold text-indigo-700 bg-indigo-100 px-2.5 py-1 rounded-full">Admin L1 ขึ้นไป</span>
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

        {{-- ── Share Links ── --}}
        <div id="share-links" class="bg-white rounded-2xl shadow-sm ring-1 ring-slate-100 overflow-hidden">
            <div class="flex items-center gap-3 px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                <div class="w-8 h-8 bg-violet-100 rounded-xl flex items-center justify-center">
                    <svg class="w-4 h-4 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                    </svg>
                </div>
                <h2 class="font-bold text-slate-800">Share Links — Permission Matrix</h2>
                <span class="ml-auto text-xs font-semibold text-violet-700 bg-violet-100 px-2.5 py-1 rounded-full">Admin L1 ขึ้นไป</span>
            </div>
            <div class="px-6 py-5 space-y-5 text-sm text-slate-700 leading-relaxed">
                <p>Share Links ช่วยให้ Admin สร้าง <strong class="text-slate-900">ลิงก์แชร์ Permission Matrix แบบ Read-only</strong> ให้กับผู้ที่ไม่มีบัญชีในระบบ UCM เช่น ผู้บริหาร ผู้ตรวจสอบ หรือทีมงานภายนอก — โดยไม่ต้อง Login</p>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    @foreach ([
                        ['label' => 'ไม่ต้อง Login', 'desc' => 'ผู้รับลิงก์เปิดดูได้ทันทีผ่าน Browser ไม่ต้องมีบัญชีในระบบ', 'color' => 'violet'],
                        ['label' => 'กำหนดอายุลิงก์', 'desc' => 'ตั้งค่าวันหมดอายุได้ตั้งแต่ 1–365 วัน (default 7 วัน) ลิงก์จะใช้ไม่ได้อัตโนมัติเมื่อหมดอายุ', 'color' => 'indigo'],
                        ['label' => 'Filter ข้อมูล', 'desc' => 'จำกัดขอบเขตข้อมูลได้ตามระบบ แผนก หรือ Username เฉพาะที่ต้องการ', 'color' => 'sky'],
                    ] as $feat)
                        <div class="p-3 bg-{{ $feat['color'] }}-50 rounded-xl border border-{{ $feat['color'] }}-100">
                            <div class="font-semibold text-{{ $feat['color'] }}-900 text-xs mb-1">{{ $feat['label'] }}</div>
                            <p class="text-xs text-{{ $feat['color'] }}-800">{{ $feat['desc'] }}</p>
                        </div>
                    @endforeach
                </div>

                <div class="border-t border-slate-100 pt-4">
                    <h3 class="font-bold text-slate-900 mb-3">การสร้าง Share Link</h3>
                    <div class="space-y-2">
                        @foreach ([
                            'ไปที่เมนู <strong>Share Links</strong> ใน Sidebar (หมวด Admin)',
                            'กดปุ่ม <strong>สร้าง Share Link ใหม่</strong> ที่มุมขวาบน',
                            'กรอก <strong>ชื่อลิงก์</strong> เช่น "รายงานสิทธิ์ Q2-2026" และ <strong>อายุ</strong> (วัน)',
                            'เลือก <strong>ระบบ</strong> ที่ต้องการแสดง (ไม่เลือก = แสดงทุกระบบ)',
                            'เพิ่ม <strong>แผนก</strong> หรือ <strong>Username</strong> เพื่อจำกัดขอบเขต (ไม่ใส่ = แสดงทุกคน)',
                            'กด <strong>สร้าง Share Link</strong> — ระบบจะสร้าง URL ให้ทันที',
                            'กด <strong>คัดลอก</strong> เพื่อคัดลอก URL ส่งให้ผู้ที่ต้องการดู',
                        ] as $i => $text)
                            <div class="flex items-start gap-3">
                                <span class="w-5 h-5 bg-violet-100 text-violet-700 text-xs font-bold rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">{{ $i+1 }}</span>
                                <p class="text-slate-700">{!! $text !!}</p>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="border-t border-slate-100 pt-4">
                    <h3 class="font-bold text-slate-900 mb-3">การจัดการ Share Links</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs border-collapse">
                            <thead>
                                <tr class="bg-slate-50">
                                    <th class="text-left px-3 py-2 font-semibold text-slate-600 border border-slate-100">การกระทำ</th>
                                    <th class="text-left px-3 py-2 font-semibold text-slate-600 border border-slate-100">รายละเอียด</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ([
                                    ['action' => 'คัดลอก URL', 'desc' => 'กดปุ่ม "คัดลอก" ในแถวของลิงก์ — URL จะถูกคัดลอกไปยัง Clipboard'],
                                    ['action' => 'เปิดลิงก์', 'desc' => 'กดปุ่ม "เปิด" เพื่อดูตัวอย่างหน้า public ใน Tab ใหม่ (เฉพาะลิงก์ที่ active)'],
                                    ['action' => 'ยกเลิก', 'desc' => 'ลิงก์จะใช้งานไม่ได้ทันที ผู้ที่เปิด URL จะเห็นหน้าแจ้งว่าถูกยกเลิก'],
                                    ['action' => 'เปิดใช้อีกครั้ง', 'desc' => 'Reactivate ลิงก์ที่เคยยกเลิก — ลิงก์จะกลับมาใช้งานได้หากยังไม่หมดอายุ'],
                                ] as $row)
                                <tr class="border border-slate-100">
                                    <td class="px-3 py-2 font-semibold text-violet-700 bg-violet-50/50 whitespace-nowrap">{{ $row['action'] }}</td>
                                    <td class="px-3 py-2 text-slate-600">{{ $row['desc'] }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="border-t border-slate-100 pt-4">
                    <h3 class="font-bold text-slate-900 mb-3">Progress Bar อายุลิงก์</h3>
                    <div class="space-y-2 text-sm text-slate-600">
                        <p>ในแต่ละ card ของ Share Link จะมี Progress Bar แสดงอายุลิงก์ที่เหลืออยู่ พร้อมเปลี่ยนสีตามสถานะ:</p>
                        <div class="flex flex-wrap gap-3 pt-1">
                            @foreach ([
                                ['color' => 'bg-violet-400', 'label' => 'สีม่วง', 'desc' => 'เหลือ > 50% ของอายุลิงก์'],
                                ['color' => 'bg-amber-400',  'label' => 'สีเหลือง', 'desc' => 'เหลือ 20–50%'],
                                ['color' => 'bg-red-400',    'label' => 'สีแดง', 'desc' => 'เหลือน้อยกว่า 20% (ใกล้หมดอายุ)'],
                            ] as $bar)
                                <div class="flex items-center gap-2 text-xs text-slate-600">
                                    <div class="w-8 h-2 rounded-full {{ $bar['color'] }}"></div>
                                    <strong>{{ $bar['label'] }}</strong> — {{ $bar['desc'] }}
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="flex items-start gap-3 p-3.5 bg-amber-50 border border-amber-100 rounded-xl text-xs text-amber-800">
                    <svg class="w-4 h-4 text-amber-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <span>Share Link แสดงข้อมูล <strong>ณ เวลาที่เปิดลิงก์</strong> เสมอ (real-time) — ไม่ใช่ Snapshot ตอนสร้างลิงก์ ทุกการเปลี่ยนสิทธิ์ใน UCM จะสะท้อนในลิงก์ทันที รวมถึงทุกการเปิดดูจะถูกบันทึกใน Audit Log อัตโนมัติ</span>
                </div>
            </div>
        </div>

        {{-- ── Permission Timeline ── --}}
        <div id="permission-timeline" class="bg-white rounded-2xl shadow-sm ring-1 ring-slate-100 overflow-hidden">
            <div class="flex items-center gap-3 px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                <div class="w-8 h-8 bg-violet-100 rounded-xl flex items-center justify-center">
                    <svg class="w-4 h-4 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h2 class="font-bold text-slate-800">Permission Timeline</h2>
            </div>
            <div class="px-6 py-5 space-y-4 text-sm text-slate-700 leading-relaxed">
                <p><strong>Permission Timeline</strong> แสดงประวัติการเปลี่ยนแปลงสิทธิ์ทั้งหมดของผู้ใช้คนใดคนหนึ่ง พร้อม Permission Matrix ปัจจุบัน</p>
                <div class="space-y-3">
                    <div class="flex gap-3 p-3 bg-violet-50 rounded-xl border border-violet-100">
                        <div class="w-2 h-2 rounded-full bg-violet-500 mt-1.5 flex-shrink-0"></div>
                        <div>
                            <div class="font-semibold text-slate-900 mb-0.5 text-xs">วิธีเข้าใช้งาน</div>
                            <p class="text-slate-600 text-xs">ไปที่หน้า <strong>จัดการผู้ใช้</strong> → กด <strong>Permission Timeline</strong> (ปุ่มสีม่วง) บนการ์ดผู้ใช้ที่ต้องการ</p>
                        </div>
                    </div>
                    <div class="flex gap-3 p-3 bg-slate-50 rounded-xl border border-slate-100">
                        <div class="w-2 h-2 rounded-full bg-indigo-400 mt-1.5 flex-shrink-0"></div>
                        <div>
                            <div class="font-semibold text-slate-900 mb-0.5 text-xs">Permission Matrix (ด้านซ้าย)</div>
                            <p class="text-slate-600 text-xs">แสดงสิทธิ์ปัจจุบันของผู้ใช้ในทุกระบบ แยกตามระบบและ Permission key — ✓ = มีสิทธิ์ / — = ไม่มีสิทธิ์</p>
                        </div>
                    </div>
                    <div class="flex gap-3 p-3 bg-slate-50 rounded-xl border border-slate-100">
                        <div class="w-2 h-2 rounded-full bg-sky-400 mt-1.5 flex-shrink-0"></div>
                        <div>
                            <div class="font-semibold text-slate-900 mb-0.5 text-xs">Timeline ประวัติ (ด้านขวา)</div>
                            <p class="text-slate-600 text-xs">แสดง 100 รายการล่าสุดของเหตุการณ์ที่เกี่ยวกับสิทธิ์ผู้ใช้ แต่ละรายการมี: Admin ที่ดำเนินการ, วันเวลา, คำอธิบาย และ Permission Matrix Snapshot ณ เวลานั้น</p>
                        </div>
                    </div>
                </div>
                <div class="flex items-start gap-3 p-3.5 bg-blue-50 border border-blue-100 rounded-xl text-xs text-blue-800">
                    <svg class="w-4 h-4 text-blue-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                    <span>ต้องการสิทธิ์ <code class="font-mono bg-blue-100 px-1 rounded">permission_timeline</code> (ค่าเริ่มต้น: Admin ระดับ 1 ขึ้นไป) — ปรับระดับขั้นต่ำได้ผ่านหน้า <strong>สิทธิ์ระบบ UCM</strong> ข้อมูลประวัติจะถูกบันทึกตั้งแต่ระบบ Audit Log เริ่มทำงาน</span>
                </div>
            </div>
        </div>

        {{-- ── ผู้ใช้ที่ไม่ได้ใช้งาน ── --}}
        <div id="inactive-users" class="bg-white rounded-2xl shadow-sm ring-1 ring-slate-100 overflow-hidden">
            <div class="flex items-center gap-3 px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                <div class="w-8 h-8 bg-rose-100 rounded-xl flex items-center justify-center">
                    <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                    </svg>
                </div>
                <h2 class="font-bold text-slate-800">ผู้ใช้ที่ไม่ได้ใช้งาน</h2>
            </div>
            <div class="px-6 py-5 space-y-4 text-sm text-slate-700 leading-relaxed">
                <p>หน้า <strong>ผู้ใช้ที่ไม่ได้ใช้งาน</strong> แสดงรายชื่อผู้ใช้ที่ไม่ได้ Login เกินช่วงเวลาที่กำหนด ช่วยในการตรวจสอบและจัดการสิทธิ์ผู้ใช้ที่ไม่ได้ Active</p>
                <div class="space-y-2">
                    <div class="flex gap-3 p-3 bg-rose-50 rounded-xl border border-rose-100">
                        <div class="w-2 h-2 rounded-full bg-rose-500 mt-1.5 flex-shrink-0"></div>
                        <div>
                            <div class="font-semibold text-slate-900 mb-0.5 text-xs">วิธีเข้าใช้งาน</div>
                            <p class="text-slate-600 text-xs">ไปที่ <strong>จัดการผู้ใช้</strong> → กดปุ่ม <strong>ไม่ได้ใช้งาน</strong> (ปุ่มสีแดง) ที่แถบเครื่องมือด้านบน</p>
                        </div>
                    </div>
                    <div class="overflow-hidden rounded-xl border border-slate-200 text-xs">
                        <div class="grid grid-cols-2 bg-slate-50 font-bold text-slate-500 px-3 py-2 border-b border-slate-200">
                            <div>ฟีเจอร์</div><div>รายละเอียด</div>
                        </div>
                        @foreach ([
                            ['ตัวกรองช่วงเวลา', 'เลือกได้ 3 ระดับ: 30 / 60 / 90 วัน'],
                            ['ค้นหา', 'ค้นหาจากชื่อ, username หรือแผนก'],
                            ['ข้อมูลที่แสดง', 'ชื่อผู้ใช้, แผนก, จำนวนสิทธิ์ที่มี, วันที่ Login ล่าสุด'],
                            ['ผู้ใช้ที่ไม่เคย Login', 'แสดงเป็น "ไม่มีข้อมูล" (Login ก่อน deploy เวอร์ชันนี้)'],
                        ] as [$feat, $desc])
                        <div class="grid grid-cols-2 px-3 py-2 border-b border-slate-100 last:border-0">
                            <div class="font-medium text-slate-700">{{ $feat }}</div>
                            <div class="text-slate-500">{{ $desc }}</div>
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="flex items-start gap-3 p-3.5 bg-amber-50 border border-amber-200 rounded-xl text-xs text-amber-800">
                    <svg class="w-4 h-4 text-amber-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                    <span>ข้อมูล Login จะถูกบันทึกตั้งแต่เวอร์ชันที่มีการ deploy migration <code class="font-mono bg-amber-100 px-1 rounded">add_last_login_at_to_ucm_users</code> เป็นต้นไป — ผู้ใช้ที่ Login ก่อนหน้านั้นจะแสดงเป็น "ไม่มีข้อมูล"</span>
                </div>
            </div>
        </div>

        {{-- ── ทดสอบการเชื่อมต่อระบบ (Health Check) ── --}}
        <div id="health-check" class="bg-white rounded-2xl shadow-sm ring-1 ring-slate-100 overflow-hidden">
            <div class="flex items-center gap-3 px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                <div class="w-8 h-8 bg-emerald-100 rounded-xl flex items-center justify-center">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h2 class="font-bold text-slate-800">ทดสอบการเชื่อมต่อระบบ (Health Check)</h2>
            </div>
            <div class="px-6 py-5 space-y-4 text-sm text-slate-700 leading-relaxed">
                <p>ปุ่ม <strong>ทดสอบ</strong> บนการ์ดระบบในหน้า <strong>ระบบที่เชื่อมต่อ</strong> ใช้ตรวจสอบว่าระบบปลายทางสามารถเชื่อมต่อได้หรือไม่ โดยไม่ต้อง Sync จริง</p>
                <div class="space-y-2">
                    @foreach ([
                        ['step' => '1', 'color' => 'bg-emerald-600', 'text' => 'กดปุ่ม <strong>ทดสอบ</strong> (สีเขียว) บนการ์ดระบบที่ต้องการ'],
                        ['step' => '2', 'color' => 'bg-emerald-600', 'text' => 'ระบบส่งคำขอ POST ไปยัง <code class="font-mono text-xs bg-slate-100 px-1 rounded">/systems/{id}/health-check</code>'],
                        ['step' => '3', 'color' => 'bg-emerald-600', 'text' => 'ผลลัพธ์แสดงเป็น Flash ข้อความสีเขียว (เชื่อมต่อสำเร็จ) หรือสีแดง (ไม่สามารถเชื่อมต่อได้) ด้านบนหน้า'],
                    ] as $item)
                        <div class="flex items-start gap-3">
                            <span class="w-5 h-5 {{ $item['color'] }} text-white text-xs font-bold rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">{{ $item['step'] }}</span>
                            <p class="text-xs text-slate-600">{!! $item['text'] !!}</p>
                        </div>
                    @endforeach
                </div>
                <div class="flex items-start gap-3 p-3.5 bg-blue-50 border border-blue-100 rounded-xl text-xs text-blue-800">
                    <svg class="w-4 h-4 text-blue-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                    <span>ปุ่มทดสอบปรากฏเฉพาะ Admin ระดับ 1 ขึ้นไป — ระบบที่ไม่มี Adapter (ยังไม่ได้ตั้งค่า Connector) จะแจ้งว่า "ระบบนี้ไม่มี Adapter"</span>
                </div>
            </div>
        </div>

</div>

@endsection
