@extends('errors.layout')

@section('title', '429 ส่งคำขอถี่เกินไป')

@section('body')
    <div class="icon-box" style="background:rgba(249,115,22,0.12)">
        <svg fill="none" stroke="#fb923c" stroke-width="1.6" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
        </svg>
    </div>
    <div class="code" style="background:linear-gradient(135deg,#fb923c,#f87171);-webkit-background-clip:text;background-clip:text">429</div>
    <h1 class="title">ส่งคำขอถี่เกินไป</h1>
    <p class="desc">
        คุณลองเข้าสู่ระบบหลายครั้งเกินไปในช่วงเวลาสั้น<br>
        กรุณารอสักครู่แล้วลองใหม่อีกครั้ง
    </p>
    <div class="detail-box">
        เพื่อความปลอดภัย ระบบจะล็อคชั่วคราวเมื่อมีการพยายามเข้าสู่ระบบซ้ำๆ<br>
        หากลืมรหัสผ่าน กรุณาติดต่อ IT Support
    </div>
    <div class="actions">
        <a href="{{ route('login') }}" class="btn" style="background:#ea580c;box-shadow:0 4px 12px rgba(234,88,12,0.35)">
            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            ลองใหม่อีกครั้ง
        </a>
    </div>
@endsection
