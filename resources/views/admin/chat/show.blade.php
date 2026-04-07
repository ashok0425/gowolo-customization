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
        <li class="nav-item"><a href="{{ route('admin.requests.show', $customizationRequest) }}">{{ $customizationRequest->ref_number }}</a></li>
        <li class="separator"><i class="flaticon-right-arrow"></i></li>
        <li class="nav-item"><a href="#">Chat</a></li>
    </ul>
</div>

<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <h4 class="card-title mb-0">
                        Chat with {{ $customizationRequest->first_name }} {{ $customizationRequest->last_name }}
                    </h4>
                    <span class="badge badge-{{ $customizationRequest->status == 2 ? 'success' : 'info' }} ml-2">
                        {{ $customizationRequest->status_label }}
                    </span>
                    <a href="{{ route('admin.requests.show', $customizationRequest) }}" class="btn btn-secondary btn-sm ml-auto">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                </div>
            </div>

            {{-- Messages --}}
            <div class="card-body p-0">
                <div id="chat-box" style="height:420px; overflow-y:scroll; background:#f0f2f5; padding:15px;">
                    @foreach($chats as $chat)
                        @php $isStaff = $chat->sender_type === 'portal_user'; @endphp
                        <div class="d-flex {{ $isStaff ? 'justify-content-end' : 'justify-content-start' }} mb-3" data-id="{{ $chat->id }}">
                            @if(!$isStaff)
                            <div class="mr-2">
                                <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center text-white"
                                     style="width:36px;height:36px;font-size:14px;">
                                    {{ strtoupper(substr($chat->sender_name ?? 'U', 0, 1)) }}
                                </div>
                            </div>
                            @endif
                            <div style="max-width:65%">
                                <div class="rounded p-2 px-3 {{ $isStaff ? 'bg-primary text-white' : 'bg-white' }}"
                                     style="box-shadow:0 1px 2px rgba(0,0,0,.1)">
                                    @if($chat->message)
                                    <p class="mb-1" style="word-break:break-word">{{ $chat->message }}</p>
                                    @endif
                                    @if($chat->file_type === 'image' && ($chat->bunny_path || $chat->local_path))
                                    <a href="#" class="chat-img-link" data-url="{{ $chat->bunny_path ? route('admin.requests.chat', $customizationRequest) : asset($chat->local_path) }}">
                                        <img src="{{ $chat->local_path ? asset($chat->local_path) : '#' }}" class="img-fluid rounded mt-1" style="max-height:200px">
                                    </a>
                                    @elseif($chat->file_type && ($chat->bunny_path || $chat->local_path))
                                    <a href="{{ $chat->local_path ? asset($chat->local_path) : '#' }}" class="badge badge-light mt-1" download>
                                        <i class="fas fa-paperclip mr-1"></i>{{ $chat->original_filename }}
                                    </a>
                                    @endif
                                </div>
                                <small class="{{ $isStaff ? 'text-right d-block' : '' }} text-muted" style="font-size:11px">
                                    {{ $isStaff ? 'You' : $chat->sender_name }} · {{ $chat->created_at->format('M d, H:i') }}
                                </small>
                            </div>
                            @if($isStaff)
                            <div class="ml-2">
                                <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white"
                                     style="width:36px;height:36px;font-size:14px;">
                                    <i class="fas fa-user-tie" style="font-size:14px"></i>
                                </div>
                            </div>
                            @endif
                        </div>
                    @endforeach
                    <div id="chat-end"></div>
                </div>
            </div>

            {{-- Message input --}}
            <div class="card-footer bg-white">
                <form id="chat-form" enctype="multipart/form-data">
                    @csrf
                    <div class="d-flex align-items-end">
                        <div class="flex-grow-1 mr-2">
                            <textarea id="chat-msg" name="message" class="form-control" rows="2"
                                      placeholder="Type a message…" style="resize:none"></textarea>
                            <div class="mt-1">
                                <input type="file" name="file" id="chat-file" class="d-none"
                                       accept="image/*,.pdf,.doc,.docx">
                                <label for="chat-file" class="btn btn-sm btn-light mb-0" title="Attach file">
                                    <i class="fas fa-paperclip"></i>
                                </label>
                                <span id="file-name" class="small text-muted ml-1"></span>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary" id="send-btn" style="height:42px">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Image preview modal --}}
