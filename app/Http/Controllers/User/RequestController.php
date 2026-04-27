<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\CustomizationAnswer;
use App\Models\CustomizationFile;
use App\Models\CustomizationRequest;
use App\Mail\RequestNotificationMail;
use App\Models\PortalNotification;
use App\Services\BunnyStorageService;
use App\Services\SiteInfoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class RequestController extends Controller
{
    const QUESTIONS = [
        'question_1'  => 'What domain name would you like to be displayed in your website?',
        'question_2'  => 'What are your gifts, talents, products and/or services and what are you passionate about?',
        'question_3'  => 'If you never got paid for it, what could you do for the rest of your life that brings you happiness?',
        'question_4'  => 'List 5 things you love to do in order of importance.',
        'question_5'  => 'How many followers do you have on other platforms',
        'question_11' => 'Do you have a thumbnail image for your content management or master courses?',
        'question_12' => 'Can you provide us with your website content for your landing page?',
        'question_13' => 'Can you provide us with your campaign content for your lead capture page?',
        'question_14' => 'Do you have product images for your e-commerce store?',
        'question_15' => 'Do you have a banner image for your e-commerce store?',
        'question_16' => 'Do you have any videos for your landing page, e-commerce store or master courses?',
        'question_17' => 'What would you like to do in your VIP to share your gift, talent, products and/or services?',
        'requirement_1' => 'How will you use this order?',
        'requirement_2' => 'Which industry is most relevant to your order?',
        'requirement_3' => 'What are you looking to achieve with this order?',
        'requirement_4' => 'Relevant data',
    ];

    public function __construct(
        private BunnyStorageService $bunny,
        private SiteInfoService $siteInfo
    ) {}

    public function dashboard()
    {
        $ssoUser  = session('auth_user');
        $requests = CustomizationRequest::where('user_id', $ssoUser['user_id'])
            ->with(['primaryTechnician'])
            ->orderByDesc('created_at')
            ->paginate(15)->withQueryString();

        return view('user.dashboard', compact('requests'));
    }

    public function create()
    {
        $ssoUser   = session('auth_user');
        $price     = $this->siteInfo->getCustomizationPrice();
        $siteInfo  = $this->siteInfo->getSiteInfo($ssoUser['user_id']);
        $questions = self::QUESTIONS;

        return view('user.request.create', compact('price', 'siteInfo', 'questions'));
    }

    public function store(Request $request)
    {
        $isCustomization = $request->input('request_type', 'customization') === 'customization';

        // Base validation (always required)
        $rules = [
            'first_name'        => 'required|string|max:100',
            'last_name'         => 'required|string|max:100',
            'email'             => 'required|email',
            'phone'             => 'required|string|max:20',
            'company_name'      => 'required|string|max:200',
            'company_phone'     => 'required|string|max:200',
            'company_address'   => 'nullable|string',
            'request_type'      => 'required|in:customization,graphic_design,web_development,software_development,app_development,gift_monetization_session',
        ];

        if ($isCustomization) {
            // Customization-specific required fields
            $rules['req_primary_color'] = 'required|string|max:20';
            $rules['req_sec_color']     = 'required|string|max:20';
            $rules['addition_feature']  = 'required|in:0,1';
        } else {
            // Non-customization: just needs a description
            $rules['request_description'] = 'required|string|min:10';
        }

        // Questionary fields are all optional
        if ($isCustomization && $request->input('addition_feature') == '1') {
            $rules = array_merge($rules, [
                'question_1'    => 'nullable|string',
                'question_2'    => 'nullable|string',
                'question_3'    => 'nullable|string',
                'question_4'    => 'nullable|string',
                'question_5'    => 'nullable|string',
                'question_11'   => 'nullable|in:0,1',
                'question_12'   => 'nullable|in:0,1',
                'question_13'   => 'nullable|in:0,1',
                'question_14'   => 'nullable|in:0,1',
                'question_15'   => 'nullable|in:0,1',
                'question_16'   => 'nullable|in:0,1',
                'question_17'   => 'nullable|string',
                'requirement_4' => 'nullable|string',
            ]);
        }

        $request->validate($rules);

        // File size validation for non-chunked uploads (logo, icon, background)
        foreach (['logo', 'icon', 'app_background'] as $field) {
            if ($request->hasFile($field) && $request->file($field)->getSize() > 5 * 1024 * 1024) {
                return back()->withErrors([$field => $request->file($field)->getClientOriginalName() . ' exceeds the 5MB limit.'])->withInput();
            }
        }

        $ssoUser   = session('auth_user');
        $lastId    = CustomizationRequest::max('id') ?? 0;
        $refNumber = 'REQ' . date('mY') . ($lastId + 1);

        $custRequest = CustomizationRequest::create([
            'request_type'        => $request->input('request_type', 'customization'),
            'ref_number'          => $refNumber,
            'user_id'             => $ssoUser['user_id'],
            'user_email'          => $ssoUser['email'],
            'user_name'           => $ssoUser['name'],
            'first_name'          => $request->first_name,
            'last_name'           => $request->last_name,
            'email'               => $request->email,
            'phone'               => $request->phone,
            'sec_phone'           => $request->sec_phone,
            'company_name'        => $request->company_name,
            'company_phone'       => $request->company_phone,
            'company_address'     => $request->company_address,
            'req_primary_color'   => $request->req_primary_color,
            'req_sec_color'       => $request->req_sec_color,
            'request_description' => $request->request_description,
            'req_logo'            => $request->boolean('req_logo'),
            'req_icon'            => $request->boolean('req_icon'),
            'req_app_background'  => $request->boolean('req_app_background'),
            'req_landing_page'    => $request->boolean('req_landing_page'),
            'req_others'          => $request->boolean('req_others'),
            'login_email'         => $request->login_email,
            'login_password'      => $request->login_password,
            'status'              => CustomizationRequest::STATUS_NEW,
            'pay_type'            => CustomizationRequest::PAY_TYPE_FREE,
        ]);

        foreach (self::QUESTIONS as $key => $text) {
            if ($request->filled($key)) {
                CustomizationAnswer::create([
                    'request_id'    => $custRequest->id,
                    'question_key'  => $key,
                    'question_text' => $text,
                    'answer'        => $request->input($key),
                ]);
            }
        }

        // Store logo/icon/background via traditional upload
        foreach (['logo', 'icon', 'app_background'] as $category) {
            if ($request->hasFile($category)) {
                $this->storeFile($request->file($category), $custRequest->id, $category, $ssoUser['user_id']);
            }
        }

        // Link all chunked file uploads (stored in session during upload)
        if ($uploads = session('pending_chunk_uploads')) {
            foreach ($uploads as $fileData) {
                CustomizationFile::create([
                    'request_id'       => $custRequest->id,
                    'uploaded_by_type' => 'user',
                    'uploaded_by_id'   => $ssoUser['user_id'],
                    'file_category'    => $fileData['file_category'],
                    'original_name'    => $fileData['original_name'],
                    'extension'        => $fileData['extension'],
                    'size_bytes'       => $fileData['size_bytes'],
                    'bunny_path'       => $fileData['bunny_path'],
                    'bunny_synced'     => $fileData['bunny_synced'],
                    'local_path'       => $fileData['local_path'],
                ]);
            }
            session()->forget('pending_chunk_uploads');
        }

        activity('customization')
            ->performedOn($custRequest)
            ->withProperties(['user_id' => $ssoUser['user_id']])
            ->log('request_created');

        // In-app notification for staff
        PortalNotification::notifyNewRequest($custRequest);

        // Notify configured email about the new request
        $notifyEmail = config('mail.notification_email');
        if ($notifyEmail) {
            try {
                Mail::to($notifyEmail)->send(new RequestNotificationMail($custRequest, 'new'));
            } catch (\Throwable $e) {
                \Log::warning('Failed to send new-request notification: ' . $e->getMessage());
            }
        }

        return redirect()->route('user.dashboard')
            ->with('success', 'Request submitted successfully! Reference: ' . $custRequest->ref_number);
    }

    public function show(string $cuid)
    {
        $customizationRequest = CustomizationRequest::where('cuid', $cuid)->firstOrFail();
        $this->authorizeSsoUser($customizationRequest);
        $customizationRequest->load(['answers', 'files', 'primaryTechnician']);

        return view('user.request.show', compact('customizationRequest'));
    }

    /**
     * Edit form — customer can edit their own request only while it's
     * still Pending (0) or Assigned (1). Once a technician starts, it's locked.
     */
    public function edit(string $cuid)
    {
        $customizationRequest = CustomizationRequest::where('cuid', $cuid)->firstOrFail();
        $this->authorizeSsoUser($customizationRequest);

        abort_unless(
            in_array($customizationRequest->status, [CustomizationRequest::STATUS_PENDING, CustomizationRequest::STATUS_ASSIGNED]),
            403,
            'This request can no longer be edited because it is already in progress.'
        );

        $customizationRequest->load(['answers', 'files']);
        return view('user.request.edit', compact('customizationRequest'));
    }

    /**
     * Persist edits, log changes via spatie activitylog.
     */
    public function update(Request $request, string $cuid)
    {
        $customizationRequest = CustomizationRequest::where('cuid', $cuid)->firstOrFail();
        $this->authorizeSsoUser($customizationRequest);

        abort_unless(
            in_array($customizationRequest->status, [CustomizationRequest::STATUS_PENDING, CustomizationRequest::STATUS_ASSIGNED]),
            403,
            'This request can no longer be edited.'
        );

        $request->validate([
            'first_name'          => 'required|string|max:100',
            'last_name'           => 'required|string|max:100',
            'email'               => 'required|email',
            'phone'               => 'required|string|max:20',
            'sec_phone'           => 'nullable|string|max:20',
            'company_name'        => 'required|string|max:200',
            'company_phone'       => 'required|string|max:200',
            'company_address'     => 'nullable|string',
            'req_primary_color'   => 'nullable|string|max:20',
            'req_sec_color'       => 'nullable|string|max:20',
            'request_description' => 'nullable|string',
        ]);

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

        $customizationRequest->update($data);

        // Diff only changed fields
        $changed = [];
        foreach ($editable as $field) {
            if ($old[$field] != $customizationRequest->$field) {
                $changed[$field] = ['old' => $old[$field], 'new' => $customizationRequest->$field];
            }
        }

        // Persist questionary answers (upsert)
        $oldAnswers = CustomizationAnswer::where('request_id', $customizationRequest->id)
            ->pluck('answer', 'question_key')->toArray();

        foreach (self::QUESTIONS as $key => $text) {
            $newValue = $request->input($key);
            if ($newValue === null || $newValue === '') continue;
            $oldValue = $oldAnswers[$key] ?? null;
            if ((string) $oldValue !== (string) $newValue) {
                $changed[$key] = ['old' => $oldValue, 'new' => $newValue];
            }
            CustomizationAnswer::updateOrCreate(
                ['request_id' => $customizationRequest->id, 'question_key' => $key],
                ['question_text' => $text, 'answer' => $newValue]
            );
        }

        // Delete files marked for removal
        if ($request->filled('delete_files')) {
            $deleteIds = array_filter((array) $request->input('delete_files'));
            if (!empty($deleteIds)) {
                $filesToDelete = CustomizationFile::where('request_id', $customizationRequest->id)
                    ->whereIn('id', $deleteIds)->get();
                foreach ($filesToDelete as $fileRecord) {
                    if ($fileRecord->bunny_path && $this->bunny->isConfigured()) {
                        try { $this->bunny->delete($fileRecord->bunny_path); } catch (\Exception) {}
                    }
                    $fileRecord->delete();
                }
                $changed['files_deleted'] = count($filesToDelete) . ' file(s) removed';
            }
        }

        // Link chunked file uploads from session
        $ssoUser = session('auth_user');
        if ($uploads = session('pending_chunk_uploads')) {
            foreach ($uploads as $fileData) {
                CustomizationFile::create([
                    'request_id'       => $customizationRequest->id,
                    'uploaded_by_type' => 'user',
                    'uploaded_by_id'   => $ssoUser['user_id'],
                    'file_category'    => $fileData['file_category'],
                    'original_name'    => $fileData['original_name'],
                    'extension'        => $fileData['extension'],
                    'size_bytes'       => $fileData['size_bytes'],
                    'bunny_path'       => $fileData['bunny_path'],
                    'bunny_synced'     => $fileData['bunny_synced'],
                    'local_path'       => $fileData['local_path'],
                ]);
            }
            session()->forget('pending_chunk_uploads');
            $changed['files_added'] = count($uploads) . ' file(s) uploaded';
        }

        if (!empty($changed)) {
            activity('customization')
                ->performedOn($customizationRequest)
                ->withProperties([
                    'changes' => $changed,
                    'edited_by' => 'customer',
                    'user_id' => session('auth_user.user_id'),
                ])
                ->log('request_edited');
        }

        return redirect()->route('user.dashboard')
            ->with('success', 'Request updated successfully.');
    }

    private function storeFile($file, int $requestId, string $category, int $userId): void
    {
        $extension  = strtolower($file->getClientOriginalExtension());
        $fileRecord = CustomizationFile::create([
            'request_id'       => $requestId,
            'uploaded_by_type' => 'user',
            'uploaded_by_id'   => $userId,
            'file_category'    => $category,
            'original_name'    => $file->getClientOriginalName(),
            'extension'        => $extension,
            'size_bytes'       => $file->getSize(),
        ]);

        if ($this->bunny->isConfigured()) {
            try {
                $bunnyPath = $this->bunny->upload($file, "requests/{$category}s");
                $fileRecord->update(['bunny_path' => $bunnyPath, 'bunny_synced' => true]);
            } catch (\Exception) {
                $filename = time() . '_' . uniqid() . '.' . $extension;
                $file->move(public_path("uploads/requests/{$category}s"), $filename);
                $fileRecord->update(['local_path' => "/uploads/requests/{$category}s/{$filename}"]);
            }
        } else {
            $filename = time() . '_' . uniqid() . '.' . $extension;
            $file->move(public_path("uploads/requests/{$category}s"), $filename);
            $fileRecord->update(['local_path' => "/uploads/requests/{$category}s/{$filename}"]);
        }
    }

    public function downloadFile(string $cuid, CustomizationFile $file)
    {
        $customizationRequest = CustomizationRequest::where('cuid', $cuid)->firstOrFail();
        $this->authorizeSsoUser($customizationRequest);

        abort_unless($file->request_id === $customizationRequest->id, 404);

        $bunny = app(BunnyStorageService::class);

        if ($file->bunny_path && $bunny->isConfigured()) {
            $url = $bunny->signedUrl($file->bunny_path);
            $response = \Illuminate\Support\Facades\Http::withOptions(['timeout' => 120])->get($url);
            abort_unless($response->successful(), 404);

            return response($response->body(), 200, [
                'Content-Type'        => 'application/octet-stream',
                'Content-Disposition' => 'attachment; filename="' . $file->original_name . '"',
                'Content-Length'      => strlen($response->body()),
            ]);
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
        $this->authorizeSsoUser($customizationRequest);

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
                $response = \Illuminate\Support\Facades\Http::withOptions(['timeout' => 120])->get($url);
                if ($response->successful()) {
                    $zip->addFromString($file->original_name, $response->body());
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

    private function authorizeSsoUser(CustomizationRequest $request): void
    {
        if ($request->user_id != session('auth_user.user_id')) {
            abort(403);
        }
    }

    /**
     * Customer approves the work once the team has marked it as "Approved by Team".
     * POST /request/{cuid}/approve
     */
    public function approve(string $cuid)
    {
        $customizationRequest = CustomizationRequest::where('cuid', $cuid)->firstOrFail();
        $this->authorizeSsoUser($customizationRequest);

        abort_unless(
            $customizationRequest->status === CustomizationRequest::STATUS_TEAM_APPROVED,
            422,
            'This request is not yet ready for your approval.'
        );

        $customizationRequest->update([
            'status' => CustomizationRequest::STATUS_APPROVED,
        ]);

        activity('customization')
            ->performedOn($customizationRequest)
            ->withProperties([
                'approved_by' => 'customer',
                'user_id'     => session('auth_user.user_id'),
            ])
            ->log('client_approved');

        // Notify staff
        \App\Models\PortalNotification::create([
            'type'            => 'client_approved',
            'title'           => 'Customer Approved Work',
            'body'            => "{$customizationRequest->first_name} {$customizationRequest->last_name} has approved the work on request {$customizationRequest->ref_number}.",
            'icon'            => 'fas fa-thumbs-up',
            'action_url'      => route('admin.requests.show', $customizationRequest->cuid),
            'action_label'    => 'View Request',
            'sender_name'     => trim("{$customizationRequest->first_name} {$customizationRequest->last_name}"),
            'ref_number'      => $customizationRequest->ref_number,
            'notifiable_type' => 'staff',
            'notifiable_id'   => null,
        ]);

        return redirect()
            ->route('user.request.show', $customizationRequest->cuid)
            ->with('success', 'Thank you! You have approved the work.');
    }
}
