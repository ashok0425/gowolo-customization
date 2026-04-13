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
            ->get();

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
        // Base validation (always required)
        $rules = [
            'first_name'        => 'required|string|max:100',
            'last_name'         => 'required|string|max:100',
            'email'             => 'required|email',
            'phone'             => 'required|string|max:20',
            'company_name'      => 'required|string|max:200',
            'company_phone'     => 'required|string|max:200',
            'company_address'   => 'nullable|string',
            'req_primary_color' => 'required|string|max:20',
            'req_sec_color'     => 'required|string|max:20',
            'addition_feature'  => 'required|in:0,1',
        ];

        // Questionary is only required when "Additional Features = Yes"
        if ($request->input('addition_feature') == '1') {
            $rules = array_merge($rules, [
                'question_1'    => 'required|string',
                'question_2'    => 'required|string',
                'question_3'    => 'required|string',
                'question_4'    => 'required|string',
                'question_5'    => 'required|string',
                'question_11'   => 'required|in:0,1',
                'question_12'   => 'required|in:0,1',
                'question_13'   => 'required|in:0,1',
                'question_14'   => 'required|in:0,1',
                'question_15'   => 'required|in:0,1',
                'question_16'   => 'required|in:0,1',
                'question_17'   => 'required|string',
                'requirement_4' => 'required|string',
            ]);
        }

        $request->validate($rules);

        $ssoUser   = session('auth_user');
        $lastId    = CustomizationRequest::max('id') ?? 0;
        $refNumber = 'REQ' . date('mY') . ($lastId + 1);

        $custRequest = CustomizationRequest::create([
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

        foreach (['logo', 'icon', 'app_background', 'document'] as $category) {
            if ($request->hasFile($category)) {
                $this->storeFile($request->file($category), $custRequest->id, $category, $ssoUser['user_id']);
            }
        }

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $this->storeFile($file, $custRequest->id, 'attachment', $ssoUser['user_id']);
            }
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

    public function show(CustomizationRequest $customizationRequest)
    {
        $this->authorizeSsoUser($customizationRequest);
        $customizationRequest->load(['answers', 'files', 'primaryTechnician']);

        return view('user.request.show', compact('customizationRequest'));
    }

    /**
     * Edit form — customer can edit their own request only while it's
     * still Pending (0) or Assigned (1). Once a technician starts, it's locked.
     */
    public function edit(CustomizationRequest $customizationRequest)
    {
        $this->authorizeSsoUser($customizationRequest);

        abort_unless(
            in_array($customizationRequest->status, [CustomizationRequest::STATUS_PENDING, CustomizationRequest::STATUS_ASSIGNED]),
            403,
            'This request can no longer be edited because it is already in progress.'
        );

        $customizationRequest->load('answers');
        return view('user.request.edit', compact('customizationRequest'));
    }

    /**
     * Persist edits, log changes via spatie activitylog.
     */
    public function update(Request $request, CustomizationRequest $customizationRequest)
    {
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

    private function authorizeSsoUser(CustomizationRequest $request): void
    {
        if ($request->user_id != session('auth_user.user_id')) {
            abort(403);
        }
    }
}