<div class="modal fade" id="imgModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Image</h5><button type="button" class="close" data-dismiss="modal">&times;</button></div>
            <div class="modal-body text-center"><img id="modal-img" src="" class="img-fluid"></div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
var lastId  = {{ $lastId }};
var reqId   = {{ $customizationRequest->id }};
var pollUrl = '{{ route('api.chat.poll', ['requestId' => $customizationRequest->id]) }}';
var postUrl = '{{ route('admin.requests.chat.store', $customizationRequest) }}';

// Scroll to bottom
function scrollBottom() {
    var box = document.getElementById('chat-box');
    box.scrollTop = box.scrollHeight;
}
scrollBottom();

// Append a message bubble
function appendMessage(msg) {
    var isStaff = msg.sender_type === 'portal_user';
    var initials = isStaff
        ? '<i class="fas fa-user-tie" style="font-size:14px"></i>'
        : (msg.sender_name || 'U').charAt(0).toUpperCase();
    var avatar = '<div class="rounded-circle ' + (isStaff ? 'bg-primary' : 'bg-secondary') +
        ' d-flex align-items-center justify-content-center text-white" style="width:36px;height:36px;font-size:14px;">' +
        initials + '</div>';

    var fileHtml = '';
    if (msg.file_type === 'image' && msg.file_url) {
        fileHtml = '<a href="#" class="chat-img-link" data-url="' + msg.file_url + '">' +
            '<img src="' + msg.file_url + '" class="img-fluid rounded mt-1" style="max-height:200px"></a>';
    } else if (msg.file_url) {
        fileHtml = '<a href="' + msg.file_url + '" class="badge badge-light mt-1" download>' +
            '<i class="fas fa-paperclip mr-1"></i>' + (msg.original_filename || 'file') + '</a>';
    }

    var html = '<div class="d-flex ' + (isStaff ? 'justify-content-end' : 'justify-content-start') + ' mb-3" data-id="' + msg.id + '">' +
        (!isStaff ? '<div class="mr-2">' + avatar + '</div>' : '') +
        '<div style="max-width:65%">' +
            '<div class="rounded p-2 px-3 ' + (isStaff ? 'bg-primary text-white' : 'bg-white') + '" style="box-shadow:0 1px 2px rgba(0,0,0,.1)">' +
                (msg.message ? '<p class="mb-1" style="word-break:break-word">' + msg.message + '</p>' : '') +
                fileHtml +
            '</div>' +
            '<small class="' + (isStaff ? 'text-right d-block ' : '') + 'text-muted" style="font-size:11px">' +
                (isStaff ? 'You' : msg.sender_name) + ' · ' + msg.created_at +
            '</small>' +
        '</div>' +
        (isStaff ? '<div class="ml-2">' + avatar + '</div>' : '') +
    '</div>';

    $('#chat-end').before(html);
}

// Poll every 5 seconds
function poll() {
    $.get(pollUrl + '?last_id=' + lastId + '&viewer=staff', function(res) {
        if (res.messages && res.messages.length) {
            res.messages.forEach(function(msg) {
                if (msg.sender_type === 'user') {
                    appendMessage(msg);
                }
            });
            lastId = res.messages[res.messages.length - 1].id;
            scrollBottom();
        }
    });
}
setInterval(poll, 5000);

// Send message
$('#chat-form').on('submit', function(e) {
    e.preventDefault();
    var msg = $('#chat-msg').val().trim();
    var file = $('#chat-file')[0].files[0];
    if (!msg && !file) return;

    var fd = new FormData(this);
    $('#send-btn').prop('disabled', true);

    $.ajax({
        url: postUrl, type: 'POST', data: fd,
        processData: false, contentType: false,
        success: function(res) {
            if (res.success) {
                appendMessage(res.chat);
                lastId = res.chat.id;
                $('#chat-msg').val('');
                $('#chat-file').val('');
                $('#file-name').text('');
                scrollBottom();
            }
        },
        complete: function() { $('#send-btn').prop('disabled', false); }
    });
});

$('#chat-file').on('change', function() {
    $('#file-name').text(this.files[0] ? this.files[0].name : '');
});

$(document).on('click', '.chat-img-link', function(e) {
    e.preventDefault();
    $('#modal-img').attr('src', $(this).data('url'));
    $('#imgModal').modal('show');
});

// Ctrl+Enter to send
$('#chat-msg').on('keydown', function(e) {
    if (e.ctrlKey && e.key === 'Enter') $('#chat-form').submit();
});
</script>
@endpush
