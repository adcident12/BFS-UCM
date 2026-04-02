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
    <form method="POST" action="{{ route('admin.oauth-clients.update', $oauthClient) }}" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- Basic Info --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
            <h3 class="text-sm font-semibold text-slate-700">ข้อมูลทั่วไป</h3>

            {{-- Slug is read-only after creation --}}
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Slug (ไม่สามารถเปลี่ยนแปลงได้)</label>
                <div class="w-full rounded-xl border border-slate-200 bg-slate-100 px-3.5 py-2.5 text-sm font-mono text-slate-500 cursor-not-allowed">
                    {{ $oauthClient->slug }}
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1.5">
                    ชื่อแอปพลิเคชัน <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" value="{{ old('name', $oauthClient->name) }}" required
                       class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('name') border-red-400 bg-red-50 @enderror">
                @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1.5">คำอธิบาย (optional)</label>
                <textarea name="description" rows="2"
                          class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent resize-none">{{ old('description', $oauthClient->description) }}</textarea>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Homepage URL (optional)</label>
                <input type="url" name="homepage_url" value="{{ old('homepage_url', $oauthClient->homepage_url) }}"
                       placeholder="https://app.example.com"
                       class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('homepage_url') border-red-400 bg-red-50 @enderror">
                @error('homepage_url') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- OAuth Config --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
            <h3 class="text-sm font-semibold text-slate-700">OAuth Configuration</h3>

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1.5">
                    Redirect URIs <span class="text-red-500">*</span>
                    <span class="text-slate-400 font-normal ml-1">— หนึ่ง URI ต่อบรรทัด</span>
                </label>
                <textarea name="redirect_uris" rows="3" required
                          class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3.5 py-2.5 text-sm font-mono text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('redirect_uris') border-red-400 bg-red-50 @enderror">{{ old('redirect_uris', implode("\n", $oauthClient->redirect_uris)) }}</textarea>
                @error('redirect_uris') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-2">
                    Allowed Scopes <span class="text-red-500">*</span>
                </label>
                <div class="grid grid-cols-2 gap-2">
                    @foreach (['openid' => 'OpenID (ตัวตนพื้นฐาน)', 'profile' => 'Profile (ชื่อ, แผนก)', 'email' => 'Email', 'permissions' => 'Permissions (สิทธิ์ในระบบ)', 'users:read' => 'Users:Read (รายชื่อพนักงาน)'] as $scope => $label)
                        <label class="flex items-center gap-2.5 cursor-pointer bg-slate-50 hover:bg-slate-100 rounded-xl px-3.5 py-2.5 border border-slate-200 transition-colors">
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
                @error('allowed_scopes') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-2">Grant Types <span class="text-red-500">*</span></label>
                <div class="space-y-2">
                    @foreach (['authorization_code' => 'Authorization Code (+ PKCE)', 'refresh_token' => 'Refresh Token', 'client_credentials' => 'Client Credentials (M2M)'] as $grant => $label)
                        <label class="flex items-center gap-2.5 cursor-pointer bg-slate-50 hover:bg-slate-100 rounded-xl px-3.5 py-2.5 border border-slate-200 transition-colors">
                            <input type="checkbox" name="grant_types[]" value="{{ $grant }}"
                                   {{ in_array($grant, old('grant_types', $oauthClient->grant_types)) ? 'checked' : '' }}
                                   class="accent-indigo-600">
                            <span class="text-sm text-slate-700">{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
                @error('grant_types') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- Security Options --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-3">
            <h3 class="text-sm font-semibold text-slate-700">ตัวเลือก</h3>

            <label class="flex items-start gap-3 cursor-pointer bg-slate-50 hover:bg-slate-100 rounded-xl px-4 py-3 border border-slate-200 transition-colors">
                <input type="hidden" name="auto_approve" value="0">
                <input type="checkbox" name="auto_approve" value="1"
                       {{ old('auto_approve', $oauthClient->auto_approve) ? 'checked' : '' }}
                       class="accent-indigo-600 mt-0.5">
                <div>
                    <span class="text-sm font-semibold text-slate-700">Auto Approve</span>
                    <p class="text-xs text-slate-500 mt-0.5">ข้ามหน้า consent สำหรับแอปภายในองค์กรที่ไว้ใจได้</p>
                </div>
            </label>

            <label class="flex items-start gap-3 cursor-pointer bg-slate-50 hover:bg-slate-100 rounded-xl px-4 py-3 border border-slate-200 transition-colors">
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

        {{-- Actions --}}
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.oauth-clients.show', $oauthClient) }}"
               class="px-4 py-2.5 rounded-xl text-sm font-medium text-slate-600 bg-white border border-slate-300 hover:bg-slate-50 transition-colors">
                ยกเลิก
            </a>
            <button type="submit"
                    class="px-5 py-2.5 rounded-xl text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 transition-colors">
                บันทึกการเปลี่ยนแปลง
            </button>
        </div>
    </form>
</div>

@endsection
