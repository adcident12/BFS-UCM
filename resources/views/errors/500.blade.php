@extends('errors.layout')

@section('title', '500 เกิดข้อผิดพลาดในระบบ')

@section('body')
    <div class="icon-box" style="background:rgba(239,68,68,0.12)">
        <svg fill="none" stroke="#f87171" stroke-width="1.6" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
    </div>
    <div class="code" style="background:linear-gradient(135deg,#f87171,#fb7185);-webkit-background-clip:text;background-clip:text">500</div>
    <h1 class="title">เกิดข้อผิดพลาดภายในระบบ</h1>
    <p class="desc">
        เกิดปัญหาบางอย่างที่ฝั่งเซิร์ฟเวอร์<br>
        ทีมงานได้รับแจ้งอัตโนมัติแล้ว กรุณาลองใหม่อีกครั้ง
    </p>
    <div class="actions">
        <a href="javascript:location.reload()" class="btn" style="background:#dc2626;box-shadow:0 4px 12px rgba(220,38,38,0.35)">
            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            ลองใหม่อีกครั้ง
        </a>
        <a href="{{ url('/') }}" class="btn-ghost">กลับหน้าหลัก</a>
    </div>
@endsection
