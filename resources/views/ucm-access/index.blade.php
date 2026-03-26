@extends('layouts.app')

@section('title', 'จัดการสิทธิ์ระบบ UCM')
@section('header', 'จัดการสิทธิ์ระบบ UCM')

@section('breadcrumbs')
<a href="{{ route('dashboard') }}" class="text-slate-400 hover:text-slate-600 font-medium transition-colors whitespace-nowrap">Dashboard</a>
<svg class="w-3.5 h-3.5 text-slate-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
</svg>
<span class="font-semibold text-slate-800 truncate">จัดการสิทธิ์ระบบ UCM</span>
@endsection

@section('content')

@php
$levelLabels = ['0' => 'ทุกคน (L0)', '1' => 'Admin L1+', '2' => 'Admin L2 เท่านั้น'];
$levelColors = [
    '0' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
    '1' => 'bg-indigo-100 text-indigo-700 border-indigo-200',
    '2' => 'bg-amber-100 text-amber-700 border-amber-200',
];
$levelBtnColors = [
    '0' => 'border-emerald-300 text-emerald-700 hover:bg-emerald-50',
    '1' => 'border-indigo-300 text-indigo-700 hover:bg-indigo-50',
    '2' => 'border-amber-300 text-amber-700 hover:bg-amber-50',
];
@endphp

{{-- Hero Banner --}}
<div class="mb-7 relative overflow-hidden rounded-2xl"
     style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 45%, #0c1a2e 100%)">
    <div class="absolute inset-0 pointer-events-none overflow-hidden">
        <div class="absolute -top-12 -right-12 w-72 h-72 bg-indigo-500/6 rounded-full blur-2xl"></div>
        <div class="absolute -bottom-16 left-24 w-56 h-56 bg-blue-400/5 rounded-full blur-2xl"></div>
        <div class="absolute inset-0"
             style="background-image:linear-gradient(rgba(255,255,255,0.025) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,0.025) 1px,transparent 1px);background-size:28px 28px"></div>
    </div>
    <div class="relative px-6 md:px-8 py-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-white/8 backdrop-blur-sm rounded-2xl flex items-center justify-center flex-shrink-0 border border-white/10">
                <svg class="w-6 h-6 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
            </div>
            <div>
                <h2 class="text-lg font-bold text-white">จัดการสิทธิ์ระบบ UCM</h2>
                <p class="text-slate-400 text-xs font-medium mt-0.5">กำหนดว่าผู้ใช้แต่ละระดับสามารถเข้าถึงฟีเจอร์ใดของ UCM ได้บ้าง</p>
            </div>
        </div>
        <div class="flex flex-wrap gap-2">
            @foreach ([0 => 'L0 ทุกคน', 1 => 'L1 Admin+', 2 => 'L2 Admin'] as $lv => $label)
            <span class="inline-flex items-center gap-1.5 text-[11px] font-semibold px-2.5 py-1 rounded-xl
                {{ [0 => 'bg-emerald-500/15 text-emerald-300 border border-emerald-500/25', 1 => 'bg-indigo-500/15 text-indigo-300 border border-indigo-500/25', 2 => 'bg-amber-500/15 text-amber-300 border border-amber-500/25'][$lv] }}">
                <span class="w-1.5 h-1.5 rounded-full {{ ['bg-emerald-400', 'bg-indigo-400', 'bg-amber-400'][$lv] }}"></span>
                {{ $label }}
            </span>
            @endforeach
        </div>
    </div>
</div>

