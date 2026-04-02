@extends('layouts.app')

@section('title', 'เพิ่ม OAuth Client')
@section('header', 'เพิ่ม OAuth Client')

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}" class="text-slate-400 hover:text-slate-600 font-medium transition-colors whitespace-nowrap">Dashboard</a>
    <svg class="w-3.5 h-3.5 text-slate-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    <a href="{{ route('admin.oauth-clients.index') }}" class="text-slate-400 hover:text-slate-600 font-medium transition-colors whitespace-nowrap">OAuth Clients</a>
    <svg class="w-3.5 h-3.5 text-slate-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    <span class="font-semibold text-slate-800 truncate">เพิ่มใหม่</span>
@endsection

@section('content')

<div class="max-w-2xl mx-auto">

    {{-- Hero Banner --}}
    <div class="mb-6 relative overflow-hidden rounded-2xl"
         style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 45%, #0c1a2e 100%)">
        <div class="absolute inset-0 pointer-events-none overflow-hidden">
            <div class="absolute -top-10 -right-10 w-60 h-60 bg-indigo-500/8 rounded-full blur-2xl"></div>
            <div class="absolute -bottom-12 left-16 w-48 h-48 bg-violet-400/6 rounded-full blur-2xl"></div>
            <div class="absolute inset-0"
                 style="background-image:linear-gradient(rgba(255,255,255,0.025) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,0.025) 1px,transparent 1px);background-size:28px 28px"></div>
        </div>
        <div class="relative px-6 py-5 flex items-center gap-4">
            <div class="w-11 h-11 bg-white/8 backdrop-blur-sm rounded-2xl flex items-center justify-center flex-shrink-0 border border-white/10">
                <svg class="w-5 h-5 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                          d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                </svg>
            </div>
            <div>
                <h2 class="text-base font-bold text-white">เพิ่ม OAuth Client</h2>
                <p class="text-slate-400 text-xs font-medium mt-0.5">ลงทะเบียนแอปพลิเคชันเพื่อใช้งาน SSO และ API ของ UCM</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm ring-1 ring-slate-100 overflow-hidden">
        <form method="POST" action="{{ route('admin.oauth-clients.store') }}">
            @csrf

            {{-- ── ข้อมูลทั่วไป ── --}}
            <div class="flex items-center gap-3 px-6 py-3.5 bg-slate-50/80 border-b border-slate-100">
                <div class="w-7 h-7 bg-indigo-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <span class="text-xs font-bold text-slate-700 uppercase tracking-wide">ข้อมูลทั่วไป</span>
            </div>
            <div class="px-6 py-5 space-y-4">

                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">
                        ชื่อแอปพลิเคชัน <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           placeholder="เช่น ระบบ HR, Repair System"
                           class="w-full px-3.5 py-2.5 border rounded-xl text-sm text-slate-800 placeholder-slate-300
                                  focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-400 transition-all
                                  {{ $errors->has('name') ? 'border-red-300 focus:ring-red-500/20 focus:border-red-400' : 'border-slate-200' }}">
                    @error('name')
                        <p class="text-red-500 text-xs mt-1.5 flex items-center gap-1 font-medium">
                            <svg class="w-3 h-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">
                        Slug <span class="text-red-500">*</span>
                        <span class="text-slate-400 font-normal ml-1">— ตัวพิมพ์เล็ก ตัวเลข และ - เท่านั้น ใช้จับคู่กับ UCM System</span>
                    </label>
                    <input type="text" name="slug" value="{{ old('slug') }}" required
                           placeholder="repair-system"
                           pattern="[a-z0-9\-]+"
                           class="w-full px-3.5 py-2.5 border rounded-xl text-sm font-mono text-slate-800 placeholder-slate-300
                                  focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-400 transition-all
                                  {{ $errors->has('slug') ? 'border-red-300 focus:ring-red-500/20 focus:border-red-400' : 'border-slate-200' }}">
                    @error('slug')
                        <p class="text-red-500 text-xs mt-1.5 flex items-center gap-1 font-medium">
                            <svg class="w-3 h-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">
                        คำอธิบาย <span class="text-slate-400 font-normal">(optional)</span>
                    </label>
                    <textarea name="description" rows="2"
                              placeholder="อธิบายสั้นๆ ว่าแอปนี้ทำอะไร จะแสดงบนหน้า consent ให้ผู้ใช้เห็น"
                              class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm text-slate-800 placeholder-slate-300
                                     focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-400 transition-all resize-none">{{ old('description') }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">
                        Homepage URL <span class="text-slate-400 font-normal">(optional)</span>
                    </label>
                    <input type="url" name="homepage_url" value="{{ old('homepage_url') }}"
                           placeholder="https://app.example.com"
                           class="w-full px-3.5 py-2.5 border rounded-xl text-sm text-slate-800 placeholder-slate-300
                                  focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-400 transition-all
                                  {{ $errors->has('homepage_url') ? 'border-red-300 focus:ring-red-500/20 focus:border-red-400' : 'border-slate-200' }}">
                    @error('homepage_url')
                        <p class="text-red-500 text-xs mt-1.5 flex items-center gap-1 font-medium">
                            <svg class="w-3 h-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

            </div>

            {{-- ── OAuth Configuration ── --}}
            <div class="flex items-center gap-3 px-6 py-3.5 bg-slate-50/80 border-y border-slate-100">
                <div class="w-7 h-7 bg-violet-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-3.5 h-3.5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                </div>
                <span class="text-xs font-bold text-slate-700 uppercase tracking-wide">OAuth Configuration</span>
            </div>
            <div class="px-6 py-5 space-y-4">

                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">
                        Redirect URIs <span class="text-red-500">*</span>
                        <span class="text-slate-400 font-normal ml-1">— หนึ่ง URI ต่อบรรทัด</span>
                    </label>
                    <textarea name="redirect_uris" rows="3" required
                              placeholder="https://app.example.com/auth/callback&#10;https://app.example.com/oauth/callback"
                              class="w-full px-3.5 py-2.5 border rounded-xl text-sm font-mono text-slate-800 placeholder-slate-300
                                     focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-400 transition-all
                                     {{ $errors->has('redirect_uris') ? 'border-red-300 focus:ring-red-500/20 focus:border-red-400' : 'border-slate-200' }}">{{ old('redirect_uris') }}</textarea>
                    @error('redirect_uris')
                        <p class="text-red-500 text-xs mt-1.5 flex items-center gap-1 font-medium">
                            <svg class="w-3 h-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-2">
                        Allowed Scopes <span class="text-red-500">*</span>
                    </label>
                    <div class="grid grid-cols-2 gap-2">
                        @foreach (['openid' => 'OpenID (ตัวตนพื้นฐาน)', 'profile' => 'Profile (ชื่อ, แผนก)', 'email' => 'Email', 'permissions' => 'Permissions (สิทธิ์ในระบบ)', 'users:read' => 'Users:Read (รายชื่อพนักงาน)'] as $scope => $label)
                            <label class="flex items-center gap-3 cursor-pointer bg-white border border-slate-200 rounded-xl px-3.5 py-3 transition-all hover:border-slate-300 has-[:checked]:bg-violet-50 has-[:checked]:border-violet-300">
                                <input type="checkbox" name="allowed_scopes[]" value="{{ $scope }}"
                                       {{ in_array($scope, old('allowed_scopes', ['openid', 'profile', 'email'])) ? 'checked' : '' }}
                                       class="sr-only peer">
                                <span class="flex-shrink-0 w-5 h-5 rounded-md border-2 border-slate-300 bg-white peer-checked:bg-indigo-600 peer-checked:border-indigo-600 transition-all flex items-center justify-center shadow-sm">
                                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </span>
                                <div>
                                    <span class="text-sm font-semibold text-slate-700">{{ $label }}</span>
                                    <span class="block text-[11px] font-mono text-slate-400 mt-0.5">{{ $scope }}</span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                    @error('allowed_scopes')
                        <p class="text-red-500 text-xs mt-1.5 flex items-center gap-1 font-medium">
                            <svg class="w-3 h-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-2">
                        Grant Types <span class="text-red-500">*</span>
                    </label>
                    <div class="space-y-2">
                        @foreach ([
                            'authorization_code' => ['Authorization Code (+ PKCE)', 'สำหรับแอปที่มีผู้ใช้ล็อกอิน'],
                            'refresh_token'      => ['Refresh Token', 'ต่ออายุ access token อัตโนมัติ'],
                            'client_credentials' => ['Client Credentials (M2M)', 'server-to-server ไม่มีผู้ใช้'],
                        ] as $grant => [$grantLabel, $grantDesc])
                            <label class="flex items-start gap-3 cursor-pointer bg-white border border-slate-200 rounded-xl px-3.5 py-3 transition-all hover:border-slate-300 has-[:checked]:bg-violet-50 has-[:checked]:border-violet-300">
                                <input type="checkbox" name="grant_types[]" value="{{ $grant }}"
                                       {{ in_array($grant, old('grant_types', ['authorization_code', 'refresh_token'])) ? 'checked' : '' }}
                                       class="sr-only peer">
                                <span class="flex-shrink-0 w-5 h-5 mt-0.5 rounded-md border-2 border-slate-300 bg-white peer-checked:bg-indigo-600 peer-checked:border-indigo-600 transition-all flex items-center justify-center shadow-sm">
                                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </span>
                                <div>
                                    <span class="text-sm font-semibold text-slate-700">{{ $grantLabel }}</span>
                                    <span class="block text-xs text-slate-400 mt-0.5">{{ $grantDesc }}</span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                    @error('grant_types')
                        <p class="text-red-500 text-xs mt-1.5 flex items-center gap-1 font-medium">
                            <svg class="w-3 h-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

            </div>

            {{-- ── ตัวเลือกความปลอดภัย ── --}}
            <div class="flex items-center gap-3 px-6 py-3.5 bg-slate-50/80 border-y border-slate-100">
                <div class="w-7 h-7 bg-amber-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <span class="text-xs font-bold text-slate-700 uppercase tracking-wide">ตัวเลือกความปลอดภัย</span>
            </div>
            <div class="px-6 py-5 space-y-3">

                <label class="flex items-start gap-3 cursor-pointer bg-white border border-slate-200 rounded-xl px-4 py-3.5 transition-all hover:border-slate-300 has-[:checked]:bg-amber-50 has-[:checked]:border-amber-200">
                    <input type="hidden" name="is_confidential" value="0">
                    <input type="checkbox" name="is_confidential" value="1"
                           {{ old('is_confidential', true) ? 'checked' : '' }}
                           class="sr-only peer">
                    <span class="flex-shrink-0 w-5 h-5 mt-0.5 rounded-md border-2 border-slate-300 bg-white peer-checked:bg-indigo-600 peer-checked:border-indigo-600 transition-all flex items-center justify-center shadow-sm">
                        <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                        </svg>
                    </span>
                    <div>
                        <span class="text-sm font-semibold text-slate-700">Confidential Client</span>
                        <p class="text-xs text-slate-500 mt-0.5">
                            แอปที่มี server-side backend — มี client_secret ปลอดภัยกว่า
                            ยกเลิกติ๊กถ้าเป็น SPA หรือ mobile app ที่ใช้ PKCE แทน secret
                        </p>
                    </div>
                </label>

                <label class="flex items-start gap-3 cursor-pointer bg-white border border-slate-200 rounded-xl px-4 py-3.5 transition-all hover:border-slate-300 has-[:checked]:bg-amber-50 has-[:checked]:border-amber-200">
                    <input type="hidden" name="auto_approve" value="0">
                    <input type="checkbox" name="auto_approve" value="1"
                           {{ old('auto_approve') ? 'checked' : '' }}
                           class="sr-only peer">
                    <span class="flex-shrink-0 w-5 h-5 mt-0.5 rounded-md border-2 border-slate-300 bg-white peer-checked:bg-indigo-600 peer-checked:border-indigo-600 transition-all flex items-center justify-center shadow-sm">
                        <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                        </svg>
                    </span>
                    <div>
                        <span class="text-sm font-semibold text-slate-700">Auto Approve</span>
                        <p class="text-xs text-slate-500 mt-0.5">
                            ข้ามหน้า consent — ใช้สำหรับแอปภายในองค์กรที่ไว้ใจได้ ผู้ใช้จะไม่ถูกถามทุกครั้ง
                        </p>
                    </div>
                </label>

            </div>

            {{-- ── Footer Actions ── --}}
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between gap-3">
                <a href="{{ route('admin.oauth-clients.index') }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold text-slate-500 hover:text-slate-800 hover:bg-slate-100 rounded-xl transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    ยกเลิก
                </a>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl shadow-sm shadow-indigo-200 transition-all hover:-translate-y-0.5 hover:shadow-md hover:shadow-indigo-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    สร้าง OAuth Client
                </button>
            </div>

        </form>
    </div>

</div>

@endsection
