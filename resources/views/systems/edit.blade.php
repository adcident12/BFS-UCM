@extends('layouts.app')
@section('title', 'แก้ไข ' . $system->name)
@section('header', 'แก้ไขระบบ')

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

    <div class="bg-white rounded-2xl shadow-sm ring-1 ring-slate-100 overflow-hidden">
        <form id="main-edit-form" method="POST" action="{{ route('systems.update', $system) }}">
            @csrf @method('PUT')

            {{-- Section: ข้อมูลระบบ --}}
            <div class="px-4 md:px-8 pt-7 pb-6">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-9 h-9 bg-indigo-100 rounded-xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-4.5 h-4.5 text-indigo-600" style="width:18px;height:18px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-800 text-sm">ข้อมูลระบบ</h3>
                        <p class="text-xs text-slate-400 mt-0.5">แก้ไขชื่อและตัวระบุของระบบ</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">ชื่อระบบ <span class="text-red-500 normal-case">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $system->name) }}" required
                               class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm text-slate-800 placeholder-slate-300
                                      focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Slug <span class="text-red-500">*</span></label>
                        <input type="text" name="slug" value="{{ old('slug', $system->slug) }}" required
                               class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm font-mono text-slate-800 placeholder-slate-300
                                      focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 transition-all">
                    </div>
                </div>

                <div class="mb-5">
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">คำอธิบาย</label>
                    <textarea name="description" rows="2"
                              class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm text-slate-800 placeholder-slate-300
                                     focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 transition-all resize-none">{{ old('description', $system->description) }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Adapter Class</label>
                    <input type="text" name="adapter_class" value="{{ old('adapter_class', $system->adapter_class) }}"
                           class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm font-mono text-slate-800 placeholder-slate-300
                                  focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 transition-all"
                           placeholder="App\Adapters\RepairSystemAdapter">
                </div>
            </div>

            {{-- Divider --}}
            <div class="relative px-8 py-1">
                <div class="border-t border-slate-100"></div>
                <div class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 bg-white px-3">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 bg-sky-100 rounded-lg flex items-center justify-center">
                            <svg class="w-3.5 h-3.5 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
                            </svg>
                        </div>
                        <span class="text-xs font-semibold text-slate-500">การเชื่อมต่อฐานข้อมูล</span>
                    </div>
                </div>
            </div>

            {{-- Section: Database --}}
            <div class="px-4 md:px-8 pt-7 pb-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase tracking-wide">DB Host</label>
                        <input type="text" name="db_host" value="{{ old('db_host', $system->db_host) }}"
                               class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm text-slate-800 placeholder-slate-300
                                      focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase tracking-wide">DB Port</label>
                        <input type="number" name="db_port" value="{{ old('db_port', $system->db_port) }}"
                               class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm text-slate-800 placeholder-slate-300
                                      focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase tracking-wide">DB Name</label>
                        <input type="text" name="db_name" value="{{ old('db_name', $system->db_name) }}"
                               class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm text-slate-800 placeholder-slate-300
                                      focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase tracking-wide">DB User</label>
                        <input type="text" name="db_user" value="{{ old('db_user', $system->db_user) }}"
                               class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm text-slate-800 placeholder-slate-300
                                      focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 transition-all">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase tracking-wide">
                            DB Password
                            <span class="text-slate-400 font-normal normal-case">(เว้นว่างเพื่อใช้ค่าเดิม)</span>
                        </label>
                        <input type="password" name="db_password"
                               class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm text-slate-800 placeholder-slate-300
                                      focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 transition-all"
                               placeholder="••••••••">
                    </div>
                </div>
            </div>

            {{-- Divider --}}
            <div class="relative px-8 py-1">
                <div class="border-t border-slate-100"></div>
                <div class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 bg-white px-3">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 bg-violet-100 rounded-lg flex items-center justify-center">
                            <svg class="w-3.5 h-3.5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                            </svg>
                        </div>
                        <span class="text-xs font-semibold text-slate-500">การแสดงผล</span>
                    </div>
                </div>
            </div>

            {{-- Section: Display --}}
            <div class="px-4 md:px-8 pt-7 pb-7">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase tracking-wide">สีประจำระบบ</label>
                        <div class="flex items-center gap-3">
                            <input type="color" name="color" value="{{ old('color', $system->color) }}"
                                   class="w-12 h-12 border-0 rounded-xl cursor-pointer p-1 bg-slate-100">
                            <span class="text-xs text-slate-400 font-mono">เลือกสีสำหรับแสดงผล</span>
                        </div>
                    </div>
                    <div class="flex items-end pb-1">
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <div class="relative">
                                <input type="checkbox" name="is_active" value="1"
                                       {{ $system->is_active ? 'checked' : '' }}
                                       class="sr-only peer">
                                <div class="w-11 h-6 bg-slate-200 peer-checked:bg-indigo-600 rounded-full transition-colors peer-focus:ring-2 peer-focus:ring-indigo-500/30 cursor-pointer"
                                     onclick="this.previousElementSibling.click()"></div>
                                <div class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow-sm transition-transform peer-checked:translate-x-5"
                                     style="pointer-events:none"></div>
                            </div>
                            <span class="text-sm font-semibold text-slate-700">เปิดใช้งาน</span>
                        </label>
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="px-4 md:px-8 py-5 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('systems.show', $system) }}"
                   class="px-5 py-2.5 text-sm font-semibold text-slate-500 hover:text-slate-800 hover:bg-slate-100 rounded-xl transition-colors duration-150">
                    ยกเลิก
                </a>
                <button type="submit"
                        class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl shadow-sm shadow-indigo-200 transition-all duration-150 hover:-translate-y-0.5 hover:shadow-md hover:shadow-indigo-200">
                    บันทึกการเปลี่ยนแปลง
                </button>
            </div>

        </form>
    </div>

    {{-- Danger Zone --}}
    <div class="mt-6 bg-white rounded-2xl shadow-sm ring-1 ring-red-100 overflow-hidden">
        <div class="px-4 md:px-8 py-5">
            <div class="flex flex-col sm:flex-row items-start gap-4">
                <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center flex-shrink-0 mt-0.5">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h4 class="text-sm font-bold text-red-800 mb-0.5">Danger Zone</h4>
                    <p class="text-xs text-red-600/80 font-medium">การลบระบบนี้จะลบ Permission และสิทธิ์ทั้งหมดที่เชื่อมโยงอยู่ด้วย และไม่สามารถกู้คืนได้</p>
                </div>
                <form id="delete-system-form" method="POST" action="{{ route('systems.destroy', $system) }}">
                    @csrf @method('DELETE')
                    <button type="button"
                            onclick="askConfirm('delete-system-form', 'ลบระบบ {{ addslashes($system->name) }} ?', 'สิทธิ์ทั้งหมดที่เชื่อมโยงจะถูกลบด้วย และไม่สามารถกู้คืนได้')"
                            class="px-4 py-2 text-sm font-semibold text-red-600 border border-red-200 hover:bg-red-600 hover:text-white rounded-xl transition-all duration-150 whitespace-nowrap">
                        ลบระบบนี้
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
