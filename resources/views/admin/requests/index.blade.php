@extends('layouts.app')
@section('title', !$seeAll ? 'My Assignments' : 'All Requests')

@push('css')
<style>
    .table-responsive { overflow: visible !important; }
    .dropdown-menu { z-index: 9999999 !important; }
</style>
@endpush

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
                                @foreach($statuses as $val => $label)
                                <option value="{{ $val }}" {{ request('status') === (string)$val ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
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
                            <button type="submit" class="btn btn-primary btn-sm mr-1"><i class="fas fa-search"></i></button>
                            <a href="{{ route('admin.requests.index') }}" class="btn btn-secondary btn-sm"><i class="fas fa-times"></i></a>
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
                                <th style="width:60px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($requests as $req)
                            <tr>
                                <td><strong>{{ $req->ref_number }}</strong></td>
                                <td>{{ $req->first_name }} {{ $req->last_name }}</td>
                                <td>{{ $req->company_name }}</td>
                                <td><span class="badge {{ $req->status_badge }}">{{ $req->status_label }}</span></td>
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
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-toggle="dropdown">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a class="dropdown-item" href="{{ route('admin.requests.show', $req) }}">
                                                <i class="fas fa-eye mr-2 text-primary"></i> View Details
                                            </a>
                                            <a class="dropdown-item" href="{{ route('admin.requests.chat', $req) }}">
                                                <i class="fas fa-comment mr-2 text-info"></i> Chat
                                            </a>
                                            @if($seeAll)
                                            <a class="dropdown-item" href="{{ route('admin.requests.logs', $req) }}">
                                                <i class="fas fa-history mr-2 text-secondary"></i> View Logs
                                            </a>
                                            @endif
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item" href="#" onclick="openStatusModal({{ $req->id }}, {{ $req->status }});return false;">
                                                <i class="fas fa-exchange-alt mr-2 text-warning"></i> Change Status
                                            </a>
                                            @if($canAssign)
                                            <a class="dropdown-item" href="#" onclick="openAssignModal({{ $req->id }}, {{ $req->assigned_tech_id1 ?? 'null' }}, {{ $req->assigned_tech_id2 ?? 'null' }}, {{ $req->supervisor_id ?? 'null' }});return false;">
                                                <i class="fas fa-user-plus mr-2 text-success"></i> Assign Technician
                                            </a>
                                            @endif
                                        </div>
                                    </div>
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

{{-- Change Status Modal --}}
<div class="modal fade" id="statusModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-exchange-alt mr-1"></i> Change Status</h5>
                <button type="button" class="close" data-dismiss="modal" style="color:#fff;"><span>&times;</span></button>
            </div>
            <form id="statusForm">
                <div class="modal-body">
                    <input type="hidden" id="statusReqId">
                    <div class="form-group">
                        <label>New Status</label>
                        <select class="form-control" id="statusSelect">
                            @if($seeAll)
                                @foreach($statuses as $val => $label)
                                <option value="{{ $val }}">{{ $label }}</option>
                                @endforeach
                            @else
                                <option value="2">In Review</option>
                                <option value="3">Sent for Review</option>
                            @endif
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Comments (optional)</label>
                        <textarea class="form-control" id="statusComments" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary btn-sm" onclick="submitStatus()">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Assign Technician Modal --}}
@if($canAssign)
<div class="modal fade" id="assignModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-user-plus mr-1"></i> Assign Technician</h5>
                <button type="button" class="close" data-dismiss="modal" style="color:#fff;"><span>&times;</span></button>
            </div>
            <form id="assignForm" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="assignReqId">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Technician 1 <span class="text-danger">*</span></label>
                                <select name="assigned_tech_id1" id="assignTech1" class="form-control" required>
                                    <option value="">— Select —</option>
                                    @foreach($technicians as $tech)
                                    <option value="{{ $tech->id }}">{{ $tech->full_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Technician 2</label>
                                <select name="assigned_tech_id2" id="assignTech2" class="form-control">
                                    <option value="">— None —</option>
                                    @foreach($technicians as $tech)
                                    <option value="{{ $tech->id }}">{{ $tech->full_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Supervisor</label>
                                <select name="supervisor_id" id="assignSupervisor" class="form-control">
                                    <option value="">— None —</option>
                                    @foreach($supervisors as $sup)
                                    <option value="{{ $sup->id }}">{{ $sup->full_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary btn-sm" onclick="submitAssign()">Assign</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection

@push('js')
<script>
// Change Status
function openStatusModal(reqId, currentStatus) {
    $('#statusReqId').val(reqId);
    $('#statusSelect').val(currentStatus);
    $('#statusComments').val('');
    $('#statusModal').modal('show');
}

function submitStatus() {
    var reqId = $('#statusReqId').val();
    $.post('/admin/requests/' + reqId + '/status', {
        _token: $('meta[name="csrf-token"]').attr('content'),
        status: $('#statusSelect').val(),
        technician_comments: $('#statusComments').val()
    }).done(function(res) {
        if (res.success) {
            $('#statusModal').modal('hide');
            location.reload();
        }
    }).fail(function(xhr) {
        alert(xhr.responseJSON?.message || 'Error updating status.');
    });
}

// Assign Technician
function openAssignModal(reqId, tech1, tech2, supervisor) {
    $('#assignReqId').val(reqId);
    $('#assignTech1').val(tech1 || '');
    $('#assignTech2').val(tech2 || '');
    $('#assignSupervisor').val(supervisor || '');
    $('#assignModal').modal('show');
}

function submitAssign() {
    var reqId = $('#assignReqId').val();
    $.post('/admin/requests/' + reqId + '/assign', {
        _token: $('meta[name="csrf-token"]').attr('content'),
        assigned_tech_id1: $('#assignTech1').val(),
        assigned_tech_id2: $('#assignTech2').val(),
        supervisor_id: $('#assignSupervisor').val()
    }).done(function() {
        $('#assignModal').modal('hide');
        location.reload();
    }).fail(function(xhr) {
        alert(xhr.responseJSON?.message || 'Error assigning technician.');
    });
}
</script>
@endpush
