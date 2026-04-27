<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\RequestNotificationMail;
use App\Models\CustomizationAnswer;
use App\Models\CustomizationFile;
use App\Models\CustomizationRequest;
use App\Models\PortalUser;
use App\Services\BunnyStorageService;
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
        // Admin/user can edit; technicians cannot
        $canEdit     = $user->hasPermissionTo('edit_request');
        $technicians = $canAssign ? PortalUser::where('is_active', true)->get() : collect();
        $supervisors = $canAssign ? PortalUser::where('is_active', true)->get() : collect();
        $statuses    = CustomizationRequest::statuses();

        return view('admin.requests.index', compact('requests', 'technicians', 'supervisors', 'seeAll', 'canAssign', 'canEdit', 'statuses'));
    }

    public function show(string $cuid)
    {
        $customizationRequest = CustomizationRequest::where('cuid', $cuid)->firstOrFail();

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

    public function assign(Request $request, string $cuid)
    {
        $customizationRequest = CustomizationRequest::where('cuid', $cuid)->firstOrFail();

        $user = Auth::guard('portal')->user();
        abort_unless($user->hasPermissionTo('assign_technician'), 403, 'Not authorized to assign.');

        $request->validate([
            'assigned_tech_id1' => 'required|exists:portal_users,id',
            'pay_type'          => 'nullable|in:1,2',
            'pay_amount'        => 'nullable|numeric|min:0',
        ]);

        $tech1      = PortalUser::find($request->assigned_tech_id1);
        $tech2      = $request->filled('assigned_tech_id2') ? PortalUser::find($request->assigned_tech_id2) : null;
        $supervisor = $request->filled('supervisor_id') ? PortalUser::find($request->supervisor_id) : null;

        $old = [
            'tech1'      => $customizationRequest->assigned_tech_name1,
            'tech2'      => $customizationRequest->assigned_tech_name2,
            'supervisor' => $customizationRequest->supervisor_name,
            'pay_type'   => $customizationRequest->pay_type,
            'pay_amount' => $customizationRequest->pay_amount,
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
            'pay_type'            => $request->pay_type ?? 1,
            'pay_amount'          => $request->pay_type == 2 ? $request->pay_amount : 0,
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

    public function updateStatus(Request $request, string $cuid)
    {
        $customizationRequest = CustomizationRequest::where('cuid', $cuid)->firstOrFail();
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
            $request->validate(['status' => 'required|in:0,1,2,3,4,5,6,7']);
        }

        // Guard: "Approved" (4) can only be set when current status is "Approved by Team" (7)
        if ((int) $request->status === CustomizationRequest::STATUS_APPROVED
            && $customizationRequest->status !== CustomizationRequest::STATUS_TEAM_APPROVED) {
            return response()->json([
                'success' => false,
                'message' => 'Request must be "Approved by Team" before it can be marked as Approved.',
            ], 422);
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

        // Notify the customer (user) about the status change via email
        $customerEmail = $customizationRequest->email ?: $customizationRequest->user_email;
        if ($customerEmail) {
            try {
                Mail::to($customerEmail)->send(new RequestNotificationMail(
                    $customizationRequest->fresh(),
                    'status_changed',
                    $statuses[$oldStatus] ?? (string) $oldStatus,
                    $statuses[(int) $request->status] ?? (string) $request->status
                ));
            } catch (\Throwable $e) {
                \Log::warning('Failed to send status-change email to customer: ' . $e->getMessage());
            }
        }

        return response()->json(['success' => true, 'message' => 'Status updated.']);
    }

    public function logs(string $cuid)
    {
        $customizationRequest = CustomizationRequest::where('cuid', $cuid)->firstOrFail();

        $logs = Activity::where('subject_type', CustomizationRequest::class)
            ->where('subject_id', $customizationRequest->id)
            ->latest()
            ->paginate(30);

        return view('admin.requests.logs', compact('customizationRequest', 'logs'));
    }

    /**
     * Edit form — admin/user only, technicians cannot edit.
     */
    public function edit(string $cuid)
    {
        $customizationRequest = CustomizationRequest::where('cuid', $cuid)->firstOrFail();

        $user = Auth::guard('portal')->user();
        abort_unless($user->hasPermissionTo('edit_request'), 403, 'Not authorized to edit requests.');

        $customizationRequest->load(['answers']);
        return view('admin.requests.edit', compact('customizationRequest'));
    }

    /**
     * Persist edits, log every changed field via spatie activitylog.
     */
    public function update(Request $request, string $cuid)
    {
        $customizationRequest = CustomizationRequest::where('cuid', $cuid)->firstOrFail();

        $user = Auth::guard('portal')->user();
        abort_unless($user->hasPermissionTo('edit_request'), 403, 'Not authorized to edit requests.');

        $request->validate([
            'first_name'        => 'required|string|max:100',
            'last_name'         => 'required|string|max:100',
            'email'             => 'required|email',
            'phone'             => 'required|string|max:20',
            'sec_phone'         => 'nullable|string|max:20',
            'company_name'      => 'required|string|max:200',
            'company_phone'     => 'required|string|max:200',
            'company_address'   => 'nullable|string',
            'req_primary_color' => 'nullable|string|max:20',
            'req_sec_color'     => 'nullable|string|max:20',
            'request_description' => 'nullable|string',
        ]);

        // Capture only the fields we allow editing
        $editable = [
            'first_name', 'last_name', 'email', 'phone', 'sec_phone',
            'company_name', 'company_phone', 'company_address',
            'req_primary_color', 'req_sec_color', 'request_description',
            'req_logo', 'req_icon', 'req_app_background', 'req_landing_page', 'req_others',
        ];

        $old = collect($editable)->mapWithKeys(fn ($k) => [$k => $customizationRequest->$k])->toArray();

        $data = $request->only(['first_name', 'last_name', 'email', 'phone', 'sec_phone',
            'company_name', 'company_phone', 'company_address',
            'req_primary_color', 'req_sec_color', 'request_description']);

        $data['req_logo']           = $request->boolean('req_logo');
        $data['req_icon']           = $request->boolean('req_icon');
        $data['req_app_background'] = $request->boolean('req_app_background');
        $data['req_landing_page']   = $request->boolean('req_landing_page');
        $data['req_others']         = $request->boolean('req_others');
        $data['last_updated_by']    = $user->id;

        $customizationRequest->update($data);

        // Diff only fields that actually changed, so the log is readable
        $changed = [];
        foreach ($editable as $field) {
            if ($old[$field] != $customizationRequest->$field) {
                $changed[$field] = ['old' => $old[$field], 'new' => $customizationRequest->$field];
            }
        }

        // ============ Persist questionary answers (upsert by question_key) ============
        $questionKeys = [
            'question_1'    => 'What domain name would you like to be displayed in your website?',
            'question_2'    => 'What are your gifts, talents, products and/or services and what are you passionate about?',
            'question_3'    => 'If you never got paid for it, what could you do for the rest of your life that brings you happiness?',
            'question_4'    => 'List 5 things you love to do in order of importance.',
            'question_5'    => 'How many followers do you have on other platforms',
            'question_11'   => 'Do you have a thumbnail image for your content management or master courses?',
            'question_12'   => 'Can you provide us with your website content for your landing page?',
            'question_13'   => 'Can you provide us with your campaign content for your lead capture page?',
            'question_14'   => 'Do you have product images for your e-commerce store?',
            'question_15'   => 'Do you have a banner image for your e-commerce store?',
            'question_16'   => 'Do you have any videos for your landing page, e-commerce store or master courses?',
            'question_17'   => 'What would you like to do in your VIP to share your gift, talent, products and/or services?',
            'requirement_1' => 'How will you use this order?',
            'requirement_2' => 'Which industry is most relevant to your order?',
            'requirement_3' => 'What are you looking to achieve with this order?',
            'requirement_4' => 'Relevant data',
        ];

        $oldAnswers = CustomizationAnswer::where('request_id', $customizationRequest->id)
            ->pluck('answer', 'question_key')->toArray();

        foreach ($questionKeys as $key => $text) {
            $newValue = $request->input($key);
            if ($newValue === null || $newValue === '') {
                continue;
            }
            $oldValue = $oldAnswers[$key] ?? null;
            if ((string) $oldValue !== (string) $newValue) {
                $changed[$key] = ['old' => $oldValue, 'new' => $newValue];
            }
            CustomizationAnswer::updateOrCreate(
                ['request_id' => $customizationRequest->id, 'question_key' => $key],
                ['question_text' => $text, 'answer' => $newValue]
            );
        }

        if (!empty($changed)) {
            activity('customization')
                ->causedBy($user)
                ->performedOn($customizationRequest)
                ->withProperties(['changes' => $changed])
                ->log('request_edited');
        }

        return redirect()->route('admin.requests.show', $customizationRequest)
            ->with('success', 'Request updated successfully.');
    }

    public function downloadFile(string $cuid, CustomizationFile $file)
    {
        $customizationRequest = CustomizationRequest::where('cuid', $cuid)->firstOrFail();

        $user   = Auth::guard('portal')->user();
        $seeAll = $user->hasPermissionTo('view_all_requests');

        if (!$seeAll) {
            abort_unless(
                $customizationRequest->assigned_tech_id1 == $user->id ||
                $customizationRequest->assigned_tech_id2 == $user->id,
                403
            );
        }

        abort_unless($file->request_id === $customizationRequest->id, 404);

        $bunny = app(BunnyStorageService::class);

        if ($file->bunny_path && $bunny->isConfigured()) {
            return redirect($bunny->signedUrl($file->bunny_path));
        }

        if ($file->local_path) {
            $path = public_path(ltrim($file->local_path, '/'));
            abort_unless(file_exists($path), 404);
            return response()->download($path, $file->original_name);
        }

        abort(404);
    }

    public function downloadAllFiles(string $cuid)
    {
        $customizationRequest = CustomizationRequest::where('cuid', $cuid)->firstOrFail();

        $user   = Auth::guard('portal')->user();
        $seeAll = $user->hasPermissionTo('view_all_requests');

        if (!$seeAll) {
            abort_unless(
                $customizationRequest->assigned_tech_id1 == $user->id ||
                $customizationRequest->assigned_tech_id2 == $user->id,
                403
            );
        }

        $files = $customizationRequest->files;
        abort_if($files->isEmpty(), 404);

        $zipName = $customizationRequest->ref_number . '_files.zip';
        $tempDir = storage_path('app/temp');
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }
        $tempPath = $tempDir . '/' . uniqid('zip_') . '.zip';

        $zip = new \ZipArchive();
        abort_unless($zip->open($tempPath, \ZipArchive::CREATE) === true, 500);

        $bunny = app(BunnyStorageService::class);

        foreach ($files as $file) {
            if ($file->bunny_path && $bunny->isConfigured()) {
                $url = $bunny->signedUrl($file->bunny_path);
                $content = @file_get_contents($url);
                if ($content !== false) {
                    $zip->addFromString($file->original_name, $content);
                }
            } elseif ($file->local_path) {
                $localPath = public_path(ltrim($file->local_path, '/'));
                if (file_exists($localPath)) {
                    $zip->addFile($localPath, $file->original_name);
                }
            }
        }

        $zip->close();

        return response()->download($tempPath, $zipName)->deleteFileAfterSend(true);
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
