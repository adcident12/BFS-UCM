<form method="POST" action="{{ $action }}" class="space-y-4">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <div>
        <label class="block text-xs font-semibold text-slate-600 mb-1.5">ชื่อ Channel</label>
        <input type="text" name="name" value="{{ old('name', $channel?->name) }}" required
               class="w-full px-3 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-300">
    </div>

    <div>
        <label class="block text-xs font-semibold text-slate-600 mb-1.5">ประเภท</label>
        <select name="type" id="add-type" onchange="toggleConfig(this.value)"
                class="w-full px-3 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-300 bg-white">
            <option value="webhook">Webhook</option>
            <option value="email">Email</option>
        </select>
    </div>

    <div id="webhook-config" class="space-y-3">
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Webhook URL</label>
            <input type="url" name="config[url]" placeholder="https://..."
                   class="w-full px-3 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-300">
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Secret (ถ้ามี)</label>
            <input type="text" name="config[secret]" placeholder="ไม่บังคับ"
                   class="w-full px-3 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-300">
        </div>
    </div>

    <div id="email-config" style="display:none">
        <label class="block text-xs font-semibold text-slate-600 mb-1.5">อีเมลผู้รับ (คั่นด้วย ,)</label>
        <input type="text" name="config[to]" placeholder="admin@example.com, it@example.com"
               class="w-full px-3 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-300">
    </div>

    <div>
        <label class="block text-xs font-semibold text-slate-600 mb-2">Events ที่แจ้งเตือน</label>
        <div class="grid grid-cols-2 gap-2">
            @foreach ($availableEvents as $key => $label)
                <label class="flex items-center gap-2 text-sm text-slate-700 cursor-pointer">
                    <input type="checkbox" name="events[]" value="{{ $key }}"
                           {{ in_array($key, old('events', [])) ? 'checked' : '' }}
                           class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-300">
                    {{ $label }}
                </label>
            @endforeach
        </div>
    </div>

    <div class="flex items-center gap-2">
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox" name="is_active" value="1" id="add-is-active" checked
               class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-300">
        <label for="add-is-active" class="text-sm font-medium text-slate-700">เปิดใช้งาน</label>
    </div>

    <div class="flex justify-end gap-2 pt-2">
        <button type="button" onclick="closeModal('modal-add')"
                class="px-4 py-2 text-sm font-semibold text-slate-600 border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors">
            ยกเลิก
        </button>
        <button type="submit"
                class="px-5 py-2 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl transition-colors">
            บันทึก
        </button>
    </div>
</form>
