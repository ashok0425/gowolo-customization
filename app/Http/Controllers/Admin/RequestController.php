<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\RequestNotificationMail;
use App\Models\CustomizationRequest;
use App\Models\PortalUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Spatie\Activitylog\Models\Activity;

class RequestController extends Controller
{
    public function index(Request $request)
    {
        $user    = Auth::guard('portal')->user();
        $seeAll  = $user->hasPermissionTo('view_all_requests');

        $query = CustomizationRequest::with(['primaryTechnician', 'secondaryTechnician', 'supervisor'])
            ->orderByDesc('created_at');

        // Users without view_all_requests only see their assigned requests
        if (!$seeAll) {
            $query->where(function ($q) use ($user) {
                $q->where('assigned_tech_id1', $user->id)
                  ->orWhere('assigned_tech_id2', $user->id);
            });
        }

        if ($request->filled('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }
        if ($request->filled('pay_status')) {
            $query->where('pay_status', $request->pay_status);
        }
        if ($request->filled('pay_type')) {
            $query->where('pay_type', $request->pay_type);
        }
        // Accept either a combined "YYYY-MM-DD - YYYY-MM-DD" range or split date_from/date_to
        $dateFrom = $request->date_from;
        $dateTo   = $request->date_to;
        if ($request->filled('date_range') && str_contains($request->date_range, ' - ')) {
            [$dateFrom, $dateTo] = array_map('trim', explode(' - ', $request->date_range, 2));
        }
        if ($dateFrom && $dateTo) {
            $query->whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59']);
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

        $requests    = $query->paginate(20)->withQueryString();
        $canAssign   = $user->hasPermissionTo('assign_technician');
        $technicians = $canAssign ? PortalUser::where('is_active', true)->get() : collect();
        $supervisors = $canAssign ? PortalUser::where('is_active', true)->get() : collect();
        $statuses    = CustomizationRequest::statuses();

        return view('admin.requests.index', compact('requests', 'technicians', 'supervisors', 'seeAll', 'canAssign', 'statuses'));
    }

    public function show(CustomizationRequest $customizationRequest)
    {
        $user   = Auth::guard('portal')->user();
        $seeAll = $user->hasPermissionTo('view_all_requests');

        if (!$seeAll) {
            abort_unless(
                $customizationRequest->assigned_tech_id1 == $user->id ||
                $customizationRequest->assigned_tech_id2 == $user->id,
                403, 'You are not assigned to this request.'
            );
        }

        $customizationRequest->load(['answers', 'files', 'primaryTechnician', 'secondaryTechnician', 'supervisor']);
        $canAssign   = $user->hasPermissionTo('assign_technician');
        $technicians = $canAssign ? PortalUser::where('is_active', true)->get() : collect();
        $supervisors = $canAssign ? PortalUser::where('is_active', true)->get() : collect();

        return view('admin.requests.show', compact('customizationRequest', 'technicians', 'supervisors', 'seeAll', 'canAssign'));
    }

    public function assign(Request $request, CustomizationRequest $customizationRequest)
    {
        $user = Auth::guard('portal')->user();
        abort_unless($user->hasPermissionTo('assign_technician'), 403, 'Not authorized to assign.');

        $request->validate([
            'assigned_tech_id1' => 'required|exists:portal_users,id',
        ]);

        $tech1      = PortalUser::find($request->assigned_tech_id1);
        $tech2      = $request->filled('assigned_tech_id2') ? PortalUser::find($request->assigned_tech_id2) : null;
        $supervisor = $request->filled('supervisor_id') ? PortalUser::find($request->supervisor_id) : null;

        $old = [
            'tech1'      => $customizationRequest->assigned_tech_name1,
            'tech2'      => $customizationRequest->assigned_tech_name2,
            'supervisor' => $customizationRequest->supervisor_name,
        ];

        $customizationRequest->update([
            'assigned_tech_id1'   => $tech1->id,
            'assigned_tech_name1' => $tech1->full_name,
            'assigned_tech_id2'   => $tech2?->id,
            'assigned_tech_name2' => $tech2?->full_name,
            'supervisor_id'       => $supervisor?->id,
            'supervisor_name'     => $supervisor?->full_name,
            'status'              => CustomizationRequest::STATUS_ASSIGNED,
            'tech_receive_date'   => now(),
            'last_updated_by'     => $user->id,
        ]);

        activity('customization')
            ->causedBy($user)
            ->performedOn($customizationRequest)
            ->withProperties(['old' => $old, 'new' => [
                'tech1' => $tech1->full_name,
                'tech2' => $tech2?->full_name,
                'supervisor' => $supervisor?->full_name,
            ]])
            ->log('technician_assigned');

        return back()->with('success', 'Technician assigned successfully.');
    }

    public function updateStatus(Request $request, CustomizationRequest $customizationRequest)
    {
        $user    = Auth::guard('portal')->user();
        $seeAll  = $user->hasPermissionTo('view_all_requests');

        abort_unless($user->hasPermissionTo('update_request_status'), 403);

        if (!$seeAll) {
            abort_unless(
                $customizationRequest->assigned_tech_id1 == $user->id ||
                $customizationRequest->assigned_tech_id2 == $user->id,
                403
            );
            // Restricted users: In Review or Sent for Review only
            $request->validate(['status' => 'required|in:2,3']);
        } else {
            $request->validate(['status' => 'required|in:0,1,2,3,4,5']);
        }

        $oldStatus = $customizationRequest->status;

        $data = [
            'status'          => $request->status,
            'last_updated_by' => $user->id,
        ];

        if ($request->status == CustomizationRequest::STATUS_COMPLETED) {
            $data['date_complete'] = now();
            if ($customizationRequest->tech_receive_date) {
                $data['num_of_days'] = $this->calcBusinessDays($customizationRequest->tech_receive_date, now());
            }
        }

        if ($request->filled('technician_comments')) {
            $data['technician_comments'] = $request->technician_comments;
        }

        if ($seeAll && $request->filled('pay_type')) {
            $data['pay_type']   = $request->pay_type;
            $data['pay_amount'] = $request->pay_amount ?? 0;
        }

        $customizationRequest->update($data);

        $statuses = CustomizationRequest::statuses();

        activity('customization')
            ->causedBy($user)
            ->performedOn($customizationRequest)
            ->withProperties([
                'old' => ['status' => $statuses[$oldStatus] ?? (string) $oldStatus],
                'new' => ['status' => $statuses[(int) $request->status] ?? (string) $request->status],
            ])
            ->log('status_changed');

        // Notify configured email about the status change
        $notifyEmail = config('mail.notification_email');
        if ($notifyEmail) {
            try {
                Mail::to($notifyEmail)->send(new RequestNotificationMail(
                    $customizationRequest->fresh(),
                    'status_changed',
                    $statuses[$oldStatus] ?? (string) $oldStatus,
                    $statuses[$request->status] ?? (string) $request->status
                ));
            } catch (\Throwable $e) {
                \Log::warning('Failed to send status-change notification: ' . $e->getMessage());
            }
        }

        return response()->json(['success' => true, 'message' => 'Status updated.']);
    }

    public function logs(CustomizationRequest $customizationRequest)
    {
        $logs = Activity::where('subject_type', CustomizationRequest::class)
            ->where('subject_id', $customizationRequest->id)
            ->latest()
            ->paginate(30);

        return view('admin.requests.logs', compact('customizationRequest', 'logs'));
    }

    private function calcBusinessDays($start, $end): int
    {
        $start = \Carbon\Carbon::parse($start);
        $end   = \Carbon\Carbon::parse($end);
        $days  = 0;
        while ($start->lt($end)) {
            if (!$start->isWeekend()) $days++;
            $start->addDay();
        }
        return $days;
    }
}
