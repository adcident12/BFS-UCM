@props([
    'method',
    'path',
    'summary',
    'description' => '',
    'auth'        => 'true',
    'rateLimit'   => null,
    'requestBody' => '',
    'responses'   => '',
    'example'     => '',
])

@php
$id = 'ep-' . md5($method . $path);

$methodColors = [
    'GET'    => 'bg-sky-100 text-sky-700 ring-1 ring-sky-200',
    'POST'   => 'bg-emerald-100 text-emerald-700 ring-1 ring-emerald-200',
    'PUT'    => 'bg-amber-100 text-amber-700 ring-1 ring-amber-200',
    'PATCH'  => 'bg-orange-100 text-orange-700 ring-1 ring-orange-200',
    'DELETE' => 'bg-red-100 text-red-700 ring-1 ring-red-200',
];
$mc = $methodColors[$method] ?? 'bg-slate-100 text-slate-700';
@endphp

<div class="bg-white rounded-2xl shadow-sm ring-1 ring-slate-100 overflow-hidden">

    {{-- Header (clickable toggle) --}}
    <button type="button"
            class="endpoint-toggle w-full text-left px-5 py-4 flex items-center gap-3 hover:bg-slate-50/70 transition-colors"
            data-target="{{ $id }}">

        <span class="text-xs font-bold px-2.5 py-1 rounded-lg font-mono flex-shrink-0 {{ $mc }}">{{ $method }}</span>

        <code class="text-sm font-mono text-slate-700 flex-1 min-w-0 truncate">{{ $path }}</code>

        <span class="text-sm text-slate-500 font-medium hidden md:block flex-shrink-0 mx-2">{{ $summary }}</span>

        <div class="flex items-center gap-2 flex-shrink-0 ml-auto">
            @if ($auth === 'true')
                <span class="text-xs bg-amber-50 text-amber-700 border border-amber-200 px-2 py-0.5 rounded-full font-medium hidden sm:inline">🔐 Auth</span>
            @else
                <span class="text-xs bg-slate-50 text-slate-500 border border-slate-200 px-2 py-0.5 rounded-full font-medium hidden sm:inline">Public</span>
            @endif
            @if ($rateLimit)
                <span class="text-xs bg-rose-50 text-rose-600 border border-rose-200 px-2 py-0.5 rounded-full font-medium hidden lg:inline">⏱ {{ $rateLimit }}</span>
            @endif
            <svg class="chevron w-4 h-4 text-slate-400 transition-transform duration-200 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </div>
    </button>

    {{-- Expandable body --}}
    <div id="{{ $id }}" class="endpoint-body" style="display:none">
        <div class="border-t border-slate-100 px-5 py-5 space-y-5">

            {{-- Summary (mobile) + Description --}}
            <div>
                <p class="text-sm font-semibold text-slate-700 md:hidden mb-1">{{ $summary }}</p>
                @if ($description)
                    <p class="text-sm text-slate-500 leading-relaxed">{{ $description }}</p>
                @endif
                <div class="flex flex-wrap items-center gap-2 mt-3">
                    @if ($auth === 'true')
                        <span class="inline-flex items-center gap-1.5 text-xs bg-amber-50 text-amber-700 border border-amber-200 px-2.5 py-1 rounded-full font-semibold">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                            </svg>
                            ต้องการ Bearer Token
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 text-xs bg-slate-50 text-slate-600 border border-slate-200 px-2.5 py-1 rounded-full font-semibold">
                            Public — ไม่ต้อง Auth
                        </span>
                    @endif
                    @if ($rateLimit)
                        <span class="inline-flex items-center gap-1.5 text-xs bg-rose-50 text-rose-600 border border-rose-200 px-2.5 py-1 rounded-full font-semibold">
                            Rate Limit: {{ $rateLimit }}
                        </span>
                    @endif
                </div>
            </div>

            {{-- Tabs: Request | Response | Example --}}
            <div class="tab-switcher">
                <div class="flex border-b border-slate-100 gap-1 mb-4">
                    <button data-tab-btn="request"
                            class="py-2 px-3 text-xs font-semibold border-b-2 transition-colors border-indigo-500 text-indigo-600">
                        Request Body
                    </button>
                    <button data-tab-btn="response"
                            class="py-2 px-3 text-xs font-semibold border-b-2 transition-colors border-transparent text-slate-400 hover:text-slate-600">
                        Responses
                    </button>
                    <button data-tab-btn="example"
                            class="py-2 px-3 text-xs font-semibold border-b-2 transition-colors border-transparent text-slate-400 hover:text-slate-600">
                        Code Example
                    </button>
                </div>

                <div data-tab-content="request">
                    <div class="code-block-wrapper relative group">
                        <button class="copy-btn absolute top-2.5 right-2.5 text-xs text-slate-400 hover:text-slate-600 bg-white/80 px-2.5 py-1 rounded-lg border border-slate-200 font-medium opacity-0 group-hover:opacity-100 transition-opacity">คัดลอก</button>
                        <pre class="bg-slate-900 rounded-xl p-4 text-xs leading-relaxed overflow-x-auto"><code class="text-slate-200 font-mono">{{ trim($requestBody) }}</code></pre>
                    </div>
                </div>

                <div data-tab-content="response" style="display:none">
                    <div class="code-block-wrapper relative group">
                        <button class="copy-btn absolute top-2.5 right-2.5 text-xs text-slate-400 hover:text-slate-600 bg-white/80 px-2.5 py-1 rounded-lg border border-slate-200 font-medium opacity-0 group-hover:opacity-100 transition-opacity">คัดลอก</button>
                        <pre class="bg-slate-900 rounded-xl p-4 text-xs leading-relaxed overflow-x-auto"><code class="text-slate-200 font-mono">{{ trim($responses) }}</code></pre>
                    </div>
                </div>

                <div data-tab-content="example" style="display:none">
                    <div class="code-block-wrapper relative group">
                        <button class="copy-btn absolute top-2.5 right-2.5 text-xs text-slate-400 hover:text-slate-600 bg-white/80 px-2.5 py-1 rounded-lg border border-slate-200 font-medium opacity-0 group-hover:opacity-100 transition-opacity">คัดลอก</button>
                        <pre class="bg-slate-900 rounded-xl p-4 text-xs leading-relaxed overflow-x-auto"><code class="text-emerald-300 font-mono">{{ trim($example) }}</code></pre>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>
