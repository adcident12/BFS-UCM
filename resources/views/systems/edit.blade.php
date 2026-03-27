@extends('layouts.app')
@section('title', 'แก้ไข ' . $system->name)

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}" class="text-slate-400 hover:text-slate-600 font-medium transition-colors whitespace-nowrap">Dashboard</a>
    <svg class="w-3.5 h-3.5 text-slate-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    <a href="{{ route('systems.index') }}" class="text-slate-400 hover:text-slate-600 font-medium transition-colors whitespace-nowrap">ระบบที่เชื่อมต่อ</a>
    <svg class="w-3.5 h-3.5 text-slate-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    <a href="{{ route('systems.show', $system) }}" class="text-slate-400 hover:text-slate-600 font-medium transition-colors truncate max-w-[120px]">{{ $system->name }}</a>
    <svg class="w-3.5 h-3.5 text-slate-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    <span class="font-semibold text-slate-800 truncate">แก้ไข</span>
@endsection

@section('content')
<div class="max-w-2xl">

    {{-- Hero Banner --}}
    <div class="mb-6 relative overflow-hidden rounded-2xl"
         style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 45%, #0c1a2e 100%)">
        <div class="absolute inset-0 pointer-events-none overflow-hidden">
            <div class="absolute -top-10 -right-10 w-60 h-60 bg-indigo-500/8 rounded-full blur-2xl"></div>
            <div class="absolute -bottom-12 left-16 w-48 h-48 bg-sky-400/6 rounded-full blur-2xl"></div>
            <div class="absolute inset-0"
                 style="background-image:linear-gradient(rgba(255,255,255,0.025) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,0.025) 1px,transparent 1px);background-size:28px 28px"></div>
        </div>
        <div class="relative px-6 py-5 flex items-center gap-4">
            <div class="w-11 h-11 rounded-2xl flex items-center justify-center flex-shrink-0 border border-white/10 bg-white/8"
                 style="background: {{ $system->color }}22">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                     style="color: {{ $system->color }}">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                          d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"/>
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 flex-wrap">
                    <h2 class="text-base font-bold text-white">แก้ไขระบบ</h2>
                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-2.5 py-0.5 rounded-full bg-white/10 text-slate-300 border border-white/10">
                        <span class="w-1.5 h-1.5 rounded-full" style="background: {{ $system->color }}"></span>
                        {{ $system->name }}
                    </span>
                </div>
                <p class="text-slate-400 text-xs font-medium mt-0.5 font-mono">{{ $system->slug }}</p>
            </div>
        </div>
    </div>

    {{-- Form Card --}}
    <div class="bg-white rounded-2xl shadow-sm ring-1 ring-slate-100 overflow-hidden">
        <form id="main-edit-form" method="POST" action="{{ route('systems.update', $system) }}">
            @csrf @method('PUT')

            {{-- ── Section A: ข้อมูลระบบ ── --}}
            <div class="flex items-center gap-3 px-6 py-3.5 bg-slate-50/80 border-b border-slate-100">
                <div class="w-7 h-7 bg-indigo-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </div>
                <span class="text-xs font-bold text-slate-700 uppercase tracking-wide">ข้อมูลระบบ</span>
            </div>

            <div class="px-6 py-5 space-y-4">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">
                            ชื่อระบบ <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" value="{{ old('name', $system->name) }}" required
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
                        </label>
                        <input type="text" name="slug" value="{{ old('slug', $system->slug) }}" required
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
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">คำอธิบาย</label>
                    <textarea name="description" rows="2"
                              class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm text-slate-800 placeholder-slate-300
                                     focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-400 transition-all resize-none"
                              placeholder="อธิบายหน้าที่หรือวัตถุประสงค์ของระบบนี้">{{ old('description', $system->description) }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">
                        Adapter Class
                        <span class="text-slate-400 font-normal">(เว้นว่างถ้ายังไม่มี)</span>
                    </label>
                    <input type="text" name="adapter_class" value="{{ old('adapter_class', $system->adapter_class) }}"
                           class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm font-mono text-slate-800 placeholder-slate-300
                                  focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-400 transition-all"
                           placeholder="App\Adapters\RepairSystemAdapter">
                </div>

            </div>

            {{-- ── Section B: ฐานข้อมูล ── --}}
            <div class="flex items-center gap-3 px-6 py-3.5 bg-slate-50/80 border-y border-slate-100">
                <div class="w-7 h-7 bg-sky-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-3.5 h-3.5 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
                    </svg>
                </div>
                <span class="text-xs font-bold text-slate-700 uppercase tracking-wide">การเชื่อมต่อฐานข้อมูล</span>
            </div>

            <div class="px-6 py-5 space-y-4">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1.5">DB Host</label>
                        <input type="text" name="db_host" value="{{ old('db_host', $system->db_host) }}"
                               class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm text-slate-800 placeholder-slate-300
                                      focus:outline-none focus:ring-2 focus:ring-sky-500/30 focus:border-sky-400 transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1.5">DB Port</label>
                        <input type="number" name="db_port" value="{{ old('db_port', $system->db_port) }}"
                               class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm text-slate-800
                                      focus:outline-none focus:ring-2 focus:ring-sky-500/30 focus:border-sky-400 transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1.5">DB Name</label>
                        <input type="text" name="db_name" value="{{ old('db_name', $system->db_name) }}"
                               class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm text-slate-800 placeholder-slate-300
                                      focus:outline-none focus:ring-2 focus:ring-sky-500/30 focus:border-sky-400 transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1.5">DB User</label>
                        <input type="text" name="db_user" value="{{ old('db_user', $system->db_user) }}"
                               class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm text-slate-800 placeholder-slate-300
                                      focus:outline-none focus:ring-2 focus:ring-sky-500/30 focus:border-sky-400 transition-all">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-slate-500 mb-1.5">
                            DB Password
                            <span class="text-slate-400 font-normal">(เว้นว่างเพื่อใช้ค่าเดิม)</span>
                        </label>
                        <input type="password" name="db_password"
                               class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm text-slate-800 placeholder-slate-300
                                      focus:outline-none focus:ring-2 focus:ring-sky-500/30 focus:border-sky-400 transition-all"
                               placeholder="••••••••">
                    </div>
                </div>

            </div>

            {{-- ── Section C: การแสดงผล ── --}}
            <div class="flex items-center gap-3 px-6 py-3.5 bg-slate-50/80 border-y border-slate-100">
                <div class="w-7 h-7 bg-violet-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-3.5 h-3.5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                    </svg>
                </div>
                <span class="text-xs font-bold text-slate-700 uppercase tracking-wide">การแสดงผล</span>
            </div>

            <div class="px-6 py-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 items-end">

                    {{-- Color Picker --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-2">สีประจำระบบ</label>
                        <div class="flex items-center gap-3">
                            <div class="relative flex-shrink-0">
                                <input type="color" name="color" id="sys-color"
                                       value="{{ old('color', $system->color) }}"
                                       class="absolute inset-0 w-full h-full opacity-0 cursor-pointer rounded-xl">
                                <div id="color-swatch"
                                     class="w-11 h-11 rounded-xl ring-2 ring-offset-2 ring-slate-200 shadow-sm cursor-pointer transition-all"
                                     style="background: {{ old('color', $system->color) }}"></div>
                            </div>
                            <div>
                                <div class="text-xs font-semibold text-slate-700">เลือกสี</div>
                                <div id="color-hex" class="text-xs font-mono text-slate-400 mt-0.5">{{ old('color', $system->color) }}</div>
                            </div>
                        </div>
                    </div>

                    {{-- Active Toggle --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-2">สถานะ</label>
                        <label class="inline-flex items-center gap-3 cursor-pointer">
                            <div class="relative">
                                <input type="checkbox" name="is_active" value="1"
                                       {{ old('is_active', $system->is_active) ? 'checked' : '' }}
                                       class="sr-only peer">
                                <div class="w-11 h-6 bg-slate-200 peer-checked:bg-indigo-600 rounded-full transition-colors cursor-pointer"
                                     onclick="this.previousElementSibling.click()"></div>
                                <div class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow-sm transition-transform peer-checked:translate-x-5 pointer-events-none"></div>
                            </div>
                            <span class="text-sm font-semibold text-slate-700">เปิดใช้งาน</span>
                        </label>
                    </div>

                </div>
            </div>

            {{-- Footer --}}
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between gap-3">
                <a href="{{ route('systems.show', $system) }}"
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

    {{-- Danger Zone --}}
    <div class="mt-5 bg-white rounded-2xl shadow-sm overflow-hidden ring-1 ring-red-100">
        <div class="flex items-center gap-3 px-6 py-3.5 bg-red-50/80 border-b border-red-100">
            <div class="w-7 h-7 bg-red-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-3.5 h-3.5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                </svg>
            </div>
            <span class="text-xs font-bold text-red-700 uppercase tracking-wide">Danger Zone</span>
        </div>
        <div class="px-6 py-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <p class="text-sm font-semibold text-slate-800">ลบระบบนี้ถาวร</p>
                <p class="text-xs text-slate-500 mt-0.5">Permission Definitions และสิทธิ์ทั้งหมดที่เชื่อมโยงจะถูกลบด้วย — ไม่สามารถกู้คืนได้</p>
            </div>
            <form id="delete-system-form" method="POST" action="{{ route('systems.destroy', $system) }}" class="flex-shrink-0">
                @csrf @method('DELETE')
                <button type="button"
                        onclick="askConfirm('delete-system-form', 'ลบระบบ {{ addslashes($system->name) }} ?', 'สิทธิ์ทั้งหมดที่เชื่อมโยงจะถูกลบด้วย และไม่สามารถกู้คืนได้')"
                        class="px-4 py-2 text-sm font-semibold text-red-600 border border-red-200 hover:bg-red-600 hover:text-white rounded-xl transition-all whitespace-nowrap cursor-pointer">
                    ลบระบบนี้
                </button>
            </form>
        </div>
    </div>

</div>

@push('scripts')
<script>
(function () {
    const input   = document.getElementById('sys-color');
    const swatch  = document.getElementById('color-swatch');
    const hexText = document.getElementById('color-hex');
    if (!input) { return; }
    input.addEventListener('input', function () {
        swatch.style.background = this.value;
        hexText.textContent = this.value;
    });
})();
</script>
@endpush

@endsection
