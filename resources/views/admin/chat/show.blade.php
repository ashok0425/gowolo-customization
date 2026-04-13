@extends('layouts.app')
@section('title', 'Chat — ' . $customizationRequest->ref_number)

@section('content')
<div class="page-header">
    <h4 class="page-title">Chat — #{{ $customizationRequest->ref_number }}</h4>
    <ul class="breadcrumbs">
        <li class="nav-home"><a href="{{ route('admin.dashboard') }}"><i class="flaticon-home"></i></a></li>
        <li class="separator"><i class="flaticon-right-arrow"></i></li>
        <li class="nav-item"><a href="{{ route('admin.requests.index') }}">Requests</a></li>
        <li class="separator"><i class="flaticon-right-arrow"></i></li>
        <li class="nav-item"><a href="#">Chat</a></li>
    </ul>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <h4 class="card-title mb-0">Chat with {{ $customizationRequest->first_name }} {{ $customizationRequest->last_name }}</h4>
                    <span class="badge {{ $customizationRequest->status_badge }} ml-2">{{ $customizationRequest->status_label }}</span>
                    <a href="{{ route('admin.requests.show', $customizationRequest) }}" class="btn btn-secondary btn-sm ml-auto"><i class="fas fa-arrow-left"></i></a>
                </div>
            </div>

            @php $portalMe = Auth::guard('portal')->user(); @endphp
            @include('partials.chat', [
                'chats'                => $chats,
                'customizationRequest' => $customizationRequest,
                'lastId'               => $lastId,
                'postUrl'              => route('admin.requests.chat.store', $customizationRequest),
                'pollUrl'              => route('api.chat.poll', ['requestId' => $customizationRequest->id]),
                'viewerType'           => 'staff',
                'viewerName'           => $portalMe->full_name,
                'viewerAvatar'         => 'https://ui-avatars.com/api/?name=' . urlencode($portalMe->full_name) . '&background=662c87&color=fff&size=64&rounded=true',
            ])
        </div>
    </div>
</div>
@endsection
