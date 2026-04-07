<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomizationActivityLog;
use App\Models\CustomizationRequest;
use App\Models\PortalUser;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RequestController extends Controller
{
    public function __construct(private ActivityLogService $logger) {}

    public function index(Request $request)
    {
        $query = CustomizationRequest::with(['primaryTechnician', 'secondaryTechnician', 'supervisor'])
            ->orderByDesc('created_at');

        if ($request->filled('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }
        if ($request->filled('pay_status')) {
            $query->where('pay_status', $request->pay_status);
        }
        if ($request->filled('pay_type')) {
            $query->where('pay_type', $request->pay_type);
        }
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereBetween('created_at', [$request->date_from . ' 00:00:00', $request->date_to . ' 23:59:59']);
        }
        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($qb) use ($q) {
                $qb->where('ref_number', 'like', "%{$q}%")
                   ->orWhere('first_name', 'like', "%{$q}%")
                   ->orWhere('last_name', 'like', "%{$q}%")
                   ->orWhere('email', 'like', "%{$q}%")
                   ->orWhere('company_name', 'like', "%{$q}%");
            });
        }

        $requests     = $query->paginate(20)->withQueryString();
        $technicians  = PortalUser::role('technician')->where('is_active', true)->get();

        return view('admin.requests.index', compact('requests', 'technicians'));
    }

    public function show(CustomizationRequest $customizationRequest)
    {
        $customizationRequest->load(['answers', 'files', 'chats', 'activityLogs', 'primaryTechnician', 'secondaryTechnician', 'supervisor']);
        $technicians = PortalUser::role('technician')->where('is_active', true)->get();
        $supervisors = PortalUser::role('supervisor')->orWhereHas('roles', fn($q) => $q->where('name', 'admin'))->get();

        return view('admin.requests.show', compact('customizationRequest', 'technicians', 'supervisors'));
    }

    public function assign(Request $request, CustomizationRequest $customizationRequest)
    {
        $request->validate([
            'assigned_tech_id1' => 'required|exists:portal_users,id',
        ]);

        $old = [
            'tech1' => $customizationRequest->assigned_tech_name1,
            'tech2' => $customizationRequest->assigned_tech_name2,
            'supervisor' => $customizationRequest->supervisor_name,
        ];

        $tech1 = PortalUser::find($request->assigned_tech_id1);
        $tech2 = $request->filled('assigned_tech_id2') ? PortalUser::find($request->assigned_tech_id2) : null;
        $supervisor = $request->filled('supervisor_id') ? PortalUser::find($request->supervisor_id) : null;

        $customizationRequest->update([
            'assigned_tech_id1'   => $tech1->id,
            'assigned_tech_name1' => $tech1->full_name,
            'assigned_tech_id2'   => $tech2?->id,
            'assigned_tech_name2' => $tech2?->full_name,
            'supervisor_id'       => $supervisor?->id,
            'supervisor_name'     => $supervisor?->full_name,
            'status'              => CustomizationRequest::STATUS_IN_PROGRESS,
            'tech_receive_date'   => now(),
            'last_updated_by'     => Auth::guard('portal')->id(),
        ]);

        $this->logger->log(
            'technician_assigned',
            $customizationRequest->id,
            $old,
            ['tech1' => $tech1->full_name, 'tech2' => $tech2?->full_name, 'supervisor' => $supervisor?->full_name],
            'Technician assigned',
            $request
        );

        return back()->with('success', 'Technician assigned successfully.');
    }

    public function updateStatus(Request $request, CustomizationRequest $customizationRequest)
    {
        $request->validate(['status' => 'required|in:0,1,2']);

        $oldStatus = $customizationRequest->status;

        $data = [
            'status'          => $request->status,
            'last_updated_by' => Auth::guard('portal')->id(),
        ];

        if ($request->status == CustomizationRequest::STATUS_COMPLETED) {
            $data['date_complete'] = now();
            if ($customizationRequest->tech_process_date) {
                $data['num_of_days'] = $this->calcBusinessDays($customizationRequest->tech_process_date, now());
            }
        }

        if ($request->filled('technician_comments')) {
            $data['technician_comments'] = $request->technician_comments;
        }

        if ($request->filled('pay_type')) {
            $data['pay_type']   = $request->pay_type;
            $data['pay_amount'] = $request->pay_amount ?? 0;
        }

        $customizationRequest->update($data);

        $this->logger->log(
            'status_changed',
            $customizationRequest->id,
            ['status' => $oldStatus],
            ['status' => $request->status],
            'Status updated',
            $request
        );

        return response()->json(['success' => true, 'message' => 'Status updated.']);
    }

    public function logs(CustomizationRequest $customizationRequest)
    {
        $logs = $customizationRequest->activityLogs()->paginate(30);
        return view('admin.requests.logs', compact('customizationRequest', 'logs'));
    }

    private function calcBusinessDays($start, $end): int
    {
        $start = \Carbon\Carbon::parse($start);
        $end   = \Carbon\Carbon::parse($end);
        $days  = 0;
        while ($start->lt($end)) {
            if (!$start->isWeekend()) {
                $days++;
            }
            $start->addDay();
        }
        return $days;
    }
}
