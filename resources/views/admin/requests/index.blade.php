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
                                            @if($canEdit)
                                            <a class="dropdown-item" href="{{ route('admin.requests.edit', $req) }}">
                                                <i class="fas fa-edit mr-2 text-warning"></i> Edit Request
                                            </a>
                                            @endif
                                            <a class="dropdown-item" href="{{ route('admin.requests.chat', $req) }}">
                                                <i class="fas fa-comment mr-2 text-info"></i> Chat
                                            </a>
                                            @if($seeAll)
                                            <a class="dropdown-item" href="{{ route('admin.requests.logs', $req) }}">
                                                <i class="fas fa-history mr-2 text-secondary"></i> View Logs
                                            </a>
                                            @endif
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item" href="#" data-toggle="modal" data-target="#statusModal"
                                               data-cuid="{{ $req->cuid }}" data-status="{{ $req->status }}">
                                                <i class="fas fa-exchange-alt mr-2 text-warning"></i> Change Status
                                            </a>
                                            @if($canAssign)
                                            <a class="dropdown-item" href="#" data-toggle="modal" data-target="#assignModal"
                                               data-cuid="{{ $req->cuid }}"
                                               data-tech1="{{ $req->assigned_tech_id1 }}"
                                               data-tech2="{{ $req->assigned_tech_id2 }}"
                                               data-supervisor="{{ $req->supervisor_id }}"
                                               data-paytype="{{ $req->pay_type }}"
                                               data-amount="{{ $req->pay_amount }}">
                                                <i class="fas fa-user-plus mr-2 text-success"></i> Assign Technician
                                            </a>
                                            @endif
                                            @if($req->pay_type == 2 && $req->pay_amount > 0)
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item" href="{{ route('documents.quotation', $req->cuid) }}" target="_blank">
                                                <i class="fas fa-file-pdf mr-2" style="color:#662c87;"></i> Download Quotation
                                            </a>
                                            @endif
                                            @if($req->pay_status == 1)
                                            <a class="dropdown-item" href="{{ route('documents.invoice', $req->cuid) }}" target="_blank">
                                                <i class="fas fa-file-invoice mr-2 text-success"></i> Download Invoice
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

