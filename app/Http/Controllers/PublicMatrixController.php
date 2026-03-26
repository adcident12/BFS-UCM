<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\MatrixShareLink;
use App\Models\System;
use App\Models\UcmUser;
use App\Services\AuditLogger;
use Illuminate\Http\Response;
use Illuminate\View\View;

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
}
