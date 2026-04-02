@extends('errors.layout')

@section('title', '503 ระบบอยู่ระหว่างบำรุงรักษา')

@section('body')
    <div class="icon-box" style="background:rgba(14,165,233,0.12)">
        <svg fill="none" stroke="#38bdf8" stroke-width="1.6" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
    </div>
    <div class="code" style="background:linear-gradient(135deg,#38bdf8,#818cf8);-webkit-background-clip:text;background-clip:text">503</div>
    <h1 class="title">ระบบอยู่ระหว่างบำรุงรักษา</h1>
    <p class="desc">
        UCM กำลังอัปเดตหรือบำรุงรักษาชั่วคราว<br>
        กรุณาลองใหม่อีกครั้งในอีกสักครู่
    </p>
    @if(isset($exception) && method_exists($exception,'getMessage') && $exception->getMessage())
        <div class="detail-box">{{ $exception->getMessage() }}</div>
    @endif
    <div class="actions">
        <a href="javascript:location.reload()" class="btn" style="background:#0284c7;box-shadow:0 4px 12px rgba(2,132,199,0.35)">
            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            ลองใหม่อีกครั้ง
        </a>
    </div>
@endsection
