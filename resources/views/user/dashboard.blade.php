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
                                    @if($req->pay_type == 1)
                                        <span class="badge badge-secondary">Free</span>
                                    @else
                                        <span class="badge {{ $req->pay_status ? 'badge-success' : 'badge-danger' }}">
                                            {{ $req->pay_status ? 'Paid' : 'Pending Payment' }}
                                        </span>
                                    @endif
                                </td>
                                <td>{{ $req->primaryTechnician?->full_name ?? '—' }}</td>
                                <td>{{ $req->created_at->format('M d, Y') }}</td>
                                <td>
                                    <a href="{{ route('user.request.show', $req) }}" class="btn btn-sm btn-primary" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('user.chat.show', $req) }}" class="btn btn-sm btn-info" title="Chat">
                                        <i class="fas fa-comment"></i>
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
