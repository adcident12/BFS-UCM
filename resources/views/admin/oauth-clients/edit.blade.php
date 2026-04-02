@extends('layouts.app')

@section('title', 'แก้ไข — ' . $oauthClient->name)
@section('header', 'แก้ไข OAuth Client')

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}" class="text-slate-400 hover:text-slate-600 font-medium transition-colors whitespace-nowrap">Dashboard</a>
    <svg class="w-3.5 h-3.5 text-slate-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    <a href="{{ route('admin.oauth-clients.index') }}" class="text-slate-400 hover:text-slate-600 font-medium transition-colors whitespace-nowrap">OAuth Clients</a>
    <svg class="w-3.5 h-3.5 text-slate-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    <a href="{{ route('admin.oauth-clients.show', $oauthClient) }}" class="text-slate-400 hover:text-slate-600 font-medium transition-colors whitespace-nowrap">{{ $oauthClient->name }}</a>
    <svg class="w-3.5 h-3.5 text-slate-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    <span class="font-semibold text-slate-800 truncate">แก้ไข</span>
@endsection

@section('content')

<div class="max-w-2xl">
    <div class="bg-white rounded-2xl shadow-sm ring-1 ring-slate-100 overflow-hidden">
        <form method="POST" action="{{ route('admin.oauth-clients.update', $oauthClient) }}">
            @csrf
            @method('PUT')

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

                {{-- Slug is read-only after creation --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">
                        Slug <span class="text-slate-400 font-normal">(ไม่สามารถเปลี่ยนแปลงได้)</span>
                    </label>
                    <div class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm font-mono text-slate-400 bg-slate-50 cursor-not-allowed select-all">
                        {{ $oauthClient->slug }}
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">
                        ชื่อแอปพลิเคชัน <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name', $oauthClient->name) }}" required
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
                        คำอธิบาย <span class="text-slate-400 font-normal">(optional)</span>
                    </label>
                    <textarea name="description" rows="2"
                              class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm text-slate-800 placeholder-slate-300
                                     focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-400 transition-all resize-none">{{ old('description', $oauthClient->description) }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">
                        Homepage URL <span class="text-slate-400 font-normal">(optional)</span>
                    </label>
                    <input type="url" name="homepage_url" value="{{ old('homepage_url', $oauthClient->homepage_url) }}"
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
                              class="w-full px-3.5 py-2.5 border rounded-xl text-sm font-mono text-slate-800 placeholder-slate-300
                                     focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-400 transition-all
                                     {{ $errors->has('redirect_uris') ? 'border-red-300 focus:ring-red-500/20 focus:border-red-400' : 'border-slate-200' }}">{{ old('redirect_uris', implode("\n", $oauthClient->redirect_uris)) }}</textarea>
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
                            <label class="flex items-center gap-2.5 cursor-pointer bg-slate-50 hover:bg-violet-50 hover:border-violet-200 rounded-xl px-3.5 py-2.5 border border-slate-200 transition-all">
                                <input type="checkbox" name="allowed_scopes[]" value="{{ $scope }}"
                                       {{ in_array($scope, old('allowed_scopes', $oauthClient->allowed_scopes)) ? 'checked' : '' }}
                                       class="accent-indigo-600">
                                <div>
                                    <span class="text-sm font-medium text-slate-700">{{ $label }}</span>
                                    <span class="block text-xs font-mono text-slate-400">{{ $scope }}</span>
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
                            <label class="flex items-start gap-2.5 cursor-pointer bg-slate-50 hover:bg-violet-50 hover:border-violet-200 rounded-xl px-3.5 py-2.5 border border-slate-200 transition-all">
                                <input type="checkbox" name="grant_types[]" value="{{ $grant }}"
                                       {{ in_array($grant, old('grant_types', $oauthClient->grant_types)) ? 'checked' : '' }}
                                       class="accent-indigo-600 mt-0.5">
                                <div>
                                    <span class="text-sm font-medium text-slate-700">{{ $grantLabel }}</span>
                                    <span class="block text-xs text-slate-400">{{ $grantDesc }}</span>
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

            {{-- ── ตัวเลือก ── --}}
            <div class="flex items-center gap-3 px-6 py-3.5 bg-slate-50/80 border-y border-slate-100">
                <div class="w-7 h-7 bg-amber-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                    </svg>
                </div>
                <span class="text-xs font-bold text-slate-700 uppercase tracking-wide">ตัวเลือก</span>
            </div>
            <div class="px-6 py-5 space-y-3">

                <label class="flex items-start gap-3 cursor-pointer bg-slate-50 hover:bg-amber-50 hover:border-amber-200 rounded-xl px-4 py-3 border border-slate-200 transition-all">
                    <input type="hidden" name="auto_approve" value="0">
                    <input type="checkbox" name="auto_approve" value="1"
                           {{ old('auto_approve', $oauthClient->auto_approve) ? 'checked' : '' }}
                           class="accent-indigo-600 mt-0.5">
                    <div>
                        <span class="text-sm font-semibold text-slate-700">Auto Approve</span>
                        <p class="text-xs text-slate-500 mt-0.5">ข้ามหน้า consent สำหรับแอปภายในองค์กรที่ไว้ใจได้</p>
                    </div>
                </label>

                <label class="flex items-start gap-3 cursor-pointer bg-slate-50 hover:bg-emerald-50 hover:border-emerald-200 rounded-xl px-4 py-3 border border-slate-200 transition-all">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1"
                           {{ old('is_active', $oauthClient->is_active) ? 'checked' : '' }}
                           class="accent-indigo-600 mt-0.5">
                    <div>
                        <span class="text-sm font-semibold text-slate-700">Active</span>
                        <p class="text-xs text-slate-500 mt-0.5">ยกเลิกติ๊กเพื่อปิดใช้งานชั่วคราวโดยไม่ต้องลบ</p>
                    </div>
                </label>

            </div>

            {{-- ── Footer Actions ── --}}
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between gap-3">
                <a href="{{ route('admin.oauth-clients.show', $oauthClient) }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold text-slate-500 hover:text-slate-800 hover:bg-slate-100 rounded-xl transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    ยกเลิก
                </a>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl shadow-sm shadow-indigo-200 transition-all hover:-translate-y-0.5 hover:shadow-md hover:shadow-indigo-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    บันทึกการเปลี่ยนแปลง
                </button>
            </div>

        </form>
    </div>
</div>

@endsection
