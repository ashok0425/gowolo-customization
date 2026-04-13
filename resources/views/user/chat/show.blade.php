@extends('layouts.app')
@section('title', 'Chat — ' . $customizationRequest->ref_number)

@section('content')
<div class="page-header">
    <h4 class="page-title">Support Chat — #{{ $customizationRequest->ref_number }}</h4>
    <ul class="breadcrumbs">
        <li class="nav-home"><a href="{{ route('user.dashboard') }}"><i class="flaticon-home"></i></a></li>
        <li class="separator"><i class="flaticon-right-arrow"></i></li>
        <li class="nav-item"><a href="{{ route('user.request.show', $customizationRequest) }}">{{ $customizationRequest->ref_number }}</a></li>
        <li class="separator"><i class="flaticon-right-arrow"></i></li>
        <li class="nav-item"><a href="#">Chat</a></li>
    </ul>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <h4 class="card-title mb-0">Chat with Support Team</h4>
                    <a href="{{ route('user.request.show', $customizationRequest) }}" class="btn btn-secondary btn-sm ml-auto"><i class="fas fa-arrow-left"></i></a>
                </div>
            </div>

            @include('partials.chat', [
                'chats'                => $chats,
                'customizationRequest' => $customizationRequest,
                'lastId'               => $lastId,
                'postUrl'              => route('user.chat.store', $customizationRequest),
                'pollUrl'              => route('api.chat.poll', ['requestId' => $customizationRequest->id]),
                'viewerType'           => 'user',
                'viewerName'           => session('auth_user.name', 'You'),
                'viewerAvatar'         => session('auth_user.profile_pic')
                    ? rtrim(config('services.dashboardv2.base_url', 'https://dashboard.gowologlobal.com'), '/') . '/' . ltrim(session('auth_user.profile_pic'), '/')
                    : 'https://ui-avatars.com/api/?name=' . urlencode(session('auth_user.name', 'User')) . '&background=1C2B36&color=fff&size=64&rounded=true',
            ])
        </div>
    </div>
</div>
@endsection
