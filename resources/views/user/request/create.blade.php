@extends('layouts.app')
@section('title', 'New Customization Request')

@push('css')
<style>
    /* Match dashboardv2 customize_request_info styling */
    .details { padding: 10px 0; }
    .details .form-group label.f-15 {
        font-size: 15px;
        font-weight: 500;
        color: #333;
        padding-left: 8px;
        padding-bottom: 6px;
        display: block;
    }
    .input_box {
        width: 100%;
        border: 1px solid #dedede;
        border-radius: 8px;
        padding: 10px 14px;
        font-size: 14px;
        background: #fff;
        transition: all 0.2s;
    }
    .input_box:focus {
        border-color: #662c87;
        outline: none;
        box-shadow: 0 0 0 3px rgba(102, 44, 135, 0.1);
    }
    .input_box[readonly] { background: #f5f5f5; color: #666; cursor: not-allowed; }

    /* "I'm requesting for" checkboxes */
    .wdth95 { font-weight: 600; padding: 0 8px 8px; color: #333; }
    .req-checkbox {
        width: 100%;
        position: relative;
        display: block;
        margin-bottom: 10px;
        border: 1px solid #dedede;
        border-radius: 20px;
        padding: 8px 16px;
        cursor: pointer;
        transition: all 0.2s;
    }
    .req-checkbox:hover { border-color: #662c87; background: #fafbfc; }
    .req-checkbox input[type="checkbox"] { margin-right: 10px; cursor: pointer; vertical-align: middle; }
    .req-checkbox label { margin: 0; cursor: pointer; font-weight: normal; color: #000; font-size: 14px; vertical-align: middle; }
    .req-checkbox:has(input:checked) { border-color: #662c87; background: #f9f3fc; }
    .req-checkbox:has(input:checked) label { color: #662c87; font-weight: 600; }

    /* Upload thumbnails */
    .upload-thumb {
        border: 2px dashed #dedede;
        border-radius: 12px;
        padding: 24px 12px;
        text-align: center;
        background: #fafbfc;
        cursor: pointer;
        transition: border-color 0.2s;
        min-height: 180px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }
    .upload-thumb:hover { border-color: #662c87; }
    .upload-thumb i { font-size: 32px; color: #bbb; }
    .upload-thumb .hint { color: #888; font-size: 12px; margin-top: 8px; }
    .upload-thumb .size-hint { color: #aaa; font-size: 11px; margin-top: 4px; }
    .upload-thumb img.preview { max-height: 100px; margin-top: 8px; border-radius: 6px; }
    .upload-label { font-weight: 700; color: #662c87; text-align: center; margin-top: 8px; display: block; font-size: 13px; }

    /* Radio pills for Yes/No questions */
    .radio-row { display: flex; gap: 12px; flex-wrap: wrap; margin-top: 6px; }
    .radio-pill {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 6px 16px; border: 2px solid #E5E7EB; border-radius: 20px;
        cursor: pointer; font-size: 13px; color: #555; margin: 0;
    }
    .radio-pill input { margin: 0; }
    .radio-pill:has(input:checked) { border-color: #662c87; background: #f9f3fc; color: #662c87; font-weight: 600; }

    /* Section headings */
    .section-heading {
        font-size: 18px; font-weight: 700; color: #333;
        padding-bottom: 8px; margin: 30px 0 16px;
        border-bottom: 2px solid #f0f0f0;
    }
    .section-heading:first-child { margin-top: 0; }

    /* Validation */
    .input_box.is-invalid, .upload-thumb.is-invalid { border-color: #e74c3c !important; }
    .field-error { color: #e74c3c; font-size: 12px; margin-top: 4px; display: none; }
    .field-error.show { display: block; }

    /* Submit area */
    .submit-area {
        display: flex; justify-content: center; gap: 12px;
        padding: 20px 0 10px; margin-top: 20px;
        border-top: 1px solid #f0f0f0;
    }
    .btn-primary_btn {
        background: #662c87; color: #fff; border: none;
        padding: 12px 40px; border-radius: 50px;
        font-weight: 600; font-size: 15px; cursor: pointer;
    }
    .btn-primary_btn:hover { background: #4f1f6c; color: #fff; }
    .btn-outline_btn {
        background: #fff; color: #662c87; border: 2px solid #662c87;
        padding: 10px 30px; border-radius: 50px;
        font-weight: 600; font-size: 14px; cursor: pointer;
    }
    .btn-outline_btn:hover { background: #f9f3fc; color: #662c87; }

    /* Additional Files button — matches dashboardv2 .upload_files */
    .upload-files-btn {
        display: inline-block;
        padding: 10px 25px;
        background: #662c87;
        color: #fff;
        font-weight: 700;
        border-radius: 15px;
        border: none;
        cursor: pointer;
        font-size: 14px;
        text-decoration: none;
    }
    .upload-files-btn:hover { background: #4f1f6c; color: #fff; text-decoration: none; }
    .upload-files-btn i { margin-right: 6px; }
    .additional-files-list {
        list-style: none;
        padding: 0;
        margin: 10px 0 0;
    }
    .additional-files-list li {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 8px 14px;
        margin-bottom: 6px;
        background: #f9f3fc;
        border: 1px solid #e6d5ef;
        border-radius: 10px;
        font-size: 13px;
        color: #333;
    }
    .additional-files-list li i.file-icon { color: #662c87; font-size: 16px; }
    .additional-files-list li .remove-file { margin-left: auto; color: #e74c3c; cursor: pointer; font-size: 16px; }
</style>
@endpush

@section('content')
<div class="page-header">
    <h4 class="page-title">Request Form</h4>
    <ul class="breadcrumbs">
        <li class="nav-home"><a href="{{ route('user.dashboard') }}"><i class="flaticon-home"></i></a></li>
        <li class="separator"><i class="flaticon-right-arrow"></i></li>
        <li class="nav-item"><a href="#">New Request</a></li>
    </ul>
</div>

@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <strong>Whoops! There were some problems with your input:</strong>
    <ul class="mb-0 mt-1">
        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
    </ul>
    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
</div>
@endif

<div class="card">
    <div class="card-body p-4">
        <p class="text-muted mb-4">
            <i class="fas fa-info-circle mr-1 text-primary"></i>
            Our marketing team can rebrand your platform for free. Please provide the information below:
        </p>

        <form method="POST" action="{{ route('user.request.store') }}" enctype="multipart/form-data" id="request-form">
            @csrf

            <div class="details">
                {{-- =============================================================
                     PHASE 1 — Main request form
                     ============================================================= --}}
                <div id="phase1">
                <h5 class="section-heading">Personal Information</h5>
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label class="f-15">First Name <span class="text-danger">*</span></label>
                        <input type="text" name="first_name" class="input_box" value="{{ old('first_name', explode(' ', session('auth_user.name', ''))[0] ?? '') }}" required readonly>
                        <div class="field-error">First Name is required</div>
                    </div>
                    <div class="col-md-6 form-group">
                        <label class="f-15">Last Name <span class="text-danger">*</span></label>
                        <input type="text" name="last_name" class="input_box" value="{{ old('last_name', implode(' ', array_slice(explode(' ', session('auth_user.name', '')), 1))) }}" required readonly>
                        <div class="field-error">Last Name is required</div>
                    </div>
                    <div class="col-md-6 form-group">
                        <label class="f-15">Phone Number <span class="text-danger">*</span></label>
                        <input type="text" name="phone" class="input_box" value="{{ old('phone', session('auth_user.phone')) }}" required readonly>
                        <div class="field-error">Phone is required</div>
                    </div>
                    <div class="col-md-6 form-group">
                        <label class="f-15">Secondary Phone Number (Optional)</label>
                        <input type="text" name="sec_phone" class="input_box" value="{{ old('sec_phone') }}">
                    </div>
                    <div class="col-md-6 form-group">
                        <label class="f-15">Email Address <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="input_box" value="{{ old('email', session('auth_user.email')) }}" required readonly>
                        <div class="field-error">Email is required</div>
                    </div>
                    <div class="col-md-6 form-group">
                        <label class="f-15">Community Name <span class="text-danger">*</span></label>
                        <input type="text" name="company_name" class="input_box" value="{{ old('company_name') }}" placeholder="Community Name" required>
                        <div class="field-error">Community Name is required</div>
                    </div>
                    <div class="col-md-6 form-group">
                        <label class="f-15">Community Handle Name <span class="text-danger">*</span></label>
                        <input type="text" name="company_phone" class="input_box" value="{{ old('company_phone') }}" placeholder="Community Handle Name" required>
                        <div class="field-error">Community Handle Name is required</div>
                    </div>
                    <div class="col-md-6 form-group">
                        <label class="f-15">Community Domain Name <small class="text-muted">(http://domainname.gowolo.biz)</small></label>
                        <input type="text" name="company_address" class="input_box" value="{{ old('company_address') }}" placeholder="Community Domain Name">
                    </div>
                </div>

                {{-- ============== Request Type ============== --}}
                <h5 class="section-heading">What are you requesting?</h5>
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label class="f-15">Request Type <span class="text-danger">*</span></label>
                        <select name="request_type" id="requestType" class="input_box" required>
                            <option value="customization" {{ old('request_type', 'customization') == 'customization' ? 'selected' : '' }}>Customization</option>
                            <option value="graphic_design" {{ old('request_type') == 'graphic_design' ? 'selected' : '' }}>Graphic Design</option>
                            <option value="web_development" {{ old('request_type') == 'web_development' ? 'selected' : '' }}>Web Development</option>
                            <option value="software_development" {{ old('request_type') == 'software_development' ? 'selected' : '' }}>Software Development</option>
                            <option value="app_development" {{ old('request_type') == 'app_development' ? 'selected' : '' }}>App Development</option>
                            <option value="gift_monetization_session" {{ old('request_type') == 'gift_monetization_session' ? 'selected' : '' }}>Gift &amp; Monetization Session</option>
                        </select>
                    </div>
                </div>

                {{-- ============== Customization-specific section (logos, icons, colors, checkboxes) ============== --}}
                <div id="customizationSection">
                    <h5 class="section-heading">I'm Requesting For</h5>
                    <div class="row">
                        <div class="col-sm-12">
                            @foreach([
                                'req_logo'           => 'Logo',
                                'req_icon'           => 'Web Icon',
                                'req_app_background' => 'App Background Image',
                                'req_landing_page'   => 'Landing Page',
                                'req_others'         => 'Others',
                            ] as $field => $label)
                            <div class="req-checkbox">
                                <input type="checkbox" name="{{ $field }}" id="{{ $field }}" value="1" {{ old($field) ? 'checked' : '' }}>
                                <label for="{{ $field }}">{{ $label }}</label>
                            </div>
                            @endforeach
                        </div>

                        <div class="col-md-6 form-group">
                            <label class="f-15">Primary Color <small class="text-muted">(eg: 000000)</small> <span class="text-danger">*</span></label>
                            <input type="text" name="req_primary_color" class="input_box req-type-customization" value="{{ old('req_primary_color') }}" placeholder="Primary Color" required>
                            <div class="field-error">Primary Color is required</div>
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="f-15">Secondary Color <small class="text-muted">(eg: 000000)</small> <span class="text-danger">*</span></label>
                            <input type="text" name="req_sec_color" class="input_box req-type-customization" value="{{ old('req_sec_color') }}" placeholder="Secondary Color" required>
                            <div class="field-error">Secondary Color is required</div>
                        </div>
                    </div>

                    {{-- File Uploads (only for Customization) --}}
                    <h5 class="section-heading">Upload Files</h5>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="upload-thumb" onclick="document.getElementById('logoFile').click()">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <div class="hint">Click to upload</div>
                                <div class="size-hint">Size: 200x60</div>
                                <img class="preview d-none" id="logoPreview">
                            </div>
                            <span class="upload-label">Entity Logo</span>
                            <input type="file" name="logo" id="logoFile" class="d-none" accept="image/jpeg,image/png,image/webp" onchange="previewImg(this,'logoPreview')">
                        </div>
                        <div class="col-md-4">
                            <div class="upload-thumb" onclick="document.getElementById('iconFile').click()">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <div class="hint">Click to upload</div>
                                <div class="size-hint">Size: 60x60</div>
                                <img class="preview d-none" id="iconPreview">
                            </div>
                            <span class="upload-label">Web Icon</span>
                            <input type="file" name="icon" id="iconFile" class="d-none" accept="image/jpeg,image/png,image/webp" onchange="previewImg(this,'iconPreview')">
                        </div>
                        <div class="col-md-4">
                            <div class="upload-thumb" onclick="document.getElementById('bgFile').click()">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <div class="hint">Click to upload</div>
                                <div class="size-hint">Size: 375x800</div>
                                <img class="preview d-none" id="bgPreview">
                            </div>
                            <span class="upload-label">App Login Background</span>
                            <input type="file" name="app_background" id="bgFile" class="d-none" accept="image/jpeg,image/png,image/webp" onchange="previewImg(this,'bgPreview')">
                        </div>
                    </div>
                </div>
                {{-- End customization-specific section --}}

                {{-- ============== Description (shown for non-customization types) ============== --}}
                <div id="genericDescriptionSection" style="display:none;">
                    <h5 class="section-heading">Project Details</h5>
                    <div class="form-group">
                        <label class="f-15">Describe your project <span class="text-danger">*</span></label>
                        <textarea name="request_description" id="genericDescription" class="input_box" rows="8" placeholder="Tell us what you need — goals, timeline, features, references...">{{ old('request_description') }}</textarea>
                    </div>
                </div>

                {{-- Description for customization "Others" (unchanged) --}}
                <div class="form-group" id="descriptionGroup" style="display:none;">
                    <label class="f-15">Request Description <span class="text-danger">*</span></label>
                    <textarea name="request_description_customization" class="input_box" rows="6" placeholder="Describe your customization needs...">{{ old('request_description_customization') }}</textarea>
                </div>

                {{-- ============== Common uploads + additional features (shown for all types) ============== --}}
                <div class="row mt-3">
                    <div class="col-sm-12 form-group">
                        <label class="f-15">Upload Document</label>
                        <input type="file" name="cust_doc_file" class="input_box" accept=".pdf,.doc,.docx,image/*">
                    </div>
                    <div class="col-sm-12 form-group">
                        <label class="f-15">Additional Files</label>
                        <div>
                            <button type="button" class="upload-files-btn" onclick="document.getElementById('additionalFilesInput').click()">
                                <i class="fas fa-plus"></i> Add Files
                            </button>
                            <input type="file" name="attachments[]" id="additionalFilesInput" class="d-none" accept="image/*,.pdf,.doc,.docx" multiple onchange="updateAdditionalFilesList(this)">
                        </div>
                        <ul class="additional-files-list" id="additionalFilesList"></ul>
                    </div>
                    <div class="col-sm-12 form-group" id="additionalFeaturesGroup">
                        <label class="f-15">Additional Features <span class="text-danger">*</span></label>
                        <div class="radio-row">
                            <label class="radio-pill"><input type="radio" name="addition_feature" value="1" {{ old('addition_feature') == '1' ? 'checked' : '' }}> Yes</label>
                            <label class="radio-pill"><input type="radio" name="addition_feature" value="0" {{ old('addition_feature', '0') == '0' ? 'checked' : '' }}> No</label>
                        </div>
                    </div>
                </div>

                </div>
                {{-- End Phase 1 --}}

                {{-- =============================================================
                     PHASE 2 — Questionary (shown only after clicking "Add Additional Features")
                     ============================================================= --}}
                <div id="phase2" style="display:none;">
                <h5 class="section-heading">Questionary Details</h5>
                <p class="text-muted mb-3">
                    <i class="fas fa-info-circle mr-1 text-primary"></i>
                    Please answer the questions below so our team can better customize your platform.
                </p>

                <div class="form-group">
                    <label class="f-15">What domain name would you like to be displayed in your website? <span class="text-danger">*</span></label>
                    <small class="d-block text-muted pl-2 pb-1">(You can promote multiple websites in your platform.)</small>
                    <textarea name="question_1" class="input_box" rows="3" required>{{ old('question_1') }}</textarea>
                </div>

                <div class="form-group">
                    <label class="f-15">What are your gifts, talents, products and/or services and what are you passionate about? <span class="text-danger">*</span></label>
                    <textarea name="question_2" class="input_box" rows="3" required>{{ old('question_2') }}</textarea>
                </div>

                <div class="form-group">
                    <label class="f-15">If you never got paid for it, what could you do for the rest of your life that brings you happiness? <span class="text-danger">*</span></label>
                    <textarea name="question_3" class="input_box" rows="3" required>{{ old('question_3') }}</textarea>
                </div>

                <div class="form-group">
                    <label class="f-15">List 5 things you love to do in order of importance. <span class="text-danger">*</span></label>
                    <textarea name="question_4" class="input_box" rows="3" required>{{ old('question_4') }}</textarea>
                </div>

                <div class="form-group">
                    <label class="f-15">How many followers do you have on other platforms <span class="text-danger">*</span></label>
                    <div class="radio-row">
                        <label class="radio-pill"><input type="radio" name="question_5" value="0-500" {{ old('question_5') == '0-500' ? 'checked' : '' }}> 0-500</label>
                        <label class="radio-pill"><input type="radio" name="question_5" value="500-5000" {{ old('question_5') == '500-5000' ? 'checked' : '' }}> 500-5000</label>
                        <label class="radio-pill"><input type="radio" name="question_5" value="5000-50000" {{ old('question_5') == '5000-50000' ? 'checked' : '' }}> 5000-50000</label>
                        <label class="radio-pill"><input type="radio" name="question_5" value="50000 or more" {{ old('question_5') == '50000 or more' ? 'checked' : '' }}> 50000 or more</label>
                    </div>
                </div>

                <div class="form-group">
                    <label class="f-15">Do you have a thumbnail image for your content management or master courses? <span class="text-danger">*</span></label>
                    <small class="d-block text-muted pl-2 pb-1">(This image is displayed on the main page of your websites.)</small>
                    <div class="radio-row">
                        <label class="radio-pill"><input type="radio" name="question_11" value="1"> Yes</label>
                        <label class="radio-pill"><input type="radio" name="question_11" value="0"> No</label>
                    </div>
                </div>

                <div class="form-group">
                    <label class="f-15">Can you provide us with your website content for your landing page? <span class="text-danger">*</span></label>
                    <div class="radio-row">
                        <label class="radio-pill"><input type="radio" name="question_12" value="1"> Yes</label>
                        <label class="radio-pill"><input type="radio" name="question_12" value="0"> No</label>
                    </div>
                </div>

                <div class="form-group">
                    <label class="f-15">Can you provide us with your campaign content for your lead capture page? <span class="text-danger">*</span></label>
                    <div class="radio-row">
                        <label class="radio-pill"><input type="radio" name="question_13" value="1"> Yes</label>
                        <label class="radio-pill"><input type="radio" name="question_13" value="0"> No</label>
                    </div>
                </div>

                <div class="form-group">
                    <label class="f-15">Do you have product images for your e-commerce store? (Your online mall) <span class="text-danger">*</span></label>
                    <div class="radio-row">
                        <label class="radio-pill"><input type="radio" name="question_14" value="1"> Yes</label>
                        <label class="radio-pill"><input type="radio" name="question_14" value="0"> No</label>
                    </div>
                </div>

                <div class="form-group">
                    <label class="f-15">Do you have a banner image for your e-commerce store? (Your online mall) <span class="text-danger">*</span></label>
                    <div class="radio-row">
                        <label class="radio-pill"><input type="radio" name="question_15" value="1"> Yes</label>
                        <label class="radio-pill"><input type="radio" name="question_15" value="0"> No</label>
                    </div>
                </div>

                <div class="form-group">
                    <label class="f-15">Do you have any videos for your landing page, e-commerce store or master courses? <span class="text-danger">*</span></label>
                    <div class="radio-row">
                        <label class="radio-pill"><input type="radio" name="question_16" value="1"> Yes</label>
                        <label class="radio-pill"><input type="radio" name="question_16" value="0"> No</label>
                    </div>
                </div>

                <div class="form-group">
                    <label class="f-15">What would you like to do in your VIP to share your gift, talent, products and/or services with your VIP followers? <span class="text-danger">*</span></label>
                    <small class="d-block text-muted pl-2 pb-1">(Ex: Teach/train, worship, etc)</small>
                    <textarea name="question_17" class="input_box" rows="3" required>{{ old('question_17') }}</textarea>
                </div>

                <div class="form-group">
                    <label class="f-15">How will you use this order? <small class="text-muted">(optional)</small></label>
                    <textarea name="requirement_1" class="input_box" rows="3">{{ old('requirement_1') }}</textarea>
                </div>

                <div class="form-group">
                    <label class="f-15">Which industry is most relevant to your order? <small class="text-muted">(optional)</small></label>
                    <textarea name="requirement_2" class="input_box" rows="3">{{ old('requirement_2') }}</textarea>
                </div>

                <div class="form-group">
                    <label class="f-15">What are you looking to achieve with this order? <small class="text-muted">(optional)</small></label>
                    <textarea name="requirement_3" class="input_box" rows="3">{{ old('requirement_3') }}</textarea>
                </div>

                <div class="form-group">
                    <label class="f-15">Relevant data <span class="text-danger">*</span></label>
                    <textarea name="requirement_4" class="input_box" rows="3" required>{{ old('requirement_4') }}</textarea>
                </div>
                </div>
                {{-- End Phase 2 --}}

                {{-- ============== Submit ============== --}}
                <div class="submit-area">
                    <a href="{{ route('user.dashboard') }}" class="btn-outline_btn" style="text-decoration:none;">Close</a>

                    {{-- Phase 1 button: either "Send Request" (if No) or "Add Additional Features" (if Yes) --}}
                    <button type="button" id="phase1Btn" class="btn-primary_btn">
                        <i class="fas fa-paper-plane mr-2"></i> Send Request
                    </button>

                    {{-- Phase 2 buttons --}}
                    <button type="button" id="phase2BackBtn" class="btn-outline_btn" style="display:none;">
                        <i class="fas fa-arrow-left mr-1"></i> Back
                    </button>
                    <button type="submit" id="phase2SubmitBtn" class="btn-primary_btn" style="display:none;">
                        <i class="fas fa-paper-plane mr-2"></i> Send Request
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('js')
<script>
// Real-time validation
$('#request-form').on('blur', 'input[required], textarea[required]', function() { validateField($(this)); });
$('#request-form').on('input', '.is-invalid', function() { validateField($(this)); });

function validateField($el) {
    var val = ($el.val() || '').trim();
    var $err = $el.siblings('.field-error');
    if (!val) { $el.addClass('is-invalid'); $err.addClass('show'); return false; }
    if ($el.attr('type') === 'email' && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val)) {
        $el.addClass('is-invalid'); $err.addClass('show'); return false;
    }
    $el.removeClass('is-invalid'); $err.removeClass('show'); return true;
}

// Show description when "Others" is checked
$('#req_others').on('change', function() { $('#descriptionGroup').toggle(this.checked); });

// ==================== Request Type toggle ====================
function toggleRequestType() {
    var type = $('#requestType').val();
    var isCustomization = (type === 'customization');

    $('#customizationSection').toggle(isCustomization);
    $('#genericDescriptionSection').toggle(!isCustomization);
    $('#additionalFeaturesGroup').toggle(isCustomization);

    // Toggle required attribute on customization-only fields
    $('.req-type-customization').each(function() {
        if (isCustomization) {
            if ($(this).data('was-required')) $(this).attr('required', true);
        } else {
            if ($(this).attr('required')) {
                $(this).data('was-required', true);
                $(this).removeAttr('required');
            }
        }
    });

    // Generic description is required for non-customization types
    if (!isCustomization) {
        $('#genericDescription').attr('required', true);
    } else {
        $('#genericDescription').removeAttr('required');
    }
}
$('#requestType').on('change', toggleRequestType);
$(function() { toggleRequestType(); });

// Button text toggle based on Additional Features selection
function updatePhase1ButtonText() {
    var yes = $('input[name="addition_feature"]:checked').val() === '1';
    if (yes) {
        $('#phase1Btn').html('<i class="fas fa-arrow-right mr-2"></i> Add Additional Features');
    } else {
        $('#phase1Btn').html('<i class="fas fa-paper-plane mr-2"></i> Send Request');
    }
}
$('input[name="addition_feature"]').on('change', updatePhase1ButtonText);
$(function() { updatePhase1ButtonText(); });

// Enable/disable required on Phase 2 fields
function setPhase2Required(enable) {
    $('#phase2').find('input, textarea').each(function() {
        if (enable) {
            if ($(this).data('was-required')) $(this).attr('required', true);
        } else {
            if ($(this).attr('required')) {
                $(this).data('was-required', true);
                $(this).removeAttr('required');
            }
        }
    });
}
// On initial load, Phase 2 is hidden → strip required
setPhase2Required(false);

// Phase 1 button click
$('#phase1Btn').on('click', function() {
    // Validate Phase 1 fields first
    var valid = true;
    $('#phase1').find('input[required], textarea[required]').each(function() {
        if (!validateField($(this))) valid = false;
    });
    if (!valid) {
        // Scroll to first error
        var $first = $('#phase1').find('.is-invalid').first();
        if ($first.length) $first[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
        return;
    }

    var yes = $('input[name="addition_feature"]:checked').val() === '1';
    if (yes) {
        // Go to Phase 2 — show questionary, hide Phase 1, re-enable required on questionary fields
        $('#phase1').hide();
        $('#phase2').show();
        setPhase2Required(true);
        $('#phase1Btn').hide();
        $('#phase2BackBtn').show();
        $('#phase2SubmitBtn').show();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    } else {
        // Submit directly
        $('#request-form').submit();
    }
});

// Phase 2 Back button
$('#phase2BackBtn').on('click', function() {
    $('#phase2').hide();
    $('#phase1').show();
    setPhase2Required(false);
    $('#phase2BackBtn').hide();
    $('#phase2SubmitBtn').hide();
    $('#phase1Btn').show();
    window.scrollTo({ top: 0, behavior: 'smooth' });
});

// Image preview
function previewImg(input, previewId) {
    var $preview = $('#' + previewId);
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) { $preview.attr('src', e.target.result).removeClass('d-none'); };
        reader.readAsDataURL(input.files[0]);
    }
}

// Additional files list (rendered after selection)
function updateAdditionalFilesList(input) {
    var $list = $('#additionalFilesList').empty();
    if (!input.files || !input.files.length) return;
    for (var i = 0; i < input.files.length; i++) {
        var f = input.files[i];
        var isImage = f.type.startsWith('image/');
        var icon = isImage ? 'fa-image' : (f.name.endsWith('.pdf') ? 'fa-file-pdf' : 'fa-file-alt');
        var sizeKb = (f.size / 1024).toFixed(1);
        $list.append('<li><i class="fas ' + icon + ' file-icon"></i><span>' + f.name + '</span><small class="text-muted">(' + sizeKb + ' KB)</small><span class="remove-file" onclick="clearAdditionalFiles()" title="Clear all">&times;</span></li>');
    }
}
function clearAdditionalFiles() {
    $('#additionalFilesInput').val('');
    $('#additionalFilesList').empty();
}
</script>
@endpush
