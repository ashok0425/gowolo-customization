<?php

namespace App\Http\Controllers\Technician;

use App\Http\Controllers\Controller;
use App\Models\CustomizationRequest;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $techId = Auth::guard('portal')->id();

        $requests = CustomizationRequest::where(function ($q) use ($techId) {
                $q->where('assigned_tech_id1', $techId)
                  ->orWhere('assigned_tech_id2', $techId);
            })
            ->orderByDesc('updated_at')
            ->paginate(20);

        $stats = [
            'total'       => CustomizationRequest::where('assigned_tech_id1', $techId)->orWhere('assigned_tech_id2', $techId)->count(),
            'in_progress' => CustomizationRequest::where('status', CustomizationRequest::STATUS_IN_PROGRESS)
                ->where(fn($q) => $q->where('assigned_tech_id1', $techId)->orWhere('assigned_tech_id2', $techId))->count(),
            'completed'   => CustomizationRequest::where('status', CustomizationRequest::STATUS_COMPLETED)
                ->where(fn($q) => $q->where('assigned_tech_id1', $techId)->orWhere('assigned_tech_id2', $techId))->count(),
        ];

        return view('technician.dashboard', compact('requests', 'stats'));
    }
}
