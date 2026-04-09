<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomizationRequest;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user   = Auth::guard('portal')->user();
        $seeAll = $user->hasPermissionTo('view_all_requests');

        if (!$seeAll) {
            $techId = $user->id;
            $base   = CustomizationRequest::where(function ($q) use ($techId) {
                $q->where('assigned_tech_id1', $techId)->orWhere('assigned_tech_id2', $techId);
            });

            $stats = [
                'total'       => (clone $base)->count(),
                'pending'     => 0,
                'in_progress' => (clone $base)->whereIn('status', [
                    CustomizationRequest::STATUS_ASSIGNED,
                    CustomizationRequest::STATUS_IN_REVIEW,
                    CustomizationRequest::STATUS_SENT_FOR_REVIEW,
                    CustomizationRequest::STATUS_APPROVED,
                ])->count(),
                'completed'   => (clone $base)->where('status', CustomizationRequest::STATUS_COMPLETED)->count(),
            ];

            $recentRequests = (clone $base)->with(['primaryTechnician'])->orderByDesc('updated_at')->limit(10)->get();
        } else {
            $stats = [
                'total'       => CustomizationRequest::count(),
                'pending'     => CustomizationRequest::where('status', CustomizationRequest::STATUS_PENDING)->count(),
                'in_progress' => CustomizationRequest::whereIn('status', [
                    CustomizationRequest::STATUS_ASSIGNED,
                    CustomizationRequest::STATUS_IN_REVIEW,
                    CustomizationRequest::STATUS_SENT_FOR_REVIEW,
                    CustomizationRequest::STATUS_APPROVED,
                ])->count(),
                'completed'   => CustomizationRequest::where('status', CustomizationRequest::STATUS_COMPLETED)->count(),
            ];

            $recentRequests = CustomizationRequest::with(['primaryTechnician'])
                ->orderByDesc('created_at')
                ->limit(10)
                ->get();
        }

        return view('admin.dashboard', compact('stats', 'recentRequests', 'seeAll'));
    }
}
