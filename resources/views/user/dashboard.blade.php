@extends('layouts.app')
@section('title', 'My Requests')

@section('content')
<div class="page-header">
    <h4 class="page-title">My Requests</h4>
    <ul class="breadcrumbs">
        <li class="nav-home"><a href="#"><i class="flaticon-home"></i></a></li>
        <li class="separator"><i class="flaticon-right-arrow"></i></li>
        <li class="nav-item"><a href="#">My Requests</a></li>
    </ul>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <h4 class="card-title">Your Customization Requests</h4>
                    <a href="{{ route('user.request.create') }}" class="btn btn-primary btn-round ml-auto btn-sm">
                        <i class="fa fa-plus"></i> New Request
                    </a>
                </div>
            </div>
            <div class="card-body">
                @if($requests->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-paint-brush fa-3x text-muted mb-3 d-block"></i>
                    <h5 class="text-muted">No requests yet</h5>
                    <p class="text-muted">Submit your first customization request to get started.</p>
                    <a href="{{ route('user.request.create') }}" class="btn btn-primary">
                        <i class="fa fa-plus mr-1"></i> New Request
                    </a>
                </div>
                @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Ref #</th>
                                <th>Company</th>
                                <th>Status</th>
                                <th>Payment</th>
                                <th>Assigned To</th>
                                <th>Submitted</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($requests as $req)
                            <tr>
                                <td><strong>{{ $req->ref_number }}</strong></td>
                                <td>{{ $req->company_name }}</td>
                                <td>
                                    <span class="badge {{ $req->status_badge }}">{{ $req->status_label }}</span>
                                </td>
                                <td>
                                    @php
                                        // Pay Now URL pattern used by dashboardv2's netwostore:
                                        // https://netwostore.gowologlobal.com/gowolo-make-payment?uid={base64(email)}&type=custom&id={id}
                                        $payBase = rtrim(config('services.dashboardv2.make_payment_url'), '/');
                                        $uid     = base64_encode(session('auth_user.email') ?? $req->email);
                                        $paymentUrl = $payBase . '?uid=' . $uid . '&type=custom&id=' . $req->id;
                                    @endphp
                                    @if(isset($req->pay_status) && $req->pay_status == 1)
                                        <span class="text-primary font-weight-bold"><i class="fas fa-check-circle mr-1"></i> Payment Done</span>
                                    @elseif($req->pay_type == 2 && !empty($req->pay_amount) && empty($req->pay_status))
                                        <a href="{{ $paymentUrl }}" target="_blank"
                                           class="btn btn-sm paynow"
                                           style="background:#662c87;color:#fff;border-radius:50px;padding:4px 14px;font-size:12px;font-weight:600;">
                                            <i class="fas fa-credit-card mr-1"></i> Pay Now ${{ number_format($req->pay_amount, 2) }}
                                        </a>
                                    @elseif($req->pay_type == 1)
                                        <span class="badge badge-secondary">Free</span>
                                    @else
                                        <span class="text-warning">Awaiting Price</span>
                                    @endif
                                </td>
                                <td>{{ $req->primaryTechnician?->full_name ?? '—' }}</td>
                                <td>{{ $req->created_at->format('M d, Y') }}</td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-toggle="dropdown">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a class="dropdown-item" href="{{ route('user.request.show', $req) }}">
                                                <i class="fas fa-eye mr-2 text-primary"></i> View Details
                                            </a>
                                            @if(in_array($req->status, [0, 1]))
                                            <a class="dropdown-item" href="{{ route('user.request.edit', $req) }}">
                                                <i class="fas fa-edit mr-2 text-warning"></i> Edit Request
                                            </a>
                                            @endif
                                            <a class="dropdown-item" href="{{ route('user.chat.show', $req) }}">
                                                <i class="fas fa-comment mr-2 text-info"></i> Chat
                                            </a>
                                            @if($req->pay_type == 2 && $req->pay_amount > 0 && $req->pay_status != 1)
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item" href="{{ route('documents.quotation', $req->cuid) }}" target="_blank">
                                                <i class="fas fa-file-pdf mr-2" style="color:#662c87;"></i> Download Quotation
                                            </a>
                                            @endif
                                            @if($req->pay_status == 1)
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item" href="{{ route('documents.invoice', $req->cuid) }}" target="_blank">
                                                <i class="fas fa-file-invoice mr-2 text-success"></i> Download Invoice
                                            </a>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center mt-3">
                    {{ $requests->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
