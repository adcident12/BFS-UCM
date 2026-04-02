@extends('errors.layout')

@section('title', '403 ไม่มีสิทธิ์เข้าถึง')

@section('body')
    <div class="icon-box" style="background:rgba(245,158,11,0.12)">
        <svg fill="none" stroke="#f59e0b" stroke-width="1.6" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
        </svg>
    </div>
    <div class="code" style="background:linear-gradient(135deg,#fbbf24,#f97316);-webkit-background-clip:text;background-clip:text">403</div>
    <h1 class="title">คุณไม่มีสิทธิ์เข้าถึงหน้านี้</h1>
    <p class="desc">
        บัญชีของคุณไม่มีสิทธิ์ดำเนินการนี้<br>
        หากคิดว่าเป็นความผิดพลาด กรุณาติดต่อผู้ดูแลระบบ
    </p>
    @if(!empty($exception) && $exception->getMessage())
        <div class="detail-box">{{ $exception->getMessage() }}</div>
    @endif
    <div class="actions">
        <a href="{{ url('/') }}" class="btn" style="background:#d97706;box-shadow:0 4px 12px rgba(217,119,6,0.35)">
            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            กลับหน้าหลัก
        </a>
        <a href="javascript:history.back()" class="btn-ghost">← ย้อนกลับ</a>
    </div>
@endsection
