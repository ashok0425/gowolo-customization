@extends('layouts.app')
@section('title', 'Request — ' . $customizationRequest->ref_number)

@section('content')
<div class="page-header">
    <h4 class="page-title">Request #{{ $customizationRequest->ref_number }}</h4>
    <ul class="breadcrumbs">
        <li class="nav-home"><a href="{{ route('user.dashboard') }}"><i class="flaticon-home"></i></a></li>
        <li class="separator"><i class="flaticon-right-arrow"></i></li>
        <li class="nav-item"><a href="{{ route('user.dashboard') }}">My Requests</a></li>
        <li class="separator"><i class="flaticon-right-arrow"></i></li>
        <li class="nav-item"><a href="#">{{ $customizationRequest->ref_number }}</a></li>
    </ul>
</div>

<div class="row">
    <div class="col-md-8">
        {{-- Status Banner --}}
        @php
            $statusConfig = [
                0 => ['border' => 'border-warning',   'icon' => 'fa-clock text-warning',         'msg' => 'Your request has been received and is awaiting assignment.'],
                1 => ['border' => 'border-info',      'icon' => 'fa-user-check text-info',        'msg' => 'A technician has been assigned and will start working shortly.'],
                2 => ['border' => 'border-primary',   'icon' => 'fa-search text-primary',         'msg' => 'Your request is currently under review by our team.'],
                3 => ['border' => 'border-secondary', 'icon' => 'fa-paper-plane text-secondary',  'msg' => 'We have sent the work to you for review. Please check and respond.'],
                4 => ['border' => 'border-success',   'icon' => 'fa-thumbs-up text-success',      'msg' => 'The customization has been approved and is being finalized.'],
                5 => ['border' => 'border-dark',      'icon' => 'fa-check-circle text-dark',      'msg' => 'Your customization is complete! Delivered on ' . ($customizationRequest->date_complete?->format('M d, Y') ?? '—') . '.'],
                6 => ['border' => 'border-success',   'icon' => 'fa-user-check text-success',     'msg' => 'You have approved this customization. Our team will finalize and deliver it.'],
                7 => ['border' => 'border-success',   'icon' => 'fa-users text-success',          'msg' => 'Our team has approved the work. Finalizing delivery shortly.'],
            ];
            $sc = $statusConfig[$customizationRequest->status] ?? $statusConfig[0];
        @endphp
        <div class="card mb-3 {{ $sc['border'] }}" style="border-left-width:4px!important">
            <div class="card-body py-3">
                <div class="d-flex align-items-center">
                    <i class="fas {{ $sc['icon'] }} fa-2x mr-3"></i>
                    <div>
                        <strong>{{ $customizationRequest->status_label }}</strong><br>
                        <small class="text-muted">{{ $sc['msg'] }}</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Details --}}
        <div class="card">
            <div class="card-header"><h4 class="card-title">Request Details</h4></div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-4">Reference #</dt>
                    <dd class="col-sm-8"><strong>{{ $customizationRequest->ref_number }}</strong></dd>
                    <dt class="col-sm-4">Company</dt>
                    <dd class="col-sm-8">{{ $customizationRequest->company_name }}</dd>
                    <dt class="col-sm-4">Phone</dt>
                    <dd class="col-sm-8">{{ $customizationRequest->phone }}</dd>
                    <dt class="col-sm-4">Submitted</dt>
                    <dd class="col-sm-8">{{ $customizationRequest->created_at->format('M d, Y H:i') }}</dd>
                    @if($customizationRequest->primaryTechnician)
                    <dt class="col-sm-4">Assigned To</dt>
                    <dd class="col-sm-8">{{ $customizationRequest->primaryTechnician->full_name }}</dd>
                    @endif
                    @if($customizationRequest->num_of_days)
                    <dt class="col-sm-4">Days Taken</dt>
                    <dd class="col-sm-8">{{ $customizationRequest->num_of_days }} business days</dd>
                    @endif
                </dl>

                @if($customizationRequest->request_description)
                <hr>
                <p><strong>Description:</strong></p>
                <p class="text-muted">{{ $customizationRequest->request_description }}</p>
                @endif
            </div>
        </div>

        {{-- Answers --}}
        @if($customizationRequest->answers->count())
        <div class="card">
            <div class="card-header"><h4 class="card-title">Your Answers</h4></div>
            <div class="card-body">
                <dl class="row">
                    @foreach($customizationRequest->answers as $answer)
                    <dt class="col-sm-5 text-muted">{{ $answer->question_text }}</dt>
                    <dd class="col-sm-7">{{ $answer->answer }}</dd>
                    @endforeach
                </dl>
            </div>
        </div>
        @endif

        {{-- Files --}}
        @if($customizationRequest->files->count())
        <div class="card">
            <div class="card-header"><h4 class="card-title">Uploaded Files</h4></div>
            <div class="card-body">
                @php $bunny = app(\App\Services\BunnyStorageService::class); @endphp
                <div class="table-responsive">
                    <table class="table table-sm align-middle">
                        <thead><tr><th>Preview</th><th>File</th><th>Category</th><th>Size</th><th>Action</th></tr></thead>
                        <tbody>
                            @foreach($customizationRequest->files as $file)
                            @php
                                $fileUrl = null;
                                if ($file->bunny_path && $bunny->isConfigured()) {
                                    $fileUrl = $bunny->signedUrl($file->bunny_path);
                                } elseif ($file->local_path) {
                                    $fileUrl = asset($file->local_path);
                                }
                            @endphp
                            <tr>
                                <td style="width:80px;">
                                    @if($file->is_image && $fileUrl)
                                        <a href="#" class="imgfile" data-toggle="modal" data-id="{{ $fileUrl }}">
                                            <img src="{{ $fileUrl }}" alt="{{ $file->original_name }}" style="max-width:60px;max-height:60px;border-radius:4px;cursor:pointer;">
                                        </a>
                                    @elseif($file->is_pdf)
                                        <i class="fas fa-file-pdf fa-2x text-danger"></i>
                                    @else
                                        <i class="fas fa-file fa-2x text-secondary"></i>
                                    @endif
                                </td>
                                <td>{{ $file->original_name }}</td>
                                <td><span class="badge badge-secondary">{{ $file->file_category }}</span></td>
                                <td>{{ number_format($file->size_bytes / 1024, 1) }} KB</td>
                                <td>
                                    <a href="{{ route('user.request.file.download', [$customizationRequest->cuid, $file->id]) }}" class="btn btn-sm btn-outline-primary" title="Download">
                                        <i class="fas fa-download"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Image preview modal --}}
        <div class="modal fade" id="filePreviewModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Image Preview</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body text-center" id="filePreviewBody"></div>
                </div>
            </div>
        </div>
        @endif

        {{-- Technician comments --}}
        @if($customizationRequest->technician_comments)
        <div class="card">
            <div class="card-header"><h4 class="card-title">Comments from Our Team</h4></div>
            <div class="card-body">
                <p>{{ $customizationRequest->technician_comments }}</p>
            </div>
        </div>
        @endif
    </div>

    <div class="col-md-4">
        {{-- Payment --}}
        @php
            $payBase = rtrim(config('services.dashboardv2.make_payment_url'), '/');
            $uid     = base64_encode(session('auth_user.email') ?? $customizationRequest->email);
            $paymentUrl = $payBase . '?uid=' . $uid . '&type=custom&id=' . $customizationRequest->id;
        @endphp
        <div class="card">
            <div class="card-header"><h4 class="card-title">Payment</h4></div>
            <div class="card-body text-center">
                @if($customizationRequest->pay_type == 1)
                    <i class="fas fa-gift fa-3x text-success mb-2 d-block"></i>
                    <p class="mb-0">This is a <strong>Free</strong> customization.</p>
                @elseif($customizationRequest->pay_status == 1)
                    <i class="fas fa-check-circle fa-3x text-success mb-2 d-block"></i>
                    <p class="mb-0">Payment <strong>Received</strong></p>
                    <p class="text-muted">${{ number_format($customizationRequest->pay_amount, 2) }}</p>
                    <a href="{{ route('documents.invoice', $customizationRequest->cuid) }}"
                       class="btn btn-block btn-outline-success mt-2" target="_blank">
                        <i class="fas fa-file-download mr-1"></i> Download Invoice
                    </a>
                @elseif($customizationRequest->pay_type == 2 && !empty($customizationRequest->pay_amount))
                    <i class="fas fa-credit-card fa-3x text-primary mb-2 d-block" style="color:#662c87!important;"></i>
                    <p class="mb-1">Amount Due</p>
                    <h3 class="font-weight-bold mb-3" style="color:#662c87;">${{ number_format($customizationRequest->pay_amount, 2) }}</h3>
                    <a href="{{ $paymentUrl }}" target="_blank" class="btn btn-block"
                       style="background:#662c87;color:#fff;border-radius:50px;padding:10px 20px;font-weight:600;">
                        <i class="fas fa-credit-card mr-2"></i> Pay Now
                    </a>
                    <a href="{{ route('documents.quotation', $customizationRequest->cuid) }}"
                       class="btn btn-block btn-outline-secondary mt-2" target="_blank">
                        <i class="fas fa-file-pdf mr-1"></i> Download Quotation
                    </a>
                    <small class="text-muted d-block mt-2">You'll be redirected to our secure payment gateway.</small>
                @else
                    <i class="fas fa-clock fa-3x text-warning mb-2 d-block"></i>
                    <p class="mb-0">Awaiting Price</p>
                    <small class="text-muted">Our team will set the price shortly.</small>
                @endif
            </div>
        </div>

        {{-- Approve Work — only when team has approved --}}
        @if($customizationRequest->status === \App\Models\CustomizationRequest::STATUS_TEAM_APPROVED)
        <div class="card" style="border: 2px solid #27ae60;">
            <div class="card-body text-center">
                <i class="fas fa-user-check fa-2x text-success mb-2 d-block"></i>
                <h6 class="mb-2">Our team has approved the work.</h6>
                <p class="text-muted small mb-3">Please review and approve to finalize.</p>
                <button type="button" class="btn btn-success btn-block" data-toggle="modal" data-target="#approveWorkModal"
                        style="border-radius:50px;padding:10px;font-weight:600;">
                    <i class="fas fa-thumbs-up mr-1"></i> Approve the Work
                </button>
            </div>
        </div>

        {{-- Approve Work Confirmation Modal --}}
        <div class="modal fade" id="approveWorkModal" tabindex="-1" role="dialog" aria-labelledby="approveWorkModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header" style="background:#27ae60;color:#fff;">
                        <h5 class="modal-title" id="approveWorkModalLabel">
                            <i class="fas fa-thumbs-up mr-2"></i> Approve Work
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color:#fff;text-shadow:none;opacity:1;">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body p-4 text-center">
                        <i class="fas fa-question-circle fa-3x text-success mb-3 d-block"></i>
                        <h5>Are you sure?</h5>
                        <p class="text-muted mb-0">You are about to approve the work for request <strong>#{{ $customizationRequest->ref_number }}</strong>. This action cannot be undone.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <form method="POST" action="{{ route('user.request.approve', $customizationRequest->cuid) }}" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-thumbs-up mr-1"></i> Yes, Approve
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Actions --}}
        <div class="card">
            <div class="card-body">
                <a href="{{ route('user.chat.show', $customizationRequest) }}" class="btn btn-info btn-block mb-2">
                    <i class="fas fa-comment mr-1"></i> Chat with Support
                </a>
                <a href="{{ route('user.dashboard') }}" class="btn btn-secondary btn-block">
                    <i class="fas fa-arrow-left mr-1"></i> Back to My Requests
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
$(document).on('click', '.imgfile', function(e) {
    e.preventDefault();
    var src = $(this).attr('data-id');
    $('#filePreviewBody').html('<img src="' + src + '" style="max-width:100%;height:auto;">');
    $('#filePreviewModal').modal('show');
});
</script>
@endpush
