@props(['label', 'color' => 'indigo'])

@php
$colors = [
    'indigo' => ['bg' => 'bg-indigo-100', 'text' => 'text-indigo-700', 'border' => 'border-indigo-200', 'dot' => 'bg-indigo-500'],
    'violet' => ['bg' => 'bg-violet-100', 'text' => 'text-violet-700', 'border' => 'border-violet-200', 'dot' => 'bg-violet-500'],
    'sky'    => ['bg' => 'bg-sky-100',    'text' => 'text-sky-700',    'border' => 'border-sky-200',    'dot' => 'bg-sky-500'],
    'emerald'=> ['bg' => 'bg-emerald-100','text' => 'text-emerald-700','border' => 'border-emerald-200','dot' => 'bg-emerald-500'],
];
$c = $colors[$color] ?? $colors['indigo'];
@endphp

<div class="mb-8">
    {{-- Group header --}}
    <div class="flex items-center gap-3 mb-3">
        <div class="flex items-center gap-2">
            <div class="w-2 h-2 rounded-full {{ $c['dot'] }}"></div>
            <span class="text-xs font-bold uppercase tracking-widest {{ $c['text'] }}">{{ $label }}</span>
        </div>
        <div class="flex-1 h-px bg-slate-100"></div>
    </div>

    {{-- Endpoints --}}
    <div class="space-y-3">
        {{ $slot }}
    </div>
</div>
