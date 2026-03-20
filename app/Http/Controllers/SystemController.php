<?php

namespace App\Http\Controllers;

use App\Adapters\AdapterFactory;
use App\Models\System;
use App\Models\SystemPermission;
use Illuminate\Http\Request;

class SystemController extends Controller
{
    public function index()
    {
        $systems = System::withCount(['permissions', 'userPermissions'])
            ->orderBy('name')
            ->get();

        return view('systems.index', compact('systems'));
    }

    public function create()
    {
        return view('systems.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:100',
            'slug'          => 'required|string|max:50|unique:systems,slug|alpha_dash',
            'description'   => 'nullable|string|max:500',
            'adapter_class' => 'nullable|string|max:200',
            'db_host'       => 'nullable|string|max:255',
            'db_port'       => 'nullable|integer|min:1|max:65535',
            'db_name'       => 'nullable|string|max:100',
            'db_user'       => 'nullable|string|max:100',
            'db_password'   => 'nullable|string|max:255',
            'api_url'       => 'nullable|url|max:500',
            'api_token'     => 'nullable|string|max:500',
            'color'         => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'icon'          => 'nullable|string|max:50',
            'is_active'     => 'boolean',
        ]);

        $system = System::create($data);

        return redirect()->route('systems.show', $system)
            ->with('success', "เพิ่มระบบ {$system->name} เรียบร้อย");
    }

    public function show(System $system)
    {
        $system->load(['permissions' => fn ($q) => $q->orderBy('group')->orderBy('sort_order')]);

        $managedGroups = [];
        if (AdapterFactory::hasAdapter($system)) {
            $managedGroups = AdapterFactory::make($system)->getManagedGroups();
        }

        return view('systems.show', compact('system', 'managedGroups'));
    }

    public function edit(System $system)
    {
        return view('systems.edit', compact('system'));
    }

    public function update(Request $request, System $system)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:100',
            'slug'          => "required|string|max:50|alpha_dash|unique:systems,slug,{$system->id}",
            'description'   => 'nullable|string|max:500',
            'adapter_class' => 'nullable|string|max:200',
            'db_host'       => 'nullable|string|max:255',
            'db_port'       => 'nullable|integer|min:1|max:65535',
            'db_name'       => 'nullable|string|max:100',
            'db_user'       => 'nullable|string|max:100',
            'db_password'   => 'nullable|string|max:255',
            'api_url'       => 'nullable|url|max:500',
            'api_token'     => 'nullable|string|max:500',
            'color'         => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'icon'          => 'nullable|string|max:50',
            'is_active'     => 'boolean',
        ]);

        // ไม่ update password/token ถ้าไม่ได้กรอกใหม่
        if (empty($data['db_password'])) {
            unset($data['db_password']);
        }
        if (empty($data['api_token'])) {
            unset($data['api_token']);
        }

        $system->update($data);

        return redirect()->route('systems.show', $system)
            ->with('success', "อัปเดตระบบ {$system->name} เรียบร้อย");
    }

    public function destroy(System $system)
    {
        $system->delete();

        return redirect()->route('systems.index')
            ->with('success', "ลบระบบ {$system->name} เรียบร้อย");
    }

    public function storePermission(Request $request, System $system)
    {
        $data = $request->validate([
            'key'          => "required|string|max:100|unique:system_permissions,key,NULL,id,system_id,{$system->id}",
            'label'        => 'required|string|max:100',
            'remote_value' => 'nullable|string|max:255',
            'group'        => 'nullable|string|max:50',
            'description'  => 'nullable|string|max:500',
            'sort_order'   => 'nullable|integer|min:0',
            'is_exclusive' => 'boolean',
        ]);

        $data['is_exclusive'] = $request->boolean('is_exclusive');

        // แจ้ง adapter ให้ provision permission definition ในระบบภายนอกเสมอ
        // (side-effect เช่น สร้าง PageGroup ใน Earth) — ทำก่อน insert UCM เสมอ
        // ถ้า adapter คืนค่า remote_value กลับมา ให้ใช้เมื่อยังไม่ได้กรอกเท่านั้น
        if (AdapterFactory::hasAdapter($system)) {
            $provisioned = AdapterFactory::make($system)
                ->provisionPermission($data['key'], $data['label'], $data['group'] ?? '');

            if ($provisioned !== null && blank($data['remote_value'] ?? null)) {
                $data['remote_value'] = (string) $provisioned;
            }
        }

        $system->permissions()->create($data);

        return back()->with('success', "เพิ่ม permission '{$data['label']}' เรียบร้อย");
    }

    public function updatePermission(Request $request, System $system, SystemPermission $permission)
    {
        abort_if($permission->system_id !== $system->id, 404);

        $data = $request->validate([
            'label'        => 'required|string|max:100',
            'remote_value' => 'nullable|string|max:255',
            'group'        => 'nullable|string|max:50',
            'description'  => 'nullable|string|max:500',
            'sort_order'   => 'nullable|integer|min:0',
            'is_exclusive' => 'boolean',
        ]);

        $data['is_exclusive'] = $request->boolean('is_exclusive');

        $permission->update($data);

        return back()->with('success', "อัปเดต permission '{$permission->key}' เรียบร้อย");
    }

    public function usersForImport(System $system)
    {
        if (! AdapterFactory::hasAdapter($system)) {
            return response()->json(['error' => 'ระบบนี้ไม่มี adapter'], 400);
        }

        $users = AdapterFactory::make($system)->getSystemUsers();

        return response()->json($users);
    }

    public function discoverPermissions(System $system)
    {
        if (! AdapterFactory::hasAdapter($system)) {
            return back()->withErrors(['ระบบ ' . $system->name . ' ไม่มี adapter รองรับ discoverPermissions']);
        }

        $adapter = AdapterFactory::make($system);
        $created = $adapter->discoverPermissions();

        if (empty($created)) {
            return back()->with('success', 'ไม่พบ permission ใหม่จาก ' . $system->name . ' (ครบถ้วนแล้ว)');
        }

        return back()->with('success', 'พบ ' . count($created) . ' permission ใหม่จาก ' . $system->name . ': ' . implode(', ', $created));
    }

    public function destroyPermission(System $system, SystemPermission $permission)
    {
        abort_if($permission->system_id !== $system->id, 404);

        // แจ้งระบบภายนอกให้ลบ permission definition ด้วย (ถ้า adapter รองรับ)
        if (filled($permission->remote_value) && AdapterFactory::hasAdapter($system)) {
            AdapterFactory::make($system)->deletePermission($permission->remote_value);
        }

        $permission->delete();

        return back()->with('success', 'ลบ permission เรียบร้อย');
    }

    // ── Managed Group CRUD ────────────────────────────────────────────────

    public function groupRecords(System $system, string $group)
    {
        if (! AdapterFactory::hasAdapter($system)) {
            return response()->json(['error' => 'ระบบนี้ไม่มี adapter'], 400);
        }

        $adapter = AdapterFactory::make($system);

        abort_unless(in_array($group, $adapter->getManagedGroups(), true), 404);

        return response()->json($adapter->getGroupRecords($group));
    }

    public function storeGroupRecord(Request $request, System $system)
    {
        abort_unless(AdapterFactory::hasAdapter($system), 400);

        $data = $request->validate([
            'group' => 'required|string|max:100',
            'name'  => 'required|string|max:255',
        ]);

        $result = AdapterFactory::make($system)->addGroupRecord($data['group'], $data['name']);

        if ($result === false) {
            return back()->withErrors(['เพิ่ม ' . $data['group'] . ' ล้มเหลว กรุณาตรวจสอบการเชื่อมต่อ']);
        }

        return back()->with('success', "เพิ่ม {$data['group']} '{$data['name']}' เรียบร้อย");
    }

    public function updateGroupRecord(Request $request, System $system, string $group, int $recordId)
    {
        abort_unless(AdapterFactory::hasAdapter($system), 400);

        $adapter = AdapterFactory::make($system);
        abort_unless(in_array($group, $adapter->getManagedGroups(), true), 404);

        $data = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $ok = $adapter->updateGroupRecord($group, $recordId, $data['name']);

        if (! $ok) {
            return back()->withErrors(['อัปเดต ' . $group . ' ล้มเหลว']);
        }

        return back()->with('success', "อัปเดต {$group} เรียบร้อย");
    }

    public function destroyGroupRecord(System $system, string $group, int $recordId)
    {
        abort_unless(AdapterFactory::hasAdapter($system), 400);

        $adapter = AdapterFactory::make($system);
        abort_unless(in_array($group, $adapter->getManagedGroups(), true), 404);

        $ok = $adapter->deleteGroupRecord($group, $recordId);

        if (! $ok) {
            return back()->withErrors(['ลบ ' . $group . ' ล้มเหลว']);
        }

        return back()->with('success', "ลบ {$group} เรียบร้อย");
    }
}
