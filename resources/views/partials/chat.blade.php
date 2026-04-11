{{--
    Shared chat partial — used by both admin and user chat views.
    Required variables: $chats, $customizationRequest, $lastId, $postUrl, $pollUrl, $viewerType ('staff'|'user'), $viewerName, $myInitial
--}}

@push('css')
<link rel="stylesheet" href="{{ asset('admin/assets/css/summernote-bs4.css') }}">
<style>
    .chat-bubble p:last-child { margin-bottom:0; }
    .chat-bubble ul, .chat-bubble ol { margin-bottom:0; padding-left:20px; }

    /* Reply preview above editor */
    .reply-preview {
        background:#f0f2f5; border-left:3px solid #662c87; padding:6px 10px; margin-bottom:8px;
        border-radius:4px; font-size:12px; color:#555; position:relative;
    }
    .reply-preview .close-reply { position:absolute; right:6px; top:2px; cursor:pointer; color:#999; font-size:14px; background:none; border:none; }
    .reply-preview .close-reply:hover { color:#333; }

    /* Reply quote shown inside a bubble */
    .reply-ref { background:#eee; border-left:3px solid #999; padding:4px 8px; margin-bottom:4px; border-radius:3px; font-size:11px; color:#666; }
    .reply-ref strong { color:#662c87; }

    /* 3-dot menu on each message */
    .chat-msg-wrap { position:relative; }
    .chat-msg-wrap .msg-menu {
        position:absolute; top:0; z-index:10;
        display:none;
    }
    .chat-msg-wrap:hover .msg-menu { display:block; }
    .chat-msg-wrap.sent .msg-menu { right:50px; }
    .chat-msg-wrap.received .msg-menu { left:50px; }
    .msg-menu .msg-dots {
        background:#fff; border:1px solid #e0e0e0; border-radius:50%; width:26px; height:26px;
        display:flex; align-items:center; justify-content:center; cursor:pointer;
        color:#999; font-size:12px; box-shadow:0 1px 3px rgba(0,0,0,0.08);
    }
    .msg-menu .msg-dots:hover { color:#662c87; border-color:#662c87; }
    .msg-menu .dropdown-menu { min-width:120px; font-size:13px; }

    /* Summernote compact */
    .note-editor { border:1px solid #ddd !important; border-radius:8px !important; }
    .note-toolbar { background:#fafbfc !important; border-bottom:1px solid #eee !important; border-radius:8px 8px 0 0 !important; padding:4px 8px !important; }
    .note-editable { min-height:60px !important; max-height:120px !important; overflow-y:auto !important; padding:8px 12px !important; font-size:14px !important; }
    .note-statusbar { display:none !important; }

    /* File preview before send */
    .file-preview { display:flex; align-items:center; gap:8px; padding:6px 10px; background:#f9f3fc; border-radius:6px; margin-top:6px; }
    .file-preview img { max-height:48px; border-radius:4px; }
    .file-preview .file-info { font-size:12px; color:#555; }
    .file-preview .remove-file { color:#e74c3c; cursor:pointer; font-size:14px; margin-left:auto; }
</style>
@endpush

<div class="card-body p-0">
    <div id="chat-box" style="height:420px; overflow-y:scroll; background:#f0f2f5; padding:15px;">
        @foreach($chats as $chat)
        @php
            $isMine = ($viewerType === 'staff' && $chat->sender_type === 'portal_user')
                   || ($viewerType === 'user' && $chat->sender_type === 'user');
        @endphp
        <div class="d-flex {{ $isMine ? 'justify-content-end' : 'justify-content-start' }} mb-3 chat-msg-wrap {{ $isMine ? 'sent' : 'received' }}" data-id="{{ $chat->id }}">
            @if(!$isMine)
            <div class="mr-2">
                <div class="rounded-circle {{ $viewerType === 'staff' ? 'bg-secondary' : 'bg-primary' }} d-flex align-items-center justify-content-center text-white" style="width:36px;height:36px;font-size:14px;">
                    @if($viewerType === 'user')
                        <i class="fas fa-headset" style="font-size:14px"></i>
                    @else
                        {{ strtoupper(substr($chat->sender_name ?? 'U', 0, 1)) }}
                    @endif
                </div>
            </div>
            @endif
            <div style="max-width:65%">
                @if($chat->reply_to_id && $chat->replyTo)
                <div class="reply-ref"><strong>{{ $chat->replyTo->sender_name }}</strong>: {{ Str::limit(strip_tags($chat->replyTo->message), 40) }}</div>
                @endif
                <div class="rounded p-2 px-3 chat-bubble {{ $isMine ? 'bg-primary text-white' : 'bg-white' }}" style="box-shadow:0 1px 2px rgba(0,0,0,.1)">
                    @if(!$isMine && $viewerType === 'user')
                    <small class="d-block font-weight-bold mb-1" style="font-size:11px">Support Team</small>
                    @endif
                    @if($chat->message){!! $chat->message !!}@endif
                    @if($chat->file_type === 'image' && ($chat->bunny_path || $chat->local_path))
                    <a href="#" class="chat-img-link" data-url="{{ $chat->local_path ? asset($chat->local_path) : '#' }}">
                        <img src="{{ $chat->local_path ? asset($chat->local_path) : '#' }}" class="img-fluid rounded mt-1" style="max-height:200px">
                    </a>
                    @elseif($chat->file_type && ($chat->bunny_path || $chat->local_path))
                    <a href="{{ $chat->local_path ? asset($chat->local_path) : '#' }}" class="badge badge-light mt-1 p-2" download style="font-size:12px;">
                        <i class="fas fa-file-download mr-1"></i>{{ $chat->original_filename }} <small class="text-muted">(Download)</small>
                    </a>
                    @endif
                </div>
                <small class="{{ $isMine ? 'text-right d-block' : '' }} text-muted" style="font-size:11px">
                    {{ $isMine ? 'You' : $chat->sender_name }} · {{ $chat->created_at->format('M d, H:i') }}
                </small>
            </div>
            @if($isMine)
            <div class="ml-2">
                <div class="rounded-circle {{ $viewerType === 'staff' ? 'bg-primary' : 'bg-secondary' }} d-flex align-items-center justify-content-center text-white" style="width:36px;height:36px;font-size:14px;">
                    @if($viewerType === 'staff')
                        <i class="fas fa-user-tie" style="font-size:14px"></i>
                    @else
                        {{ $myInitial }}
                    @endif
                </div>
            </div>
            @endif
            {{-- 3-dot menu --}}
            <div class="msg-menu dropdown">
                <span class="msg-dots" data-toggle="dropdown"><i class="fas fa-ellipsis-v"></i></span>
                <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item reply-btn" href="#" data-id="{{ $chat->id }}" data-sender="{{ $chat->sender_name }}" data-text="{{ Str::limit(strip_tags($chat->message), 50) }}">
                        <i class="fas fa-reply mr-2"></i> Reply
                    </a>
                </div>
            </div>
        </div>
        @endforeach
        <div id="chat-end"></div>
    </div>
</div>

{{-- Input area --}}
<div class="card-footer bg-white p-3">
    <div id="replyPreview" class="reply-preview" style="display:none;">
        <span id="replyText"></span>
        <button class="close-reply" onclick="clearReply()">&times;</button>
        <input type="hidden" id="replyToId" value="">
    </div>

    <form id="chat-form" enctype="multipart/form-data">
        @csrf
        <div id="chat-editor"></div>
        <div id="filePreview" class="file-preview" style="display:none;">
            <img id="fileThumb" src="" class="d-none">
            <i id="fileIcon" class="fas fa-file d-none" style="font-size:24px;color:#662c87;"></i>
            <span class="file-info" id="fileInfo"></span>
            <span class="remove-file" onclick="removeFile()" title="Remove">&times;</span>
        </div>
        <div class="d-flex align-items-center justify-content-between mt-2">
            <div>
                <input type="file" name="file" id="chat-file" class="d-none" accept="image/*,.pdf,.doc,.docx,.mp4">
                <label for="chat-file" class="btn btn-sm btn-light mb-0" title="Attach file"><i class="fas fa-paperclip"></i></label>
            </div>
            <button type="submit" class="btn btn-primary btn-sm" id="send-btn">
                <i class="fas fa-paper-plane mr-1"></i> Send
            </button>
        </div>
    </form>
</div>

<div class="modal fade" id="imgModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Image</h5><button type="button" class="close" data-dismiss="modal"><span>&times;</span></button></div>
            <div class="modal-body text-center"><img id="modal-img" src="" class="img-fluid"></div>
        </div>
    </div>
</div>

@push('js')
<script src="{{ asset('admin/assets/js/summernote-bs4.js') }}"></script>
<script>
var lastId     = {{ $lastId }};
var postUrl    = '{{ $postUrl }}';
var pollUrl    = '{{ $pollUrl }}';
var viewerType = '{{ $viewerType }}';
var myName     = '{{ $viewerName }}';
var myInitial  = '{{ $myInitial }}';

$('#chat-editor').summernote({
    placeholder: 'Type a message...',
    height: 80,
    toolbar: [
        ['style', ['bold', 'italic', 'underline']],
        ['para', ['ul', 'ol']]
    ],
    callbacks: {
        onKeydown: function(e) {
            if (e.ctrlKey && e.key === 'Enter') { e.preventDefault(); $('#chat-form').submit(); }
        }
    }
});

function scrollBottom() { var b = document.getElementById('chat-box'); b.scrollTop = b.scrollHeight; }
scrollBottom();

// Reply
$(document).on('click', '.reply-btn', function() {
    $('#replyToId').val($(this).data('id'));
    $('#replyText').html('<strong>' + $(this).data('sender') + ':</strong> ' + $(this).data('text'));
    $('#replyPreview').show();
    $('#chat-editor').summernote('focus');
});
function clearReply() { $('#replyToId').val(''); $('#replyPreview').hide(); }

// File preview
$('#chat-file').on('change', function() {
    var file = this.files[0];
    if (!file) { removeFile(); return; }
    var isImage = file.type.startsWith('image/');
    if (isImage) {
        var reader = new FileReader();
        reader.onload = function(e) {
            $('#fileThumb').attr('src', e.target.result).removeClass('d-none');
            $('#fileIcon').addClass('d-none');
        };
        reader.readAsDataURL(file);
    } else {
        $('#fileThumb').addClass('d-none');
        $('#fileIcon').removeClass('d-none');
    }
    $('#fileInfo').text(file.name + ' (' + (file.size / 1024).toFixed(1) + ' KB)');
    $('#filePreview').show();
});

function removeFile() {
    $('#chat-file').val('');
    $('#filePreview').hide();
    $('#fileThumb').addClass('d-none');
    $('#fileIcon').addClass('d-none');
    $('#fileInfo').text('');
}

function appendMessage(msg) {
    var isMine = (viewerType === 'staff' && msg.sender_type === 'portal_user')
              || (viewerType === 'user' && msg.sender_type === 'user');
    var avatarBg = isMine ? (viewerType === 'staff' ? 'bg-primary' : 'bg-secondary') : (viewerType === 'staff' ? 'bg-secondary' : 'bg-primary');
    var avatarContent = isMine
        ? (viewerType === 'staff' ? '<i class="fas fa-user-tie" style="font-size:14px"></i>' : myInitial)
        : (viewerType === 'user' ? '<i class="fas fa-headset" style="font-size:14px"></i>' : ((msg.sender_name||'U').charAt(0).toUpperCase()));
    var avatar = '<div class="rounded-circle '+avatarBg+' d-flex align-items-center justify-content-center text-white" style="width:36px;height:36px;font-size:14px;">'+avatarContent+'</div>';

    var fileHtml = '';
    if (msg.file_type === 'image' && msg.file_url) {
        fileHtml = '<a href="#" class="chat-img-link" data-url="'+msg.file_url+'"><img src="'+msg.file_url+'" class="img-fluid rounded mt-1" style="max-height:200px"></a>';
    } else if (msg.file_url) {
        fileHtml = '<a href="'+msg.file_url+'" class="badge badge-light mt-1 p-2" download style="font-size:12px;"><i class="fas fa-file-download mr-1"></i>'+(msg.original_filename||'file')+' <small class="text-muted">(Download)</small></a>';
    }

    var replyHtml = '';
    if (msg.reply_sender && msg.reply_text) replyHtml = '<div class="reply-ref"><strong>'+msg.reply_sender+'</strong>: '+msg.reply_text+'</div>';

    var supportLabel = (!isMine && viewerType === 'user') ? '<small class="d-block font-weight-bold mb-1" style="font-size:11px">Support Team</small>' : '';
    var plainText = (msg.message||'').replace(/<[^>]*>/g,'').substring(0,50);

    var html = '<div class="d-flex '+(isMine?'justify-content-end':'justify-content-start')+' mb-3 chat-msg-wrap '+(isMine?'sent':'received')+'" data-id="'+msg.id+'">' +
        (!isMine ? '<div class="mr-2">'+avatar+'</div>' : '') +
        '<div style="max-width:65%">' + replyHtml +
            '<div class="rounded p-2 px-3 chat-bubble '+(isMine?'bg-primary text-white':'bg-white')+'" style="box-shadow:0 1px 2px rgba(0,0,0,.1)">' +
                supportLabel + (msg.message||'') + fileHtml +
            '</div>' +
            '<small class="'+(isMine?'text-right d-block ':'')+' text-muted" style="font-size:11px">'+(isMine?'You':msg.sender_name)+' · '+msg.created_at+'</small>' +
        '</div>' +
        (isMine ? '<div class="ml-2">'+avatar+'</div>' : '') +
        '<div class="msg-menu dropdown"><span class="msg-dots" data-toggle="dropdown"><i class="fas fa-ellipsis-v"></i></span><div class="dropdown-menu dropdown-menu-right"><a class="dropdown-item reply-btn" href="#" data-id="'+msg.id+'" data-sender="'+(msg.sender_name||'')+'" data-text="'+plainText+'"><i class="fas fa-reply mr-2"></i> Reply</a></div></div>' +
    '</div>';
    $('#chat-end').before(html);
}

// Poll
var pollSender = viewerType === 'staff' ? 'user' : 'portal_user';
function poll() {
    $.get(pollUrl + '?last_id=' + lastId + '&viewer=' + viewerType, function(res) {
        if (res.messages && res.messages.length) {
            res.messages.forEach(function(m) { if (m.sender_type === pollSender) appendMessage(m); });
            lastId = res.messages[res.messages.length-1].id;
            scrollBottom();
        }
    });
}
setInterval(poll, 5000);

// Send
$('#chat-form').on('submit', function(e) {
    e.preventDefault();
    var msg = $('#chat-editor').summernote('code');
    var file = $('#chat-file')[0].files[0];
    if ($('#chat-editor').summernote('isEmpty') && !file) return;

    var fd = new FormData();
    fd.append('_token', $('meta[name="csrf-token"]').attr('content'));
    fd.append('message', msg);
    fd.append('reply_to_id', $('#replyToId').val() || '');
    if (file) fd.append('file', file);

    $('#send-btn').prop('disabled', true);
    $.ajax({ url:postUrl, type:'POST', data:fd, processData:false, contentType:false,
        success: function(res) {
            if (res.success) {
                var chatData = res.chat || {
                    id: ++lastId, sender_type: viewerType === 'staff' ? 'portal_user' : 'user',
                    sender_name: myName, message: msg, file_type:null, file_url:null,
                    reply_sender: null, reply_text: null,
                    created_at: new Date().toLocaleString('en-US',{month:'short',day:'numeric',hour:'2-digit',minute:'2-digit'})
                };
                appendMessage(chatData);
                if (chatData.id > lastId) lastId = chatData.id;
                $('#chat-editor').summernote('reset');
                removeFile();
                clearReply();
                scrollBottom();
            }
        },
        complete: function() { $('#send-btn').prop('disabled', false); }
    });
});

$(document).on('click', '.chat-img-link', function(e) { e.preventDefault(); $('#modal-img').attr('src', $(this).data('url')); $('#imgModal').modal('show'); });
</script>
@endpush
