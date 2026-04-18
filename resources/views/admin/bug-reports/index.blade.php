@extends('layouts.app')
@section('title', 'Bug Reports')

@push('css')
<style>
    .stat-card {
        border-radius: 12px;
        border: none;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        transition: transform 0.15s;
    }
    .stat-card:hover { transform: translateY(-2px); }
    .stat-card .card-body { padding: 20px; }
    .stat-card .stat-number { font-size: 28px; font-weight: 700; margin: 0; }
    .stat-card .stat-label { color: #888; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; margin: 0; }
    .stat-card .stat-icon {
        width: 48px; height: 48px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 20px; float: right;
    }
    .stat-card.total    .stat-icon { background: #f3e8fb; color: #662c87; }
    .stat-card.approved .stat-icon { background: #e8f5e9; color: #27ae60; }
    .stat-card.dup      .stat-icon { background: #f5f5f5; color: #666; }
    .stat-card.rejected .stat-icon { background: #fdecea; color: #e74c3c; }
    .stat-card.review   .stat-icon { background: #fff3cd; color: #f39c12; }
</style>
@endpush

@section('content')
<div class="page-header">
    <h4 class="page-title">Bug Reports</h4>
    <ul class="breadcrumbs">
        <li class="nav-home"><a href="{{ route('admin.dashboard') }}"><i class="flaticon-home"></i></a></li>
        <li class="separator"><i class="flaticon-right-arrow"></i></li>
        <li class="nav-item"><a href="#">Bug Reports</a></li>
    </ul>
</div>

{{-- ============ Summary Cards ============ --}}
<div class="row">
    <div class="col-md col-sm-6">
        <div class="card stat-card total">
            <div class="card-body">
                <div class="stat-icon"><i class="fas fa-bug"></i></div>
                <p class="stat-number">{{ $summary['total'] }}</p>
                <p class="stat-label">Total Bugs</p>
            </div>
        </div>
    </div>
    <div class="col-md col-sm-6">
        <div class="card stat-card approved">
            <div class="card-body">
                <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                <p class="stat-number">{{ $summary['approved'] }}</p>
                <p class="stat-label">Approved</p>
            </div>
        </div>
    </div>
    <div class="col-md col-sm-6">
        <div class="card stat-card dup">
            <div class="card-body">
                <div class="stat-icon"><i class="fas fa-copy"></i></div>
                <p class="stat-number">{{ $summary['duplicated'] }}</p>
                <p class="stat-label">Duplicated</p>
            </div>
        </div>
    </div>
    <div class="col-md col-sm-6">
        <div class="card stat-card rejected">
            <div class="card-body">
                <div class="stat-icon"><i class="fas fa-times-circle"></i></div>
                <p class="stat-number">{{ $summary['rejected'] }}</p>
                <p class="stat-label">Rejected</p>
            </div>
        </div>
    </div>
    <div class="col-md col-sm-6">
        <div class="card stat-card review">
            <div class="card-body">
                <div class="stat-icon"><i class="fas fa-clock"></i></div>
                <p class="stat-number">{{ $summary['in_review'] }}</p>
                <p class="stat-label">In Review</p>
            </div>
        </div>
    </div>
</div>

{{-- ============ Pie Chart (Top 5 Reporters) ============ --}}
<div class="row">
    <div class="col-md-5">
        <div class="card">
            <div class="card-header"><h4 class="card-title">Top 5 Reporters</h4></div>
            <div class="card-body">
                @if($topReporters->isEmpty())
                    <p class="text-muted text-center py-4">No data to display.</p>
                @else
                    <canvas id="reportersPie" height="260"></canvas>
                @endif
            </div>
        </div>
    </div>

    {{-- ============ Filters ============ --}}
    <div class="col-md-7">
        <div class="card">
            <div class="card-header"><h4 class="card-title">Filter</h4></div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.bug-reports.index') }}">
                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label>User</label>
                            <select name="user_id" class="form-control">
                                <option value="">All Users</option>
                                @foreach($users as $u)
                                <option value="{{ $u->user_id }}" {{ request('user_id') == $u->user_id ? 'selected' : '' }}>{{ $u->user_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Status</label>
                            <select name="status" class="form-control">
                                <option value="">All Statuses</option>
                                @foreach($statuses as $val => $label)
                                <option value="{{ $val }}" {{ request('status') === $val ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Search</label>
                            <input type="text" name="search" class="form-control" placeholder="Message, user, email..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div>
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="fas fa-search mr-1"></i> Filter
                        </button>
                        <a href="{{ route('admin.bug-reports.index') }}" class="btn btn-secondary btn-sm">Reset</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ============ Bug Reports Table ============ --}}
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">All Bug Reports ({{ $bugReports->total() }})</h4>
            </div>
            <div class="card-body p-0">
                @if($bugReports->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-bug fa-3x text-muted mb-3 d-block"></i>
                    <h5 class="text-muted">No bug reports match your filters</h5>
                </div>
                @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
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
                                    <span class="badge {{ $report->status_badge }}">{{ $report->status_label }}</span>
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
                <div class="p-3">{{ $bugReports->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
@if($topReporters->isNotEmpty())
(function() {
    var ctx = document.getElementById('reportersPie');
    if (!ctx) return;
    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: {!! json_encode($topReporters->pluck('user_name')) !!},
            datasets: [{
                data: {!! json_encode($topReporters->pluck('bug_count')) !!},
                backgroundColor: ['#662c87', '#8e44ad', '#e74c3c', '#f39c12', '#27ae60'],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { boxWidth: 14, font: { size: 11 } } }
            }
        }
    });
})();
@endif
</script>
@endpush
