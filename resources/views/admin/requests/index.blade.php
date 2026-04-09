@extends('layouts.app')
@section('title', !$seeAll ? 'My Assignments' : 'All Requests')

@section('content')
<div class="page-header">
    <h4 class="page-title">{{ !$seeAll ? 'My Assignments' : 'All Requests' }}</h4>
    <ul class="breadcrumbs">
        <li class="nav-home"><a href="{{ route('admin.dashboard') }}"><i class="flaticon-home"></i></a></li>
        <li class="separator"><i class="flaticon-right-arrow"></i></li>
        <li class="nav-item"><a href="#">Requests</a></li>
    </ul>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                {{-- Filters --}}
                <form method="GET" action="{{ route('admin.requests.index') }}" id="filter-form">
                    <div class="row mb-3">
                        <div class="col-sm-3">
                            <label>Status</label>
                            <select name="status" class="form-control select2">
                                <option value="">All</option>
                                <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Pending</option>
                                <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Assigned</option>
                                <option value="2" {{ request('status') == '2' ? 'selected' : '' }}>In Review</option>
                                <option value="3" {{ request('status') == '3' ? 'selected' : '' }}>Sent for Review</option>
                                <option value="4" {{ request('status') == '4' ? 'selected' : '' }}>Approved</option>
                                <option value="5" {{ request('status') == '5' ? 'selected' : '' }}>Completed</option>
                            </select>
                        </div>
                        @if($seeAll)
                        <div class="col-sm-2">
                            <label>Type</label>
                            <select name="pay_type" class="form-control select2">
                                <option value="">All</option>
                                <option value="1" {{ request('pay_type') == '1' ? 'selected' : '' }}>Free</option>
                                <option value="2" {{ request('pay_type') == '2' ? 'selected' : '' }}>Paid</option>
                            </select>
                        </div>
                        <div class="col-sm-2">
                            <label>Payment</label>
                            <select name="pay_status" class="form-control select2">
                                <option value="">All</option>
                                <option value="1" {{ request('pay_status') == '1' ? 'selected' : '' }}>Paid</option>
                                <option value="0" {{ request('pay_status') == '0' ? 'selected' : '' }}>Unpaid</option>
                            </select>
                        </div>
                        @endif
                        <div class="col-sm-3">
                            <label>Date Range</label>
                            <input type="text" name="date_range" class="form-control date-range"
                                   placeholder="Select date range"
                                   value="{{ request('date_from') && request('date_to') ? request('date_from') . ' - ' . request('date_to') : '' }}"
                                   autocomplete="off">
                        </div>
                        <div class="col-sm-2">
                            <label>Search</label>
                            <input type="text" name="search" class="form-control" placeholder="Ref, name, email…" value="{{ request('search') }}">
                        </div>
                        <div class="col-sm-1 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary btn-sm mr-1">
                                <i class="fas fa-search"></i>
                            </button>
                            <a href="{{ route('admin.requests.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-times"></i>
                            </a>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead>
                            <tr>
                                <th>Ref #</th>
                                <th>Customer</th>
                                <th>Company</th>
                                <th>Status</th>
                                @if($seeAll)
                                <th>Type</th>
                                <th>Payment</th>
                                @endif
                                <th>Tech 1</th>
                                <th>Supervisor</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($requests as $req)
                            <tr>
                                <td><strong>{{ $req->ref_number }}</strong></td>
                                <td>{{ $req->first_name }} {{ $req->last_name }}</td>
                                <td>{{ $req->company_name }}</td>
                                <td>
                                    <span class="badge {{ $req->status_badge }}">{{ $req->status_label }}</span>
                                </td>
                                @if($seeAll)
                                <td>
                                    @if($req->pay_type == 1)
                                        <span class="badge badge-secondary">Free</span>
                                    @else
                                        <span class="badge badge-primary">Paid</span>
                                    @endif
                                </td>
                                <td>
                                    @if($req->pay_type == 1)
                                        <span class="badge badge-secondary">N/A</span>
                                    @else
                                        <span class="badge {{ $req->pay_status ? 'badge-success' : 'badge-danger' }}">
                                            {{ $req->pay_status ? 'Paid' : 'Unpaid' }}
                                        </span>
                                    @endif
                                </td>
                                @endif
                                <td>{{ $req->primaryTechnician?->full_name ?? '—' }}</td>
                                <td>{{ $req->supervisor_name ?? '—' }}</td>
                                <td>{{ $req->created_at->format('M d, Y') }}</td>
                                <td>
                                    <a href="{{ route('admin.requests.show', $req) }}" class="btn btn-sm btn-primary" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.requests.chat', $req) }}" class="btn btn-sm btn-info" title="Chat">
                                        <i class="fas fa-comment"></i>
                                    </a>
                                    @if($seeAll)
                                    <a href="{{ route('admin.requests.logs', $req) }}" class="btn btn-sm btn-secondary" title="Logs">
                                        <i class="fas fa-history"></i>
                                    </a>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="{{ !$seeAll ? 8 : 10 }}" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-2x d-block mb-2"></i>
                                    No requests found.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-end mt-3">
                    {{ $requests->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
