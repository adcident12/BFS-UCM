@props([])

<div class="code-block-wrapper relative group">
    <button class="copy-btn absolute top-2.5 right-2.5 text-xs text-slate-400 hover:text-slate-600 bg-white/80 px-2.5 py-1 rounded-lg border border-slate-200 font-medium opacity-0 group-hover:opacity-100 transition-opacity">คัดลอก</button>
    <pre class="bg-slate-900 rounded-xl p-4 text-xs leading-relaxed overflow-x-auto"><code class="text-emerald-300 font-mono">{{ trim($slot) }}</code></pre>
</div>
