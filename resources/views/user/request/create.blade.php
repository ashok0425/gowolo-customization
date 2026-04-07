@extends('layouts.app')
@section('title', 'New Customization Request')

@section('content')
<div class="page-header">
    <h4 class="page-title">New Customization Request</h4>
    <ul class="breadcrumbs">
        <li class="nav-home"><a href="{{ route('user.dashboard') }}"><i class="flaticon-home"></i></a></li>
        <li class="separator"><i class="flaticon-right-arrow"></i></li>
        <li class="nav-item"><a href="#">New Request</a></li>
    </ul>
</div>

<form id="request-form">
    @csrf
    <div class="row">
        {{-- Personal & Company Info --}}
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><h4 class="card-title">Your Information</h4></div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>First Name <span class="text-danger">*</span></label>
                                <input type="text" name="first_name" class="form-control" value="{{ session('auth_user.first_name') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Last Name <span class="text-danger">*</span></label>
                                <input type="text" name="last_name" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" value="{{ session('auth_user.email') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Phone <span class="text-danger">*</span></label>
                                <input type="text" name="phone" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Alternate Phone</label>
                                <input type="text" name="sec_phone" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h4 class="card-title">Company / Community Information</h4></div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Company Name <span class="text-danger">*</span></label>
                                <input type="text" name="company_name" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Company Phone <span class="text-danger">*</span></label>
                                <input type="text" name="company_phone" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Company Address <span class="text-danger">*</span></label>
                                <textarea name="company_address" class="form-control" rows="2" required></textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Primary Brand Color</label>
                                <input type="color" name="req_primary_color" class="form-control" value="#662c87">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Secondary Color</label>
                                <input type="color" name="req_sec_color" class="form-control" value="#1C2B36">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Questionnaire --}}
            <div class="card">
                <div class="card-header"><h4 class="card-title">Questionnaire</h4></div>
                <div class="card-body">
                    @foreach($questions as $key => $question)
                    <div class="form-group">
                        <label>{{ $loop->iteration }}. {{ $question }}
                            @if(in_array($key, ['question_1','question_2','question_3']))
                            <span class="text-danger">*</span>
                            @endif
                        </label>
                        <textarea name="{{ $key }}" class="form-control" rows="2"
                            {{ in_array($key, ['question_1','question_2','question_3']) ? 'required' : '' }}></textarea>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Right: Requirements & Files --}}
        <div class="col-md-4">
            <div class="card">
                <div class="card-header"><h4 class="card-title">What do you need?</h4></div>
                <div class="card-body">
                    @foreach([
                        'req_logo'           => 'Logo Design',
                        'req_icon'           => 'App Icon',
                        'req_app_background' => 'App Background',
                        'req_landing_page'   => 'Landing Page',
                        'req_others'         => 'Other Customization',
                        'req_donation'       => 'Donation Features',
                    ] as $field => $label)
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="{{ $field }}" id="{{ $field }}" value="1">
                        <label class="form-check-label" for="{{ $field }}">{{ $label }}</label>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h4 class="card-title">Upload Files</h4></div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Logo File</label>
                        <input type="file" name="logo" class="form-control-file" accept="image/*">
                    </div>
                    <div class="form-group">
                        <label>Icon File</label>
                        <input type="file" name="icon" class="form-control-file" accept="image/*">
                    </div>
                    <div class="form-group">
                        <label>Background Image</label>
                        <input type="file" name="app_background" class="form-control-file" accept="image/*">
                    </div>
                    <div class="form-group">
                        <label>Other Attachments</label>
                        <input type="file" name="attachments[]" class="form-control-file" multiple
                               accept="image/*,.pdf,.doc,.docx">
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h4 class="card-title">Additional Details</h4></div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="request_description" class="form-control" rows="4"
                                  placeholder="Describe your customization needs…"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Gowolo Login Email</label>
                        <input type="email" name="login_email" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Gowolo Login Password</label>
                        <input type="password" name="login_password" class="form-control">
                    </div>
                </div>
            </div>

            @if($price > 0)
            <div class="alert alert-info">
                <i class="fas fa-info-circle mr-1"></i>
                Customization fee: <strong>${{ number_format($price, 2) }}</strong>
            </div>
            @endif

            <button type="submit" class="btn btn-primary btn-block btn-lg" id="submit-btn">
                <i class="fas fa-paper-plane mr-1"></i> Submit Request
            </button>
        </div>
    </div>
</form>
@endsection

@push('js')
<script>
$('#request-form').on('submit', function(e) {
    e.preventDefault();
    var btn = $('#submit-btn').prop('disabled', true).text('Submitting…');
    var fd = new FormData(this);

    $.ajax({
        url: '{{ route('user.request.store') }}',
        type: 'POST',
        data: fd,
        processData: false,
        contentType: false,
        success: function(res) {
            if (res.success) {
                window.location.href = '{{ route('user.dashboard') }}';
            } else {
                alert(res.message || 'Error submitting request.');
                btn.prop('disabled', false).text('Submit Request');
            }
        },
        error: function(xhr) {
            var msg = xhr.responseJSON?.message || 'Validation error.';
            alert(msg);
            btn.prop('disabled', false).text('Submit Request');
        }
    });
});
</script>
@endpush
