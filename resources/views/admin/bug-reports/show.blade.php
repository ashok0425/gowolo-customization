@extends('layouts.app')
@section('title', 'Bug Report #' . $bugReport->id)

@section('content')
<div class="page-header">
    <h4 class="page-title">Bug Report #{{ $bugReport->id }}</h4>
    <ul class="breadcrumbs">
        <li class="nav-home"><a href="{{ route('admin.dashboard') }}"><i class="flaticon-home"></i></a></li>
        <li class="separator"><i class="flaticon-right-arrow"></i></li>
        <li class="nav-item"><a href="{{ route('admin.bug-reports.index') }}">Bug Reports</a></li>
        <li class="separator"><i class="flaticon-right-arrow"></i></li>
        <li class="nav-item"><a href="#">#{{ $bugReport->id }}</a></li>
    </ul>
</div>

<div class="row">
    <div class="col-md-8">
        {{-- Bug Message --}}
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <h4 class="card-title">Bug Description</h4>
                    <div class="ml-auto">
                        @if($bugReport->is_read)
                            <span class="badge badge-success" style="font-size:13px;padding:6px 12px;">Read</span>
                        @else
                            <span class="badge badge-warning" style="font-size:13px;padding:6px 12px;">New</span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="mb-3" style="white-space:pre-wrap;">{{ $bugReport->message }}</div>
            </div>
        </div>

        {{-- Steps to Reproduce --}}
        @if($bugReport->steps_to_reproduce)
        <div class="card">
            <div class="card-header"><h4 class="card-title">Steps to Reproduce</h4></div>
            <div class="card-body">
                <div style="white-space:pre-wrap;">{{ $bugReport->steps_to_reproduce }}</div>
            </div>
        </div>
        @endif

        {{-- Screenshot --}}
        @if($bugReport->screenshot_path)
        <div class="card">
            <div class="card-header"><h4 class="card-title">Screenshot</h4></div>
            <div class="card-body text-center">
                <a href="{{ asset($bugReport->screenshot_path) }}" data-toggle="lightbox">
                    <img src="{{ asset($bugReport->screenshot_path) }}" alt="Bug Screenshot"
                         class="img-fluid" style="max-height:500px;border-radius:8px;border:1px solid #ddd;">
                </a>
            </div>
        </div>
        @endif
    </div>

    <div class="col-md-4">
        {{-- Reporter Info --}}
        <div class="card">
            <div class="card-header"><h4 class="card-title">Reporter</h4></div>
            <div class="card-body">
                <table class="table table-sm table-borderless">
                    <tr><th>Name</th><td>{{ $bugReport->user_name }}</td></tr>
                    <tr><th>Email</th><td>{{ $bugReport->user_email }}</td></tr>
                    <tr><th>Submitted</th><td>{{ $bugReport->created_at->format('M d, Y H:i') }}</td></tr>
                </table>
            </div>
        </div>

        {{-- Status & Remark Form --}}
        <div class="card">
            <div class="card-header"><h4 class="card-title">Review &amp; Feedback</h4></div>
            <div class="card-body">
                @if(session('success'))
                <div class="alert alert-success py-2">{{ session('success') }}</div>
                @endif

                <form method="POST" action="{{ route('admin.bug-reports.update', $bugReport) }}">
                    @csrf @method('PUT')

                    <div class="form-group">
                        <label><strong>Status</strong></label>
                        <select name="status" class="form-control">
                            @foreach(\App\Models\BugReport::statuses() as $val => $label)
                            <option value="{{ $val }}" {{ ($bugReport->status ?? 'in_review') === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label><strong>Remark / Feedback</strong> <small class="text-muted">(visible to user)</small></label>
                        <textarea name="remark" class="form-control" rows="5" placeholder="Leave feedback for the user...">{{ old('remark', $bugReport->remark) }}</textarea>
                    </div>

                    @if($bugReport->reviewed_at)
                    <small class="text-muted d-block mb-2">
                        Last reviewed on {{ $bugReport->reviewed_at->format('M d, Y H:i') }}
                    </small>
                    @endif

                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-save mr-1"></i> Save
                    </button>
                </form>
            </div>
        </div>

        {{-- Actions --}}
        <div class="card">
            <div class="card-body">
                <a href="{{ route('admin.bug-reports.index') }}" class="btn btn-light btn-block">
                    <i class="fas fa-arrow-left mr-1"></i> Back to List
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
