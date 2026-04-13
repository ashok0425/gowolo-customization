<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\BugReport;
use Illuminate\Http\Request;

class BugReportController extends Controller
{
    public function index()
    {
        $ssoUser = session('auth_user');
        $bugReports = BugReport::where('user_id', $ssoUser['user_id'])
            ->orderByDesc('created_at')
            ->get();

        return view('user.bug-report.index', compact('bugReports'));
    }

    public function create()
    {
        return view('user.bug-report.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'message'            => 'required|string|max:5000',
            'steps_to_reproduce' => 'nullable|string|max:5000',
            'screenshot'         => 'nullable|image|max:5120',
        ]);

        $ssoUser = session('auth_user');

        $screenshotPath = null;
        if ($request->hasFile('screenshot')) {
            $file      = $request->file('screenshot');
            $filename  = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/bug-reports'), $filename);
            $screenshotPath = '/uploads/bug-reports/' . $filename;
        }

        BugReport::create([
            'user_id'            => $ssoUser['user_id'],
            'user_name'          => $ssoUser['name'],
            'user_email'         => $ssoUser['email'],
            'message'            => $request->message,
            'steps_to_reproduce' => $request->steps_to_reproduce,
            'screenshot_path'    => $screenshotPath,
        ]);

        return redirect()->route('user.dashboard')
            ->with('success', 'Bug report submitted successfully. Thank you!');
    }
}
