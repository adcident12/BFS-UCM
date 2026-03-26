<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $shareLink->label }} — UCM Permission Matrix</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
    <style>
        body { font-family: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif; }

        /* Matrix styles */
        .matrix-wrap {
            overflow: auto;
            max-height: calc(100vh - 260px);
            min-height: 300px;
            border-radius: 0 0 16px 16px;
        }
        .matrix-table {
            border-collapse: separate;
            border-spacing: 0;
            font-size: 11px;
            white-space: nowrap;
        }
        .col-user {
            position: sticky;
            left: 0;
            z-index: 20;
            min-width: 200px;
            max-width: 200px;
            background: #fff;
            border-right: 2px solid #e2e8f0;
        }
        thead .col-user { z-index: 40; background: #f8fafc; }
        .row-system th {
            position: sticky;
            top: 0;
            z-index: 30;
            background: #f8fafc;
        }
        .row-system .col-user { z-index: 40; }
        .row-perm th {
            position: sticky;
            top: 41px;
            z-index: 30;
            background: #f8fafc;
        }
        .row-perm .col-user { z-index: 40; }
        .perm-label-wrap {
            display: flex;
            align-items: flex-end;
            justify-content: center;
            height: 100px;
            padding: 6px 4px 4px;
        }
        .perm-label-inner {
            writing-mode: vertical-rl;
            transform: rotate(180deg);
            font-size: 10px;
            font-weight: 600;
            color: #475569;
            line-height: 1.2;
            max-height: 88px;
            overflow: hidden;
            text-overflow: ellipsis;
            cursor: default;
        }
        .sys-header {
            border-left: 2px solid #e2e8f0;
            border-bottom: 1px solid #e2e8f0;
            padding: 8px 10px;
            font-weight: 700;
            font-size: 11px;
            text-align: center;
            white-space: nowrap;
        }
        .perm-cell {
            border-left: 1px solid #f1f5f9;
            border-bottom: 1px solid #f1f5f9;
            text-align: center;
            padding: 6px 4px;
            min-width: 36px;
            max-width: 36px;
            transition: background 0.1s;
        }
        .perm-cell.sys-first { border-left: 2px solid #e2e8f0; }
        tr:hover .perm-cell { background: rgba(99,102,241,0.04); }
        .perm-cell.col-hover { background: rgba(99,102,241,0.08) !important; }
        .user-row:hover .col-user { background: #f8fafc; }
        .perm-count {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 20px;
            height: 16px;
            border-radius: 99px;
            font-size: 9px;
            font-weight: 700;
            padding: 0 4px;
        }
    </style>
</head>
<body class="bg-slate-50 min-h-screen">

    {{-- Header --}}
    <header class="bg-[#0f172a] text-white px-6 py-4 shadow-lg">
        <div class="max-w-screen-2xl mx-auto flex items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0"
                     style="background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #06b6d4 100%)">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div>
                    <div class="text-xs font-bold text-white leading-tight">UCM — Permission Matrix</div>
                    <div class="text-[10px] text-slate-400 leading-tight font-medium">Read-only · ไม่ต้อง Login</div>
                </div>
            </div>
            <div class="text-right">
                <div class="text-sm font-bold text-white">{{ $shareLink->label }}</div>
                <div class="text-[11px] text-slate-400 mt-0.5">
                    หมดอายุ {{ $shareLink->expires_at->format('d/m/Y H:i') }}
                    · ดูแล้ว {{ number_format($shareLink->view_count) }} ครั้ง
                </div>
            </div>
        </div>
    </header>

    {{-- Content --}}
    <main class="max-w-screen-2xl mx-auto px-4 py-6">

        {{-- Filter summary --}}
        @if ($shareLink->hasFilters())
            <div class="mb-4 flex flex-wrap gap-2">
                @if (! empty($shareLink->filter_system_ids))
                    @foreach ($systems as $system)
                        <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border text-xs font-semibold"
                             style="border-color: {{ $system->color ?? '#6366f1' }}40; background: {{ $system->color ?? '#6366f1' }}10; color: {{ $system->color ?? '#6366f1' }}">
                            <div class="w-2 h-2 rounded-full" style="background: {{ $system->color ?? '#6366f1' }}"></div>
                            {{ $system->name }}
                        </div>
                    @endforeach
                @endif
                @if (! empty($shareLink->filter_departments))
                    @foreach ($shareLink->filter_departments as $dept)
                        <span class="inline-flex items-center px-3 py-1.5 rounded-xl border border-sky-200 bg-sky-50 text-sky-700 text-xs font-semibold">
                            {{ $dept }}
                        </span>
                    @endforeach
                @endif
                @if (! empty($shareLink->filter_usernames))
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-slate-200 bg-slate-50 text-slate-600 text-xs font-semibold">
                        {{ count($shareLink->filter_usernames) }} username
                    </span>
                @endif
            </div>
        @endif

        {{-- System legend + summary --}}
        @php
            $displaySystems = $systems;
            $totalPermCols  = $displaySystems->sum(fn ($s) => $s->permissions->count());
        @endphp
        <div class="flex flex-wrap gap-2 mb-4">
            @foreach ($displaySystems as $system)
                @if ($system->permissions->isNotEmpty())
                    <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border text-xs font-semibold"
                         style="border-color: {{ $system->color ?? '#6366f1' }}40; background: {{ $system->color ?? '#6366f1' }}10; color: {{ $system->color ?? '#6366f1' }}">
                        <div class="w-2 h-2 rounded-full" style="background: {{ $system->color ?? '#6366f1' }}"></div>
                        {{ $system->name }}
                        <span class="opacity-60">({{ $system->permissions->count() }} สิทธิ์)</span>
                    </div>
                @endif
            @endforeach
            <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-slate-200 bg-slate-50 text-xs font-semibold text-slate-500">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                {{ number_format($users->count()) }} ผู้ใช้ · {{ $totalPermCols }} คอลัมน์สิทธิ์
            </div>
        </div>

        {{-- Matrix Table --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="matrix-wrap" id="matrix-wrap">
                <table class="matrix-table" id="matrix-table">
                    <thead>
                        <tr class="row-system">
                            <th class="col-user border-b border-slate-200 text-left px-4 py-2.5">
                                <span class="text-xs font-semibold text-slate-500">ผู้ใช้</span>
                            </th>
                            @foreach ($displaySystems as $system)
                                @if ($system->permissions->isNotEmpty())
                                    <th colspan="{{ $system->permissions->count() }}"
                                        class="sys-header"
                                        style="color: {{ $system->color ?? '#6366f1' }}; background: {{ $system->color ?? '#6366f1' }}0d; border-top: 3px solid {{ $system->color ?? '#6366f1' }};">
                                        <div class="flex items-center justify-center gap-1.5 whitespace-nowrap">
                                            <div class="w-2 h-2 rounded-full flex-shrink-0" style="background: {{ $system->color ?? '#6366f1' }}"></div>
                                            {{ $system->name }}
                                        </div>
                                    </th>
                                @endif
                            @endforeach
                            <th class="sys-header border-l-2 border-slate-200 text-slate-500 bg-slate-50" style="min-width:52px">
                                รวม
                            </th>
                        </tr>
                        <tr class="row-perm border-b-2 border-slate-200">
                            <th class="col-user px-4 pb-1 pt-0 text-left">
                                <span class="text-[10px] font-medium text-slate-400">เลื่อน → เพื่อดูสิทธิ์เพิ่มเติม</span>
                            </th>
                            @php $colIndex = 0; @endphp
                            @foreach ($displaySystems as $sysIdx => $system)
                                @foreach ($system->permissions as $permIdx => $perm)
                                    @php
                                        $isFirst = $permIdx === 0;
                                        $colIndex++;
                                    @endphp
                                    <th class="text-center {{ $isFirst ? 'border-l-2 border-slate-200' : '' }}"
                                        data-col="{{ $colIndex }}"
                                        style="background: {{ $system->color ?? '#6366f1' }}08;"
                                        title="{{ $system->name }} — {{ $perm->label }}">
                                        <div class="perm-label-wrap">
                                            <span class="perm-label-inner" style="color: {{ $system->color ?? '#6366f1' }}cc">
                                                {{ $perm->label }}
                                            </span>
                                        </div>
                                    </th>
                                @endforeach
                            @endforeach
                            <th class="border-l-2 border-slate-200 bg-slate-50" style="min-width:52px">
                                <div class="perm-label-wrap">
                                    <span class="perm-label-inner text-slate-400">จำนวนสิทธิ์</span>
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $user)
                            @php
                                $grantedMap = [];
                                foreach ($user->systemPermissions as $up) {
                                    $grantedMap[$up->system_id][$up->permission_key] = true;
                                }
                                $totalGranted = count($user->systemPermissions);
                            @endphp
                            <tr class="user-row" data-user="{{ $user->id }}">
                                <td class="col-user px-4 py-2.5 border-b border-slate-50">
                                    <div class="flex items-start justify-between gap-2">
                                        <div class="min-w-0">
                                            <div class="font-semibold text-slate-700 text-xs truncate">{{ $user->name }}</div>
                                            <div class="text-slate-400 font-mono text-[10px]">{{ $user->username }}</div>
                                            @if ($user->department)
                                                <div class="text-slate-400 text-[10px] truncate max-w-[140px]" title="{{ $user->department }}">
                                                    {{ $user->department }}
                                                </div>
                                            @endif
                                        </div>
                                        @if ($totalGranted > 0)
                                            <span class="perm-count bg-indigo-100 text-indigo-700 flex-shrink-0 mt-0.5">
                                                {{ $totalGranted }}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                @php $colIndex = 0; @endphp
                                @foreach ($displaySystems as $system)
                                    @foreach ($system->permissions as $permIdx => $perm)
                                        @php
                                            $isFirst  = $permIdx === 0;
                                            $granted  = isset($grantedMap[$system->id][$perm->key]);
                                            $colIndex++;
                                            $hexColor = $system->color ?? '#6366f1';
                                        @endphp
                                        <td class="perm-cell {{ $isFirst ? 'sys-first' : '' }} border-b border-slate-50"
                                            data-col="{{ $colIndex }}"
                                            style="{{ $granted ? "background: {$hexColor}12;" : '' }}"
                                            title="{{ $granted ? '✓ ' : '' }}{{ $user->name }} — {{ $system->name }} › {{ $perm->label }}">
                                            @if ($granted)
                                                <div class="inline-flex items-center justify-center w-5 h-5 rounded-full mx-auto"
                                                     style="background: {{ $hexColor }}22;">
                                                    <svg class="w-3 h-3" fill="none" stroke="{{ $hexColor }}" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                                    </svg>
                                                </div>
                                            @else
                                                <div class="w-3 h-px bg-slate-100 mx-auto"></div>
                                            @endif
                                        </td>
                                    @endforeach
                                @endforeach
                                <td class="perm-cell sys-first border-b border-slate-50 border-l-2 border-l-slate-200 bg-slate-50/50">
                                    @if ($totalGranted > 0)
                                        <span class="perm-count bg-indigo-100 text-indigo-700 mx-auto">{{ $totalGranted }}</span>
                                    @else
                                        <div class="w-3 h-px bg-slate-200 mx-auto"></div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="999" class="px-6 py-14 text-center text-slate-400 text-sm font-medium">
                                    ไม่พบข้อมูลผู้ใช้
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-5 py-3 border-t border-slate-100 flex items-center justify-between gap-4 flex-wrap">
                <span class="text-xs text-slate-500 font-medium">
                    แสดง {{ number_format($users->count()) }} ผู้ใช้ทั้งหมด
                </span>
                <span class="text-xs text-slate-400">
                    ข้อมูล ณ {{ now()->format('d/m/Y H:i') }}
                </span>
            </div>
        </div>
    </main>

    <script>
    (function () {
        var table = document.getElementById('matrix-table');
        if (! table) { return; }

        var activeCol = null;

        table.addEventListener('mouseover', function (e) {
            var cell = e.target.closest('[data-col]');
            if (! cell) { return; }
            var col = cell.dataset.col;
            if (col === activeCol) { return; }
            activeCol = col;
            table.querySelectorAll('.perm-cell.col-hover').forEach(function (el) { el.classList.remove('col-hover'); });
            table.querySelectorAll('[data-col="' + col + '"]').forEach(function (el) {
                if (el.tagName === 'TD') { el.classList.add('col-hover'); }
            });
        });

        table.addEventListener('mouseleave', function () {
            activeCol = null;
            table.querySelectorAll('.perm-cell.col-hover').forEach(function (el) { el.classList.remove('col-hover'); });
        });
    })();
    </script>
</body>
</html>
