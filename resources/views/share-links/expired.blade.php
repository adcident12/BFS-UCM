<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ลิงก์ไม่สามารถใช้งานได้ — UCM</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:ital,wght@0,400;0,500;0,600;0,700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
    <style>body { font-family: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif; }</style>
</head>
<body class="min-h-screen flex flex-col" style="background: #f1f5f9">

    {{-- Header --}}
    <header style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 60%, #0c1a2e 100%)"
            class="px-6 py-4 shadow-xl relative overflow-hidden">
        <div class="absolute inset-0 pointer-events-none"
             style="background-image:linear-gradient(rgba(255,255,255,0.03) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,0.03) 1px,transparent 1px);background-size:28px 28px"></div>
        <div class="relative max-w-screen-xl mx-auto flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg"
                 style="background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #06b6d4 100%)">
                <svg class="w-4.5 h-4.5 text-white w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <div>
                <div class="text-sm font-bold text-white leading-tight">UCM</div>
                <div class="text-[11px] text-slate-400 leading-tight font-medium">User Centralized Management</div>
            </div>
        </div>
    </header>

    {{-- Content --}}
    <main class="flex-1 flex items-center justify-center px-4 py-12">
        <div class="w-full max-w-sm">

            @php
                $isRevoked = $shareLink && ! $shareLink->is_active;
                $isExpired = $shareLink && $shareLink->expires_at->isPast();
            @endphp

            {{-- Card --}}
            <div class="bg-white rounded-3xl shadow-xl border border-slate-100 overflow-hidden">

                {{-- Colored top band --}}
                <div class="h-1.5 w-full
                    {{ $isRevoked ? 'bg-red-400' : 'bg-slate-300' }}"></div>

                <div class="px-8 py-10 text-center">

                    {{-- Icon circle --}}
                    <div class="w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6
                                {{ $isRevoked ? 'bg-red-50 border-2 border-red-100' : 'bg-slate-50 border-2 border-slate-100' }}">
                        @if ($isRevoked)
                            <svg class="w-9 h-9 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                            </svg>
                        @elseif ($isExpired)
                            <svg class="w-9 h-9 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        @else
                            <svg class="w-9 h-9 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        @endif
                    </div>

                    {{-- Status badge --}}
                    <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold mb-4
                                {{ $isRevoked ? 'bg-red-50 text-red-600 border border-red-100' : 'bg-slate-100 text-slate-500 border border-slate-200' }}">
                        <span class="w-1.5 h-1.5 rounded-full flex-shrink-0
                                     {{ $isRevoked ? 'bg-red-400' : 'bg-slate-400' }}"></span>
                        {{ $isRevoked ? 'ถูกยกเลิกแล้ว' : ($isExpired ? 'หมดอายุแล้ว' : 'ไม่พบลิงก์นี้') }}
                    </div>

                    {{-- Heading --}}
                    @if ($isRevoked)
                        <h1 class="text-xl font-bold text-slate-800 mb-2">ลิงก์ถูกยกเลิก</h1>
                    @elseif ($isExpired)
                        <h1 class="text-xl font-bold text-slate-800 mb-2">ลิงก์หมดอายุแล้ว</h1>
                    @else
                        <h1 class="text-xl font-bold text-slate-800 mb-2">ไม่พบลิงก์นี้</h1>
                    @endif

                    {{-- Description --}}
                    @if ($isRevoked)
                        <p class="text-sm text-slate-500 leading-relaxed">
                            ลิงก์
                            <span class="font-semibold text-slate-700 bg-slate-50 px-1.5 py-0.5 rounded-md border border-slate-200 text-xs font-mono">
                                {{ $shareLink->label }}
                            </span>
                            <br>ถูกยกเลิกโดยผู้ดูแลระบบ
                        </p>
                    @elseif ($isExpired)
                        <p class="text-sm text-slate-500 leading-relaxed">
                            ลิงก์
                            <span class="font-semibold text-slate-700 bg-slate-50 px-1.5 py-0.5 rounded-md border border-slate-200 text-xs font-mono">
                                {{ $shareLink->label }}
                            </span>
                            <br>หมดอายุเมื่อ
                            <span class="font-semibold text-slate-600">{{ $shareLink->expires_at->format('d/m/Y เวลา H:i') }}</span>
                        </p>
                    @else
                        <p class="text-sm text-slate-500 leading-relaxed">
                            ลิงก์ที่คุณเข้าถึงไม่มีอยู่ในระบบ<br>
                            กรุณาตรวจสอบ URL อีกครั้ง
                        </p>
                    @endif

                    {{-- Action hint --}}
                    @if ($shareLink)
                        <div class="mt-6 bg-slate-50 rounded-xl px-4 py-3 border border-slate-100 text-xs text-slate-500 font-medium">
                            <svg class="w-4 h-4 text-slate-400 mx-auto mb-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            กรุณาติดต่อผู้ที่แชร์ลิงก์นี้ให้คุณ<br>เพื่อขอลิงก์ใหม่
                        </div>
                    @endif

                </div>

                {{-- Footer --}}
                <div class="px-8 py-4 bg-slate-50 border-t border-slate-100 text-center">
                    <p class="text-[11px] text-slate-400 font-medium">
                        UCM — User Centralized Management
                        <span class="mx-1.5 text-slate-200">·</span>
                        Permission Matrix · Read-only
                    </p>
                </div>

            </div>

        </div>
    </main>

</body>
</html>
