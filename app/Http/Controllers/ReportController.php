<?php

namespace App\Http\Controllers;

use App\Models\System;
use App\Models\UcmUser;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function permissionMatrix(Request $request): View
    {
        $systems = System::where('is_active', true)
            ->with('permissions')
            ->orderBy('name')
            ->get();

        $query = UcmUser::where('is_active', true)
            ->with(['systemPermissions'])
            ->orderBy('name');

        if ($request->filled('system_id')) {
            $query->whereHas('systemPermissions', fn ($q) => $q->where('system_id', $request->system_id));
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(fn ($q) => $q->where('name', 'like', "%{$search}%")
                ->orWhere('username', 'like', "%{$search}%")
                ->orWhere('department', 'like', "%{$search}%")
            );
        }

        $users = $query->paginate(50)->withQueryString();

        $selectedSystem = $request->filled('system_id')
            ? $systems->firstWhere('id', $request->system_id)
            : null;

        return view('reports.permission-matrix', compact('systems', 'users', 'selectedSystem'));
    }

    public function exportPermissionMatrix(Request $request): Response
    {
        $systems = System::where('is_active', true)
            ->with('permissions')
            ->orderBy('name')
            ->get();

        $query = UcmUser::where('is_active', true)
            ->with(['systemPermissions'])
            ->orderBy('name');

        if ($request->filled('system_id')) {
            $query->whereHas('systemPermissions', fn ($q) => $q->where('system_id', $request->system_id));
            $systems = $systems->where('id', $request->system_id)->values();
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(fn ($q) => $q->where('name', 'like', "%{$search}%")
                ->orWhere('username', 'like', "%{$search}%")
            );
        }

        $users = $query->get();

        // Build CSV header
        $headers = ['Username', 'ชื่อ', 'แผนก', 'ตำแหน่ง'];
        foreach ($systems as $system) {
            foreach ($system->permissions as $perm) {
                $headers[] = $system->name.' — '.$perm->label;
            }
        }

        $rows = [];
        foreach ($users as $user) {
            $grantedMap = [];
            foreach ($user->systemPermissions as $up) {
                $grantedMap[$up->system_id][$up->permission_key] = true;
            }

            $row = [$user->username, $user->name, $user->department ?? '', $user->title ?? ''];

            foreach ($systems as $system) {
                foreach ($system->permissions as $perm) {
                    $row[] = isset($grantedMap[$system->id][$perm->key]) ? '✓' : '';
                }
            }

            $rows[] = $row;
        }

        $filename = 'permission-matrix-'.now()->format('Ymd-His').'.csv';

        $csv = implode(',', array_map(fn ($h) => '"'.str_replace('"', '""', $h).'"', $headers))."\n";
        foreach ($rows as $row) {
            $csv .= implode(',', array_map(fn ($v) => '"'.str_replace('"', '""', (string) $v).'"', $row))."\n";
        }

        return response("\xEF\xBB\xBF".$csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