{{-- Feature groups --}}
<div class="space-y-6">
    @foreach ($grouped as $groupName => $features)
    <div class="bg-white rounded-2xl shadow-sm ring-1 ring-slate-100 overflow-hidden">

        {{-- Group header --}}
        <div class="flex items-center gap-2.5 px-6 py-3.5 bg-slate-50/70 border-b border-slate-100">
            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
            </svg>
            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">{{ $groupName }}</span>
            <span class="ml-auto text-xs text-slate-400">{{ $features->count() }} ฟีเจอร์</span>
        </div>

        <div class="divide-y divide-slate-50">
            @foreach ($features as $feature)
            @php
            $key = $feature['key'];
            $effectiveLevel = $feature['effective_level'];
            $isDefault = $feature['override'] === null;
            $defaultLevel = $feature['default_level'];
            $isLockable = $feature['lockable'] ?? true;
            $grants = $feature['grants'];
            @endphp

            <div class="px-6 py-4">
                <div class="flex flex-wrap items-start gap-3">

                    {{-- Feature info --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="text-sm font-semibold text-slate-800">{{ $feature['name'] }}</span>
                            <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-0.5 rounded-lg border {{ $levelColors[$effectiveLevel] }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ ['bg-emerald-500','bg-indigo-500','bg-amber-500'][$effectiveLevel] }}"></span>
                                {{ $levelLabels[$effectiveLevel] }}
                            </span>
                            @if (!$isDefault)
                            <span class="text-[10px] font-semibold text-violet-600 bg-violet-50 border border-violet-200 px-1.5 py-0.5 rounded-md"
                                  title="Default: {{ $levelLabels[$defaultLevel] }}">override</span>
                            @endif
                            @if (!$isLockable)
                            <span class="text-[10px] font-semibold text-slate-400 bg-slate-50 border border-slate-200 px-1.5 py-0.5 rounded-md"
                                  title="ระดับนี้ไม่สามารถเปลี่ยนได้เพื่อป้องกันการล็อคตัวเอง">ล็อค</span>
                            @endif
                        </div>
                        <p class="text-xs text-slate-400 mt-0.5">{{ $feature['description'] }}</p>
                        @if (!$isDefault)
                        <p class="text-[10px] text-slate-400 mt-1">
                            Default: <span class="font-semibold">{{ $levelLabels[$defaultLevel] }}</span>
                            · แก้ไขโดย {{ $feature['override']?->updatedBy?->name ?? 'Unknown' }}
                            เมื่อ {{ $feature['override']?->updated_at?->diffForHumans() }}
                        </p>
                        @endif
                    </div>

                    {{-- Level selector --}}
                    @if ($isLockable)
                    <div class="flex items-center gap-1 flex-shrink-0">
                        @foreach ([0, 1, 2] as $lv)
                        <form method="POST" action="{{ route('ucm-access.level.update', $key) }}" class="inline">
                            @csrf
                            <input type="hidden" name="min_level" value="{{ $lv }}">
                            <button type="submit"
                                    class="px-2.5 py-1.5 text-xs font-semibold rounded-xl border transition-all
                                           {{ $effectiveLevel === $lv
                                               ? 'border-current ' . $levelColors[$lv] . ' shadow-sm'
                                               : 'border-slate-200 text-slate-400 hover:border-slate-300 hover:text-slate-600 bg-white' }}"
                                    title="{{ $levelLabels[$lv] }}"
                                    @if ($effectiveLevel === $lv) disabled @endif>
                                L{{ $lv }}
                            </button>
                        </form>
                        @endforeach
                        @if (!$isDefault)
                        <form method="POST" action="{{ route('ucm-access.level.update', $key) }}" class="inline">
                            @csrf
                            <input type="hidden" name="min_level" value="{{ $defaultLevel }}">
                            <button type="submit"
                                    class="px-2 py-1.5 text-xs font-semibold text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition-colors border border-transparent"
                                    title="รีเซ็ตกลับค่า Default ({{ $levelLabels[$defaultLevel] }})">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                            </button>
                        </form>
                        @endif
                    </div>
                    @endif

                    {{-- Grants toggle --}}
                    <button type="button"
                            onclick="toggleGrants('{{ $key }}')"
                            class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 px-2.5 py-1.5 rounded-xl transition-colors border border-slate-200 hover:border-indigo-200 flex-shrink-0">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        สิทธิ์พิเศษ
                        @if ($grants->isNotEmpty())
                        <span class="bg-indigo-100 text-indigo-700 px-1.5 rounded-full text-[10px] font-bold">{{ $grants->count() }}</span>
                        @endif
                    </button>
                </div>

                {{-- Grants panel --}}
                <div id="grants-{{ $key }}" class="hidden mt-3 pt-3 border-t border-slate-100">
                    <p class="text-xs font-semibold text-slate-500 mb-2">สิทธิ์พิเศษรายบุคคล (เข้าถึงได้แม้ระดับต่ำกว่า {{ $levelLabels[$effectiveLevel] }})</p>

                    @if ($grants->isNotEmpty())
                    <div class="flex flex-wrap gap-2 mb-3">
                        @foreach ($grants as $grant)
                        <div class="flex items-center gap-2 bg-slate-50 border border-slate-200 rounded-xl px-3 py-1.5">
                            <div class="w-6 h-6 rounded-lg flex items-center justify-center text-[10px] font-bold text-white flex-shrink-0"
                                 style="background: linear-gradient(135deg, #6366f1, #8b5cf6)">
                                {{ strtoupper(substr($grant->user?->name ?? '?', 0, 1)) }}
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs font-semibold text-slate-700 leading-tight">{{ $grant->user?->name ?? 'Unknown' }}</p>
                                <p class="text-[10px] text-slate-400 leading-tight">
                                    {{ $grant->user?->username }}
                                    @if ($grant->user?->department) · {{ $grant->user->department }} @endif
                                    · Admin L{{ $grant->user?->is_admin ?? 0 }}
                                </p>
                            </div>
                            <form id="del-grant-{{ $grant->id }}" method="POST" action="{{ route('ucm-access.grant.destroy', [$key, $grant]) }}">
                                @csrf @method('DELETE')
                                <button type="button"
                                        onclick="askConfirm('del-grant-{{ $grant->id }}', 'ถอนสิทธิ์พิเศษ', 'ถอนสิทธิ์พิเศษของ {{ addslashes($grant->user?->name ?? 'ผู้ใช้นี้') }}?')"
                                        class="ml-1 w-5 h-5 flex items-center justify-center text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors"
                                        title="ถอนสิทธิ์">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <p class="text-xs text-slate-400 mb-3">ยังไม่มีสิทธิ์พิเศษ</p>
                    @endif

                    {{-- Add grant --}}
                    <div class="flex items-start gap-2 relative">
                        <div class="flex-1 relative" id="grant-search-wrap-{{ $key }}">
                            <input type="text"
                                   id="grant-search-{{ $key }}"
                                   placeholder="ค้นหาชื่อ / username / แผนก..."
                                   oninput="searchGrant('{{ $key }}', this.value)"
                                   autocomplete="off"
                                   class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 transition-all">
                            <div id="grant-dropdown-{{ $key }}"
                                 class="hidden absolute z-20 top-full mt-1 left-0 right-0 bg-white border border-slate-200 rounded-xl shadow-lg overflow-hidden max-h-48 overflow-y-auto">
                            </div>
                        </div>
                        <form method="POST" action="{{ route('ucm-access.grant.store', $key) }}" id="grant-form-{{ $key }}">
                            @csrf
                            <input type="hidden" name="user_id" id="grant-user-id-{{ $key }}">
                            <button type="submit" id="grant-btn-{{ $key }}" disabled
                                    class="px-3 py-2 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 disabled:bg-slate-200 disabled:text-slate-400 disabled:cursor-not-allowed rounded-xl transition-colors whitespace-nowrap">
                                + เพิ่มสิทธิ์
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endforeach
</div>

@push('scripts')
<script>
/* ── Grant panel toggle ─────────────────────────────────────── */
function toggleGrants(key) {
    var panel = document.getElementById('grants-' + key);
    if (panel) { panel.classList.toggle('hidden'); }
}

/* ── User search for grant ──────────────────────────────────── */
var _searchTimers = {};

function searchGrant(key, q) {
    clearTimeout(_searchTimers[key]);
    var dropdown = document.getElementById('grant-dropdown-' + key);
    var userId   = document.getElementById('grant-user-id-' + key);
    var btn      = document.getElementById('grant-btn-' + key);

    // clear selected user when typing
    if (userId) { userId.value = ''; }
    if (btn)    { btn.disabled = true; }

    if (!q || q.trim().length < 2) {
        if (dropdown) { dropdown.classList.add('hidden'); }
        return;
    }

    _searchTimers[key] = setTimeout(function() {
        fetch('/admin/ucm-access/search-users?q=' + encodeURIComponent(q), {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(function(r) { return r.json(); })
        .then(function(users) {
            if (!dropdown) { return; }
            if (!users.length) {
                dropdown.innerHTML = '<p class="text-xs text-slate-400 px-4 py-3">ไม่พบผู้ใช้</p>';
                dropdown.classList.remove('hidden');
                return;
            }
            var levelLabels = { 0: 'L0', 1: 'L1', 2: 'L2' };
            dropdown.innerHTML = users.map(function(u) {
                return '<button type="button" onclick="selectGrantUser(\'' + key + '\',' + u.id + ',\'' + u.name.replace(/'/g, "\\'") + '\')"'
                    + ' class="w-full text-left flex items-center gap-2.5 px-3 py-2.5 hover:bg-indigo-50 transition-colors">'
                    + '<div class="w-7 h-7 rounded-lg flex items-center justify-center text-[10px] font-bold text-white flex-shrink-0" style="background:linear-gradient(135deg,#6366f1,#8b5cf6)">'
                    + u.name.charAt(0).toUpperCase()
                    + '</div><div class="flex-1 min-w-0">'
                    + '<p class="text-sm font-semibold text-slate-700 truncate">' + u.name + '</p>'
                    + '<p class="text-xs text-slate-400">' + u.username + (u.department ? ' · ' + u.department : '') + ' · Admin ' + (levelLabels[u.is_admin] || 'L0') + '</p>'
                    + '</div></button>';
            }).join('');
            dropdown.classList.remove('hidden');
        })
        .catch(function() {});
    }, 300);
}

function selectGrantUser(key, userId, userName) {
    var input    = document.getElementById('grant-search-' + key);
    var hidden   = document.getElementById('grant-user-id-' + key);
    var btn      = document.getElementById('grant-btn-' + key);
    var dropdown = document.getElementById('grant-dropdown-' + key);

    if (input)    { input.value = userName; }
    if (hidden)   { hidden.value = userId; }
    if (btn)      { btn.disabled = false; }
    if (dropdown) { dropdown.classList.add('hidden'); }
}

/* ── Close dropdowns when clicking outside ──────────────────── */
document.addEventListener('click', function(e) {
    document.querySelectorAll('[id^="grant-dropdown-"]').forEach(function(d) {
        if (!d.parentElement.contains(e.target)) { d.classList.add('hidden'); }
    });
});
</script>
@endpush

@endsection
