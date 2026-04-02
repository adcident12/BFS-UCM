<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>อนุญาตการเข้าถึง — UCM</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'] }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Inter', ui-sans-serif, system-ui, sans-serif; }
        .bg-grid {
            background-color: #0f172a;
            background-image:
                linear-gradient(rgba(99,102,241,0.07) 1px, transparent 1px),
                linear-gradient(90deg, rgba(99,102,241,0.07) 1px, transparent 1px);
            background-size: 40px 40px;
        }
        .card-glow {
            box-shadow: 0 0 0 1px rgba(99,102,241,0.12), 0 25px 50px rgba(0,0,0,0.4), 0 0 80px rgba(99,102,241,0.08);
        }
    </style>
</head>
<body class="bg-grid min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-md">
        {{-- Card --}}
        <div class="bg-slate-900 rounded-2xl p-8 card-glow">

            {{-- UCM + App header --}}
            <div class="text-center mb-7">
                <div class="flex items-center justify-center gap-3 mb-5">
                    {{-- UCM icon --}}
                    <div class="w-12 h-12 rounded-xl bg-indigo-600 flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>

                    <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>

                    {{-- Client app icon placeholder --}}
                    <div class="w-12 h-12 rounded-xl bg-slate-700 border border-slate-600 flex items-center justify-center flex-shrink-0">
                        @if ($client->homepage_url)
                            <img src="https://www.google.com/s2/favicons?sz=32&domain_url={{ urlencode($client->homepage_url) }}"
                                 alt="{{ $client->name }}" class="w-6 h-6 rounded"
                                 onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                        @endif
                        <svg class="w-6 h-6 text-slate-400 {{ $client->homepage_url ? 'hidden' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                </div>

                <h1 class="text-xl font-semibold text-white mb-1">
                    <span class="text-indigo-400">{{ $client->name }}</span> ขอเข้าถึงบัญชีของคุณ
                </h1>
                <p class="text-sm text-slate-400">
                    คุณกำลังเข้าสู่ระบบในฐานะ
                    <span class="text-slate-200 font-medium">{{ $user->name ?: $user->username }}</span>
                    ({{ $user->username }})
                </p>
            </div>

            {{-- Description --}}
            @if ($client->description)
                <p class="text-sm text-slate-400 text-center mb-5 leading-relaxed">{{ $client->description }}</p>
            @endif

            {{-- Requested Scopes --}}
            <div class="mb-6">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-3">แอปนี้จะสามารถ</p>
                <ul class="space-y-2">
                    @foreach ($scopes as $scope)
                        <li class="flex items-start gap-3 bg-slate-800/60 rounded-xl px-4 py-3">
                            <div class="w-5 h-5 rounded-full bg-emerald-500/20 flex items-center justify-center flex-shrink-0 mt-0.5">
                                <svg class="w-3 h-3 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-slate-200">
                                    @switch($scope)
                                        @case('openid') อ่านข้อมูลตัวตนพื้นฐาน (OpenID) @break
                                        @case('profile') อ่านข้อมูลโปรไฟล์ (ชื่อ, แผนก, ตำแหน่ง) @break
                                        @case('email') อ่านที่อยู่อีเมล @break
                                        @case('permissions') อ่านสิทธิ์การใช้งานในระบบนี้ @break
                                        @case('users:read') อ่านรายชื่อผู้ใช้ทั้งหมด @break
                                        @default {{ $scope }}
                                    @endswitch
                                </p>
                                <p class="text-xs text-slate-500 mt-0.5">
                                    @switch($scope)
                                        @case('openid') รหัสผู้ใช้และข้อมูล session @break
                                        @case('profile') ชื่อ-นามสกุล, ชื่อผู้ใช้, แผนก, ตำแหน่งงาน @break
                                        @case('email') อีเมลที่ใช้งานในระบบ @break
                                        @case('permissions') รายการสิทธิ์ที่คุณมีใน {{ $client->name }} @break
                                        @case('users:read') รายชื่อพนักงานทั้งองค์กร @break
                                        @default scope: {{ $scope }}
                                    @endswitch
                                </p>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- Security notice --}}
            <div class="flex items-start gap-2 bg-slate-800/40 rounded-xl px-4 py-3 mb-6">
                <svg class="w-4 h-4 text-amber-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                <p class="text-xs text-slate-400 leading-relaxed">
                    UCM จะไม่แชร์รหัสผ่านของคุณกับ {{ $client->name }}
                    คุณสามารถยกเลิกการเข้าถึงได้ตลอดเวลาจากการตั้งค่าบัญชี
                </p>
            </div>

            {{-- Action buttons --}}
            <form method="POST" action="{{ route('oauth.authorize.approve') }}">
                @csrf
                <input type="hidden" name="client_id"             value="{{ $params['client_id'] }}">
                <input type="hidden" name="redirect_uri"          value="{{ $params['redirect_uri'] }}">
                <input type="hidden" name="scope"                 value="{{ implode(' ', $scopes) }}">
                <input type="hidden" name="state"                 value="{{ $params['state'] ?? '' }}">
                <input type="hidden" name="nonce"                 value="{{ $params['nonce'] ?? '' }}">
                <input type="hidden" name="code_challenge"        value="{{ $params['code_challenge'] ?? '' }}">
                <input type="hidden" name="code_challenge_method" value="{{ $params['code_challenge_method'] ?? 'S256' }}">

                <div class="flex gap-3">
                    <button type="submit" name="action" value="deny"
                            class="flex-1 px-4 py-2.5 rounded-xl text-sm font-medium text-slate-300
                                   bg-slate-700 hover:bg-slate-600 border border-slate-600
                                   transition-colors duration-150">
                        ปฏิเสธ
                    </button>
                    <button type="submit" name="action" value="approve"
                            class="flex-1 px-4 py-2.5 rounded-xl text-sm font-semibold text-white
                                   bg-indigo-600 hover:bg-indigo-500
                                   transition-colors duration-150">
                        อนุญาต
                    </button>
                </div>
            </form>

            {{-- Footer --}}
            <p class="text-center text-xs text-slate-600 mt-5">
                ระบบ User Centralized Management (UCM)
                @if ($client->homepage_url)
                    &nbsp;·&nbsp;
                    <a href="{{ $client->homepage_url }}" target="_blank" rel="noopener noreferrer"
                       class="text-slate-500 hover:text-slate-300 underline transition-colors">
                        เว็บไซต์ของแอป
                    </a>
                @endif
            </p>

        </div>
    </div>

</body>
</html>
