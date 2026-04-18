<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BugReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BugReportController extends Controller
{
    /**
     * List bug reports with analytics summary cards, top-5 reporters pie chart
     * data, and filters (user, status, search).
     */
    public function index(Request $request)
    {
        $query = BugReport::orderByDesc('created_at');

        // Filters
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($qb) use ($q) {
                $qb->where('message', 'like', "%{$q}%")
                   ->orWhere('steps_to_reproduce', 'like', "%{$q}%")
                   ->orWhere('user_name', 'like', "%{$q}%")
                   ->orWhere('user_email', 'like', "%{$q}%");
            });
        }

        $bugReports = $query->paginate(20)->withQueryString();

        // ============ Analytics ============
        $summary = [
            'total'      => BugReport::count(),
            'approved'   => BugReport::where('status', BugReport::STATUS_APPROVED)->count(),
            'duplicated' => BugReport::where('status', BugReport::STATUS_DUPLICATED)->count(),
            'rejected'   => BugReport::where('status', BugReport::STATUS_REJECTED)->count(),
            'in_review'  => BugReport::where('status', BugReport::STATUS_IN_REVIEW)
                                ->orWhereNull('status')->count(),
        ];

        // Top 5 users by bug report count
        $topReporters = BugReport::selectRaw('user_id, user_name, COUNT(*) as bug_count')
            ->whereNotNull('user_id')
            ->groupBy('user_id', 'user_name')
            ->orderByDesc('bug_count')
            ->limit(5)
            ->get();

        // Distinct users for filter dropdown
        $users = BugReport::selectRaw('user_id, user_name')
            ->whereNotNull('user_id')
            ->groupBy('user_id', 'user_name')
            ->orderBy('user_name')
            ->get();

        $statuses = BugReport::statuses();

        return view('admin.bug-reports.index', compact(
            'bugReports', 'summary', 'topReporters', 'users', 'statuses'
        ));
    }

    public function show(BugReport $bugReport)
    {
        if (!$bugReport->is_read) {
            $bugReport->update(['is_read' => true]);
        }

        return view('admin.bug-reports.show', compact('bugReport'));
    }

    /**
     * Update status + remark. Remark is visible to the user on their own view.
     */
    public function update(Request $request, BugReport $bugReport)
    {
        $request->validate([
            'status' => 'required|in:' . implode(',', array_keys(BugReport::statuses())),
            'remark' => 'nullable|string|max:2000',
        ]);

        $bugReport->update([
            'status'      => $request->status,
            'remark'      => $request->remark,
            'reviewed_by' => Auth::guard('portal')->id(),
            'reviewed_at' => now(),
        ]);

        return redirect()
            ->route('admin.bug-reports.show', $bugReport)
            ->with('success', 'Bug report updated.');
    }
}
