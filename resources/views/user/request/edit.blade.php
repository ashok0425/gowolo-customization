@extends('layouts.app')
@section('title', 'Edit Request — ' . $customizationRequest->ref_number)

@php
    // Index answers by question_key for prefill
    $answers = $customizationRequest->answers->pluck('answer', 'question_key')->toArray();
@endphp

@section('content')
<div class="page-header">
    <h4 class="page-title">Edit Request #{{ $customizationRequest->ref_number }}</h4>
    <ul class="breadcrumbs">
        <li class="nav-home"><a href="{{ route('user.dashboard') }}"><i class="flaticon-home"></i></a></li>
        <li class="separator"><i class="flaticon-right-arrow"></i></li>
        <li class="nav-item"><a href="#">Edit</a></li>
    </ul>
</div>

@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show">
    <ul class="mb-0">
        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
    </ul>
    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
</div>
@endif

<div class="card">
    <div class="card-header">
        <h4 class="card-title">Edit Your Request</h4>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('user.request.update', $customizationRequest) }}" enctype="multipart/form-data" id="edit-form">
            @csrf @method('PUT')

            <h6 class="mb-3 mt-2 font-weight-bold">Personal Information</h6>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>First Name <span class="text-danger">*</span></label>
                        <input type="text" name="first_name" class="form-control" value="{{ old('first_name', $customizationRequest->first_name) }}" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Last Name <span class="text-danger">*</span></label>
                        <input type="text" name="last_name" class="form-control" value="{{ old('last_name', $customizationRequest->last_name) }}" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $customizationRequest->email) }}" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Phone <span class="text-danger">*</span></label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone', $customizationRequest->phone) }}" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Secondary Phone</label>
                        <input type="text" name="sec_phone" class="form-control" value="{{ old('sec_phone', $customizationRequest->sec_phone) }}">
                    </div>
                </div>
            </div>

            <h6 class="mb-3 mt-4 font-weight-bold">Community Information</h6>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Community Name <span class="text-danger">*</span></label>
                        <input type="text" name="company_name" class="form-control" value="{{ old('company_name', $customizationRequest->company_name) }}" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Community Handle Name <span class="text-danger">*</span></label>
                        <input type="text" name="company_phone" class="form-control" value="{{ old('company_phone', $customizationRequest->company_phone) }}" required>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Community Domain Name</label>
                        <input type="text" name="company_address" class="form-control" value="{{ old('company_address', $customizationRequest->company_address) }}">
                    </div>
                </div>
            </div>

            <h6 class="mb-3 mt-4 font-weight-bold">Requirements</h6>
            <div class="row">
                @foreach([
                    'req_logo'           => 'Logo',
                    'req_icon'           => 'Web Icon',
                    'req_app_background' => 'App Background Image',
                    'req_landing_page'   => 'Landing Page',
                    'req_others'         => 'Others',
                ] as $field => $label)
                <div class="col-md-6 mb-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="{{ $field }}" id="edit_{{ $field }}" value="1" {{ old($field, $customizationRequest->$field) ? 'checked' : '' }}>
                        <label class="form-check-label" for="edit_{{ $field }}">{{ $label }}</label>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Primary Color</label>
                        <input type="text" name="req_primary_color" class="form-control" value="{{ old('req_primary_color', $customizationRequest->req_primary_color) }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Secondary Color</label>
                        <input type="text" name="req_sec_color" class="form-control" value="{{ old('req_sec_color', $customizationRequest->req_sec_color) }}">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Request Description</label>
                        <textarea name="request_description" class="form-control" rows="4">{{ old('request_description', $customizationRequest->request_description) }}</textarea>
                    </div>
                </div>
            </div>

            <h6 class="mb-3 mt-4 font-weight-bold">Additional Information</h6>

            <div class="form-group">
                <label>What domain name would you like to be displayed in your website?</label>
                <small class="d-block text-muted mb-1">(You can promote multiple websites in your platform.)</small>
                <textarea name="question_1" class="form-control" rows="3">{{ old('question_1', $answers['question_1'] ?? '') }}</textarea>
            </div>

            <div class="form-group">
                <label>What are your gifts, talents, products and/or services and what are you passionate about?</label>
                <textarea name="question_2" class="form-control" rows="3">{{ old('question_2', $answers['question_2'] ?? '') }}</textarea>
            </div>

            <div class="form-group">
                <label>If you never got paid for it, what could you do for the rest of your life that brings you happiness?</label>
                <textarea name="question_3" class="form-control" rows="3">{{ old('question_3', $answers['question_3'] ?? '') }}</textarea>
            </div>

            <div class="form-group">
                <label>List 5 things you love to do in order of importance.</label>
                <textarea name="question_4" class="form-control" rows="3">{{ old('question_4', $answers['question_4'] ?? '') }}</textarea>
            </div>

            <div class="form-group">
                <label>How many followers do you have on other platforms</label>
                <select name="question_5" class="form-control">
                    <option value="">— Select —</option>
                    @foreach(['0-500','500-5000','5000-50000','50000 or more'] as $opt)
                    <option value="{{ $opt }}" {{ old('question_5', $answers['question_5'] ?? '') === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                    @endforeach
                </select>
            </div>

            @php
                $yesNoQuestions = [
                    'question_11' => 'Do you have a thumbnail image for your content management or master courses?',
                    'question_12' => 'Can you provide us with your website content for your landing page?',
                    'question_13' => 'Can you provide us with your campaign content for your lead capture page?',
                    'question_14' => 'Do you have product images for your e-commerce store?',
                    'question_15' => 'Do you have a banner image for your e-commerce store?',
                    'question_16' => 'Do you have any videos for your landing page, e-commerce store or master courses?',
                ];
            @endphp

            @foreach($yesNoQuestions as $key => $label)
            <div class="form-group">
                <label>{{ $label }}</label>
                <div>
                    <label class="mr-3"><input type="radio" name="{{ $key }}" value="1" {{ old($key, $answers[$key] ?? '') === '1' ? 'checked' : '' }}> Yes</label>
                    <label><input type="radio" name="{{ $key }}" value="0" {{ old($key, $answers[$key] ?? '') === '0' ? 'checked' : '' }}> No</label>
                </div>
            </div>
            @endforeach

            <div class="form-group">
                <label>What would you like to do in your VIP to share your gift, talent, products and/or services with your VIP followers?</label>
                <small class="d-block text-muted mb-1">(Ex: Teach/train, worship, etc)</small>
                <textarea name="question_17" class="form-control" rows="3">{{ old('question_17', $answers['question_17'] ?? '') }}</textarea>
            </div>

            <div class="form-group">
                <label>How will you use this order? <small class="text-muted">(optional)</small></label>
                <textarea name="requirement_1" class="form-control" rows="3">{{ old('requirement_1', $answers['requirement_1'] ?? '') }}</textarea>
            </div>

            <div class="form-group">
                <label>Which industry is most relevant to your order? <small class="text-muted">(optional)</small></label>
                <textarea name="requirement_2" class="form-control" rows="3">{{ old('requirement_2', $answers['requirement_2'] ?? '') }}</textarea>
            </div>

            <div class="form-group">
                <label>What are you looking to achieve with this order? <small class="text-muted">(optional)</small></label>
                <textarea name="requirement_3" class="form-control" rows="3">{{ old('requirement_3', $answers['requirement_3'] ?? '') }}</textarea>
            </div>

            <div class="form-group">
                <label>Relevant data</label>
                <textarea name="requirement_4" id="requirement4Editor" class="form-control" rows="3">{{ old('requirement_4', $answers['requirement_4'] ?? '') }}</textarea>
            </div>

            <h6 class="mb-3 mt-4 font-weight-bold">Files</h6>

            {{-- Existing files --}}
            @if($customizationRequest->files->count())
            <div class="mb-3">
                <label class="d-block mb-2">Current Files</label>
                @foreach($customizationRequest->files as $file)
                <div class="d-flex align-items-center justify-content-between p-2 mb-1 border rounded" id="file-row-{{ $file->id }}">
                    <div>
                        <i class="fas {{ in_array($file->extension, ['mp4','mov','webm']) ? 'fa-video' : (in_array($file->extension, ['jpg','jpeg','png','gif','webp']) ? 'fa-image' : ($file->extension === 'pdf' ? 'fa-file-pdf' : 'fa-file-alt')) }} mr-2 text-primary"></i>
                        <span>{{ $file->original_name }}</span>
                        <small class="text-muted ml-2">({{ number_format(($file->size_bytes ?: 0) / 1024, 1) }} KB)</small>
                        <span class="badge badge-light ml-1">{{ $file->file_category }}</span>
                    </div>
                    <label class="text-danger mb-0" style="cursor:pointer;">
                        <input type="checkbox" name="delete_files[]" value="{{ $file->id }}" class="mr-1"> Remove
                    </label>
                </div>
                @endforeach
            </div>
            @endif

            {{-- Add new files --}}
            <div class="form-group">
                <label>Upload Files <small class="text-muted">(images, videos, audio, docs — up to 1GB each)</small></label>
                <div id="fileDropZone" class="file-drop-zone">
                    <i class="fas fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
                    <p class="mb-1">Drag & drop files here or click to browse</p>
                    <input type="file" id="fileBrowseInput" class="d-none" multiple>
                </div>
                <div id="fileUploadList" class="mt-2"></div>
                <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="addMoreFilesBtn">
                    <i class="fas fa-plus mr-1"></i> Add File
                </button>
            </div>

            <div class="d-flex justify-content-between mt-3">
                <a href="{{ route('user.dashboard') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Save Changes</button>
            </div>
        </form>
    </div>
</div>

@push('css')
<link href="{{ asset('common/vendor/summernote/summernote-bs4.min.css') }}" rel="stylesheet">
<style>
    .file-drop-zone {
        border: 2px dashed #c9b3d9; border-radius: 12px; padding: 30px 20px;
        text-align: center; cursor: pointer; transition: all 0.2s; background: #fdfaff;
    }
    .file-drop-zone:hover, .file-drop-zone.dragover { border-color: #662c87; background: #f9f3fc; }
    .file-drop-zone p { color: #888; font-size: 14px; margin: 0; }
    .file-upload-item {
        display: flex; align-items: center; gap: 10px;
        padding: 8px 14px; margin-bottom: 6px;
        background: #f9f3fc; border: 1px solid #e6d5ef; border-radius: 10px; font-size: 13px;
    }
    .file-upload-item .progress { flex: 1; height: 6px; border-radius: 3px; margin: 0; }
    .file-upload-item .file-pct { min-width: 38px; text-align: right; font-weight: 600; font-size: 12px; }
    .file-upload-item .file-remove { color: #e74c3c; cursor: pointer; font-size: 16px; margin-left: auto; }
    .file-upload-item .file-icon { color: #662c87; font-size: 16px; min-width: 18px; }
    .file-upload-item.upload-done { background: #eafaf1; border-color: #b2dfdb; }
    .file-upload-item.upload-error { background: #fdecea; border-color: #f5c6cb; }
</style>
@endpush

@push('js')
<script src="{{ asset('common/vendor/summernote/summernote-bs4.min.js') }}"></script>
<script>
$('#requirement4Editor').summernote({
    height: 150,
    toolbar: [
        ['style', ['bold', 'italic', 'underline']],
        ['para', ['ul', 'ol', 'paragraph']],
        ['insert', ['link']],
        ['view', ['codeview']]
    ],
    disableDragAndDrop: true
});
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/resumable.js/1.1.0/resumable.min.js"></script>
<script>
var uploadingCount = 0;
function fileIcon(name) {
    var ext = name.split('.').pop().toLowerCase();
    if (['jpg','jpeg','png','gif','webp','svg'].indexOf(ext) > -1) return 'fa-image';
    if (['mp4','mov','webm','avi','mkv'].indexOf(ext) > -1) return 'fa-video';
    if (['mp3','wav','ogg','aac','flac'].indexOf(ext) > -1) return 'fa-music';
    if (ext === 'pdf') return 'fa-file-pdf';
    if (['doc','docx'].indexOf(ext) > -1) return 'fa-file-word';
    return 'fa-file-alt';
}
var r = new Resumable({
    target: '{{ route("user.chunk.upload") }}',
    chunkSize: 2 * 1024 * 1024, simultaneousUploads: 3, testChunks: true,
    maxFileSize: 1 * 1024 * 1024 * 1024,
    maxFileSizeErrorCallback: function(file) { alert(file.fileName + ' exceeds 1GB limit.'); }
});
if (r.support) {
    var $zone = $('#fileDropZone');
    r.assignBrowse(document.getElementById('fileBrowseInput'));
    r.assignDrop($zone[0]);
    $zone.on('click', function() { $('#fileBrowseInput').click(); });
    $zone.on('dragover', function() { $(this).addClass('dragover'); });
    $zone.on('dragleave drop', function() { $(this).removeClass('dragover'); });
    $('#addMoreFilesBtn').on('click', function() { $('#fileBrowseInput').click(); });

    r.on('fileAdded', function(file) {
        uploadingCount++;
        var uid = file.uniqueIdentifier;
        var sizeLabel = file.size > 1048576 ? (file.size/1048576).toFixed(1)+' MB' : (file.size/1024).toFixed(1)+' KB';
        $('#fileUploadList').append(
            '<div class="file-upload-item" id="fu-'+uid+'">'+
            '<i class="fas '+fileIcon(file.fileName)+' file-icon"></i>'+
            '<span style="min-width:120px;">'+file.fileName+'</span>'+
            '<small class="text-muted">('+sizeLabel+')</small>'+
            '<div class="progress"><div class="progress-bar bg-primary" style="width:0%"></div></div>'+
            '<span class="file-pct">0%</span>'+
            '<span class="file-remove" data-uid="'+uid+'">&times;</span></div>');
        r.upload();
    });
    r.on('fileProgress', function(file) {
        var pct = Math.floor(file.progress()*100), $row = $('#fu-'+file.uniqueIdentifier);
        $row.find('.progress-bar').css('width', pct+'%');
        $row.find('.file-pct').text(pct >= 100 ? 'Processing...' : pct+'%');
    });
    r.on('fileSuccess', function(file) {
        uploadingCount--;
        var $row = $('#fu-'+file.uniqueIdentifier);
        $row.addClass('upload-done').find('.progress-bar').addClass('bg-success').css('width','100%');
        $row.find('.file-pct').html('<i class="fas fa-check text-success"></i>');
    });
    r.on('fileError', function(file, response) {
        uploadingCount--;
        var msg='Upload failed'; try{msg=JSON.parse(response).error||msg;}catch(e){}
        var $row = $('#fu-'+file.uniqueIdentifier);
        $row.addClass('upload-error').find('.progress-bar').addClass('bg-danger').css('width','100%');
        $row.find('.file-pct').html('<i class="fas fa-times text-danger" title="'+msg+'"></i>');
    });
    $(document).on('click', '.file-remove', function() {
        var uid = $(this).data('uid'), file = r.getFromUniqueIdentifier(uid);
        if (file) { if (file.isUploading()) uploadingCount--; r.removeFile(file); }
        $('#fu-'+uid).remove();
    });
}
$('#edit-form').on('submit', function(e) {
    if (uploadingCount > 0) { e.preventDefault(); alert('Please wait for all file uploads to complete.'); return false; }
});
</script>
@endpush
@endsection
