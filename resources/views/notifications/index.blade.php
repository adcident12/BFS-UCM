@extends('layouts.app')

@section('title', 'Notification Channels')

@section('breadcrumbs')
<a href="{{ route('dashboard') }}" class="text-slate-400 hover:text-slate-600 font-medium transition-colors whitespace-nowrap">Dashboard</a>
<svg class="w-3.5 h-3.5 text-slate-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
<span class="font-semibold text-slate-800 truncate">Notification Channels</span>
@endsection

@section('content')

{{-- Hero Banner --}}
<div class="mb-7 relative overflow-hidden rounded-2xl"
     style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 45%, #0c1a2e 100%)">
    <div class="absolute inset-0 pointer-events-none overflow-hidden">
        <div class="absolute -top-12 -right-12 w-72 h-72 bg-emerald-500/6 rounded-full blur-2xl"></div>
        <div class="absolute -bottom-16 left-24 w-56 h-56 bg-cyan-400/5 rounded-full blur-2xl"></div>
        <div class="absolute inset-0"
             style="background-image:linear-gradient(rgba(255,255,255,0.025) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,0.025) 1px,transparent 1px);background-size:28px 28px"></div>
    </div>
    <div class="relative px-6 md:px-8 py-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-white/8 backdrop-blur-sm rounded-2xl flex items-center justify-center flex-shrink-0 border border-white/10">
                <svg class="w-6 h-6 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                          d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
            </div>
            <div>
                <h2 class="text-lg font-bold text-white">Notification Channels</h2>
                <p class="text-slate-400 text-xs font-medium mt-0.5">ตั้งค่าการแจ้งเตือนผ่าน Email และ Webhook</p>
            </div>
        </div>
        <button onclick="openModal('modal-add')"
                class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-500/20 hover:bg-emerald-500/30 border border-emerald-400/30 rounded-xl text-emerald-300 hover:text-emerald-200 text-xs font-semibold transition-all">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            เพิ่ม Channel
        </button>
    </div>
</div>

@if (session('success'))
    <div class="mb-5 flex items-center gap-3 px-4 py-3 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-700 text-sm font-medium flash-msg">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        {{ session('success') }}
    </div>
@endif

@if ($errors->any())
    @php
        $editChannelId = old('_edit_channel_id');
        $editingChannel = $editChannelId ? $channels->firstWhere('id', (int) $editChannelId) : null;
    @endphp
    @if ($editingChannel)
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                openEditModal(@json($editingChannel), @json($availableEvents));
            });
        </script>
    @else
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                openModal('modal-add');
            });
        </script>
    @endif
@endif

{{-- Channel List --}}
@php
$availableEvents = [
    // Users
    'permissions_updated'  => 'สิทธิ์เปลี่ยนแปลง',
    'account_status_changed' => 'เปลี่ยน Status ผู้ใช้ในระบบ',
    'user_imported'        => 'Import ผู้ใช้',
    'user_bulk_imported'   => 'Import ผู้ใช้ Bulk',
    'user_removed'         => 'ลบผู้ใช้',
    'admin_level_updated'  => 'เปลี่ยน Admin Level',
    // Systems
    'system_created'       => 'เพิ่ม System',
    'system_updated'       => 'แก้ไข System',
    'system_deleted'       => 'ลบ System',
    'system_2way_toggled'  => 'Toggle 2-Way Sync',
    // Connectors
    'connector_created'    => 'สร้าง Connector',
    'connector_updated'    => 'แก้ไข Connector',
    'connector_deleted'    => 'ลบ Connector',
    // Security / API
    'login_failed'         => 'Login ล้มเหลว',
    'api_token_issued'     => 'ออก API Token',
];
@endphp

