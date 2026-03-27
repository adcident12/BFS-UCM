<!DOCTYPE html>
<html lang="th" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'UCM') — User Centralized Management</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif; }
        .sidebar-gradient { background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #06b6d4 100%); }
        .nav-active { background: linear-gradient(135deg, rgba(99,102,241,0.2) 0%, rgba(139,92,246,0.1) 100%); }
        @keyframes pulse-dot { 0%, 100% { opacity: 1; } 50% { opacity: 0.4; } }
        .pulse-dot { animation: pulse-dot 2s ease-in-out infinite; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(-8px); } to { opacity: 1; transform: translateY(0); } }
        .flash-msg { animation: fadeIn 0.3s ease-out; }
        @media (max-width: 1023px) {
            body.sidebar-open { overflow: hidden; }
        }
        /* Sidebar accordion */
        .nav-acc-body { overflow: hidden; transition: max-height 0.22s ease; }
        .nav-acc-chevron { transition: transform 0.22s ease; flex-shrink: 0; }
        .nav-acc-chevron.closed { transform: rotate(-90deg); }
    </style>
    @stack('styles')
</head>
<body class="bg-slate-50 min-h-screen flex font-sans">

    {{-- ═══ Global Confirm / Alert Modal ═══════════════════════════════════ --}}
    <div id="g-modal" style="display:none"
         class="fixed inset-0 z-[9998] flex items-center justify-center"
         onclick="if(event.target===this)closeConfirm()">
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm mx-4 p-6 ring-1 ring-slate-100">
            <div class="flex items-start gap-3 mb-5">
                <div id="g-modal-icon" class="w-11 h-11 rounded-2xl flex items-center justify-center flex-shrink-0"></div>
                <div class="flex-1 min-w-0">
                    <h3 id="g-modal-title" class="font-bold text-slate-800 text-sm leading-snug"></h3>
                    <p id="g-modal-detail" style="display:none" class="text-xs text-slate-500 mt-1.5 font-medium"></p>
                </div>
            </div>
            <div class="flex justify-end gap-2">
                <button id="g-modal-cancel" onclick="closeConfirm()"
                        class="px-4 py-2.5 text-sm font-semibold text-slate-600 border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors">
                    ยกเลิก
                </button>
                <button id="g-modal-ok" onclick="submitConfirm()"
                        class="px-4 py-2.5 text-sm font-semibold text-white bg-red-600 hover:bg-red-700 rounded-xl transition-colors">
                    ยืนยัน
                </button>
            </div>
        </div>
    </div>

    {{-- Mobile sidebar overlay --}}
    <div id="sidebar-overlay"
         class="fixed inset-0 bg-black/50 backdrop-blur-sm z-20 hidden lg:hidden"
         onclick="closeSidebar()"></div>

    {{-- Sidebar --}}
    <aside id="sidebar"
           class="w-64 bg-[#0f172a] text-white flex flex-col h-screen fixed top-0 left-0 z-30 shadow-2xl
                  -translate-x-full lg:translate-x-0 transition-transform duration-300">

        {{-- Gradient accent top bar --}}
        <div class="h-1 w-full sidebar-gradient flex-shrink-0"></div>

        {{-- Logo --}}
        <div class="px-5 py-5 border-b border-white/5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 sidebar-gradient rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg shadow-indigo-500/30">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div>
                    <div class="text-sm font-bold text-white leading-tight tracking-wide">UCM</div>
                    <div class="text-xs text-slate-400 leading-tight font-medium">User Management</div>
                </div>
            </div>
        </div>

        {{-- Nav --}}
        @php
            $activeSection = '';
            if (request()->routeIs('dashboard') || request()->routeIs('users.*') || request()->routeIs('systems.*'))
                $activeSection = 'main';
            elseif (request()->routeIs('admin.levels') || request()->routeIs('queue.monitor') || request()->routeIs('connectors.*') || request()->routeIs('audit.*') || request()->routeIs('notifications.*') || request()->routeIs('reports.*') || request()->routeIs('share-links.*'))
                $activeSection = 'admin';
            elseif (request()->routeIs('docs.manual'))
                $activeSection = 'docs';
            elseif (request()->routeIs('docs.install') || request()->routeIs('api-docs') || request()->is('api-docs/swagger'))
                $activeSection = 'dev';
        @endphp
        <nav id="sidebar-nav" class="flex-1 px-3 py-4 overflow-y-auto overflow-x-hidden">

            {{-- ── เมนูหลัก ── --}}
            <button type="button" onclick="navToggle('main')"
                    class="w-full flex items-center justify-between px-3 py-1 mb-1.5 text-[10px] font-semibold text-slate-500 uppercase tracking-widest hover:text-slate-300 transition-colors">
                <span>เมนูหลัก</span>
                <svg id="nav-chevron-main" class="nav-acc-chevron w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div id="nav-body-main" class="nav-acc-body space-y-0.5">
                @php
                    $navItems = [
                        ['route' => 'dashboard',      'label' => 'Dashboard',       'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
                        ['route' => 'users.index',    'label' => 'จัดการผู้ใช้',     'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'],
                        ['route' => 'systems.index',  'label' => 'ระบบที่เชื่อมต่อ',  'icon' => 'M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2'],
                    ];
                @endphp
                @foreach ($navItems as $item)
                    @php $active = request()->routeIs($item['route']) || request()->routeIs($item['route'] . '.*') @endphp
                    <a href="{{ route($item['route']) }}" onclick="closeSidebar()"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 group focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-400/50 focus-visible:ring-offset-1 focus-visible:ring-offset-slate-900
                              {{ $active ? 'nav-active text-white border border-indigo-500/30' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 transition-colors
                                    {{ $active ? 'bg-indigo-600/80 text-white shadow-sm shadow-indigo-500/40' : 'text-slate-500 group-hover:text-slate-300' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"/>
                            </svg>
                        </div>
                        <span class="truncate">{{ $item['label'] }}</span>
                        @if ($active)<div class="ml-auto w-1.5 h-1.5 rounded-full bg-indigo-400 flex-shrink-0"></div>@endif
                    </a>
                @endforeach
            </div>

            {{-- ── ผู้ดูแลระบบ (Admin L1+ หรือ มีสิทธิ์พิเศษ) ── --}}
            @if (auth()->user()->isAdmin() || auth()->user()->featureGrants()->exists())
            <div class="h-px bg-white/5 mx-3 my-3"></div>
            <button type="button" onclick="navToggle('admin')"
                    class="w-full flex items-center justify-between px-3 py-1 mb-1.5 text-[10px] font-semibold text-slate-500 uppercase tracking-widest hover:text-slate-300 transition-colors">
                <span>ผู้ดูแลระบบ</span>
                <svg id="nav-chevron-admin" class="nav-acc-chevron w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div id="nav-body-admin" class="nav-acc-body space-y-0.5">
                @php
                    $auditActive = request()->routeIs('audit.*');
                    $auditDepts  = array_map('strtoupper', config('auth.audit_departments', []));
                    $canSeeAudit = auth()->user()?->canAccess('audit_log') || in_array(strtoupper(auth()->user()?->department ?? ''), $auditDepts);
                @endphp
                @if ($canSeeAudit)
                <a href="{{ route('audit.index') }}" onclick="closeSidebar()"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 group focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-400/50 focus-visible:ring-offset-1 focus-visible:ring-offset-slate-900
                          {{ $auditActive ? 'nav-active text-white border border-indigo-500/30' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 transition-colors
                                {{ $auditActive ? 'bg-indigo-600/80 text-white shadow-sm shadow-indigo-500/40' : 'text-slate-500 group-hover:text-slate-300' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                        </svg>
                    </div>
                    <span class="truncate">Audit Log</span>
                    @if ($auditActive)<div class="ml-auto w-1.5 h-1.5 rounded-full bg-indigo-400 flex-shrink-0"></div>@endif
                </a>
                @endif
                @if (auth()->user()->canAccess('permission_matrix'))
                @php $reportActive = request()->routeIs('reports.*') @endphp
                <a href="{{ route('reports.permission-matrix') }}" onclick="closeSidebar()"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 group focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-400/50 focus-visible:ring-offset-1 focus-visible:ring-offset-slate-900
                          {{ $reportActive ? 'nav-active text-white border border-indigo-500/30' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 transition-colors
                                {{ $reportActive ? 'bg-indigo-600/80 text-white shadow-sm shadow-indigo-500/40' : 'text-slate-500 group-hover:text-slate-300' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <span class="truncate">Permission Matrix</span>
                    @if ($reportActive)<div class="ml-auto w-1.5 h-1.5 rounded-full bg-indigo-400 flex-shrink-0"></div>@endif
                </a>
                @endif
                @if (auth()->user()->canAccess('share_link_manage'))
                @php $shareLinkActive = request()->routeIs('share-links.*') @endphp
                <a href="{{ route('share-links.index') }}" onclick="closeSidebar()"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 group focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-400/50 focus-visible:ring-offset-1 focus-visible:ring-offset-slate-900
                          {{ $shareLinkActive ? 'nav-active text-white border border-indigo-500/30' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 transition-colors
                                {{ $shareLinkActive ? 'bg-indigo-600/80 text-white shadow-sm shadow-indigo-500/40' : 'text-slate-500 group-hover:text-slate-300' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                        </svg>
                    </div>
                    <span class="truncate">Share Links</span>
                    @if ($shareLinkActive)<div class="ml-auto w-1.5 h-1.5 rounded-full bg-indigo-400 flex-shrink-0"></div>@endif
                </a>
                @endif
                @if (auth()->user()->canAccess('permission_center'))
                @php $permCenterActive = request()->routeIs('permissions.*') @endphp
                <a href="{{ route('permissions.index') }}" onclick="closeSidebar()"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 group focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-400/50 focus-visible:ring-offset-1 focus-visible:ring-offset-slate-900
                          {{ $permCenterActive ? 'nav-active text-white border border-indigo-500/30' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 transition-colors
                                {{ $permCenterActive ? 'bg-indigo-600/80 text-white shadow-sm shadow-indigo-500/40' : 'text-slate-500 group-hover:text-slate-300' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <span class="truncate">จัดการ Permissions</span>
                    @if ($permCenterActive)<div class="ml-auto w-1.5 h-1.5 rounded-full bg-indigo-400 flex-shrink-0"></div>@endif
                </a>
                @endif
                @if (auth()->user()->canAccess('admin_levels'))
                @php $adminActive = request()->routeIs('admin.levels') @endphp
                <a href="{{ route('admin.levels') }}" onclick="closeSidebar()"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 group focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-400/50 focus-visible:ring-offset-1 focus-visible:ring-offset-slate-900
                          {{ $adminActive ? 'nav-active text-white border border-indigo-500/30' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 transition-colors
                                {{ $adminActive ? 'bg-indigo-600/80 text-white shadow-sm shadow-indigo-500/40' : 'text-slate-500 group-hover:text-slate-300' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <span class="truncate">จัดการสิทธิ์ Admin</span>
                    @if ($adminActive)<div class="ml-auto w-1.5 h-1.5 rounded-full bg-indigo-400 flex-shrink-0"></div>@endif
                </a>
                @endif
                @if (auth()->user()->canAccess('ucm_access'))
                @php $ucmAccessActive = request()->routeIs('ucm-access.*') @endphp
                <a href="{{ route('ucm-access.index') }}" onclick="closeSidebar()"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 group focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-400/50 focus-visible:ring-offset-1 focus-visible:ring-offset-slate-900
                          {{ $ucmAccessActive ? 'nav-active text-white border border-indigo-500/30' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 transition-colors
                                {{ $ucmAccessActive ? 'bg-indigo-600/80 text-white shadow-sm shadow-indigo-500/40' : 'text-slate-500 group-hover:text-slate-300' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <span class="truncate">สิทธิ์ระบบ UCM</span>
                    @if ($ucmAccessActive)<div class="ml-auto w-1.5 h-1.5 rounded-full bg-indigo-400 flex-shrink-0"></div>@endif
                </a>
                @endif
                @if (auth()->user()->canAccess('queue_monitor'))
                @php $queueActive = request()->routeIs('queue.monitor') @endphp
                <a href="{{ route('queue.monitor') }}" onclick="closeSidebar()"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 group focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-400/50 focus-visible:ring-offset-1 focus-visible:ring-offset-slate-900
                          {{ $queueActive ? 'nav-active text-white border border-indigo-500/30' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 transition-colors
                                {{ $queueActive ? 'bg-indigo-600/80 text-white shadow-sm shadow-indigo-500/40' : 'text-slate-500 group-hover:text-slate-300' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                        </svg>
                    </div>
                    <span class="truncate">Queue Monitor</span>
                    @if ($queueActive)<div class="ml-auto w-1.5 h-1.5 rounded-full bg-indigo-400 flex-shrink-0"></div>@endif
                </a>
                @endif
                @if (auth()->user()->canAccess('connector_wizard'))
                @php $connActive = request()->routeIs('connectors.*') @endphp
                <a href="{{ route('connectors.index') }}" onclick="closeSidebar()"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 group focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-400/50 focus-visible:ring-offset-1 focus-visible:ring-offset-slate-900
                          {{ $connActive ? 'nav-active text-white border border-indigo-500/30' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 transition-colors
                                {{ $connActive ? 'bg-indigo-600/80 text-white shadow-sm shadow-indigo-500/40' : 'text-slate-500 group-hover:text-slate-300' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <span class="truncate">Connector Wizard</span>
                    @if ($connActive)<div class="ml-auto w-1.5 h-1.5 rounded-full bg-indigo-400 flex-shrink-0"></div>@endif
                </a>
                @endif
                @if (auth()->user()->canAccess('notifications'))
                @php $notifActive = request()->routeIs('notifications.*') @endphp
                <a href="{{ route('notifications.index') }}" onclick="closeSidebar()"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 group focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-400/50 focus-visible:ring-offset-1 focus-visible:ring-offset-slate-900
                          {{ $notifActive ? 'nav-active text-white border border-indigo-500/30' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 transition-colors
                                {{ $notifActive ? 'bg-indigo-600/80 text-white shadow-sm shadow-indigo-500/40' : 'text-slate-500 group-hover:text-slate-300' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                    </div>
                    <span class="truncate">Notifications</span>
                    @if ($notifActive)<div class="ml-auto w-1.5 h-1.5 rounded-full bg-indigo-400 flex-shrink-0"></div>@endif
                </a>
                @endif
            </div>
            @endif

            {{-- ── คู่มือ ── --}}
            <div class="h-px bg-white/5 mx-3 my-3"></div>
            <button type="button" onclick="navToggle('docs')"
                    class="w-full flex items-center justify-between px-3 py-1 mb-1.5 text-[10px] font-semibold text-slate-500 uppercase tracking-widest hover:text-slate-300 transition-colors">
                <span>คู่มือ</span>
                <svg id="nav-chevron-docs" class="nav-acc-chevron w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div id="nav-body-docs" class="nav-acc-body space-y-0.5">
                @php $manualActive = request()->routeIs('docs.manual') @endphp
                <a href="{{ route('docs.manual') }}" onclick="closeSidebar()"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 group focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-400/50 focus-visible:ring-offset-1 focus-visible:ring-offset-slate-900
                          {{ $manualActive ? 'nav-active text-white border border-indigo-500/30' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 transition-colors
                                {{ $manualActive ? 'bg-indigo-600/80 text-white shadow-sm shadow-indigo-500/40' : 'text-slate-500 group-hover:text-slate-300' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <span class="truncate">คู่มือผู้ใช้งาน</span>
                    @if ($manualActive)<div class="ml-auto w-1.5 h-1.5 rounded-full bg-indigo-400 flex-shrink-0"></div>@endif
                </a>
            </div>

            {{-- ── นักพัฒนา ── --}}
            <div class="h-px bg-white/5 mx-3 my-3"></div>
            <button type="button" onclick="navToggle('dev')"
                    class="w-full flex items-center justify-between px-3 py-1 mb-1.5 text-[10px] font-semibold text-slate-500 uppercase tracking-widest hover:text-slate-300 transition-colors">
                <span>นักพัฒนา</span>
                <svg id="nav-chevron-dev" class="nav-acc-chevron w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div id="nav-body-dev" class="nav-acc-body space-y-0.5">
                @php
                    $installActive = request()->routeIs('docs.install');
                    $apiDocsActive = request()->routeIs('api-docs');
                    $swaggerActive = request()->is('api-docs/swagger');
                @endphp
                <a href="{{ route('docs.install') }}" onclick="closeSidebar()"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 group focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-400/50 focus-visible:ring-offset-1 focus-visible:ring-offset-slate-900
                          {{ $installActive ? 'nav-active text-white border border-indigo-500/30' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 transition-colors
                                {{ $installActive ? 'bg-indigo-600/80 text-white shadow-sm shadow-indigo-500/40' : 'text-slate-500 group-hover:text-slate-300' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/>
                        </svg>
                    </div>
                    <span class="truncate">Install Guide</span>
                    @if ($installActive)<div class="ml-auto w-1.5 h-1.5 rounded-full bg-indigo-400 flex-shrink-0"></div>@endif
                </a>
                <a href="{{ route('api-docs') }}" onclick="closeSidebar()"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 group focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-400/50 focus-visible:ring-offset-1 focus-visible:ring-offset-slate-900
                          {{ $apiDocsActive ? 'nav-active text-white border border-indigo-500/30' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 transition-colors
                                {{ $apiDocsActive ? 'bg-indigo-600/80 text-white shadow-sm shadow-indigo-500/40' : 'text-slate-500 group-hover:text-slate-300' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                        </svg>
                    </div>
                    <span class="truncate">API Docs</span>
                    @if ($apiDocsActive)<div class="ml-auto w-1.5 h-1.5 rounded-full bg-indigo-400 flex-shrink-0"></div>@endif
                </a>
                <a href="{{ url('api-docs/swagger') }}" onclick="closeSidebar()"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 group focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-400/50 focus-visible:ring-offset-1 focus-visible:ring-offset-slate-900
                          {{ $swaggerActive ? 'nav-active text-white border border-indigo-500/30' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 transition-colors
                                {{ $swaggerActive ? 'bg-indigo-600/80 text-white shadow-sm shadow-indigo-500/40' : 'text-slate-500 group-hover:text-slate-300' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2V9M9 21H5a2 2 0 01-2-2V9m0 0h18"/>
                        </svg>
                    </div>
                    <span class="truncate">Swagger UI</span>
                    @if ($swaggerActive)<div class="ml-auto w-1.5 h-1.5 rounded-full bg-indigo-400 flex-shrink-0"></div>@endif
                </a>
            </div>

            {{-- ── บัญชี (ไม่มี accordion — 1 รายการ) ── --}}
            <div class="h-px bg-white/5 mx-3 my-3"></div>
            <p class="px-3 mb-1.5 text-[10px] font-semibold text-slate-500 uppercase tracking-widest">บัญชี</p>
            @php $profileActive = request()->routeIs('users.show') && request()->route('user')?->id === auth()->id() @endphp
            <a href="{{ route('users.show', auth()->user()) }}" onclick="closeSidebar()"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 group focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-400/50 focus-visible:ring-offset-1 focus-visible:ring-offset-slate-900
                      {{ $profileActive ? 'nav-active text-white border border-indigo-500/30' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 transition-colors
                            {{ $profileActive ? 'bg-indigo-600/80 text-white shadow-sm shadow-indigo-500/40' : 'text-slate-500 group-hover:text-slate-300' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <span class="truncate">โปรไฟล์ของฉัน</span>
                @if ($profileActive)<div class="ml-auto w-1.5 h-1.5 rounded-full bg-indigo-400 flex-shrink-0"></div>@endif
            </a>

        </nav>

        {{-- User --}}
        <div class="px-4 py-4 border-t border-white/5 bg-white/[0.02] flex-shrink-0">
            <div class="flex items-center gap-3 mb-2.5">
                <div class="w-9 h-9 sidebar-gradient rounded-xl flex items-center justify-center text-sm font-bold text-white shadow-sm shadow-indigo-500/30 flex-shrink-0">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="text-sm font-semibold text-white truncate">{{ auth()->user()->name }}</div>
                    <div class="text-xs text-slate-400 truncate font-mono">{{ auth()->user()->username }}</div>
                </div>
            </div>
            @php $adminLevel = auth()->user()->is_admin ?? 0; @endphp
            <div class="mb-2.5">
                @if ($adminLevel >= 2)
                    <span class="inline-flex items-center gap-1.5 text-[10px] font-bold text-amber-300 bg-amber-500/15 border border-amber-500/25 px-2.5 py-1 rounded-lg">
                        <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        Admin ระดับ 2
                    </span>
                @elseif ($adminLevel >= 1)
                    <span class="inline-flex items-center gap-1.5 text-[10px] font-bold text-indigo-300 bg-indigo-500/15 border border-indigo-500/25 px-2.5 py-1 rounded-lg">
                        <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        Admin ระดับ 1
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 text-[10px] font-bold text-slate-400 bg-white/5 border border-white/10 px-2.5 py-1 rounded-lg">
                        <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        ผู้ใช้ทั่วไป
                    </span>
                @endif
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-xs text-slate-400 hover:text-white hover:bg-white/5 transition-colors duration-150 group">
                    <svg class="w-3.5 h-3.5 flex-shrink-0 group-hover:text-red-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    ออกจากระบบ
                </button>
            </form>
        </div>
    </aside>

    {{-- Main Content --}}
    <div class="flex-1 lg:ml-64 flex flex-col min-h-screen min-w-0">

        {{-- Sticky Top bar --}}
        <header class="sticky top-0 z-20 bg-white/80 backdrop-blur-md border-b border-slate-100/80 px-4 md:px-8 py-3.5 flex items-center justify-between shadow-sm shadow-slate-100/50">
            <div class="flex items-center gap-2 min-w-0">
                {{-- Hamburger — mobile only --}}
                <button id="sidebar-toggle"
                        class="lg:hidden w-9 h-9 flex items-center justify-center rounded-xl text-slate-500 hover:text-slate-700 hover:bg-slate-100 transition-colors flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                {{-- Breadcrumbs (or plain title fallback) --}}
                @hasSection('breadcrumbs')
                    <nav aria-label="breadcrumb" class="flex items-center gap-1.5 text-sm min-w-0 overflow-hidden">
                        @yield('breadcrumbs')
                    </nav>
                @else
                    <h1 class="text-base font-semibold text-slate-800 tracking-tight truncate">@yield('header', 'Dashboard')</h1>
                @endif
            </div>
            <div class="flex items-center gap-2 flex-shrink-0">
                {{-- Animated AD Connected badge --}}
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200/60 shadow-sm">
                    <span class="relative flex h-2 w-2">
                        <span class="pulse-dot absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                    <span class="hidden sm:inline">AD Connected</span>
                </span>
                {{-- Current date --}}
                <span class="text-xs text-slate-400 font-medium hidden md:block pl-2 border-l border-slate-200">
                    {{ now()->locale('th')->translatedFormat('j M Y') }}
                </span>
            </div>
        </header>

        {{-- Flash messages --}}
        @if (session('success'))
            <div id="flash-success" class="flash-msg mx-4 md:mx-6 lg:mx-8 mt-4 flex items-center gap-3 p-4 bg-emerald-50 border border-emerald-200/80 text-emerald-800 rounded-2xl text-sm shadow-sm shadow-emerald-100">
                <div class="w-8 h-8 bg-emerald-100 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="flex-1 font-medium">{{ session('success') }}</div>
                <button onclick="this.closest('#flash-success').remove()" class="text-emerald-400 hover:text-emerald-600 transition-colors flex-shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <script>setTimeout(function(){ var el=document.getElementById('flash-success'); if(el){el.style.transition='opacity 0.5s';el.style.opacity='0';setTimeout(function(){el.remove()},500)} }, 5000);</script>
        @endif

        @if (session('error'))
            <div id="flash-error" class="flash-msg mx-4 md:mx-6 lg:mx-8 mt-4 flex items-center gap-3 p-4 bg-red-50 border border-red-200/80 text-red-800 rounded-2xl text-sm shadow-sm shadow-red-100">
                <div class="w-8 h-8 bg-red-100 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="flex-1 font-medium">{{ session('error') }}</div>
                <button onclick="this.closest('#flash-error').remove()" class="text-red-400 hover:text-red-600 transition-colors flex-shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <script>setTimeout(function(){ var el=document.getElementById('flash-error'); if(el){el.style.transition='opacity 0.5s';el.style.opacity='0';setTimeout(function(){el.remove()},500)} }, 6000);</script>
        @endif

        @if (session('warning'))
            <div id="flash-warning" class="flash-msg mx-4 md:mx-6 lg:mx-8 mt-4 flex items-center gap-3 p-4 bg-amber-50 border border-amber-200/80 text-amber-800 rounded-2xl text-sm shadow-sm shadow-amber-100">
                <div class="w-8 h-8 bg-amber-100 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="flex-1 font-medium">{{ session('warning') }}</div>
                <button onclick="this.closest('#flash-warning').remove()" class="text-amber-400 hover:text-amber-600 transition-colors flex-shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <script>setTimeout(function(){ var el=document.getElementById('flash-warning'); if(el){el.style.transition='opacity 0.5s';el.style.opacity='0';setTimeout(function(){el.remove()},500)} }, 5000);</script>
        @endif

        {{-- Page content --}}
        <main class="flex-1 p-4 md:p-6 lg:p-8 min-w-0">
            @yield('content')
        </main>
    </div>

<script>
/* ─── Global Confirm / Alert Modal ─────────────────────────────────────── */
(function () {
    var _formId = null;
    var ICONS = {
        danger:  { bg: 'bg-red-100',   html: '<svg class="w-5 h-5 text-red-600"   fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>', okClass: 'px-4 py-2.5 text-sm font-semibold text-white bg-red-600   hover:bg-red-700   rounded-xl transition-colors' },
        error:   { bg: 'bg-red-100',   html: '<svg class="w-5 h-5 text-red-600"   fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>', okClass: 'px-4 py-2.5 text-sm font-semibold text-white bg-red-600   hover:bg-red-700   rounded-xl transition-colors' },
        warning: { bg: 'bg-amber-100', html: '<svg class="w-5 h-5 text-amber-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>', okClass: 'px-4 py-2.5 text-sm font-semibold text-white bg-amber-500 hover:bg-amber-600 rounded-xl transition-colors' },
        info:    { bg: 'bg-blue-100',  html: '<svg class="w-5 h-5 text-blue-600"  fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>', okClass: 'px-4 py-2.5 text-sm font-semibold text-white bg-blue-600  hover:bg-blue-700  rounded-xl transition-colors' },
    };

    function render(title, detail, iconKey, okText, showCancel) {
        var ic = ICONS[iconKey] || ICONS.danger;
        var icon   = document.getElementById('g-modal-icon');
        var titleEl= document.getElementById('g-modal-title');
        var detEl  = document.getElementById('g-modal-detail');
        var cancel = document.getElementById('g-modal-cancel');
        var ok     = document.getElementById('g-modal-ok');

        icon.className  = 'w-11 h-11 rounded-2xl flex items-center justify-center flex-shrink-0 ' + ic.bg;
        icon.innerHTML  = ic.html;
        titleEl.textContent = title;
        detEl.textContent   = detail || '';
        detEl.style.display = detail ? '' : 'none';
        cancel.style.display = showCancel ? '' : 'none';
        ok.textContent  = okText;
        ok.className    = ic.okClass;
        document.getElementById('g-modal').style.display = 'flex';
    }

    /* askConfirm(formId, message, detail?)  — เดิมมีในทุกหน้า ตอนนี้ global */
    window.askConfirm = function (formId, msg, detail) {
        _formId = formId;
        render(msg, detail || '', 'danger', 'ยืนยัน', true);
    };

    /* showAlert(message, type?)  — แทน native alert() */
    window.showAlert = function (msg, type) {
        _formId = null;
        render(msg, '', type || 'error', 'ปิด', false);
    };

    window.closeConfirm = function () {
        document.getElementById('g-modal').style.display = 'none';
        _formId = null;
    };

    window.submitConfirm = function () {
        if (_formId) { document.getElementById(_formId).submit(); }
        window.closeConfirm();
    };
})();
</script>

<script>
(function () {
    var sidebar = document.getElementById('sidebar');
    var overlay = document.getElementById('sidebar-overlay');
    var toggle  = document.getElementById('sidebar-toggle');
    var isOpen  = false;

    function openSidebar() {
        isOpen = true;
        sidebar.classList.remove('-translate-x-full');
        overlay.classList.remove('hidden');
        document.body.classList.add('sidebar-open');
    }
    window.closeSidebar = function () {
        isOpen = false;
        sidebar.classList.add('-translate-x-full');
        overlay.classList.add('hidden');
        document.body.classList.remove('sidebar-open');
    };

    if (toggle) {
        toggle.addEventListener('click', function () {
            isOpen ? window.closeSidebar() : openSidebar();
        });
    }

    // Close on resize to desktop
    window.addEventListener('resize', function () {
        if (window.innerWidth >= 1024) window.closeSidebar();
    });
})();
</script>

<script>
/* ─── Sidebar nav accordion (true accordion — 1 open at a time) ─────────── */
(function () {
    var SECTIONS      = ['main', 'admin', 'docs', 'dev'];
    var activeSection = '{{ $activeSection ?? "" }}';

    function getEl(id) { return document.getElementById('nav-body-' + id); }
    function getCh(id) { return document.getElementById('nav-chevron-' + id); }

    function expand(id) {
        var el = getEl(id); if (!el) return;
        el.style.maxHeight = el.scrollHeight + 'px';
        el.dataset.open = '1';
        var ch = getCh(id); if (ch) ch.classList.remove('closed');
    }

    function collapse(id) {
        var el = getEl(id); if (!el) return;
        el.style.maxHeight = '0px';
        el.dataset.open = '0';
        var ch = getCh(id); if (ch) ch.classList.add('closed');
    }

    // Toggle: open clicked section, close all others
    window.navToggle = function (id) {
        var el = getEl(id); if (!el) return;
        var isOpen = el.dataset.open === '1';
        SECTIONS.forEach(function (s) { collapse(s); });
        if (!isOpen) expand(id);
        try { localStorage.setItem('nav-open', isOpen ? '' : id); } catch(e) {}
    };

    // Init: open active section (or last manually opened), close all others
    var savedOpen = '';
    try { savedOpen = localStorage.getItem('nav-open') || ''; } catch(e) {}
    var openId = activeSection || savedOpen || 'main';
    SECTIONS.forEach(function (id) {
        if (!getEl(id)) return;
        if (id === openId) expand(id); else collapse(id);
    });
})();
</script>
@stack('scripts')
</body>
</html>
