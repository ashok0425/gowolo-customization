{{--
    Shared chat partial — 1:1 clone of dashboardv2 customization_user_chat.blade.php.
    Required variables: $chats, $customizationRequest, $lastId, $postUrl, $pollUrl,
                        $viewerType ('staff'|'user'), $viewerName, $viewerAvatar
--}}

@push('css')
<link href="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote-bs4.min.css" rel="stylesheet">
<style>
    /* ============ dashboardv2 customization_user_chat.blade.php styling ============ */
    #chat {
        min-height: 300px;
        width: 100%;
        overflow-x: hidden;
        overflow-y: scroll;
        margin: 0 auto;
        background-color: #e5e5e5;
        padding: 20px;
        max-height: 500px;
    }
    #chatboxdiv img { margin: 5px; margin-left: 5px !important; }
    .chatboxclose { margin: 10px; }

    .messages { padding: 0; margin: 0 0 0 60px; }
    .messages img {
        width: 64px;
        border-radius: 64px;
    }
    ul li { list-style: none; }

    .messages .message {
        margin-bottom: 15px;
        position: relative;
    }
    .messages .message:last-child { margin-bottom: 0; }

    .received,
    .sent {
        max-width: 60%;
        padding: 8px 14px;
        border-radius: 10px;
        word-break: break-word;
        display: inline-block;
    }
    .received {
        background: #ffffff;
        box-shadow: 0 1px 2px rgba(0,0,0,.08);
    }
    .sent {
        background: #c7cbd1;
        float: right;
        text-align: left;
        box-shadow: 0 1px 2px rgba(0,0,0,.08);
    }
    .message p { margin: 5px 0; }
    .chat-bubble p:last-child { margin-bottom: 0; }
    .chat-bubble ul, .chat-bubble ol { margin-bottom: 0; padding-left: 18px; }

    /* Image / file inside bubbles */
    .imgfile img { height: auto; max-width: 180px; border-radius: 6px !important; padding: 4px; cursor: pointer; transition: opacity 0.2s; }
    .imgfile img:hover { opacity: 0.85; }
    .pdf_file { display: inline-block; }
    .pdf_view { position: relative; }

    /* Reply quote inside bubble */
    .reply-ref {
        background: #f1f1f1;
        border-left: 3px solid #999;
        padding: 4px 8px;
        margin-bottom: 4px;
        border-radius: 3px;
        font-size: 11px;
        color: #666;
    }
    .reply-ref strong { color: #662c87; }

    /* Reply preview above editor */
    .reply-preview {
        background: #f0f2f5;
        border-left: 3px solid #662c87;
        padding: 6px 10px;
        margin-bottom: 8px;
        border-radius: 4px;
        font-size: 12px;
        color: #555;
        position: relative;
    }
    .reply-preview .close-reply {
        position: absolute;
        right: 6px;
        top: 2px;
        background: none;
        border: none;
        color: #999;
        font-size: 14px;
        cursor: pointer;
    }

    /* Reply button — centered on top of each bubble */
    .chat-bubble { position: relative; }
    .reply-inline {
        display: none;
        align-items: center;
        gap: 4px;
        background: #fff;
        border: 1px solid #e0e0e0;
        border-radius: 20px;
        padding: 2px 10px;
        font-size: 10px;
        color: #666;
        cursor: pointer;
        text-decoration: none;
        box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        transition: all 0.15s;
        position: absolute;
        top: -12px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 2;
        white-space: nowrap;
    }
    .message:hover .reply-inline { display: inline-flex; }
    .reply-inline:hover {
        background: #f9f3fc;
        color: #662c87;
        border-color: #662c87;
        text-decoration: none;
    }
    .reply-inline i { font-size: 9px; }

    /* Bootstrap custom file input override */
    .custom-file-label::after { content: "Browse"; }
    .custom-file-label { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }

    /* Summernote styling */
    .note-editor.note-frame { border: 1px solid #1C2B36 !important; border-radius: 6px !important; }
    .note-toolbar { background: #fafbfc !important; border-bottom: 1px solid #eee !important; padding: 4px 8px !important; }
    .note-editable { min-height: 50px !important; max-height: 120px !important; overflow-y: auto !important; padding: 8px 12px !important; font-size: 14px !important; }
    .note-statusbar { display: none !important; }

    /* Primary button — matches dashboardv2 .primary_btn */
    .primary_btn {
        background: #662c87;
        color: #fff;
        border: none;
        padding: 10px 28px;
        border-radius: 25px;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
    }
    .primary_btn:hover { background: #4f1f6c; color: #fff; }
</style>
@endpush

{{-- Card-header Customization Chat (dashboardv2 structure) --}}
<div class="card-header"><h2 class="mb-0" style="font-size:18px;font-weight:700;">Customization Chat</h2></div>

<div class="card-body" id="chat">
    <ul class="messages">
        @foreach($chats as $chat)
        @php
            $isMine = ($viewerType === 'staff' && $chat->sender_type === 'portal_user')
                   || ($viewerType === 'user' && $chat->sender_type === 'user');

            // Avatar — use ui-avatars for initials-based fallback
            $bg = $chat->sender_type === 'portal_user' ? '662c87' : '1C2B36';
            $avatarUrl = 'https://ui-avatars.com/api/?name=' . urlencode($chat->sender_name ?? 'User') . '&background=' . $bg . '&color=fff&size=64&rounded=true';

            // If local_path is already a full URL (migrated legacy file on dashboardv2),
            // use it as-is. Otherwise prefix with the current app URL via asset().
            if ($chat->local_path) {
                $fileUrl = preg_match('#^https?://#i', $chat->local_path)
                    ? $chat->local_path
                    : asset($chat->local_path);
            } else {
                $fileUrl = $chat->bunny_path ?? null;
            }
        @endphp

        <li class="message clearfix {{ $isMine ? 'sent-wrap' : '' }}" id="li_{{ $chat->id }}" data-id="{{ $chat->id }}">
            @if(!$isMine)
            <img src="{{ $avatarUrl }}" alt="{{ $chat->sender_name }}" style="float:left;width:50px;height:50px;margin-left:-53px;">
            @endif

            @if($isMine)
            <img style="float:right;width:50px;height:50px;" class="avatar-lg" src="{{ $avatarUrl }}" alt="You">
            @endif

            <div class="{{ $isMine ? 'sent' : 'received' }} chat-bubble">
                <a href="#" class="reply-inline reply-btn" data-id="{{ $chat->id }}" data-sender="{{ $chat->sender_name }}" data-text="{{ Str::limit(strip_tags($chat->message), 50) }}">
                    <i class="fas fa-reply"></i> Reply
                </a>

                @if($chat->reply_to_id && $chat->replyTo)
                <div class="reply-ref"><strong>{{ $chat->replyTo->sender_name }}</strong>: {{ Str::limit(strip_tags($chat->replyTo->message), 40) }}</div>
                @endif

                @if($chat->message)
                <p style="padding-bottom:10px">{!! $chat->message !!}</p>
                @endif

                @if($chat->file_type === 'image' && $fileUrl)
                    <a href="{{ $fileUrl }}" target="_blank" class="imgfile">
                        <img src="{{ $fileUrl }}" alt="">
                    </a>
                @elseif($chat->file_type === 'pdf' && $fileUrl)
                    <a href="{{ $fileUrl }}" data-toggle="modal" data-id="{{ $fileUrl }}" class="pdf_file" download>
                        <button class="btn-sm ml-3 mb-1" style="background-color:#662c87; color:white; border:none; padding:5px 15px; border-radius:5px;">
                            <i class="fas fa-file-pdf mr-1"></i> {{ $chat->original_filename }}
                        </button>
                    </a>
                @elseif($chat->file_type && $fileUrl)
                    <a href="{{ $fileUrl }}" download class="pdf_file">
                        <button class="btn-sm ml-3 mb-1" style="background-color:#662c87; color:white; border:none; padding:5px 15px; border-radius:5px;">
                            <i class="fas fa-download mr-1"></i> {{ $chat->original_filename }}
                        </button>
                    </a>
                @endif

                <div class="d-flex justify-content-{{ $isMine ? 'end' : 'start' }}" style="font-size:11px;color:#777;">
                    {{ $chat->created_at->format('m/d/Y h:i:A') }}
                </div>
            </div>
        </li>
        @endforeach
        <div id="chat-end"></div>
    </ul>
</div>

{{-- Chat input area (dashboardv2 structure) --}}
<div class="chats">
    <form id="addForm" enctype="multipart/form-data">
        @csrf
        <div class="card-body">

            {{-- Reply preview --}}
            <div id="replyPreview" class="reply-preview" style="display:none;">
                <span id="replyText"></span>
                <button class="close-reply" onclick="clearReply()">&times;</button>
                <input type="hidden" id="replyToId" value="">
            </div>

            <div>
                <p class="font-weight-bold mb-1">Chats</p>
                <textarea class="form-control border border-dark" name="comment" id="comment" rows="1"></textarea>
            </div>

            <div class="form-group row mt-3 mx-0">
                <div class="custom-file">
                    <input type="file" class="custom-file-input" id="file" name="file" accept="image/*,.pdf,.doc,.docx,.mp4">
                    <label class="custom-file-label" for="file">Choose file</label>
                </div>
            </div>
        </div>

        <div class="d-flex align-items-center justify-content-center pb-3">
            <button type="submit" id="addRowButton1" class="primary_btn">
                <i class="fas fa-paper-plane mr-1"></i> Send
            </button>
        </div>
    </form>
</div>


@push('js')
<script src="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote-bs4.min.js"></script>
<script>
var lastId     = {{ $lastId }};
var postUrl    = '{{ $postUrl }}';
var pollUrl    = '{{ $pollUrl }}';
var viewerType = '{{ $viewerType }}';
var myName     = '{{ $viewerName }}';

// Summernote init matching dashboardv2 exactly
$('#comment').summernote({
    toolbar: [
        ['style', ['bold']],
        ['para', ['ul']],
        ['font', ['fontname']]
    ],
    fontNames: ['Arial', 'Courier New', 'Times New Roman', 'Verdana'],
    placeholder: 'Type here…',
    height: 60,
    callbacks: {
        onKeydown: function(e) {
            if (e.ctrlKey && e.key === 'Enter') { e.preventDefault(); $('#addForm').submit(); }
        }
    }
});

// Bootstrap custom-file-input label update
$('.custom-file-input').on('change', function() {
    var fileName = this.files[0] ? this.files[0].name : 'Choose file';
    $(this).next('.custom-file-label').text(fileName);
});

function scrollBottom() { var b = document.getElementById('chat'); b.scrollTop = b.scrollHeight; }
scrollBottom();

// Reply handler
$(document).on('click', '.reply-btn', function(e) {
    e.preventDefault();
    $('#replyToId').val($(this).data('id'));
    $('#replyText').html('<strong>' + $(this).data('sender') + ':</strong> ' + $(this).data('text'));
    $('#replyPreview').show();
});
function clearReply() { $('#replyToId').val(''); $('#replyPreview').hide(); }

function avatarForSender(senderType, senderName) {
    var bg = senderType === 'portal_user' ? '662c87' : '1C2B36';
    return 'https://ui-avatars.com/api/?name=' + encodeURIComponent(senderName || 'User') + '&background=' + bg + '&color=fff&size=64&rounded=true';
}

function appendMessage(msg) {
    var isMine = (viewerType === 'staff' && msg.sender_type === 'portal_user')
              || (viewerType === 'user' && msg.sender_type === 'user');
    var avatarUrl = avatarForSender(msg.sender_type, msg.sender_name);

    var fileHtml = '';
    if (msg.file_type === 'image' && msg.file_url) {
        fileHtml = '<a href="' + msg.file_url + '" target="_blank" class="imgfile"><img src="' + msg.file_url + '" alt=""></a>';
    } else if (msg.file_type === 'pdf' && msg.file_url) {
        fileHtml = '<a href="' + msg.file_url + '" class="pdf_file" download><button class="btn-sm ml-3 mb-1" style="background-color:#662c87; color:white; border:none; padding:5px 15px; border-radius:5px;"><i class="fas fa-file-pdf mr-1"></i> ' + (msg.original_filename || 'file') + '</button></a>';
    } else if (msg.file_url) {
        fileHtml = '<a href="' + msg.file_url + '" class="pdf_file" download><button class="btn-sm ml-3 mb-1" style="background-color:#662c87; color:white; border:none; padding:5px 15px; border-radius:5px;"><i class="fas fa-download mr-1"></i> ' + (msg.original_filename || 'file') + '</button></a>';
    }

    var replyHtml = '';
    if (msg.reply_sender && msg.reply_text) {
        replyHtml = '<div class="reply-ref"><strong>' + msg.reply_sender + '</strong>: ' + msg.reply_text + '</div>';
    }

    var plainText = (msg.message || '').replace(/<[^>]*>/g, '').substring(0, 50);
    var timeClass = isMine ? 'justify-content-end' : 'justify-content-start';

    var avatarLeft  = '<img src="' + avatarUrl + '" alt="' + (msg.sender_name || '') + '" style="float:left;width:50px;height:50px;margin-left:-53px;">';
    var avatarRight = '<img style="float:right;width:50px;height:50px;" class="avatar-lg" src="' + avatarUrl + '" alt="You">';

    var replyBtn = '<a href="#" class="reply-inline reply-btn" data-id="' + msg.id + '" data-sender="' + (msg.sender_name || '') + '" data-text="' + plainText + '"><i class="fas fa-reply"></i> Reply</a>';

    var html = '<li class="message clearfix ' + (isMine ? 'sent-wrap' : '') + '" id="li_' + msg.id + '" data-id="' + msg.id + '">'
        + (isMine ? avatarRight : avatarLeft)
        + '<div class="' + (isMine ? 'sent' : 'received') + ' chat-bubble">'
        +     replyBtn
        +     replyHtml
        +     (msg.message ? '<p style="padding-bottom:10px">' + msg.message + '</p>' : '')
        +     fileHtml
        +     '<div class="d-flex ' + timeClass + '" style="font-size:11px;color:#777;">' + msg.created_at + '</div>'
        + '</div>'
        + '</li>';

    $('#chat-end').before(html);
}

// Poll for new messages from the other side
var pollSender = viewerType === 'staff' ? 'user' : 'portal_user';
function poll() {
    $.get(pollUrl + '?last_id=' + lastId + '&viewer=' + viewerType, function(res) {
        if (res.messages && res.messages.length) {
            res.messages.forEach(function(m) { if (m.sender_type === pollSender) appendMessage(m); });
            lastId = res.messages[res.messages.length - 1].id;
            scrollBottom();
        }
    });
}
setInterval(poll, 5000);

// Submit
$('#addForm').on('submit', function(e) {
    e.preventDefault();
    var msg = $('#comment').summernote('code');
    var file = $('#file')[0].files[0];
    if ($('#comment').summernote('isEmpty') && !file) return;

    var fd = new FormData();
    fd.append('_token', $('meta[name="csrf-token"]').attr('content'));
    fd.append('message', msg);
    fd.append('reply_to_id', $('#replyToId').val() || '');
    if (file) fd.append('file', file);

    var $btn = $('#addRowButton1');
    var originalHtml = $btn.html();
    $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Sending...');

    $.ajax({
        url: postUrl, type: 'POST', data: fd, processData: false, contentType: false,
        success: function(res) {
            if (res.success) {
                var chatData = res.chat || {
                    id: ++lastId,
                    sender_type: viewerType === 'staff' ? 'portal_user' : 'user',
                    sender_name: myName, message: msg, file_type: null, file_url: null,
                    reply_sender: null, reply_text: null,
                    created_at: new Date().toLocaleString('en-US', {month:'2-digit',day:'2-digit',year:'numeric',hour:'2-digit',minute:'2-digit',hour12:true})
                };
                appendMessage(chatData);
                if (chatData.id > lastId) lastId = chatData.id;
                $('#comment').summernote('reset');
                $('#file').val('');
                $('.custom-file-label').text('Choose file');
                clearReply();
                scrollBottom();
            } else {
                alert('Failed to send message. Please try again.');
            }
        },
        error: function(xhr) {
            alert(xhr.responseJSON?.message || 'Failed to send message. Please try again.');
        },
        complete: function() {
            $btn.prop('disabled', false).html(originalHtml);
        }
    });
});

// Images open in new tab via target="_blank" — no JS handler needed
</script>
@endpush
