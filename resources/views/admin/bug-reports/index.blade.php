@extends('layouts.app')
@section('title', 'Bug Reports')

@section('content')
<div class="page-header">
    <h4 class="page-title">Bug Reports</h4>
    <ul class="breadcrumbs">
        <li class="nav-home"><a href="{{ route('admin.dashboard') }}"><i class="flaticon-home"></i></a></li>
        <li class="separator"><i class="flaticon-right-arrow"></i></li>
        <li class="nav-item"><a href="#">Bug Reports</a></li>
    </ul>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">All Bug Reports</h4>
            </div>
            <div class="card-body">
                @if($bugReports->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-bug fa-3x text-muted mb-3 d-block"></i>
                    <h5 class="text-muted">No bug reports yet</h5>
                </div>
                @else
                <div class="table-responsive">
                    <table class="table table-hover" id="bugReportsTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>User</th>
                                <th>Message</th>
                                <th>Screenshot</th>
                                <th>Status</th>
                                <th>Submitted</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bugReports as $report)
                            <tr class="{{ !$report->is_read ? 'font-weight-bold' : '' }}">
                                <td>{{ $report->id }}</td>
                                <td>
                                    {{ $report->user_name }}<br>
                                    <small class="text-muted">{{ $report->user_email }}</small>
                                </td>
                                <td>{{ Str::limit($report->message, 80) }}</td>
                                <td>
                                    @if($report->screenshot_path)
                                        <span class="badge badge-info"><i class="fas fa-image"></i> Yes</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($report->is_read)
                                        <span class="badge badge-success">Read</span>
                                    @else
                                        <span class="badge badge-warning">New</span>
                                    @endif
                                </td>
                                <td>{{ $report->created_at->format('M d, Y H:i') }}</td>
                                <td>
                                    <a href="{{ route('admin.bug-reports.show', $report) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                </td>
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

@push('js')
<script>
$(function() {
    $('#bugReportsTable').DataTable({
        order: [[0, 'desc']],
        pageLength: 25
    });
});
</script>
@endpush
