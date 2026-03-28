<?php

namespace App\Http\Controllers;

use App\Adapters\AdapterFactory;
use App\Models\AuditLog;
use App\Models\System;
use App\Models\SystemPermission;
use App\Models\UcmUser;
use App\Services\AuditLogger;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SystemController extends Controller
{
    private function authUser(): ?UcmUser
    {
        /** @var UcmUser|null */
        return Auth::user();
    }

    public function healthCheck(System $system): JsonResponse
    {
        abort_unless($this->authUser()?->canAccess('system_list'), 403);

        if (! AdapterFactory::hasAdapter($system)) {
            return response()->json(['ok' => false, 'message' => 'ระบบนี้ไม่มี Adapter']);
        }

        try {
            $result = AdapterFactory::make($system)->testConnection();
        } catch (\Throwable $e) {
            $result = ['ok' => false, 'message' => $e->getMessage()];
        }

        return response()->json($result);
    }

    public function index()
    {
        abort_unless($this->authUser()?->canAccess('system_list'), 403);

        $systems = System::withCount(['permissions', 'userPermissions'])
            ->orderBy('name')
            ->get();

        return view('systems.index', compact('systems'));
    }

    public function create()
    {
        abort_unless($this->authUser()?->canAccess('system_create_edit'), 403, 'เฉพาะ Admin ระดับ 2 เท่านั้นที่สามารถเพิ่มระบบได้');

        return view('systems.create');
    }

    public function store(Request $request)
    {
        abort_unless($this->authUser()?->canAccess('system_create_edit'), 403, 'เฉพาะ Admin ระดับ 2 เท่านั้นที่สามารถเพิ่มระบบได้');

        $data = $request->validate([
            'name' => 'required|string|max:100',
            'slug' => 'required|string|max:50|unique:systems,slug|alpha_dash',
            'description' => 'nullable|string|max:500',
            'adapter_class' => 'nullable|string|max:200',
            'db_host' => 'nullable|string|max:255',
            'db_port' => 'nullable|integer|min:1|max:65535',
            'db_name' => 'nullable|string|max:100',
            'db_user' => 'nullable|string|max:100',
            'db_password' => 'nullable|string|max:255',
            'api_url' => 'nullable|url|max:500',
            'api_token' => 'nullable|string|max:500',
            'color' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'icon' => 'nullable|string|max:50',
            'is_active' => 'boolean',
        ]);

        $system = System::create($data);

        AuditLogger::log(
            AuditLog::CATEGORY_SYSTEMS,
            AuditLog::EVENT_SYSTEM_CREATED,
            "สร้างระบบใหม่: {$system->name} (slug: {$system->slug})",
            ['system_id' => $system->id, 'name' => $system->name, 'slug' => $system->slug],
            $this->authUser(),
            'system', $system->id, $system->name,
        );

        app(NotificationService::class)->dispatch('system_created', [
            'system' => $system->name,
            'slug' => $system->slug,
            'performed_by' => $this->authUser()?->username,
            'description' => "สร้างระบบใหม่: {$system->name}",
        ]);

        return redirect()->route('systems.show', $system)
            ->with('success', "เพิ่มระบบ {$system->name} เรียบร้อย");
    }

    public function show(System $system)
    {
        abort_unless($this->authUser()?->canAccess('system_list'), 403);

        $system->load(['permissions' => fn ($q) => $q->orderBy('group')->orderBy('sort_order')]);

        $managedGroups    = [];
        $groupSchemas     = [];
        $groupDeleteModes = [];

        if (AdapterFactory::hasAdapter($system)) {
            $adapter       = AdapterFactory::make($system);
            $managedGroups = $adapter->getManagedGroups();

            foreach ($managedGroups as $g) {
                $schema = $adapter->getGroupSchema($g);
                if (! empty($schema)) {
                    $groupSchemas[$g] = $schema;
                }

                $groupDeleteModes[$g] = $adapter->getGroupDeleteMode($g);
            }
        }

        return view('systems.show', compact('system', 'managedGroups', 'groupSchemas', 'groupDeleteModes'));
    }

    public function edit(System $system)
    {
        abort_unless($this->authUser()?->canAccess('system_create_edit'), 403, 'เฉพาะ Admin ระดับ 2 เท่านั้นที่สามารถแก้ไขระบบได้');

        return view('systems.edit', compact('system'));
    }

    public function update(Request $request, System $system)
    {
        abort_unless($this->authUser()?->canAccess('system_create_edit'), 403, 'เฉพาะ Admin ระดับ 2 เท่านั้นที่สามารถแก้ไขระบบได้');

        $data = $request->validate([
            'name' => 'required|string|max:100',
            'slug' => "required|string|max:50|alpha_dash|unique:systems,slug,{$system->id}",
            'description' => 'nullable|string|max:500',
            'adapter_class' => 'nullable|string|max:200',
            'db_host' => 'nullable|string|max:255',
            'db_port' => 'nullable|integer|min:1|max:65535',
            'db_name' => 'nullable|string|max:100',
            'db_user' => 'nullable|string|max:100',
            'db_password' => 'nullable|string|max:255',
            'api_url' => 'nullable|url|max:500',
            'api_token' => 'nullable|string|max:500',
            'color' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'icon' => 'nullable|string|max:50',
            'is_active' => 'boolean',
        ]);

        // ไม่ update password/token ถ้าไม่ได้กรอกใหม่
        if (empty($data['db_password'])) {
            unset($data['db_password']);
        }
        if (empty($data['api_token'])) {
            unset($data['api_token']);
        }

        $system->update($data);

        AuditLogger::log(
            AuditLog::CATEGORY_SYSTEMS,
            AuditLog::EVENT_SYSTEM_UPDATED,
            "อัปเดตระบบ: {$system->name}",
            ['system_id' => $system->id, 'name' => $system->name],
            $this->authUser(),
            'system', $system->id, $system->name,
        );

        app(NotificationService::class)->dispatch('system_updated', [
            'system' => $system->name,
            'performed_by' => $this->authUser()?->username,
            'description' => "อัปเดตระบบ: {$system->name}",
        ]);

        return redirect()->route('systems.show', $system)
            ->with('success', "อัปเดตระบบ {$system->name} เรียบร้อย");
    }

    public function destroy(System $system)
    {
        abort_unless($this->authUser()?->canAccess('system_create_edit'), 403, 'เฉพาะ Admin ระดับ 2 เท่านั้นที่สามารถลบระบบได้');

        $systemName = $system->name;
        $systemId = $system->id;
        $system->delete();

        AuditLogger::log(
            AuditLog::CATEGORY_SYSTEMS,
            AuditLog::EVENT_SYSTEM_DELETED,
            "ลบระบบ: {$systemName}",
            ['system_id' => $systemId, 'name' => $systemName],
            $this->authUser(),
            'system', $systemId, $systemName,
        );

        app(NotificationService::class)->dispatch('system_deleted', [
            'system' => $systemName,
            'performed_by' => $this->authUser()?->username,
            'description' => "ลบระบบ: {$systemName}",
        ]);

        return redirect()->route('systems.index')
            ->with('success', "ลบระบบ {$systemName} เรียบร้อย");
    }

    public function toggle2WayPermissions(System $system)
    {
        abort_unless($this->authUser()?->canAccess('system_create_edit'), 403, 'เฉพาะ Admin ระดับ 2 เท่านั้นที่สามารถเปิด/ปิด 2-way permission sync ได้');
        abort_unless(AdapterFactory::adapterSupports2Way($system), 422, 'ระบบนี้ไม่รองรับ 2-way permission sync');

        $system->update(['two_way_permissions' => ! $system->two_way_permissions]);

        $state = $system->two_way_permissions ? 'เปิด' : 'ปิด';

        AuditLogger::log(
            AuditLog::CATEGORY_SYSTEMS,
            AuditLog::EVENT_SYSTEM_2WAY_TOGGLED,
            "{$state} 2-way permission sync สำหรับระบบ {$system->name}",
            ['system_id' => $system->id, 'two_way_permissions' => $system->two_way_permissions],
            $this->authUser(),
            'system', $system->id, $system->name,
        );

        app(NotificationService::class)->dispatch('system_2way_toggled', [
            'system_id' => $system->id,
            'system_name' => $system->name,
            'two_way_permissions' => $system->two_way_permissions,
            'description' => "{$state} 2-way permission sync สำหรับระบบ {$system->name}",
        ]);

        return back()->with('success', "{$state} 2-way permission sync สำหรับ {$system->name} เรียบร้อย");
    }

    public function storePermission(Request $request, System $system)
    {
        abort_unless($this->authUser()?->canAccess('system_create_edit'), 403, 'เฉพาะ Admin ระดับ 2 เท่านั้นที่สามารถเพิ่ม Permission ได้');

        $data = $request->validate([
            'key' => "required|string|max:100|unique:system_permissions,key,NULL,id,system_id,{$system->id}",
            'label' => 'required|string|max:100',
            'remote_value' => 'nullable|string|max:255',
            'group' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:500',
            'sort_order' => 'nullable|integer|min:0',
            'is_exclusive' => 'boolean',
        ]);

        $data['is_exclusive'] = $request->boolean('is_exclusive');

        // Provision permission definition ในระบบภายนอก เฉพาะเมื่อ 2-way เปิดอยู่
        if (AdapterFactory::supports2WayPermissions($system)) {
            $provisioned = AdapterFactory::make($system)
                ->provisionPermission($data['key'], $data['label'], $data['group'] ?? '');

            if ($provisioned !== null && blank($data['remote_value'] ?? null)) {
                $data['remote_value'] = (string) $provisioned;
            }
        }

        $perm = $system->permissions()->create($data);

        AuditLogger::log(
            AuditLog::CATEGORY_SYSTEMS,
            AuditLog::EVENT_PERM_DEF_CREATED,
            "เพิ่ม permission key '{$perm->key}' ({$perm->label}) ในระบบ {$system->name}",
            ['system_id' => $system->id, 'system_name' => $system->name, 'key' => $perm->key, 'label' => $perm->label, 'group' => $perm->group],
            $this->authUser(),
            'system', $system->id, $system->name,
        );

        app(NotificationService::class)->dispatch('perm_def_created', [
            'system' => $system->name,
            'key' => $perm->key,
            'label' => $perm->label,
            'performed_by' => $this->authUser()?->username,
            'description' => "เพิ่ม permission key '{$perm->key}' ({$perm->label}) ในระบบ {$system->name}",
        ]);

        return back()->with('success', "เพิ่ม permission '{$data['label']}' เรียบร้อย");
    }

    public function updatePermission(Request $request, System $system, SystemPermission $permission)
    {
        abort_unless($this->authUser()?->canAccess('permission_update'), 403, 'เฉพาะ Admin ระดับ 1 ขึ้นไปเท่านั้นที่สามารถแก้ไข Permission ได้');
        abort_if($permission->system_id !== $system->id, 404);

        $data = $request->validate([
            'label' => 'required|string|max:100',
            'remote_value' => 'nullable|string|max:255',
            'group' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:500',
            'sort_order' => 'nullable|integer|min:0',
            'is_exclusive' => 'boolean',
        ]);

        $data['is_exclusive'] = $request->boolean('is_exclusive');

        $permission->update($data);

        // อัปเดต permission definition ในระบบภายนอก เฉพาะเมื่อ 2-way เปิดอยู่
        if (filled($permission->remote_value) && AdapterFactory::supports2WayPermissions($system)) {
            AdapterFactory::make($system)->updatePermission(
                $permission->remote_value,
                $permission->label,
                $permission->group ?? ''
            );
        }

        AuditLogger::log(
            AuditLog::CATEGORY_SYSTEMS,
            AuditLog::EVENT_PERM_DEF_UPDATED,
            "อัปเดต permission key '{$permission->key}' ในระบบ {$system->name}",
            ['system_id' => $system->id, 'system_name' => $system->name, 'key' => $permission->key, 'label' => $permission->label],
            $this->authUser(),
            'system', $system->id, $system->name,
        );

        app(NotificationService::class)->dispatch('perm_def_updated', [
            'system' => $system->name,
            'key' => $permission->key,
            'label' => $permission->label,
            'performed_by' => $this->authUser()?->username,
            'description' => "อัปเดต permission key '{$permission->key}' ({$permission->label}) ในระบบ {$system->name}",
        ]);

        return back()->with('success', "อัปเดต permission '{$permission->key}' เรียบร้อย");
    }

    public function usersForImport(System $system)
    {
        abort_unless($this->authUser()?->canAccess('user_import_ldap'), 403);

        if (! AdapterFactory::hasAdapter($system)) {
            return response()->json(['error' => 'ระบบนี้ไม่มี adapter'], 400);
        }

        $users = AdapterFactory::make($system)->getSystemUsers();

        return response()->json($users);
    }

    public function discoverPermissions(System $system)
    {
        abort_unless($this->authUser()?->canAccess('permission_update'), 403, 'เฉพาะ Admin ระดับ 1 ขึ้นไปเท่านั้นที่สามารถ Discover Permissions ได้');

        if (! AdapterFactory::hasAdapter($system)) {
            return back()->withErrors(['ระบบ '.$system->name.' ไม่มี adapter รองรับ discoverPermissions']);
        }

        $adapter = AdapterFactory::make($system);
        $created = $adapter->discoverPermissions();

        if (empty($created)) {
            return back()->with('success', 'ไม่พบ permission ใหม่จาก '.$system->name.' (ครบถ้วนแล้ว)');
        }

        AuditLogger::log(
            AuditLog::CATEGORY_SYSTEMS,
            AuditLog::EVENT_PERM_DEF_DISCOVERED,
            'Discover permission definitions จากระบบ '.$system->name.': '.count($created).' รายการ',
            ['system_id' => $system->id, 'system_name' => $system->name, 'created' => $created],
            $this->authUser(),
            'system', $system->id, $system->name,
        );

        app(NotificationService::class)->dispatch('perm_def_discovered', [
            'system' => $system->name,
            'count' => count($created),
            'keys' => $created,
            'performed_by' => $this->authUser()?->username,
            'description' => 'Discover permission definitions จากระบบ '.$system->name.': '.count($created).' รายการ',
        ]);

        return back()->with('success', 'พบ '.count($created).' permission ใหม่จาก '.$system->name.': '.implode(', ', $created));
    }

    public function destroyPermission(System $system, SystemPermission $permission)
    {
        abort_unless($this->authUser()?->canAccess('system_create_edit'), 403, 'เฉพาะ Admin ระดับ 1 ขึ้นไปเท่านั้นที่สามารถลบ Permission ได้');
        abort_if($permission->system_id !== $system->id, 404);

        // ลบ permission definition จากระบบภายนอก เฉพาะเมื่อ 2-way เปิดอยู่
        if (filled($permission->remote_value) && AdapterFactory::supports2WayPermissions($system)) {
            AdapterFactory::make($system)->deletePermission($permission->remote_value);
        }

        $permKey = $permission->key;
        $permLabel = $permission->label;
        $permission->delete();

        AuditLogger::log(
            AuditLog::CATEGORY_SYSTEMS,
            AuditLog::EVENT_PERM_DEF_DELETED,
            "ลบ permission key '{$permKey}' ({$permLabel}) ออกจากระบบ {$system->name}",
            ['system_id' => $system->id, 'system_name' => $system->name, 'key' => $permKey, 'label' => $permLabel],
            $this->authUser(),
            'system', $system->id, $system->name,
        );

        app(NotificationService::class)->dispatch('perm_def_deleted', [
            'system' => $system->name,
            'key' => $permKey,
            'label' => $permLabel,
            'performed_by' => $this->authUser()?->username,
            'description' => "ลบ permission key '{$permKey}' ({$permLabel}) ออกจากระบบ {$system->name}",
        ]);

        return back()->with('success', 'ลบ permission เรียบร้อย');
    }

    // ── Managed Group CRUD ────────────────────────────────────────────────

    public function groupRecords(System $system, string $group)
    {
        abort_unless($this->authUser()?->canAccess('system_list'), 403);

        if (! AdapterFactory::hasAdapter($system)) {
            return response()->json(['error' => 'ระบบนี้ไม่มี adapter'], 400);
        }

        $adapter = AdapterFactory::make($system);

        abort_unless(in_array($group, $adapter->getManagedGroups(), true), 404);

        return response()->json($adapter->getGroupRecords($group));
    }

    public function storeGroupRecord(Request $request, System $system)
    {
        abort_unless($this->authUser()?->canAccess('system_create_edit'), 403, 'เฉพาะ Admin เท่านั้นที่สามารถเพิ่มข้อมูล Reference ได้');
        abort_unless(AdapterFactory::hasAdapter($system), 400);

        $adapter = AdapterFactory::make($system);

        $groupName = $request->validate(['group' => 'required|string|max:100'])['group'];
        abort_unless(in_array($groupName, $adapter->getManagedGroups(), true), 404);

        $schema = $adapter->getGroupSchema($groupName);

        $rules = ['name' => 'required|string|max:255'];
        foreach ($schema as $col => $def) {
            $colRules = $def['required'] ? 'required' : 'nullable';
            $colRules .= match ($def['type'] ?? 'text') {
                'number' => '|numeric',
                default  => '|string|max:255',
            };
            $rules[$col] = $colRules;
        }

        $data  = $request->validate($rules);
        $extra = array_intersect_key($data, $schema);

        $result = $adapter->addGroupRecord($groupName, $data['name'], $extra);

        if ($result === false) {
            return back()->withErrors(['เพิ่ม '.$groupName.' ล้มเหลว กรุณาตรวจสอบการเชื่อมต่อ']);
        }

        AuditLogger::log(
            AuditLog::CATEGORY_SYSTEMS,
            AuditLog::EVENT_GROUP_RECORD_CREATED,
            "เพิ่ม {$groupName} '{$data['name']}' ในระบบ {$system->name}",
            ['system_id' => $system->id, 'group' => $groupName, 'name' => $data['name']],
            $this->authUser(),
            'system', $system->id, $system->name,
        );

        app(NotificationService::class)->dispatch('group_record_created', [
            'system' => $system->name,
            'group' => $groupName,
            'name' => $data['name'],
            'performed_by' => $this->authUser()?->username,
            'description' => "เพิ่ม {$groupName} '{$data['name']}' ในระบบ {$system->name}",
        ]);

        return back()->with('success', "เพิ่ม {$groupName} '{$data['name']}' เรียบร้อย");
    }

    public function updateGroupRecord(Request $request, System $system, string $group, int $recordId)
    {
        abort_unless($this->authUser()?->canAccess('system_create_edit'), 403, 'เฉพาะ Admin ระดับ 2 เท่านั้นที่สามารถแก้ไขข้อมูล Reference ได้');
        abort_unless(AdapterFactory::hasAdapter($system), 400);

        $adapter = AdapterFactory::make($system);
        abort_unless(in_array($group, $adapter->getManagedGroups(), true), 404);

        $schema = $adapter->getGroupSchema($group);

        $rules = ['name' => 'required|string|max:255'];
        foreach ($schema as $col => $def) {
            $colRules = $def['required'] ? 'required' : 'nullable';
            $colRules .= match ($def['type'] ?? 'text') {
                'number' => '|numeric',
                default  => '|string|max:255',
            };
            $rules[$col] = $colRules;
        }

        $data  = $request->validate($rules);
        $extra = array_intersect_key($data, $schema);

        $ok = $adapter->updateGroupRecord($group, $recordId, $data['name'], $extra);

        if (! $ok) {
            return back()->withErrors(['อัปเดต '.$group.' ล้มเหลว']);
        }

        AuditLogger::log(
            AuditLog::CATEGORY_SYSTEMS,
            AuditLog::EVENT_GROUP_RECORD_UPDATED,
            "อัปเดต {$group} #{$recordId} '{$data['name']}' ในระบบ {$system->name}",
            ['system_id' => $system->id, 'group' => $group, 'record_id' => $recordId, 'name' => $data['name']],
            $this->authUser(),
            'system', $system->id, $system->name,
        );

        app(NotificationService::class)->dispatch('group_record_updated', [
            'system' => $system->name,
            'group' => $group,
            'record_id' => $recordId,
            'name' => $data['name'],
            'performed_by' => $this->authUser()?->username,
            'description' => "อัปเดต {$group} #{$recordId} '{$data['name']}' ในระบบ {$system->name}",
        ]);

        return back()->with('success', "อัปเดต {$group} เรียบร้อย");
    }

    public function discoverGroupRecords(System $system, string $group)
    {
        abort_unless($this->authUser()?->canAccess('system_create_edit'), 403);
        abort_unless(AdapterFactory::hasAdapter($system), 400);

        $adapter = AdapterFactory::make($system);
        abort_unless(in_array($group, $adapter->getManagedGroups(), true), 404);

        $records = $adapter->getGroupRecords($group);
        $count   = count($records);

        AuditLogger::log(
            AuditLog::CATEGORY_SYSTEMS,
            AuditLog::EVENT_GROUP_RECORDS_DISCOVERED,
            "Discover {$group} ในระบบ {$system->name}: {$count} รายการ",
            ['system_id' => $system->id, 'group' => $group, 'count' => $count],
            $this->authUser(),
            'system', $system->id, $system->name,
        );

        app(NotificationService::class)->dispatch('group_records_discovered', [
            'system' => $system->name,
            'group' => $group,
            'count' => $count,
            'performed_by' => $this->authUser()?->username,
            'description' => "Discover {$group} ในระบบ {$system->name}: {$count} รายการ",
        ]);

        return back()->with('success', "Discover {$group}: พบ {$count} รายการ");
    }

    public function destroyGroupRecord(System $system, string $group, int $recordId)
    {
        abort_unless($this->authUser()?->canAccess('system_create_edit'), 403, 'เฉพาะ Admin ระดับ 2 เท่านั้นที่สามารถลบข้อมูล Reference ได้');
        abort_unless(AdapterFactory::hasAdapter($system), 400);

        $adapter = AdapterFactory::make($system);
        abort_unless(in_array($group, $adapter->getManagedGroups(), true), 404);

        $ok = $adapter->deleteGroupRecord($group, $recordId);

        if (! $ok) {
            return back()->withErrors(['ลบ '.$group.' ล้มเหลว']);
        }

        AuditLogger::log(
            AuditLog::CATEGORY_SYSTEMS,
            AuditLog::EVENT_GROUP_RECORD_DELETED,
            "ลบ {$group} #{$recordId} ในระบบ {$system->name}",
            ['system_id' => $system->id, 'group' => $group, 'record_id' => $recordId],
            $this->authUser(),
            'system', $system->id, $system->name,
        );

        app(NotificationService::class)->dispatch('group_record_deleted', [
            'system' => $system->name,
            'group' => $group,
            'record_id' => $recordId,
            'performed_by' => $this->authUser()?->username,
            'description' => "ลบ {$group} #{$recordId} ในระบบ {$system->name}",
        ]);

        return back()->with('success', "ลบ {$group} เรียบร้อย");
    }
}
