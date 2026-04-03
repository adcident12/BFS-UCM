<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMatrixShareLinkRequest;
use App\Models\AuditLog;
use App\Models\MatrixShareLink;
use App\Models\System;
use App\Models\UcmUser;
use App\Services\AuditLogger;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class MatrixShareLinkController extends Controller
{
    public function __construct(private readonly NotificationService $notifications) {}

    public function index(): View
    {
        /** @var \App\Models\UcmUser|null */
        $user = Auth::user();
        abort_unless($user?->canAccess('share_link_manage'), 403);

        $shareLinks = MatrixShareLink::with(['creator', 'revoker'])
            ->latest()
            ->get();

        $systems = System::where('is_active', true)->orderBy('name')->get();

        $departments = UcmUser::where('is_active', true)
            ->whereNotNull('department')
            ->where('department', '!=', '')
            ->distinct()
            ->orderBy('department')
            ->pluck('department');

        return view('share-links.index', compact('shareLinks', 'systems', 'departments'));
    }

    public function store(StoreMatrixShareLinkRequest $request): RedirectResponse
    {
        /** @var \App\Models\UcmUser */
        $user = Auth::user();

        $usernamesRaw = $request->input('filter_usernames', '');
        $filterUsernames = collect(preg_split('/[\s,;\n]+/', $usernamesRaw))
            ->map(fn ($u) => trim($u))
            ->filter()
            ->values()
            ->all();

        $link = MatrixShareLink::create([
            'token'              => MatrixShareLink::generateToken(),
            'label'              => $request->label,
            'filter_system_ids'  => $request->input('filter_system_ids') ?: null,
            'filter_departments' => $request->input('filter_departments') ?: null,
            'filter_usernames'   => $filterUsernames ?: null,
            'is_active'          => true,
            'expires_at'         => now()->addDays((int) $request->expires_days),
            'created_by'         => $user->id,
        ]);

        AuditLogger::log(
            AuditLog::CATEGORY_SHARE_LINKS,
            AuditLog::EVENT_SHARE_LINK_CREATED,
            'สร้าง Share Link: '.$link->label,
            [
                'share_link_id' => $link->id,
                'expires_at'    => $link->expires_at->toDateTimeString(),
                'has_filters'   => $link->hasFilters(),
            ],
            $user,
        );

        $this->notifications->dispatch('share_link_created', [
            'label'      => $link->label,
            'created_by' => $user->name,
            'expires_at' => $link->expires_at->format('d/m/Y'),
        ]);

        return redirect()->route('share-links.index')->with('success', 'สร้าง Share Link สำเร็จ');
    }

    public function toggle(MatrixShareLink $shareLink): RedirectResponse
    {
        /** @var \App\Models\UcmUser */
        $user = Auth::user();
        abort_unless($user?->canAccess('share_link_manage'), 403);

        $wasActive = $shareLink->is_active;

        if ($wasActive) {
            $shareLink->update([
                'is_active'  => false,
                'revoked_by' => $user->id,
                'revoked_at' => now(),
            ]);

            AuditLogger::log(
                AuditLog::CATEGORY_SHARE_LINKS,
                AuditLog::EVENT_SHARE_LINK_REVOKED,
                'ยกเลิก Share Link: '.$shareLink->label,
                ['share_link_id' => $shareLink->id],
                $user,
            );

            $this->notifications->dispatch('share_link_revoked', [
                'label'      => $shareLink->label,
                'revoked_by' => $user->name,
            ]);
        } else {
            $shareLink->update([
                'is_active'  => true,
                'revoked_by' => null,
                'revoked_at' => null,
            ]);

            AuditLogger::log(
                AuditLog::CATEGORY_SHARE_LINKS,
                AuditLog::EVENT_SHARE_LINK_REACTIVATED,
                'เปิดใช้งาน Share Link อีกครั้ง: '.$shareLink->label,
                ['share_link_id' => $shareLink->id],
                $user,
            );
        }

        $message = $wasActive ? 'ยกเลิก Share Link แล้ว' : 'เปิดใช้งาน Share Link แล้ว';

        return redirect()->route('share-links.index')->with('success', $message);
    }
}