<div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
    @forelse ($channels as $channel)
        <div class="flex items-center gap-4 px-6 py-4 {{ !$loop->last ? 'border-b border-slate-50' : '' }} group">
            {{-- Icon --}}
            <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0
                        {{ $channel->type === 'webhook' ? 'bg-indigo-50' : 'bg-sky-50' }}">
                @if ($channel->type === 'webhook')
                    <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                    </svg>
                @else
                    <svg class="w-5 h-5 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                @endif
            </div>

            {{-- Info --}}
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="font-semibold text-slate-800 text-sm">{{ $channel->name }}</span>
                    <span class="text-[11px] font-bold px-2 py-0.5 rounded-full
                                 {{ $channel->type === 'webhook' ? 'bg-indigo-100 text-indigo-700' : 'bg-sky-100 text-sky-700' }}">
                        {{ strtoupper($channel->type) }}
                    </span>
                    <span class="inline-flex items-center gap-1 text-[11px] font-semibold rounded-full px-2 py-0.5
                                 {{ $channel->is_active ? 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200' : 'bg-slate-100 text-slate-500' }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $channel->is_active ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                        {{ $channel->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
                <div class="text-xs text-slate-400 mt-1 truncate">
                    @if ($channel->type === 'webhook')
                        {{ $channel->config['url'] ?? '-' }}
                    @else
                        {{ implode(', ', $channel->config['to'] ?? []) }}
                    @endif
                </div>
                <div class="flex flex-wrap gap-1 mt-1.5">
                    @foreach ($channel->events as $ev)
                        <span class="text-[10px] font-medium bg-slate-100 text-slate-600 px-1.5 py-0.5 rounded">
                            {{ $availableEvents[$ev] ?? $ev }}
                        </span>
                    @endforeach
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-2 flex-shrink-0">
                <button onclick='openEditModal(@json($channel), @json($availableEvents))'
                        class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </button>
                <form id="del-channel-{{ $channel->id }}"
                      method="POST" action="{{ route('notifications.destroy', $channel) }}">
                    @csrf @method('DELETE')
                    <button type="button"
                            onclick="askConfirm('del-channel-{{ $channel->id }}', 'ลบ Channel นี้?', '{{ addslashes($channel->name) }}')"
                            class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    @empty
        <div class="px-6 py-16 text-center">
            <div class="w-14 h-14 bg-slate-50 rounded-2xl flex items-center justify-center mx-auto mb-3">
                <svg class="w-7 h-7 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
            </div>
            <p class="text-sm font-semibold text-slate-400">ยังไม่มี Notification Channel</p>
            <p class="text-xs text-slate-300 mt-1">กด "เพิ่ม Channel" เพื่อเริ่มต้น</p>
        </div>
    @endforelse
</div>

{{-- Add Modal --}}
<div id="modal-add" style="display:none"
     class="fixed inset-0 z-50 flex items-center justify-center"
     onclick="if(event.target===this)closeModal('modal-add')">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4 p-6 ring-1 ring-slate-100">
        <h3 class="font-bold text-slate-800 text-base mb-5">เพิ่ม Notification Channel</h3>
        @include('notifications._form', ['action' => route('notifications.store'), 'method' => 'POST', 'channel' => null, 'availableEvents' => $availableEvents])
    </div>
</div>

{{-- Edit Modal --}}
<div id="modal-edit" style="display:none"
     class="fixed inset-0 z-50 flex items-center justify-center"
     onclick="if(event.target===this)closeModal('modal-edit')">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4 p-6 ring-1 ring-slate-100">
        <h3 class="font-bold text-slate-800 text-base mb-5">แก้ไข Notification Channel</h3>
        <div id="modal-edit-body"></div>
    </div>
</div>

@push('scripts')
<script>
const availableEvents = @json($availableEvents);
const updateRouteTemplate = "{{ route('notifications.update', ['notificationChannel' => '__ID__']) }}";

function openModal(id) {
    document.getElementById(id).style.display = 'flex';
}

function closeModal(id) {
    document.getElementById(id).style.display = 'none';
}

function openEditModal(channel, events) {
    const typeWebhook = channel.type === 'webhook';
    const selectedEvents = channel.events || [];
    const isActive = channel.is_active;

    let eventsHtml = Object.entries(events).map(([key, label]) => `
        <label class="flex items-center gap-2 text-sm text-slate-700 cursor-pointer">
            <input type="checkbox" name="events[]" value="${key}"
                   ${selectedEvents.includes(key) ? 'checked' : ''}
                   class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-300">
            ${label}
        </label>
    `).join('');

    const configUrl = channel.config?.url || '';
    const configSecret = channel.config?.secret || '';
    const configTo = (channel.config?.to || []).join(', ');

    document.getElementById('modal-edit-body').innerHTML = `
        <form method="POST" action="${updateRouteTemplate.replace('__ID__', channel.id)}" class="space-y-4">
            <input type="hidden" name="_method" value="PUT">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="_edit_channel_id" value="${channel.id}">

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1.5">ชื่อ Channel</label>
                <input type="text" name="name" value="${channel.name}" required
                       class="w-full px-3 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-300">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1.5">ประเภท</label>
                <select name="type" id="edit-type" onchange="toggleEditConfig(this.value)"
                        class="w-full px-3 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-300 bg-white">
                    <option value="webhook" ${typeWebhook ? 'selected' : ''}>Webhook</option>
                    <option value="email" ${!typeWebhook ? 'selected' : ''}>Email</option>
                </select>
            </div>

            <div id="edit-webhook-config" ${typeWebhook ? '' : 'style="display:none"'} class="space-y-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Webhook URL</label>
                    <input type="url" name="config[url]" value="${configUrl}"
                           class="w-full px-3 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-300">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Secret (ถ้ามี)</label>
                    <input type="text" name="config[secret]" value="${configSecret}"
                           class="w-full px-3 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-300">
                </div>
            </div>

            <div id="edit-email-config" ${!typeWebhook ? '' : 'style="display:none"'}>
                <label class="block text-xs font-semibold text-slate-600 mb-1.5">อีเมลผู้รับ (คั่นด้วย ,)</label>
                <input type="text" name="config[to]" value="${configTo}"
                       class="w-full px-3 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-300">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-2">Events ที่แจ้งเตือน</label>
                <div class="grid grid-cols-2 gap-2">${eventsHtml}</div>
            </div>

            <div class="flex items-center gap-2">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" id="edit-is-active" ${isActive ? 'checked' : ''}
                       class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-300">
                <label for="edit-is-active" class="text-sm font-medium text-slate-700">เปิดใช้งาน</label>
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="closeModal('modal-edit')"
                        class="px-4 py-2 text-sm font-semibold text-slate-600 border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors">
                    ยกเลิก
                </button>
                <button type="submit"
                        class="px-5 py-2 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl transition-colors">
                    บันทึก
                </button>
            </div>
        </form>
    `;

    openModal('modal-edit');
}

function toggleConfig(type) {
    document.getElementById('webhook-config').style.display = type === 'webhook' ? 'block' : 'none';
    document.getElementById('email-config').style.display   = type === 'email'   ? 'block' : 'none';
}

function toggleEditConfig(type) {
    document.getElementById('edit-webhook-config').style.display = type === 'webhook' ? 'block' : 'none';
    document.getElementById('edit-email-config').style.display   = type === 'email'   ? 'block' : 'none';
}
</script>
@endpush

@endsection
