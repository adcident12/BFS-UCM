@extends('errors.layout')

@section('title', '419 Session หมดอายุ')

@section('body')
    <div class="icon-box" style="background:rgba(168,85,247,0.12)">
        <svg fill="none" stroke="#c084fc" stroke-width="1.6" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
    </div>
    <div class="code" style="background:linear-gradient(135deg,#c084fc,#818cf8);-webkit-background-clip:text;background-clip:text">419</div>
    <h1 class="title">Session หมดอายุแล้ว</h1>
    <p class="desc">
        หน้านี้ถูกเปิดค้างไว้นานเกินไปจน session หมดอายุ<br>
        กรุณากลับไปและลองทำรายการใหม่อีกครั้ง
    </p>
    <div class="actions">
        <a href="javascript:history.back()" class="btn" style="background:#9333ea;box-shadow:0 4px 12px rgba(147,51,234,0.35)">
            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            กลับและลองใหม่
        </a>
        <a href="{{ url('/') }}" class="btn-ghost">กลับหน้าหลัก</a>
    </div>
@endsection
