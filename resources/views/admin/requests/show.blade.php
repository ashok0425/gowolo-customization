@extends('layouts.app')
@section('title', 'Request — ' . $customizationRequest->ref_number)

@section('content')
<div class="page-header">
    <h4 class="page-title">Request #{{ $customizationRequest->ref_number }}</h4>
    <ul class="breadcrumbs">
        <li class="nav-home"><a href="{{ route('admin.dashboard') }}"><i class="flaticon-home"></i></a></li>
        <li class="separator"><i class="flaticon-right-arrow"></i></li>
        <li class="nav-item"><a href="{{ route('admin.requests.index') }}">Requests</a></li>
        <li class="separator"><i class="flaticon-right-arrow"></i></li>
        <li class="nav-item"><a href="#">{{ $customizationRequest->ref_number }}</a></li>
    </ul>
</div>

<div class="row">
    {{-- Left: Details + Answers --}}
    <div class="col-md-8">

        {{-- Customer Info --}}
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <h4 class="card-title">Customer Information</h4>
                    <div class="ml-auto">
                        <span class="badge {{ $customizationRequest->status_badge }}" style="font-size:13px;padding:6px 12px;">
                            {{ $customizationRequest->status_label }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless">
                            <tr><th>Name</th><td>{{ $customizationRequest->first_name }} {{ $customizationRequest->last_name }}</td></tr>
                            <tr><th>Email</th><td>{{ $customizationRequest->email }}</td></tr>
                            <tr><th>Phone</th><td>{{ $customizationRequest->phone }}</td></tr>
                            <tr><th>Alt Phone</th><td>{{ $customizationRequest->sec_phone ?? '—' }}</td></tr>
                            <tr><th>Submitted</th><td>{{ $customizationRequest->created_at->format('M d, Y H:i') }}</td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless">
                            <tr><th>Company</th><td>{{ $customizationRequest->company_name }}</td></tr>
                            <tr><th>Company Phone</th><td>{{ $customizationRequest->company_phone }}</td></tr>
                            <tr><th>Address</th><td>{{ $customizationRequest->company_address }}</td></tr>
                            @if($customizationRequest->login_email)
                            <tr><th>Login Email</th><td>{{ $customizationRequest->login_email }}</td></tr>
                            @endif
                        </table>
                    </div>
                </div>

                @if($customizationRequest->request_description)
                <hr>
                <p class="text-muted mb-1"><strong>Description:</strong></p>
                <p>{{ $customizationRequest->request_description }}</p>
                @endif

                <hr>
                <div class="row">
                    @foreach([
                        'req_logo' => 'Logo',
                        'req_icon' => 'Icon',
                        'req_app_background' => 'App Background',
                        'req_landing_page' => 'Landing Page',
                        'req_others' => 'Others',
                        'req_donation' => 'Donation',
                    ] as $field => $label)
                    <div class="col-md-4 mb-2">
                        <span class="badge {{ $customizationRequest->$field ? 'badge-success' : 'badge-light' }}">
                            {{ $label }}: {{ $customizationRequest->$field ? 'Yes' : 'No' }}
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Answers --}}
        @if($customizationRequest->answers->count())
        <div class="card">
            <div class="card-header"><h4 class="card-title">Questionnaire Answers</h4></div>
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
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead><tr><th>File</th><th>Category</th><th>Size</th></tr></thead>
                        <tbody>
                            @foreach($customizationRequest->files as $file)
                            <tr>
                                <td>{{ $file->original_name }}</td>
                                <td><span class="badge badge-secondary">{{ $file->file_category }}</span></td>
                                <td>{{ number_format($file->size_bytes / 1024, 1) }} KB</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        {{-- Status Update (for techs and admins) --}}
        @if(!$isTech || $customizationRequest->status == 1)
        <div class="card">
            <div class="card-header"><h4 class="card-title">Update Status</h4></div>
            <div class="card-body">
                <form id="statusForm">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Status</label>
                                <select name="status" id="statusSelect" class="form-control">
                                    @if($isTech)
                                        <option value="2" {{ $customizationRequest->status == 2 ? 'selected' : '' }}>In Review</option>
                                        <option value="3" {{ $customizationRequest->status == 3 ? 'selected' : '' }}>Sent for Review</option>
                                    @else
                                        <option value="0" {{ $customizationRequest->status == 0 ? 'selected' : '' }}>Pending</option>
                                        <option value="1" {{ $customizationRequest->status == 1 ? 'selected' : '' }}>Assigned</option>
                                        <option value="2" {{ $customizationRequest->status == 2 ? 'selected' : '' }}>In Review</option>
                                        <option value="3" {{ $customizationRequest->status == 3 ? 'selected' : '' }}>Sent for Review</option>
                                        <option value="4" {{ $customizationRequest->status == 4 ? 'selected' : '' }}>Approved</option>
                                        <option value="5" {{ $customizationRequest->status == 5 ? 'selected' : '' }}>Completed</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                        @if(!$isTech)
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Pay Type</label>
                                <select name="pay_type" class="form-control">
                                    <option value="1" {{ $customizationRequest->pay_type == 1 ? 'selected' : '' }}>Free</option>
                                    <option value="2" {{ $customizationRequest->pay_type == 2 ? 'selected' : '' }}>Paid</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Amount (USD)</label>
                                <input type="number" name="pay_amount" class="form-control" step="0.01" min="0"
                                       value="{{ $customizationRequest->pay_amount }}">
                            </div>
                        </div>
                        @endif
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Technician Comments</label>
                                <textarea name="technician_comments" class="form-control" rows="3">{{ $customizationRequest->technician_comments }}</textarea>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Status</button>
                </form>
            </div>
        </div>
        @endif

    </div>

    {{-- Right: Assignment + Actions --}}
    <div class="col-md-4">

        {{-- Current Assignment --}}
        <div class="card">
            <div class="card-header"><h4 class="card-title">Assignment</h4></div>
            <div class="card-body">
                <table class="table table-sm table-borderless">
                    <tr>
                        <th>Tech 1</th>
                        <td>{{ $customizationRequest->assigned_tech_name1 ?? '—' }}</td>
                    </tr>
                    <tr>
                        <th>Tech 2</th>
                        <td>{{ $customizationRequest->assigned_tech_name2 ?? '—' }}</td>
                    </tr>
                    <tr>
                        <th>Supervisor</th>
                        <td>{{ $customizationRequest->supervisor_name ?? '—' }}</td>
                    </tr>
                    <tr>
                        <th>Received</th>
                        <td>{{ $customizationRequest->tech_receive_date?->format('M d, Y') ?? '—' }}</td>
                    </tr>
                    <tr>
                        <th>Completed</th>
                        <td>{{ $customizationRequest->date_complete?->format('M d, Y') ?? '—' }}</td>
                    </tr>
                    <tr>
                        <th>Business Days</th>
                        <td>{{ $customizationRequest->num_of_days ?? '—' }}</td>
                    </tr>
                </table>
            </div>
        </div>

        {{-- Assign form — admin/supervisor only --}}
        @if(!$isTech)
        <div class="card">
            <div class="card-header"><h4 class="card-title">Assign Technician</h4></div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.requests.assign', $customizationRequest) }}">
                    @csrf
                    <div class="form-group">
                        <label>Technician 1 <span class="text-danger">*</span></label>
                        <select name="assigned_tech_id1" class="form-control select2" required>
                            <option value="">— Select —</option>
                            @foreach($technicians as $tech)
                            <option value="{{ $tech->id }}" {{ $customizationRequest->assigned_tech_id1 == $tech->id ? 'selected' : '' }}>
                                {{ $tech->full_name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Technician 2</label>
                        <select name="assigned_tech_id2" class="form-control select2">
                            <option value="">— None —</option>
                            @foreach($technicians as $tech)
                            <option value="{{ $tech->id }}" {{ $customizationRequest->assigned_tech_id2 == $tech->id ? 'selected' : '' }}>
                                {{ $tech->full_name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Supervisor</label>
                        <select name="supervisor_id" class="form-control select2">
                            <option value="">— None —</option>
                            @foreach($supervisors as $sup)
                            <option value="{{ $sup->id }}" {{ $customizationRequest->supervisor_id == $sup->id ? 'selected' : '' }}>
                                {{ $sup->full_name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Assign</button>
                </form>
            </div>
        </div>
        @endif

        {{-- Quick Actions --}}
        <div class="card">
            <div class="card-header"><h4 class="card-title">Actions</h4></div>
            <div class="card-body">
                <a href="{{ route('admin.requests.chat', $customizationRequest) }}" class="btn btn-info btn-block mb-2">
                    <i class="fas fa-comment mr-1"></i> Open Chat
                </a>
                @if(!$isTech)
                <a href="{{ route('admin.requests.logs', $customizationRequest) }}" class="btn btn-secondary btn-block mb-2">
                    <i class="fas fa-history mr-1"></i> View Logs
                </a>
                @endif
                <a href="{{ route('admin.requests.index') }}" class="btn btn-light btn-block">
                    <i class="fas fa-arrow-left mr-1"></i> Back to List
                </a>
            </div>
        </div>

    </div>
</div>
@endsection

@push('js')
<script>
$('#statusForm').on('submit', function(e) {
    e.preventDefault();
    $.post('{{ route('admin.requests.status', $customizationRequest) }}', $(this).serialize())
        .done(function(res) {
            if (res.success) {
                toastr ? toastr.success(res.message) : alert(res.message);
                setTimeout(() => location.reload(), 800);
            }
        })
        .fail(function() { alert('Error updating status.'); });
});
</script>
@endpush
