@extends('errors.layout')

@section('title', '404 ไม่พบหน้าที่ขอ')

@section('body')
    <div class="icon-box" style="background:rgba(99,102,241,0.15)">
        <svg fill="none" stroke="#818cf8" stroke-width="1.6" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
    </div>
    <div class="code">404</div>
    <h1 class="title">ไม่พบหน้าที่คุณขอ</h1>
    <p class="desc">
        หน้านี้อาจถูกย้าย ลบ หรือ URL ที่ระบุไม่ถูกต้อง<br>
        ลองตรวจสอบ URL อีกครั้ง หรือกลับไปหน้าหลัก
    </p>
    <div class="actions">
        <a href="{{ url('/') }}" class="btn">
            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            กลับหน้าหลัก
        </a>
        <a href="javascript:history.back()" class="btn-ghost">
            ← ย้อนกลับ
        </a>
    </div>
@endsection
