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

        return view('systems.show', compact('system'));
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
            'remote_value' => 'nullable|string|max:100',
            'group'        => 'nullable|string|max:50',
            'description'  => 'nullable|string|max:500',
            'sort_order'   => 'nullable|integer|min:0',
            'is_exclusive' => 'boolean',
        ]);

        $data['is_exclusive'] = $request->boolean('is_exclusive');

        $system->permissions()->create($data);

        return back()->with('success', "เพิ่ม permission '{$data['label']}' เรียบร้อย");
    }

    public function updatePermission(Request $request, System $system, SystemPermission $permission)
    {
        abort_if($permission->system_id !== $system->id, 404);

        $data = $request->validate([
            'label'        => 'required|string|max:100',
            'remote_value' => 'nullable|string|max:100',
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

        $permission->delete();

        return back()->with('success', 'ลบ permission เรียบร้อย');
    }
}
