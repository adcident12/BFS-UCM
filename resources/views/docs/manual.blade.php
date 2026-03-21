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
    ['id' => 'overview',     'label' => 'ภาพรวมระบบ'],
    ['id' => 'login',        'label' => 'การเข้าสู่ระบบ'],
    ['id' => 'dashboard',    'label' => 'Dashboard'],
    ['id' => 'users',        'label' => 'จัดการผู้ใช้'],
    ['id' => 'export-csv',   'label' => 'ส่งออก CSV'],
    ['id' => 'permissions',  'label' => 'จัดการสิทธิ์'],
    ['id' => 'systems',      'label' => 'ระบบที่เชื่อมต่อ'],
    ['id' => 'ref-data',     'label' => 'ข้อมูล Reference'],
    ['id' => 'twoway-sync',  'label' => '2-Way Sync'],
    ['id' => 'admin-levels', 'label' => 'สิทธิ์ Admin'],
    ['id' => 'ad-check',     'label' => 'ตรวจสอบ AD'],
    ['id' => 'sync',         'label' => 'การ Sync สิทธิ์'],
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
                        <p class="text-xs text-emerald-700">กำหนด Editable / Read Only / Denied ต่อระบบและโมดูลได้ละเอียด</p>
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
                <p>หน้า Dashboard แสดงภาพรวมของระบบ UCM ทั้งหมด แบ่งเป็น 3 ส่วน:</p>
                <div class="space-y-3">
                    <div class="flex gap-3 p-3 bg-slate-50 rounded-xl border border-slate-100">
                        <div class="w-2 h-2 rounded-full bg-indigo-500 mt-1.5 flex-shrink-0"></div>
                        <div>
                            <div class="font-semibold text-slate-900 mb-0.5">Stat Cards (4 การ์ด)</div>
                            <p class="text-slate-600 text-xs">แสดงจำนวนระบบที่เชื่อมต่อ, ผู้ใช้งานทั้งหมด, จำนวน Sync สำเร็จและล้มเหลวในวันนี้</p>
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
                        <div class="w-2 h-2 rounded-full bg-violet-500 mt-1.5 flex-shrink-0"></div>
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
                    <h3 class="font-bold text-slate-900 mb-3">ระดับสิทธิ์</h3>
                    <div class="space-y-2">
                        <div class="flex items-center gap-3 p-3 bg-indigo-50 rounded-xl border border-indigo-100">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-indigo-200 text-indigo-800">Editable</span>
                            <p class="text-slate-700 text-xs">สามารถเข้าถึงและแก้ไขข้อมูลในโมดูลนั้นได้ (s_id = 2)</p>
                        </div>
                        <div class="flex items-center gap-3 p-3 bg-sky-50 rounded-xl border border-sky-100">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-sky-200 text-sky-800">Read Only</span>
                            <p class="text-slate-700 text-xs">สามารถดูข้อมูลได้อย่างเดียว ไม่สามารถแก้ไขได้ (s_id = 1)</p>
                        </div>
                        <div class="flex items-center gap-3 p-3 bg-rose-50 rounded-xl border border-rose-100">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-rose-200 text-rose-800">Denied</span>
                            <p class="text-slate-700 text-xs">ไม่มีสิทธิ์เข้าถึงโมดูลนั้น (s_id = 3)</p>
                        </div>
                    </div>
                </div>

                <div class="border-t border-slate-100 pt-4">
                    <h3 class="font-bold text-slate-900 mb-2">วิธีการกำหนดสิทธิ์</h3>
                    <div class="space-y-2">
                        @foreach ([
                            'เลือกระบบที่ต้องการกำหนดสิทธิ์ (แต่ละระบบมีส่วนแยก)',
                            'แต่ละกลุ่ม (เช่น Daily Flight, PAX, RAMP) แสดงเป็นแถว — เลือก Editable / Read Only / Denied ด้วยการคลิก Radio Pill',
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
                            <p class="text-slate-700">สิทธิ์ผู้ใช้ใน UCM ไม่ตรงกับระบบปลายทาง — กด "บันทึกสิทธิ์" เพื่อ Sync ใหม่</p>
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

    </div>
</div>

{{-- Smooth scroll highlight active TOC item --}}
<script>
(function () {
    var links = document.querySelectorAll('.toc-link');
    var ids   = Array.from(links).map(function (l) { return l.getAttribute('href').slice(1); });

    function onScroll() {
        var scrollY = window.scrollY + 120;
        var active  = ids[0];
        ids.forEach(function (id) {
            var el = document.getElementById(id);
            if (el && el.offsetTop <= scrollY) active = id;
        });
        links.forEach(function (l) {
            var isCur = l.getAttribute('href') === '#' + active;
            l.className = 'toc-link flex items-center gap-2 px-3 py-2 rounded-xl text-sm transition-colors ' +
                (isCur ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-slate-500 hover:text-slate-800 hover:bg-slate-50');
            l.querySelector('span').className = 'w-1.5 h-1.5 rounded-full flex-shrink-0 ' +
                (isCur ? 'bg-indigo-500' : 'bg-slate-300');
        });
    }

    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
})();
</script>

@endsection
