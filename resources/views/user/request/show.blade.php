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
        <div class="card mb-3
            @if($customizationRequest->status == 0) border-warning
            @elseif($customizationRequest->status == 1) border-info
            @else border-success @endif" style="border-left-width:4px!important">
            <div class="card-body py-3">
                <div class="d-flex align-items-center">
                    @if($customizationRequest->status == 0)
                        <i class="fas fa-clock fa-2x text-warning mr-3"></i>
                        <div><strong>Awaiting Review</strong><br><small class="text-muted">Your request has been received and will be reviewed shortly.</small></div>
                    @elseif($customizationRequest->status == 1)
                        <i class="fas fa-spinner fa-spin fa-2x text-info mr-3"></i>
                        <div><strong>In Progress</strong><br><small class="text-muted">Our team is working on your customization.</small></div>
                    @else
                        <i class="fas fa-check-circle fa-2x text-success mr-3"></i>
                        <div><strong>Completed</strong><br><small class="text-muted">Your customization has been completed on {{ $customizationRequest->date_complete?->format('M d, Y') }}.</small></div>
                    @endif
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
        <div class="card">
            <div class="card-header"><h4 class="card-title">Payment</h4></div>
            <div class="card-body text-center">
                @if($customizationRequest->pay_type == 1)
                    <i class="fas fa-gift fa-3x text-success mb-2 d-block"></i>
                    <p class="mb-0">This is a <strong>Free</strong> customization.</p>
                @elseif($customizationRequest->pay_status)
                    <i class="fas fa-check-circle fa-3x text-success mb-2 d-block"></i>
                    <p class="mb-0">Payment <strong>Received</strong></p>
                    <p class="text-muted">${{ number_format($customizationRequest->pay_amount, 2) }}</p>
                @else
                    <i class="fas fa-exclamation-circle fa-3x text-warning mb-2 d-block"></i>
                    <p class="mb-0">Payment <strong>Pending</strong></p>
                    @if($customizationRequest->pay_amount > 0)
                    <p class="text-muted">${{ number_format($customizationRequest->pay_amount, 2) }}</p>
                    @endif
                @endif
            </div>
        </div>

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
