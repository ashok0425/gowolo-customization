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
                                    @elseif($file->is_video && $fileUrl)
                                        <a href="#" class="videofile" data-toggle="modal" data-url="{{ $fileUrl }}">
                                            <i class="fas fa-video fa-2x text-primary" style="cursor:pointer;"></i>
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
                                    <a href="{{ route('admin.requests.file.download', [$customizationRequest->cuid, $file->id]) }}" class="btn btn-sm btn-outline-primary" title="Download">
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
                        <h5 class="modal-title">File Preview</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body text-center" id="filePreviewBody"></div>
                </div>
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

        {{-- Quick Actions --}}
        <div class="card">
            <div class="card-header"><h4 class="card-title">Actions</h4></div>
            <div class="card-body">
                <a href="{{ route('admin.requests.chat', $customizationRequest) }}" class="btn btn-info btn-block mb-2">
                    <i class="fas fa-comment mr-1"></i> Open Chat
                </a>
                @if($seeAll)
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
$(document).on('click', '.imgfile', function(e) {
    e.preventDefault();
    var src = $(this).attr('data-id');
    $('#filePreviewBody').html('<img src="' + src + '" style="max-width:100%;height:auto;">');
    $('#filePreviewModal').modal('show');
});
$(document).on('click', '.videofile', function(e) {
    e.preventDefault();
    var src = $(this).attr('data-url');
    $('#filePreviewBody').html('<video controls style="max-width:100%;height:auto;"><source src="' + src + '">Your browser does not support video playback.</video>');
    $('#filePreviewModal').modal('show');
});
$('#filePreviewModal').on('hidden.bs.modal', function() {
    $('#filePreviewBody').find('video').each(function() { this.pause(); });
});
</script>
@endpush
