<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomizationRequest;
use App\Models\PortalUser;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user   = Auth::guard('portal')->user();
        $isTech = $user->hasRole('technician');

        if ($isTech) {
            $techId = $user->id;
            $base   = CustomizationRequest::where(function ($q) use ($techId) {
                $q->where('assigned_tech_id1', $techId)->orWhere('assigned_tech_id2', $techId);
            });

            $stats = [
                'total'       => (clone $base)->count(),
                'new'         => 0,
                'in_progress' => (clone $base)->where('status', CustomizationRequest::STATUS_IN_PROGRESS)->count(),
                'completed'   => (clone $base)->where('status', CustomizationRequest::STATUS_COMPLETED)->count(),
            ];

            $recentRequests = (clone $base)->with(['primaryTechnician'])->orderByDesc('updated_at')->limit(10)->get();
        } else {
            $stats = [
                'total'       => CustomizationRequest::count(),
                'new'         => CustomizationRequest::where('status', CustomizationRequest::STATUS_NEW)->count(),
                'in_progress' => CustomizationRequest::where('status', CustomizationRequest::STATUS_IN_PROGRESS)->count(),
                'completed'   => CustomizationRequest::where('status', CustomizationRequest::STATUS_COMPLETED)->count(),
            ];

            $recentRequests = CustomizationRequest::with(['primaryTechnician'])
                ->orderByDesc('created_at')
                ->limit(10)
                ->get();
        }

        return view('admin.dashboard', compact('stats', 'recentRequests', 'isTech'));
    }
}
