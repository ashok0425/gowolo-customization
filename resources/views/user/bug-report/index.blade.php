@extends('layouts.app')
@section('title', 'My Bug Reports')

@section('content')
<div class="page-header">
    <h4 class="page-title">My Bug Reports</h4>
    <ul class="breadcrumbs">
        <li class="nav-home"><a href="{{ route('user.dashboard') }}"><i class="flaticon-home"></i></a></li>
        <li class="separator"><i class="flaticon-right-arrow"></i></li>
        <li class="nav-item"><a href="#">Bug Reports</a></li>
    </ul>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <h4 class="card-title">Your Bug Reports</h4>
                    <a href="{{ route('user.bug-report.create') }}" class="btn btn-primary btn-round ml-auto btn-sm">
                        <i class="fa fa-plus"></i> Report a Bug
                    </a>
                </div>
            </div>
            <div class="card-body">
                @if($bugReports->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-bug fa-3x text-muted mb-3 d-block"></i>
                    <h5 class="text-muted">No bug reports yet</h5>
                    <p class="text-muted">If you find any issues, let us know!</p>
                    <a href="{{ route('user.bug-report.create') }}" class="btn btn-primary">
                        <i class="fa fa-plus mr-1"></i> Report a Bug
                    </a>
                </div>
                @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Message</th>
                                <th>Screenshot</th>
                                <th>Status</th>
                                <th>Submitted</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bugReports as $report)
                            <tr>
                                <td>{{ $report->id }}</td>
                                <td>{{ Str::limit($report->message, 100) }}</td>
                                <td>
                                    @if($report->screenshot_path)
                                        <a href="{{ asset($report->screenshot_path) }}" target="_blank">
                                            <img src="{{ asset($report->screenshot_path) }}" alt="Screenshot"
                                                 style="max-width:60px;max-height:40px;border-radius:4px;border:1px solid #ddd;">
                                        </a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($report->is_read)
                                        <span class="badge badge-success">Reviewed</span>
                                    @else
                                        <span class="badge badge-warning">Pending</span>
                                    @endif
                                </td>
                                <td>{{ $report->created_at->format('M d, Y H:i') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
