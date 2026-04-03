<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\MatrixShareLink;
use App\Models\System;
use App\Models\UcmUser;
use App\Services\AuditLogger;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PublicMatrixController extends Controller
{
    public function show(string $token): View|Response
    {
        $shareLink = MatrixShareLink::where('token', $token)->first();

        if (! $shareLink || ! $shareLink->isAccessible()) {
            return response()->view('share-links.expired', compact('shareLink'), 410);
        }

        $systems = System::where('is_active', true)
            ->with('permissions')
            ->orderBy('name')
            ->get();

        if (! empty($shareLink->filter_system_ids)) {
            $systems = $systems->whereIn('id', $shareLink->filter_system_ids)->values();
        }

        $query = UcmUser::where('is_active', true)
            ->with(['systemPermissions'])
            ->orderBy('name');

        if (! empty($shareLink->filter_departments)) {
            $query->whereIn('department', $shareLink->filter_departments);
        }

        if (! empty($shareLink->filter_usernames)) {
            $query->whereIn('username', $shareLink->filter_usernames);
        }

        $users = $query->get();

        AuditLogger::log(
            AuditLog::CATEGORY_SHARE_LINKS,
            AuditLog::EVENT_SHARE_LINK_VIEWED,
            'เปิด Share Link: '.$shareLink->label,
            ['share_link_id' => $shareLink->id],
            actor: null,
        );

        $shareLink->recordView();

        return view('share-links.public', compact('shareLink', 'systems', 'users'));
    }

    public function export(string $token): Response|StreamedResponse
    {
        $shareLink = MatrixShareLink::where('token', $token)->first();

        if (! $shareLink || ! $shareLink->isAccessible()) {
            return response()->view('share-links.expired', compact('shareLink'), 410);
        }

        $systems = System::where('is_active', true)
            ->with('permissions')
            ->orderBy('name')
            ->get();

        if (! empty($shareLink->filter_system_ids)) {
            $systems = $systems->whereIn('id', $shareLink->filter_system_ids)->values();
        }

        $query = UcmUser::where('is_active', true)
            ->with(['systemPermissions'])
            ->orderBy('name');

        if (! empty($shareLink->filter_departments)) {
            $query->whereIn('department', $shareLink->filter_departments);
        }

        if (! empty($shareLink->filter_usernames)) {
            $query->whereIn('username', $shareLink->filter_usernames);
        }

        $users = $query->get();

        // Build permission column headers: [system_id => [perm_key => label]]
        $permCols = [];
        foreach ($systems as $system) {
            foreach ($system->permissions as $perm) {
                $permCols[] = ['system_id' => $system->id, 'key' => $perm->key, 'header' => $system->name.' — '.$perm->label];
            }
        }

        $filename = 'permission-matrix-'.now()->format('Ymd-His').'.csv';

        return response()->streamDownload(function () use ($users, $permCols) {
            $out = fopen('php://output', 'w');
            // UTF-8 BOM for Excel
            fwrite($out, "\xEF\xBB\xBF");

            // Header row
            $headers = ['Username', 'ชื่อ', 'แผนก', 'ตำแหน่ง'];
            foreach ($permCols as $col) {
                $headers[] = $col['header'];
            }
            $headers[] = 'รวมสิทธิ์';
            fputcsv($out, $headers);

            // Data rows
            foreach ($users as $user) {
                $grantedMap = [];
                foreach ($user->systemPermissions as $up) {
                    $grantedMap[$up->system_id][$up->permission_key] = true;
                }

                $row = [$user->username, $user->name, $user->department ?? '', $user->title ?? ''];
                $total = 0;
                foreach ($permCols as $col) {
                    $has = isset($grantedMap[$col['system_id']][$col['key']]);
                    $row[] = $has ? '✓' : '';
                    if ($has) {
                        $total++;
                    }
                }
                $row[] = $total;
                fputcsv($out, $row);
            }

            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
