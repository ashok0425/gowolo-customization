@extends('layouts.app')
@section('title', 'Edit Request — ' . $customizationRequest->ref_number)

@php
    // Index answers by question_key for prefill
    $answers = $customizationRequest->answers->pluck('answer', 'question_key')->toArray();
@endphp

@section('content')
<div class="page-header">
    <h4 class="page-title">Edit Request #{{ $customizationRequest->ref_number }}</h4>
    <ul class="breadcrumbs">
        <li class="nav-home"><a href="{{ route('user.dashboard') }}"><i class="flaticon-home"></i></a></li>
        <li class="separator"><i class="flaticon-right-arrow"></i></li>
        <li class="nav-item"><a href="#">Edit</a></li>
    </ul>
</div>

@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show">
    <ul class="mb-0">
        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
    </ul>
    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
</div>
@endif

<div class="card">
    <div class="card-header">
        <h4 class="card-title">Edit Your Request</h4>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('user.request.update', $customizationRequest) }}">
            @csrf @method('PUT')

            <h6 class="mb-3 mt-2 font-weight-bold">Personal Information</h6>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>First Name <span class="text-danger">*</span></label>
                        <input type="text" name="first_name" class="form-control" value="{{ old('first_name', $customizationRequest->first_name) }}" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Last Name <span class="text-danger">*</span></label>
                        <input type="text" name="last_name" class="form-control" value="{{ old('last_name', $customizationRequest->last_name) }}" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $customizationRequest->email) }}" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Phone <span class="text-danger">*</span></label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone', $customizationRequest->phone) }}" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Secondary Phone</label>
                        <input type="text" name="sec_phone" class="form-control" value="{{ old('sec_phone', $customizationRequest->sec_phone) }}">
                    </div>
                </div>
            </div>

            <h6 class="mb-3 mt-4 font-weight-bold">Community Information</h6>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Community Name <span class="text-danger">*</span></label>
                        <input type="text" name="company_name" class="form-control" value="{{ old('company_name', $customizationRequest->company_name) }}" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Community Handle Name <span class="text-danger">*</span></label>
                        <input type="text" name="company_phone" class="form-control" value="{{ old('company_phone', $customizationRequest->company_phone) }}" required>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Community Domain Name</label>
                        <input type="text" name="company_address" class="form-control" value="{{ old('company_address', $customizationRequest->company_address) }}">
                    </div>
                </div>
            </div>

            <h6 class="mb-3 mt-4 font-weight-bold">Requirements</h6>
            <div class="row">
                @foreach([
                    'req_logo'           => 'Logo',
                    'req_icon'           => 'Web Icon',
                    'req_app_background' => 'App Background Image',
                    'req_landing_page'   => 'Landing Page',
                    'req_others'         => 'Others',
                ] as $field => $label)
                <div class="col-md-6 mb-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="{{ $field }}" id="edit_{{ $field }}" value="1" {{ old($field, $customizationRequest->$field) ? 'checked' : '' }}>
                        <label class="form-check-label" for="edit_{{ $field }}">{{ $label }}</label>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Primary Color</label>
                        <input type="text" name="req_primary_color" class="form-control" value="{{ old('req_primary_color', $customizationRequest->req_primary_color) }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Secondary Color</label>
                        <input type="text" name="req_sec_color" class="form-control" value="{{ old('req_sec_color', $customizationRequest->req_sec_color) }}">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Request Description</label>
                        <textarea name="request_description" class="form-control" rows="4">{{ old('request_description', $customizationRequest->request_description) }}</textarea>
                    </div>
                </div>
            </div>

            <h6 class="mb-3 mt-4 font-weight-bold">Additional Information</h6>

            <div class="form-group">
                <label>What domain name would you like to be displayed in your website?</label>
                <small class="d-block text-muted mb-1">(You can promote multiple websites in your platform.)</small>
                <textarea name="question_1" class="form-control" rows="3">{{ old('question_1', $answers['question_1'] ?? '') }}</textarea>
            </div>

            <div class="form-group">
                <label>What are your gifts, talents, products and/or services and what are you passionate about?</label>
                <textarea name="question_2" class="form-control" rows="3">{{ old('question_2', $answers['question_2'] ?? '') }}</textarea>
            </div>

            <div class="form-group">
                <label>If you never got paid for it, what could you do for the rest of your life that brings you happiness?</label>
                <textarea name="question_3" class="form-control" rows="3">{{ old('question_3', $answers['question_3'] ?? '') }}</textarea>
            </div>

            <div class="form-group">
                <label>List 5 things you love to do in order of importance.</label>
                <textarea name="question_4" class="form-control" rows="3">{{ old('question_4', $answers['question_4'] ?? '') }}</textarea>
            </div>

            <div class="form-group">
                <label>How many followers do you have on other platforms</label>
                <select name="question_5" class="form-control">
                    <option value="">— Select —</option>
                    @foreach(['0-500','500-5000','5000-50000','50000 or more'] as $opt)
                    <option value="{{ $opt }}" {{ old('question_5', $answers['question_5'] ?? '') === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                    @endforeach
                </select>
            </div>

            @php
                $yesNoQuestions = [
                    'question_11' => 'Do you have a thumbnail image for your content management or master courses?',
                    'question_12' => 'Can you provide us with your website content for your landing page?',
                    'question_13' => 'Can you provide us with your campaign content for your lead capture page?',
                    'question_14' => 'Do you have product images for your e-commerce store?',
                    'question_15' => 'Do you have a banner image for your e-commerce store?',
                    'question_16' => 'Do you have any videos for your landing page, e-commerce store or master courses?',
                ];
            @endphp

            @foreach($yesNoQuestions as $key => $label)
            <div class="form-group">
                <label>{{ $label }}</label>
                <div>
                    <label class="mr-3"><input type="radio" name="{{ $key }}" value="1" {{ old($key, $answers[$key] ?? '') === '1' ? 'checked' : '' }}> Yes</label>
                    <label><input type="radio" name="{{ $key }}" value="0" {{ old($key, $answers[$key] ?? '') === '0' ? 'checked' : '' }}> No</label>
                </div>
            </div>
            @endforeach

            <div class="form-group">
                <label>What would you like to do in your VIP to share your gift, talent, products and/or services with your VIP followers?</label>
                <small class="d-block text-muted mb-1">(Ex: Teach/train, worship, etc)</small>
                <textarea name="question_17" class="form-control" rows="3">{{ old('question_17', $answers['question_17'] ?? '') }}</textarea>
            </div>

            <div class="form-group">
                <label>How will you use this order? <small class="text-muted">(optional)</small></label>
                <textarea name="requirement_1" class="form-control" rows="3">{{ old('requirement_1', $answers['requirement_1'] ?? '') }}</textarea>
            </div>

            <div class="form-group">
                <label>Which industry is most relevant to your order? <small class="text-muted">(optional)</small></label>
                <textarea name="requirement_2" class="form-control" rows="3">{{ old('requirement_2', $answers['requirement_2'] ?? '') }}</textarea>
            </div>

            <div class="form-group">
                <label>What are you looking to achieve with this order? <small class="text-muted">(optional)</small></label>
                <textarea name="requirement_3" class="form-control" rows="3">{{ old('requirement_3', $answers['requirement_3'] ?? '') }}</textarea>
            </div>

            <div class="form-group">
                <label>Relevant data</label>
                <textarea name="requirement_4" class="form-control" rows="3">{{ old('requirement_4', $answers['requirement_4'] ?? '') }}</textarea>
            </div>

            <div class="d-flex justify-content-between mt-3">
                <a href="{{ route('user.dashboard') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Save Changes</button>
            </div>
        </form>
    </div>
</div>
@endsection
