<!DOCTYPE html>
<html lang="th" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('doc-title', 'Docs') — UCM Documentation</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600;1,700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif; }

        /* ── Scrollbar ── */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        /* ── Code font ── */
        code, pre, kbd { font-family: 'JetBrains Mono', ui-monospace, monospace; }

        /* ── Docs-specific prose ── */
        .docs-prose h2 {
            font-size: 1.25rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 0.75rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #f1f5f9;
        }
        .docs-prose h3 {
            font-size: 1rem;
            font-weight: 700;
            color: #1e293b;
            margin-top: 1.25rem;
            margin-bottom: 0.5rem;
        }
        .docs-prose p { color: #475569; line-height: 1.75; font-size: 0.9375rem; margin-bottom: 0.875rem; }
        .docs-prose p:last-child { margin-bottom: 0; }
        .docs-prose a { color: #4f46e5; text-decoration: underline; text-decoration-color: #a5b4fc; text-underline-offset: 2px; }
        .docs-prose a:hover { color: #3730a3; }
        .docs-prose ul { list-style: disc; padding-left: 1.5rem; color: #475569; font-size: 0.9375rem; line-height: 1.75; }
        .docs-prose ol { list-style: decimal; padding-left: 1.5rem; color: #475569; font-size: 0.9375rem; line-height: 1.75; }
        .docs-prose li { margin-bottom: 0.25rem; }
        .docs-prose strong { color: #1e293b; font-weight: 600; }
        .docs-prose code:not(pre code) {
            font-size: 0.8125rem;
            background: #f1f5f9;
            color: #4f46e5;
            padding: 0.1rem 0.35rem;
            border-radius: 0.25rem;
            border: 1px solid #e2e8f0;
        }
        .docs-prose pre {
            background: #0f172a;
            border-radius: 0.75rem;
            padding: 1.25rem 1.5rem;
            overflow-x: auto;
            margin: 1rem 0;
            border: 1px solid #1e293b;
        }
        .docs-prose pre code {
            color: #e2e8f0;
            font-size: 0.8125rem;
            line-height: 1.7;
            background: transparent;
            border: none;
            padding: 0;
        }
        .docs-prose table { width: 100%; border-collapse: collapse; font-size: 0.875rem; margin: 1rem 0; }
        .docs-prose th { background: #f8fafc; color: #475569; font-weight: 600; text-align: left; padding: 0.625rem 0.875rem; border: 1px solid #e2e8f0; }
        .docs-prose td { padding: 0.5rem 0.875rem; border: 1px solid #e2e8f0; color: #475569; }
        .docs-prose tr:nth-child(even) td { background: #fafafa; }

        /* ── Callout boxes ── */
        .docs-note   { background: #eff6ff; border-left: 3px solid #3b82f6; padding: 0.875rem 1rem; border-radius: 0 0.5rem 0.5rem 0; color: #1e40af; font-size: 0.875rem; line-height: 1.6; }
        .docs-tip    { background: #f0fdf4; border-left: 3px solid #22c55e; padding: 0.875rem 1rem; border-radius: 0 0.5rem 0.5rem 0; color: #166534; font-size: 0.875rem; line-height: 1.6; }
        .docs-warn   { background: #fffbeb; border-left: 3px solid #f59e0b; padding: 0.875rem 1rem; border-radius: 0 0.5rem 0.5rem 0; color: #92400e; font-size: 0.875rem; line-height: 1.6; }
        .docs-danger { background: #fff1f2; border-left: 3px solid #f43f5e; padding: 0.875rem 1rem; border-radius: 0 0.5rem 0.5rem 0; color: #9f1239; font-size: 0.875rem; line-height: 1.6; }

        /* ── Left sidebar nav ── */
        .docs-nav-link {
            display: flex; align-items: center; gap: 0.5rem;
            padding: 0.375rem 0.75rem; border-radius: 0.5rem;
            font-size: 0.875rem; color: #64748b; font-weight: 500;
            transition: background 0.15s, color 0.15s;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .docs-nav-link:hover { background: #f1f5f9; color: #0f172a; }
        .docs-nav-link.active { background: #eef2ff; color: #4f46e5; font-weight: 600; }
        .docs-nav-link.active .nav-dot { background: #4f46e5; }
        .nav-dot { width: 6px; height: 6px; border-radius: 50%; background: #cbd5e1; flex-shrink: 0; }

        /* ── Right TOC ── */
        .toc-item {
            display: block;
            font-size: 0.8125rem; color: #94a3b8;
            padding: 0.25rem 0;
            border-left: 2px solid transparent;
            padding-left: 0.875rem;
            transition: color 0.15s, border-color 0.15s;
            text-decoration: none;
            line-height: 1.4;
        }
        .toc-item:hover { color: #475569; }
        .toc-item.active { color: #4f46e5; border-left-color: #4f46e5; font-weight: 500; }

        /* ── Fade-in animation for flash ── */
        @keyframes fadeIn { from { opacity: 0; transform: translateY(-8px); } to { opacity: 1; transform: translateY(0); } }
        .flash-msg { animation: fadeIn 0.3s ease-out; }

        /* ── Section scroll padding ── */
        [id] { scroll-margin-top: 5.5rem; }
    </style>
    @stack('styles')
</head>
<body class="min-h-screen" style="background: #FAFAF9;">

    {{-- ═══ Top Navigation Bar ═══════════════════════════════════════════════ --}}
    <header class="fixed top-0 left-0 right-0 z-40 h-14 bg-white border-b border-slate-200/80 flex items-center px-4 gap-3 shadow-sm">

        {{-- Back to UCM --}}
        <a href="{{ route('dashboard') }}"
           class="flex items-center gap-1.5 text-slate-500 hover:text-slate-900 transition-colors text-sm font-medium flex-shrink-0 mr-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            <span class="hidden sm:inline">กลับสู่ UCM</span>
        </a>

        {{-- Divider --}}
        <div class="w-px h-5 bg-slate-200 flex-shrink-0"></div>

        {{-- Brand --}}
        <div class="flex items-center gap-2.5 flex-shrink-0">
            <div class="w-7 h-7 rounded-lg flex items-center justify-center flex-shrink-0"
                 style="background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%)">
                <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
            </div>
            <div>
                <span class="text-sm font-bold text-slate-900 leading-tight">UCM</span>
                <span class="text-sm font-medium text-slate-400 ml-1">Docs</span>
            </div>
        </div>

        {{-- Spacer --}}
        <div class="flex-1"></div>

        {{-- Mobile: hamburger to toggle sidebar --}}
        <button type="button" id="docs-sidebar-toggle"
                class="lg:hidden p-2 rounded-lg text-slate-500 hover:bg-slate-100 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>

        {{-- User avatar --}}
        @auth
        <div class="flex items-center gap-2 flex-shrink-0">
            <div class="w-7 h-7 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0"
                 style="background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%)">
                {{ mb_strtoupper(mb_substr(auth()->user()->display_name ?? auth()->user()->name ?? 'U', 0, 1)) }}
            </div>
            <span class="hidden sm:block text-sm font-medium text-slate-600 truncate max-w-[120px]">
                {{ auth()->user()->display_name ?? auth()->user()->name }}
            </span>
        </div>
        @endauth

    </header>

    {{-- ═══ Sidebar Overlay (mobile) ══════════════════════════════════════════ --}}
    <div id="docs-sidebar-overlay"
         class="fixed inset-0 bg-black/40 backdrop-blur-sm z-30 hidden lg:hidden"
         onclick="docsSidebarClose()"></div>

    {{-- ═══ Left Sidebar ════════════════════════════════════════════════════════ --}}
    <aside id="docs-sidebar"
           class="fixed top-14 bottom-0 left-0 z-30 w-64 bg-white border-r border-slate-200/80 overflow-y-auto
                  -translate-x-full lg:translate-x-0 transition-transform duration-300">

        <div class="px-3 py-5">

            {{-- ── คู่มือ ── --}}
            <p class="px-3 mb-2 text-[10px] font-semibold text-slate-400 uppercase tracking-widest">คู่มือ</p>
            <div class="space-y-0.5 mb-5">
                <a href="{{ route('docs.manual') }}"
                   class="docs-nav-link {{ request()->routeIs('docs.manual') ? 'active' : '' }}">
                    <span class="nav-dot"></span>
                    คู่มือผู้ใช้งาน
                </a>
            </div>

            {{-- ── นักพัฒนา ── --}}
            <p class="px-3 mb-2 text-[10px] font-semibold text-slate-400 uppercase tracking-widest">นักพัฒนา</p>
            <div class="space-y-0.5 mb-5">
                <a href="{{ route('docs.install') }}"
                   class="docs-nav-link {{ request()->routeIs('docs.install') ? 'active' : '' }}">
                    <span class="nav-dot"></span>
                    Install Guide
                </a>
                <a href="{{ route('api-docs') }}"
                   class="docs-nav-link {{ request()->routeIs('api-docs') ? 'active' : '' }}">
                    <span class="nav-dot"></span>
                    API Reference
                </a>
                <a href="{{ url('api-docs/swagger') }}"
                   class="docs-nav-link {{ request()->is('api-docs/swagger') ? 'active' : '' }}">
                    <span class="nav-dot"></span>
                    Swagger UI
                </a>
            </div>

            {{-- ── ใน Page นี้ (mobile: show page TOC inline) ── --}}
            @if(trim($__env->yieldPushContent('doc-page-toc')) !== '')
            <div class="xl:hidden">
                <div class="h-px bg-slate-100 mb-4"></div>
                <p class="px-3 mb-2 text-[10px] font-semibold text-slate-400 uppercase tracking-widest">ในหน้านี้</p>
                <div class="space-y-0.5" id="mobile-toc-list">
                    @stack('doc-page-toc')
                </div>
            </div>
            @endif

        </div>
    </aside>

    {{-- ═══ Main Layout ══════════════════════════════════════════════════════════ --}}
    <div class="lg:ml-64 pt-14 min-h-screen">
        <div class="flex">

            {{-- ── Main Content ── --}}
            <main class="flex-1 min-w-0 px-6 md:px-10 py-8 max-w-3xl xl:max-w-none">
                @yield('content')
            </main>

            {{-- ── Right: On This Page TOC (desktop only) ── --}}
            @if(trim($__env->yieldPushContent('doc-page-toc')) !== '')
            <aside class="hidden xl:block w-56 flex-shrink-0 py-8 pr-6">
                <div class="sticky top-20">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3">On this page</p>
                    <nav id="toc-right">
                        @stack('doc-page-toc')
                    </nav>
                </div>
            </aside>
            @endif

        </div>

        {{-- ═══ Footer ════════════════════════════════════════════════════════ --}}
        <footer class="border-t border-slate-200/80 bg-white px-6 md:px-10 pt-8 pb-6">
            <div class="max-w-5xl">

                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6">

                    {{-- Brand --}}
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0"
                             style="background: linear-gradient(135deg,#4f46e5 0%,#7c3aed 100%)">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </div>
                        <div>
                            <div class="text-sm font-bold text-slate-900">UCM Documentation</div>
                            <div class="text-[11px] text-slate-400 mt-0.5">{{ config('app.name') }}</div>
                        </div>
                    </div>

                    {{-- Nav columns --}}
                    <div class="flex flex-wrap gap-x-10 gap-y-4 text-sm">
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">คู่มือ</p>
                            <div class="space-y-1.5">
                                <a href="{{ route('docs.manual') }}" class="block text-slate-500 hover:text-indigo-600 transition-colors">คู่มือผู้ใช้งาน</a>
                            </div>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">นักพัฒนา</p>
                            <div class="space-y-1.5">
                                <a href="{{ route('docs.install') }}" class="block text-slate-500 hover:text-indigo-600 transition-colors">Install Guide</a>
                                <a href="{{ route('api-docs') }}" class="block text-slate-500 hover:text-indigo-600 transition-colors">API Reference</a>
                                <a href="{{ url('api-docs/swagger') }}" class="block text-slate-500 hover:text-indigo-600 transition-colors">Swagger UI</a>
                            </div>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">ระบบ</p>
                            <div class="space-y-1.5">
                                <a href="{{ route('dashboard') }}" class="block text-slate-500 hover:text-indigo-600 transition-colors">กลับสู่ UCM</a>
                                <a href="{{ route('notifications.index') }}" class="block text-slate-500 hover:text-indigo-600 transition-colors">Notifications</a>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- Bottom row --}}
                <div class="mt-6 pt-5 border-t border-slate-100 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2">
                    <span class="text-[11px] text-slate-400">
                        &copy; {{ now()->year }} <span class="font-semibold text-slate-500">{{ config('app.name') }}</span>. All rights reserved.
                    </span>
                    <div class="flex items-center gap-3 text-[11px] text-slate-300">
                        <span>v{{ config('app.version', '1.0.0') }}</span>
                        <span class="w-1 h-1 rounded-full bg-slate-200"></span>
                        <span>Laravel {{ app()->version() }}</span>
                        <span class="w-1 h-1 rounded-full bg-slate-200"></span>
                        <span>PHP {{ PHP_MAJOR_VERSION }}.{{ PHP_MINOR_VERSION }}</span>
                    </div>
                </div>

            </div>
        </footer>

    </div>

    {{-- ═══ Scripts ════════════════════════════════════════════════════════════ --}}
    <script>
    // Mobile sidebar toggle
    document.getElementById('docs-sidebar-toggle').addEventListener('click', function () {
        var sidebar  = document.getElementById('docs-sidebar');
        var overlay  = document.getElementById('docs-sidebar-overlay');
        var isOpen   = !sidebar.classList.contains('-translate-x-full');
        if (isOpen) { docsSidebarClose(); } else { docsSidebarOpen(); }
    });
    function docsSidebarOpen() {
        document.getElementById('docs-sidebar').classList.remove('-translate-x-full');
        document.getElementById('docs-sidebar-overlay').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    function docsSidebarClose() {
        document.getElementById('docs-sidebar').classList.add('-translate-x-full');
        document.getElementById('docs-sidebar-overlay').classList.add('hidden');
        document.body.style.overflow = '';
    }

    // Scroll-spy: highlight the active section in the right TOC
    (function () {
        var tocLinks = document.querySelectorAll('#toc-right .toc-item');
        if (!tocLinks.length) { return; }

        var sections = Array.from(tocLinks).map(function (link) {
            var id = link.getAttribute('href').replace('#', '');
            return { link: link, el: document.getElementById(id) };
        }).filter(function (s) { return s.el; });

        function onScroll() {
            var scrollY = window.scrollY + 100;
            var active  = null;
            sections.forEach(function (s) {
                if (s.el.offsetTop <= scrollY) { active = s; }
            });
            tocLinks.forEach(function (l) { l.classList.remove('active'); });
            if (active) { active.link.classList.add('active'); }
        }

        window.addEventListener('scroll', onScroll, { passive: true });
        onScroll();
    })();
    </script>
    @stack('scripts')
</body>
</html>
