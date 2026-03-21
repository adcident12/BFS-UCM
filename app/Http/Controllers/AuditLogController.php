<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\UcmUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        /** @var UcmUser $user */
        $user = Auth::user();
        $allowedDepts = array_map('strtoupper', config('auth.audit_departments', []));

        if (! $user->isAdmin() && ! in_array(strtoupper($user->department ?? ''), $allowedDepts)) {
            abort(403, 'คุณไม่มีสิทธิ์เข้าถึงหน้านี้');
        }

        $query = AuditLog::query()->latest('created_at');

        if ($request->filled('category')) {
            $query->where('event_category', $request->category);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('actor_username', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('subject_label', 'like', "%{$search}%");
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->paginate(50)->withQueryString();

        $categoryCounts = AuditLog::query()
            ->selectRaw('event_category, count(*) as total')
            ->groupBy('event_category')
            ->pluck('total', 'event_category');

        return view('audit.index', compact('logs', 'categoryCounts'));
    }
}
