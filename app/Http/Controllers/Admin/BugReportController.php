<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BugReport;

class BugReportController extends Controller
{
    public function index()
    {
        $bugReports = BugReport::orderByDesc('created_at')->get();

        return view('admin.bug-reports.index', compact('bugReports'));
    }

    public function show(BugReport $bugReport)
    {
        if (!$bugReport->is_read) {
            $bugReport->update(['is_read' => true]);
        }

        return view('admin.bug-reports.show', compact('bugReport'));
    }
}
