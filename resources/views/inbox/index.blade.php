@extends('layouts.app')
@section('title', $pageTitle)

@push('css')
<style>
    .inbox-item {
        display: flex;
        gap: 14px;
        padding: 16px 20px;
        border-bottom: 1px solid #f1f1f4;
        transition: background 0.15s;
        cursor: pointer;
        text-decoration: none !important;
        color: inherit;
    }
    .inbox-item:last-child { border-bottom: none; }
    /* Unread — light brown tint */
    .inbox-item.unread {
        background: #fdf6ec;
    }
    .inbox-item.unread:hover {
        background: #f9eddb;
    }
    /* Read — white */
    .inbox-item.read {
        background: #ffffff;
    }
    .inbox-item.read:hover {
        background: #fafbfc;
    }
    .inbox-icon {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        background: #f3e8fb;
        color: #662c87;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        flex-shrink: 0;
    }
    .inbox-icon.chat {
        background: #e8f5e9;
        color: #27ae60;
    }
    .inbox-body { flex: 1; min-width: 0; }
    .inbox-header-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 2px;
    }
    .inbox-title {
        font-size: 14px;
        font-weight: 600;
        color: #333;
        margin: 0;
    }
    .inbox-item.read .inbox-title {
        font-weight: 500;
        color: #666;
    }
    .inbox-time {
        font-size: 11px;
        color: #999;
        white-space: nowrap;
        margin-left: 12px;
    }
    .inbox-text {
        font-size: 13px;
        color: #555;
        line-height: 1.5;
        margin: 2px 0 6px;
    }
    .inbox-item.read .inbox-text { color: #888; }
    .inbox-meta {
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 11px;
    }
    .inbox-ref { color: #662c87; font-weight: 600; }
    .inbox-sender { color: #777; }
    .unread-dot {
        display: inline-block;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #e74c3c;
        margin-right: 6px;
    }
</style>
@endpush

@section('content')
<div class="page-header">
    <h4 class="page-title">{{ $pageTitle }}</h4>
    <ul class="breadcrumbs">
        <li class="nav-home"><a href="{{ Auth::guard('portal')->check() ? route('admin.dashboard') : route('user.dashboard') }}"><i class="flaticon-home"></i></a></li>
        <li class="separator"><i class="flaticon-right-arrow"></i></li>
        <li class="nav-item"><a href="#">{{ $pageTitle }}</a></li>
    </ul>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">
                    @if($pageType === 'messages')
                        <i class="fas fa-comment-dots mr-1" style="color:#662c87;"></i> Messages
                    @else
                        <i class="fas fa-bell mr-1" style="color:#662c87;"></i> Notifications
                    @endif
                    <small class="text-muted">({{ $notifications->total() }} total)</small>
                </h4>
            </div>
            <div class="card-body p-0">
                @forelse($notifications as $notif)
                <a href="{{ route('inbox.read', $notif->id) }}" class="inbox-item {{ $notif->is_read ? 'read' : 'unread' }}">
                    <div class="inbox-icon {{ $notif->type === 'new_chat' ? 'chat' : '' }}">
                        <i class="fas {{ $notif->type === 'new_chat' ? 'fa-envelope' : 'fa-bell' }}"></i>
                    </div>
                    <div class="inbox-body">
                        <div class="inbox-header-row">
                            <p class="inbox-title">
                                @if(!$notif->is_read)<span class="unread-dot"></span>@endif
                                {{ $notif->title }}
                            </p>
                            <span class="inbox-time">{{ $notif->created_at->diffForHumans() }}</span>
                        </div>
                        <div class="inbox-text">{{ $notif->body }}</div>
                        <div class="inbox-meta">
                            @if($notif->ref_number)
                                <span class="inbox-ref">#{{ $notif->ref_number }}</span>
                            @endif
                            @if($notif->sender_name)
                                <span class="inbox-sender">from {{ $notif->sender_name }}</span>
                            @endif
                            @if($notif->action_label)
                                <span class="ml-auto text-primary" style="color:#662c87!important;">{{ $notif->action_label }} <i class="fas fa-arrow-right ml-1"></i></span>
                            @endif
                        </div>
                    </div>
                </a>
                @empty
                <div class="text-center text-muted py-5">
                    <i class="fas {{ $emptyIcon }} fa-3x d-block mb-3" style="color:#ddd;"></i>
                    <h5>{{ $emptyText }}</h5>
                </div>
                @endforelse
            </div>
            @if($notifications->hasPages())
            <div class="card-footer">
                {{ $notifications->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
