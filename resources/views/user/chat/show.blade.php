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
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <h4 class="card-title mb-0">Chat with Support Team</h4>
                    <a href="{{ route('user.request.show', $customizationRequest) }}" class="btn btn-secondary btn-sm ml-auto">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                </div>
            </div>

            {{-- Messages --}}
            <div class="card-body p-0">
                <div id="chat-box" style="height:420px; overflow-y:scroll; background:#f0f2f5; padding:15px;">
                    @foreach($chats as $chat)
                        @php $isUser = $chat->sender_type === 'user'; @endphp
                        <div class="d-flex {{ $isUser ? 'justify-content-end' : 'justify-content-start' }} mb-3" data-id="{{ $chat->id }}">
                            @if(!$isUser)
                            <div class="mr-2">
                                <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white"
                                     style="width:36px;height:36px;">
                                    <i class="fas fa-headset" style="font-size:14px"></i>
                                </div>
                            </div>
                            @endif
                            <div style="max-width:65%">
                                <div class="rounded p-2 px-3 {{ $isUser ? 'bg-primary text-white' : 'bg-white' }}"
                                     style="box-shadow:0 1px 2px rgba(0,0,0,.1)">
                                    @if(!$isUser)
                                    <small class="d-block font-weight-bold mb-1" style="font-size:11px">
                                        Support Team
                                    </small>
                                    @endif
                                    @if($chat->message)
                                    <p class="mb-1" style="word-break:break-word">{{ $chat->message }}</p>
                                    @endif
                                    @if($chat->file_type === 'image' && $chat->local_path)
                                    <a href="#" class="chat-img-link" data-url="{{ asset($chat->local_path) }}">
                                        <img src="{{ asset($chat->local_path) }}" class="img-fluid rounded mt-1" style="max-height:200px">
                                    </a>
                                    @elseif($chat->file_type && $chat->local_path)
                                    <a href="{{ asset($chat->local_path) }}" class="badge badge-light mt-1" download>
                                        <i class="fas fa-paperclip mr-1"></i>{{ $chat->original_filename }}
                                    </a>
                                    @endif
                                </div>
                                <small class="{{ $isUser ? 'text-right d-block' : '' }} text-muted" style="font-size:11px">
                                    {{ $chat->created_at->format('M d, H:i') }}
                                </small>
                            </div>
                            @if($isUser)
                            <div class="ml-2">
                                <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center text-white"
                                     style="width:36px;height:36px;font-size:14px;">
                                    {{ strtoupper(substr(session('auth_user.name', 'U'), 0, 1)) }}
                                </div>
                            </div>
                            @endif
                        </div>
                    @endforeach
                    <div id="chat-end"></div>
                </div>
            </div>

            {{-- Input --}}
            <div class="card-footer bg-white">
                <form id="chat-form" enctype="multipart/form-data">
                    @csrf
                    <div class="d-flex align-items-end">
                        <div class="flex-grow-1 mr-2">
                            <textarea id="chat-msg" name="message" class="form-control" rows="2"
                                      placeholder="Type a message… (Ctrl+Enter to send)" style="resize:none"></textarea>
                            <div class="mt-1">
                                <input type="file" name="file" id="chat-file" class="d-none"
                                       accept="image/*,.pdf,.doc,.docx">
                                <label for="chat-file" class="btn btn-sm btn-light mb-0" title="Attach">
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
var pollUrl = '{{ route('api.chat.poll', ['requestId' => $customizationRequest->id]) }}';
var postUrl = '{{ route('user.chat.store', $customizationRequest) }}';
var myName  = '{{ session('auth_user.name', 'You') }}';

function scrollBottom() {
    var box = document.getElementById('chat-box');
    box.scrollTop = box.scrollHeight;
}
scrollBottom();

function appendMessage(msg) {
    var isUser = msg.sender_type === 'user';
    var fileHtml = '';
    if (msg.file_type === 'image' && msg.file_url) {
        fileHtml = '<a href="#" class="chat-img-link" data-url="' + msg.file_url + '">' +
            '<img src="' + msg.file_url + '" class="img-fluid rounded mt-1" style="max-height:200px"></a>';
    } else if (msg.file_url) {
        fileHtml = '<a href="' + msg.file_url + '" class="badge badge-light mt-1" download>' +
            '<i class="fas fa-paperclip mr-1"></i>' + (msg.original_filename || 'file') + '</a>';
    }

    var supportAvatar = '<div class="mr-2"><div class="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white" style="width:36px;height:36px;"><i class="fas fa-headset" style="font-size:14px"></i></div></div>';
    var userAvatar = '<div class="ml-2"><div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center text-white" style="width:36px;height:36px;font-size:14px;">' + myName.charAt(0).toUpperCase() + '</div></div>';

    var html = '<div class="d-flex ' + (isUser ? 'justify-content-end' : 'justify-content-start') + ' mb-3" data-id="' + msg.id + '">' +
        (!isUser ? supportAvatar : '') +
        '<div style="max-width:65%">' +
            '<div class="rounded p-2 px-3 ' + (isUser ? 'bg-primary text-white' : 'bg-white') + '" style="box-shadow:0 1px 2px rgba(0,0,0,.1)">' +
                (!isUser ? '<small class="d-block font-weight-bold mb-1" style="font-size:11px">Support Team</small>' : '') +
                (msg.message ? '<p class="mb-1" style="word-break:break-word">' + msg.message + '</p>' : '') +
                fileHtml +
            '</div>' +
            '<small class="' + (isUser ? 'text-right d-block ' : '') + 'text-muted" style="font-size:11px">' + msg.created_at + '</small>' +
        '</div>' +
        (isUser ? userAvatar : '') +
    '</div>';

    $('#chat-end').before(html);
}

// Poll for new staff messages every 5 seconds
function poll() {
    $.get(pollUrl + '?last_id=' + lastId + '&viewer=user', function(res) {
        if (res.messages && res.messages.length) {
            res.messages.forEach(function(msg) {
                if (msg.sender_type === 'portal_user') {
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
                // Optimistically show sent message
                var fakeMsg = {
                    id: ++lastId,
                    sender_type: 'user',
                    sender_name: myName,
                    message: msg,
                    file_type: null,
                    file_url: null,
                    created_at: new Date().toLocaleString('en-US', {month:'short',day:'numeric',hour:'2-digit',minute:'2-digit'})
                };
                appendMessage(fakeMsg);
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

$('#chat-msg').on('keydown', function(e) {
    if (e.ctrlKey && e.key === 'Enter') $('#chat-form').submit();
});
</script>
@endpush
