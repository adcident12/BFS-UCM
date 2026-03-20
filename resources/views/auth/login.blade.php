<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบ — UCM</title>
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
        @keyframes float { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-8px)} }
        .logo-float { animation: float 4s ease-in-out infinite; }
    </style>
</head>
<body class="min-h-screen bg-grid flex items-center justify-center p-4">

    {{-- Background blobs --}}
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-40 -right-40 w-96 h-96 bg-indigo-600/10 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-violet-600/10 rounded-full blur-3xl"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-indigo-900/20 rounded-full blur-3xl"></div>
    </div>

    <div class="w-full max-w-md relative">

        {{-- Logo / Title --}}
        <div class="text-center mb-8">
            <div class="logo-float inline-flex items-center justify-center w-20 h-20 rounded-3xl mb-5 shadow-2xl shadow-indigo-500/30"
                 style="background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #06b6d4 100%)">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-white tracking-tight">User Centralized Management</h1>
            <p class="text-slate-400 text-sm mt-2 font-medium">เข้าสู่ระบบด้วยบัญชี Active Directory</p>
        </div>

        {{-- Card --}}
        <div class="bg-white/[0.03] backdrop-blur-xl rounded-3xl card-glow p-8 border border-white/10">

            @if ($errors->any())
                <div class="mb-5 flex items-start gap-3 p-4 bg-red-500/10 border border-red-500/20 rounded-2xl text-sm text-red-300">
                    <svg class="w-4 h-4 mt-0.5 flex-shrink-0 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            @if (session('status'))
                <div class="mb-5 flex items-start gap-3 p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl text-sm text-emerald-300">
                    <svg class="w-4 h-4 mt-0.5 flex-shrink-0 text-emerald-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                {{-- Username field --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-300 mb-2">ชื่อผู้ใช้ (AD Username)</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                            <svg class="w-4.5 h-4.5 text-slate-500" style="width:18px;height:18px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <input type="text" name="username" value="{{ old('username') }}" required autofocus
                               class="w-full pl-11 pr-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-slate-500 text-sm
                                      focus:outline-none focus:ring-2 focus:ring-indigo-500/60 focus:border-indigo-500/50 transition-all duration-150
                                      @error('username') border-red-500/50 @enderror"
                               placeholder="firstname.lastname">
                    </div>
                    @error('username')
                        <p class="text-red-400 text-xs mt-1.5 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password field --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-300 mb-2">รหัสผ่าน</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                            <svg style="width:18px;height:18px" class="text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <input id="password-input" type="password" name="password" required
                               class="w-full pl-11 pr-12 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-slate-500 text-sm
                                      focus:outline-none focus:ring-2 focus:ring-indigo-500/60 focus:border-indigo-500/50 transition-all duration-150"
                               placeholder="••••••••">
                        {{-- Show/hide password toggle --}}
                        <button type="button" id="toggle-password"
                                onclick="(function(){var i=document.getElementById('password-input');var e=document.getElementById('eye-open');var c=document.getElementById('eye-closed');if(i.type==='password'){i.type='text';e.style.display='none';c.style.display=''}else{i.type='password';e.style.display='';c.style.display='none'}})()"
                                class="absolute inset-y-0 right-0 flex items-center pr-4 text-slate-500 hover:text-slate-300 transition-colors">
                            <svg id="eye-open" style="width:18px;height:18px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg id="eye-closed" style="display:none;width:18px;height:18px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Remember me --}}
                <div class="flex items-center">
                    <label class="flex items-center gap-2.5 cursor-pointer group">
                        <input type="checkbox" name="remember" id="remember"
                               class="w-4 h-4 rounded bg-white/10 border border-white/20 text-indigo-500 focus:ring-indigo-500/40 transition-colors cursor-pointer">
                        <span class="text-sm text-slate-400 group-hover:text-slate-300 transition-colors font-medium">จดจำการเข้าสู่ระบบ</span>
                    </label>
                </div>

                {{-- Submit --}}
                <button type="submit"
                        class="w-full py-3 px-6 rounded-xl text-white text-sm font-semibold tracking-wide transition-all duration-200
                               hover:opacity-90 hover:shadow-lg hover:shadow-indigo-500/30 hover:-translate-y-0.5
                               active:translate-y-0 active:shadow-none"
                        style="background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #06b6d4 100%)">
                    เข้าสู่ระบบ
                </button>
            </form>
        </div>

        <p class="text-center text-slate-600 text-xs mt-6 font-medium">
            ใช้บัญชี Active Directory เท่านั้น &nbsp;•&nbsp; ติดต่อ IT หากไม่สามารถเข้าสู่ระบบได้
        </p>
    </div>
</body>
</html>
