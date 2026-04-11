@extends('layouts.app')
@section('title', 'New Customization Request')

@push('css')
<style>
    /* Progress steps — full-width bar */
    .progress-steps {
        display:flex; justify-content:space-between; align-items:flex-start;
        padding:25px 40px 10px; position:relative;
    }
    .progress-steps::before {
        content:''; position:absolute; top:45px; left:60px; right:60px;
        height:3px; background:#D1D5DB; z-index:0;
    }
    .step-wrap { text-align:center; position:relative; z-index:2; }
    .step-circle {
        width:40px; height:40px; border-radius:50%; display:flex; align-items:center; justify-content:center;
        font-weight:600; font-size:16px; margin:0 auto;
        background:#E5E7EB; color:#9CA3AF; transition:all 0.3s;
    }
    .step-circle.active { background:#662c87; color:#fff; }
    .step-circle.done { background:#27ae60; color:#fff; }
    .step-label { font-size:11px; margin-top:6px; color:#888; }

    /* Step panels */
    .step-panel { display:none; }
    .step-panel.active { display:block; }

    /* Checkbox pills */
    .req-pill { display:inline-block; margin:4px; }
    .req-pill input { display:none; }
    .req-pill label {
        display:inline-block; padding:8px 18px; border:2px solid #E5E7EB; border-radius:20px;
        font-size:13px; font-weight:500; color:#555; cursor:pointer; transition:all 0.2s;
    }
    .req-pill input:checked + label { border-color:#662c87; background:#f9f3fc; color:#662c87; font-weight:600; }

    /* Upload boxes */
    .upload-box {
        border:2px dashed #ddd; border-radius:12px; padding:20px; text-align:center;
        background:#fafbfc; cursor:pointer; transition:border-color 0.2s;
    }
    .upload-box:hover { border-color:#662c87; }
    .upload-box i { font-size:28px; color:#bbb; }
    .upload-box p { margin:8px 0 0; font-size:13px; color:#888; }
    .upload-box img.preview { max-height:80px; margin-top:8px; border-radius:8px; }

    /* Real-time validation */
    .form-control.is-invalid { border-color:#e74c3c !important; }
    .field-error { color:#e74c3c; font-size:12px; margin-top:4px; display:none; }
    .field-error.show { display:block; }
</style>
@endpush

@section('content')
<div class="page-header">
    <h4 class="page-title">Customization Request Form</h4>
</div>

{{-- Server-side validation errors --}}
@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <strong>Please fix the following errors:</strong>
    <ul class="mb-0 mt-1">
        @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
</div>
@endif

<div class="card">
    <div class="card-body">

        {{-- Step indicators --}}
        <div class="progress-steps">
            <div class="step-wrap">
                <div class="step-circle active" id="stepCircle1">1</div>
                <div class="step-label">Details</div>
            </div>
            <div class="step-wrap">
                <div class="step-circle" id="stepCircle2">2</div>
                <div class="step-label">Requirements</div>
            </div>
            <div class="step-wrap">
                <div class="step-circle" id="stepCircle3">3</div>
                <div class="step-label">Review</div>
            </div>
        </div>

        <form method="POST" action="{{ route('user.request.store') }}" enctype="multipart/form-data" id="request-form">
            @csrf

            {{-- ==================== STEP 1: Personal & Community Info ==================== --}}
            <div class="step-panel active" id="step1">
                <h5 class="mb-3 mt-4 font-weight-bold">Personal & Community Information</h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>First Name <span class="text-danger">*</span></label>
                            <input type="text" name="first_name" class="form-control" data-label="First Name"
                                   value="{{ old('first_name', explode(' ', session('auth_user.name', ''))[0] ?? '') }}" required>
                            <div class="field-error">First Name is required</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Last Name <span class="text-danger">*</span></label>
                            <input type="text" name="last_name" class="form-control" data-label="Last Name"
                                   value="{{ old('last_name', implode(' ', array_slice(explode(' ', session('auth_user.name', '')), 1))) }}" required>
                            <div class="field-error">Last Name is required</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Phone Number <span class="text-danger">*</span></label>
                            <input type="text" name="phone" class="form-control" data-label="Phone"
                                   value="{{ old('phone', session('auth_user.phone')) }}" required>
                            <div class="field-error">Phone Number is required</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Secondary Phone (Optional)</label>
                            <input type="text" name="sec_phone" class="form-control" value="{{ old('sec_phone') }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Email Address <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" data-label="Email"
                                   value="{{ old('email', session('auth_user.email')) }}" required>
                            <div class="field-error">Valid Email is required</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Community Name <span class="text-danger">*</span></label>
                            <input type="text" name="company_name" class="form-control" data-label="Community Name"
                                   value="{{ old('company_name') }}" placeholder="Your community/business name" required>
                            <div class="field-error">Community Name is required</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Community Handle Name <span class="text-danger">*</span></label>
                            <input type="text" name="company_phone" class="form-control" data-label="Community Handle Name"
                                   value="{{ old('company_phone') }}" placeholder="e.g. MyBrand" required>
                            <div class="field-error">Community Handle Name is required</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Community Domain Name <small class="text-muted">(optional)</small></label>
                            <input type="text" name="company_address" class="form-control"
                                   value="{{ old('company_address') }}" placeholder="http://yourdomain.gowolo.biz">
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-3">
                    <button type="button" class="btn btn-primary" onclick="goToStep(2)">Next <i class="fas fa-arrow-right ml-1"></i></button>
                </div>
            </div>

            {{-- ==================== STEP 2: Requirements & Uploads ==================== --}}
            <div class="step-panel" id="step2">
                <h5 class="mb-3 mt-4 font-weight-bold">Customization Requirements</h5>

                <div class="form-group">
                    <label class="d-block mb-2">I'm requesting for:</label>
                    <div>
                        @foreach([
                            'req_logo'           => 'Logo',
                            'req_icon'           => 'Web Icon',
                            'req_app_background' => 'App Background Image',
                            'req_landing_page'   => 'Landing Page',
                            'req_others'         => 'Others',
                        ] as $field => $label)
                        <span class="req-pill">
                            <input type="checkbox" name="{{ $field }}" id="{{ $field }}" value="1" {{ old($field) ? 'checked' : '' }}>
                            <label for="{{ $field }}">{{ $label }}</label>
                        </span>
                        @endforeach
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Primary Color <small class="text-muted">(e.g. #662c87)</small> <span class="text-danger">*</span></label>
                            <input type="text" name="req_primary_color" class="form-control" data-label="Primary Color"
                                   value="{{ old('req_primary_color') }}" placeholder="Enter color code" required>
                            <div class="field-error">Primary Color is required</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Secondary Color <small class="text-muted">(e.g. #1C2B36)</small></label>
                            <input type="text" name="req_sec_color" class="form-control" value="{{ old('req_sec_color') }}" placeholder="Enter color code">
                        </div>
                    </div>
                </div>

                <div class="form-group" id="descriptionGroup" style="display:none;">
                    <label>Request Description <span class="text-danger">*</span></label>
                    <textarea name="request_description" class="form-control" rows="4" placeholder="Describe your customization needs...">{{ old('request_description') }}</textarea>
                </div>

                {{-- Questionnaire (questions 6-17 — 1-5 are covered by form fields above) --}}
                <h6 class="mt-4 mb-3 font-weight-bold">Additional Information</h6>
                @php
                    $extraQuestions = [
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
                    ];
                @endphp
                <div class="row">
                    @foreach($extraQuestions as $key => $question)
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>{{ $question }}</label>
                            <input type="text" name="{{ $key }}" class="form-control" value="{{ old($key) }}" placeholder="Enter your answer">
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="form-group">
                    <label class="d-block">Do you have an existing logo?</label>
                    <div class="btn-group btn-group-toggle" data-toggle="buttons">
                        <label class="btn btn-outline-secondary" id="logoYesBtn">
                            <input type="radio" name="has_logo" value="yes"> Yes
                        </label>
                        <label class="btn btn-outline-secondary active" id="logoNoBtn">
                            <input type="radio" name="has_logo" value="no" checked> No
                        </label>
                    </div>
                </div>

                <div id="logoUploadSection" style="display:none;">
                    <div class="row">
                        <div class="col-md-4">
                            <label class="d-block mb-1">Entity Logo <small class="text-muted">(200x60)</small></label>
                            <div class="upload-box" onclick="document.getElementById('logoFile').click()">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <p>Click to upload logo</p>
                                <img class="preview d-none" id="logoPreview">
                            </div>
                            <input type="file" name="logo" id="logoFile" class="d-none" accept="image/*" onchange="previewImg(this,'logoPreview')">
                        </div>
                        <div class="col-md-4">
                            <label class="d-block mb-1">Web Icon <small class="text-muted">(60x60)</small></label>
                            <div class="upload-box" onclick="document.getElementById('iconFile').click()">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <p>Click to upload icon</p>
                                <img class="preview d-none" id="iconPreview">
                            </div>
                            <input type="file" name="icon" id="iconFile" class="d-none" accept="image/*" onchange="previewImg(this,'iconPreview')">
                        </div>
                        <div class="col-md-4">
                            <label class="d-block mb-1">App Login Background <small class="text-muted">(375x800)</small></label>
                            <div class="upload-box" onclick="document.getElementById('bgFile').click()">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <p>Click to upload background</p>
                                <img class="preview d-none" id="bgPreview">
                            </div>
                            <input type="file" name="app_background" id="bgFile" class="d-none" accept="image/*" onchange="previewImg(this,'bgPreview')">
                        </div>
                    </div>
                </div>

                <div class="form-group mt-3">
                    <label>Upload Document (optional)</label>
                    <input type="file" name="attachments[]" class="form-control-file" accept="image/*,.pdf,.doc,.docx" multiple>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <button type="button" class="btn btn-secondary" onclick="goToStep(1)"><i class="fas fa-arrow-left mr-1"></i> Back</button>
                    <button type="button" class="btn btn-primary" onclick="goToStep(3)">Next <i class="fas fa-arrow-right ml-1"></i></button>
                </div>
            </div>

            {{-- ==================== STEP 3: Review & Submit ==================== --}}
            <div class="step-panel" id="step3">
                <h5 class="mb-3 mt-4 font-weight-bold">Review Your Request</h5>
                <p class="text-muted mb-3">Please review your details before submitting.</p>

                <div id="reviewSummary"></div>

                <div class="d-flex justify-content-between mt-4">
                    <button type="button" class="btn btn-secondary" onclick="goToStep(2)"><i class="fas fa-arrow-left mr-1"></i> Back</button>
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-paper-plane mr-1"></i> Submit Request
                    </button>
                </div>
            </div>

        </form>
    </div>
</div>
@endsection

@push('js')
<script>
var currentStep = 1;

// Real-time validation on blur
$('#request-form').on('blur', 'input[required], textarea[required]', function() {
    validateField($(this));
});
$('#request-form').on('input', 'input.is-invalid, textarea.is-invalid', function() {
    validateField($(this));
});

function validateField($el) {
    var val = $el.val().trim();
    var $err = $el.siblings('.field-error');
    if (!val) {
        $el.addClass('is-invalid');
        $err.addClass('show');
        return false;
    }
    if ($el.attr('type') === 'email' && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val)) {
        $el.addClass('is-invalid');
        $err.addClass('show');
        return false;
    }
    $el.removeClass('is-invalid');
    $err.removeClass('show');
    return true;
}

function validateStep(step) {
    var valid = true;
    $('#step' + step).find('input[required], textarea[required]').each(function() {
        if (!validateField($(this))) valid = false;
    });
    return valid;
}

function goToStep(step) {
    // Validate before moving forward
    if (step > currentStep && !validateStep(currentStep)) {
        return;
    }

    if (step === 3) buildReview();

    $('.step-panel').removeClass('active');
    $('#step' + step).addClass('active');

    for (var i = 1; i <= 3; i++) {
        var $c = $('#stepCircle' + i);
        $c.removeClass('active done');
        if (i < step) $c.addClass('done').html('<i class="fas fa-check"></i>');
        else if (i === step) $c.addClass('active').text(i);
        else $c.text(i);
    }
    currentStep = step;
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// Toggle description when "Others" is checked
$('#req_others').on('change', function() { $('#descriptionGroup').toggle(this.checked); });

// Toggle logo upload
$('input[name="has_logo"]').on('change', function() { $('#logoUploadSection').toggle($(this).val() === 'yes'); });

function previewImg(input, previewId) {
    var $preview = $('#' + previewId);
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) { $preview.attr('src', e.target.result).removeClass('d-none'); };
        reader.readAsDataURL(input.files[0]);
    }
}

function buildReview() {
    var fd = new FormData($('#request-form')[0]);
    function field(label, val) {
        if (!val) return '';
        return '<div class="col-md-6"><div class="form-group"><label class="text-muted mb-0" style="font-size:12px;">' + label + '</label><p class="mb-0 font-weight-bold" style="font-size:14px;">' + val + '</p></div></div>';
    }
    var html = '<div class="row">';
    html += field('First Name', fd.get('first_name'));
    html += field('Last Name', fd.get('last_name'));
    html += field('Email', fd.get('email'));
    html += field('Phone', fd.get('phone'));
    html += field('Secondary Phone', fd.get('sec_phone'));
    html += field('Community Name', fd.get('company_name'));
    html += field('Community Handle Name', fd.get('company_phone'));
    html += field('Community Domain Name', fd.get('company_address'));
    html += field('Primary Color', fd.get('req_primary_color'));
    html += field('Secondary Color', fd.get('req_sec_color'));

    var reqs = [];
    ['req_logo','req_icon','req_app_background','req_landing_page','req_others'].forEach(function(f) {
        if (fd.get(f)) reqs.push($('label[for="' + f + '"]').text().trim());
    });
    if (reqs.length) html += field('Requesting For', reqs.join(', '));

    var desc = fd.get('request_description');
    if (desc) html += '<div class="col-md-12"><div class="form-group"><label class="text-muted mb-0" style="font-size:12px;">Description</label><p class="mb-0" style="font-size:14px;">' + desc + '</p></div></div>';

    // Extra questions
    var qLabels = {
        'question_6':'Existing Gowolo Account?', 'question_7':'Content Type', 'question_8':'Important Features',
        'question_9':'Expected Members', 'question_10':'Target Audience', 'question_11':'Donation/Payment Features',
        'question_12':'Custom Landing Page', 'question_13':'App Background Style', 'question_14':'Push Notifications',
        'question_15':'Website Integration', 'question_16':'Launch Timeline', 'question_17':'Additional Requirements'
    };
    for (var qk in qLabels) {
        var qv = fd.get(qk);
        if (qv) html += field(qLabels[qk], qv);
    }

    html += '</div>';
    $('#reviewSummary').html(html);
}
</script>
@endpush
