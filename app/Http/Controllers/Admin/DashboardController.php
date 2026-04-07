<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomizationRequest;
use App\Models\PortalUser;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total'       => CustomizationRequest::count(),
            'new'         => CustomizationRequest::where('status', CustomizationRequest::STATUS_NEW)->count(),
            'in_progress' => CustomizationRequest::where('status', CustomizationRequest::STATUS_IN_PROGRESS)->count(),
            'completed'   => CustomizationRequest::where('status', CustomizationRequest::STATUS_COMPLETED)->count(),
            'paid'        => CustomizationRequest::where('pay_status', 1)->count(),
            'unpaid'      => CustomizationRequest::where('pay_status', 0)->where('pay_type', 2)->count(),
        ];

        $recentRequests = CustomizationRequest::with(['primaryTechnician'])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        $technicians = PortalUser::role('technician')->where('is_active', true)->get();

        return view('admin.dashboard', compact('stats', 'recentRequests', 'technicians'));
    }
}