{{-- ================= Change Status Modal (Bootstrap 4) ================= --}}
<div class="modal fade" id="statusModal" tabindex="-1" role="dialog" aria-labelledby="statusModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background:#662c87;color:#fff;">
                <h5 class="modal-title" id="statusModalLabel"><i class="fas fa-exchange-alt mr-2"></i> Change Request Status</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color:#fff;text-shadow:none;opacity:1;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="statusForm">
                <div class="modal-body p-4">
                    <input type="hidden" id="statusReqId">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><strong>New Status</strong></label>
                                <select class="form-control form-control-lg" id="statusSelect">
                                    @if($seeAll)
                                        @foreach($statuses as $val => $label)
                                        {{-- "Approved" (4) is only selectable once the request is already "Approved by Team" (7) --}}
                                        <option value="{{ $val }}" {{ $val == 4 ? 'data-requires-team="1"' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    @else
                                        <option value="2">In Review</option>
                                        <option value="3">Sent for Review</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label><strong>Comments</strong> <small class="text-muted">(optional)</small></label>
                        <textarea class="form-control" id="statusComments" rows="4" placeholder="Add any notes or comments about this status change..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="submitStatus()">
                        <i class="fas fa-check mr-1"></i> Update Status
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ================= Assign Technician Modal (Bootstrap 4) ================= --}}
@if($canAssign)
<div class="modal fade" id="assignModal" tabindex="-1" role="dialog" aria-labelledby="assignModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background:#662c87;color:#fff;">
                <h5 class="modal-title" id="assignModalLabel"><i class="fas fa-user-plus mr-2"></i> Assign Technician & Set Price</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color:#fff;text-shadow:none;opacity:1;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="assignForm">
                @csrf
                <div class="modal-body p-4">
                    <input type="hidden" id="assignReqId">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><strong>Technician 1</strong> <span class="text-danger">*</span></label>
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
                                <label><strong>Technician 2</strong> <small class="text-muted">(optional)</small></label>
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
                                <label><strong>Supervisor</strong> <small class="text-muted">(optional)</small></label>
                                <select name="supervisor_id" id="assignSupervisor" class="form-control">
                                    <option value="">— None —</option>
                                    @foreach($supervisors as $sup)
                                    <option value="{{ $sup->id }}">{{ $sup->full_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <hr>
                    <h6 class="mb-3"><i class="fas fa-dollar-sign mr-1 text-success"></i> Payment Details</h6>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><strong>Request Type</strong></label>
                                <select name="pay_type" id="assignPayType" class="form-control">
                                    <option value="1">Free</option>
                                    <option value="2">Paid</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6" id="amountGroup" style="display:none;">
                            <div class="form-group">
                                <label><strong>Amount (USD)</strong> <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">$</span>
                                    </div>
                                    <input type="number" name="pay_amount" id="assignAmount" class="form-control" step="0.01" min="0" placeholder="0.00">
                                </div>
                                <small class="text-muted">User will see "Pay Now" after amount is set.</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="submitAssign()">
                        <i class="fas fa-check mr-1"></i> Assign & Save
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection

@push('js')
<script>
// ==================== Status Modal ====================
// Populate from data-attributes when modal opens
$('#statusModal').on('show.bs.modal', function(e) {
    var $link = $(e.relatedTarget);
    var currentStatus = parseInt($link.data('status'), 10);

    $('#statusReqId').val($link.data('cuid'));
    $('#statusComments').val('');

    // "Approved" (4) option is only selectable when current status is
    // "Approved by Team" (7). Hide it otherwise.
    $('#statusSelect option').each(function() {
        var val = parseInt($(this).val(), 10);
        if (val === 4) {
            if (currentStatus === 7) {
                $(this).prop('hidden', false).prop('disabled', false);
            } else {
                $(this).prop('hidden', true).prop('disabled', true);
            }
        }
    });

    $('#statusSelect').val(currentStatus);
});

function submitStatus() {
    var cuid = $('#statusReqId').val();
    $.post('/admin/requests/' + cuid + '/status', {
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

// ==================== Assign Technician Modal ====================
$('#assignModal').on('show.bs.modal', function(e) {
    var $link = $(e.relatedTarget);
    $('#assignReqId').val($link.data('cuid'));
    $('#assignTech1').val($link.data('tech1') || '');
    $('#assignTech2').val($link.data('tech2') || '');
    $('#assignSupervisor').val($link.data('supervisor') || '');
    $('#assignPayType').val($link.data('paytype') || 1);
    $('#assignAmount').val($link.data('amount') || '');
    // Toggle amount visibility based on pay type
    $('#amountGroup').toggle($('#assignPayType').val() == '2');
});

// Toggle amount field when pay type changes
$(document).on('change', '#assignPayType', function() {
    $('#amountGroup').toggle(this.value == '2');
});

function submitAssign() {
    var cuid = $('#assignReqId').val();
    var payType = $('#assignPayType').val();
    var payAmount = $('#assignAmount').val();

    if (payType == '2' && (!payAmount || parseFloat(payAmount) <= 0)) {
        alert('Please enter a valid amount for paid requests.');
        $('#assignAmount').focus();
        return;
    }

    $.post('/admin/requests/' + cuid + '/assign', {
        _token: $('meta[name="csrf-token"]').attr('content'),
        assigned_tech_id1: $('#assignTech1').val(),
        assigned_tech_id2: $('#assignTech2').val(),
        supervisor_id: $('#assignSupervisor').val(),
        pay_type: payType,
        pay_amount: payAmount
    }).done(function() {
        $('#assignModal').modal('hide');
        location.reload();
    }).fail(function(xhr) {
        alert(xhr.responseJSON?.message || 'Error assigning technician.');
    });
}
</script>
@endpush
