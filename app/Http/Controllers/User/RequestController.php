<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\CustomizationAnswer;
use App\Models\CustomizationFile;
use App\Models\CustomizationRequest;
use App\Mail\RequestNotificationMail;
use App\Services\BunnyStorageService;
use App\Services\SiteInfoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class RequestController extends Controller
{
    const QUESTIONS = [
        'question_1'  => 'What is the name of your community/business?',
        'question_2'  => 'What is your community handle name?',
        'question_3'  => 'What is your desired domain name?',
        'question_4'  => 'Describe your primary brand colors',
        'question_5'  => 'Do you have an existing logo?',
        'question_6'  => 'Do you have an existing Gowolo account?',
        'question_7'  => 'What type of content will you share?',
        'question_8'  => 'What features are most important to you?',
        'question_9'  => 'How many members do you expect?',
        'question_10' => 'What is your target audience?',
        'question_11' => 'Do you need donation/payment features?',
        'question_12' => 'Do you need a custom landing page?',
        'question_13' => 'What is your preferred app background style?',
        'question_14' => 'Do you need push notifications?',
        'question_15' => 'Do you have a website to integrate with?',
        'question_16' => 'What is your launch timeline?',
        'question_17' => 'Any additional requirements?',
        'requirement_1' => 'Logo requirement details',
        'requirement_2' => 'Icon requirement details',
        'requirement_3' => 'Background requirement details',
        'requirement_4' => 'Other requirement details',
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
        $request->validate([
            'first_name'      => 'required|string|max:100',
            'last_name'       => 'required|string|max:100',
            'email'           => 'required|email',
            'phone'           => 'required|string|max:20',
            'company_name'    => 'required|string|max:200',
            'company_phone'   => 'required|string|max:20',
            'company_address' => 'required|string',
            'question_1'      => 'required',
            'question_2'      => 'required',
            'question_3'      => 'required',
        ]);

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

        // Notify configured email about the new request
        $notifyEmail = config('mail.notification_email');
        if ($notifyEmail) {
            try {
                Mail::to($notifyEmail)->send(new RequestNotificationMail($custRequest, 'new'));
            } catch (\Throwable $e) {
                \Log::warning('Failed to send new-request notification: ' . $e->getMessage());
            }
        }

        return response()->json([
            'success'    => true,
            'message'    => 'Request submitted successfully.',
            'request_id' => $custRequest->id,
            'ref_number' => $custRequest->ref_number,
        ]);
    }

    public function show(CustomizationRequest $customizationRequest)
    {
        $this->authorizeSsoUser($customizationRequest);
        $customizationRequest->load(['answers', 'files', 'primaryTechnician']);

        return view('user.request.show', compact('customizationRequest'));
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
