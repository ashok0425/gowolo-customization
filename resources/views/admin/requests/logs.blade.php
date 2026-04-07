@extends('layouts.app')
@section('title', 'Logs — ' . $customizationRequest->ref_number)

@section('content')
<div class="page-header">
    <h4 class="page-title">Activity Logs — #{{ $customizationRequest->ref_number }}</h4>
    <ul class="breadcrumbs">
        <li class="nav-home"><a href="{{ route('admin.dashboard') }}"><i class="flaticon-home"></i></a></li>
        <li class="separator"><i class="flaticon-right-arrow"></i></li>
        <li class="nav-item"><a href="{{ route('admin.requests.index') }}">Requests</a></li>
        <li class="separator"><i class="flaticon-right-arrow"></i></li>
        <li class="nav-item"><a href="{{ route('admin.requests.show', $customizationRequest) }}">{{ $customizationRequest->ref_number }}</a></li>
        <li class="separator"><i class="flaticon-right-arrow"></i></li>
        <li class="nav-item"><a href="#">Logs</a></li>
    </ul>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <h4 class="card-title">Activity Log</h4>
                    <a href="{{ route('admin.requests.show', $customizationRequest) }}" class="btn btn-secondary btn-sm ml-auto">
                        <i class="fas fa-arrow-left mr-1"></i> Back to Request
                    </a>
                </div>
            </div>
            <div class="card-body">
                @forelse($logs as $log)
                <div class="d-flex mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                    <div class="mr-3 text-center" style="min-width:40px">
                        <div class="icon-big text-center icon-primary">
                            @php
                                $icon = match($log->description) {
                                    'technician_assigned' => 'fa-user-plus',
                                    'status_changed'      => 'fa-exchange-alt',
                                    'chat_sent'           => 'fa-comment',
                                    'request_created'     => 'fa-plus-circle',
                                    default               => 'fa-info-circle',
                                };
                            @endphp
                            <i class="fas {{ $icon }} text-primary"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between">
                            <strong>{{ ucwords(str_replace('_', ' ', $log->description)) }}</strong>
                            <small class="text-muted">{{ $log->created_at->format('M d, Y H:i') }}</small>
                        </div>
                        @if($log->causer)
                        <small class="text-muted">by {{ $log->causer->full_name ?? $log->causer->name ?? '—' }}</small>
                        @endif
                        @if($log->properties->get('old') || $log->properties->get('new'))
                        <div class="mt-1 small">
                            @if($log->properties->get('old'))
                            <span class="text-danger">Before: {{ json_encode($log->properties->get('old')) }}</span><br>
                            @endif
                            @if($log->properties->get('new'))
                            <span class="text-success">After: {{ json_encode($log->properties->get('new')) }}</span>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>
                @empty
                <div class="text-center text-muted py-4">
                    <i class="fas fa-history fa-2x d-block mb-2"></i>
                    No activity recorded yet.
                </div>
                @endforelse

                <div class="mt-3">{{ $logs->links() }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
