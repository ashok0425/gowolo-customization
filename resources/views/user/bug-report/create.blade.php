@extends('layouts.app')
@section('title', 'Report a Bug')

@section('content')
<div class="page-header">
    <h4 class="page-title">Report a Bug</h4>
    <ul class="breadcrumbs">
        <li class="nav-home"><a href="{{ route('user.dashboard') }}"><i class="flaticon-home"></i></a></li>
        <li class="separator"><i class="flaticon-right-arrow"></i></li>
        <li class="nav-item"><a href="#">Report a Bug</a></li>
    </ul>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Submit Bug Report</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('user.bug-report.store') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="form-group">
                        <label>Message <span class="text-danger">*</span></label>
                        <textarea name="message" class="form-control @error('message') is-invalid @enderror"
                                  rows="5" placeholder="Describe the bug you encountered..." required>{{ old('message') }}</textarea>
                        @error('message')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Steps to Reproduce <span class="text-muted">(optional)</span></label>
                        <textarea name="steps_to_reproduce" class="form-control @error('steps_to_reproduce') is-invalid @enderror"
                                  rows="4" placeholder="1. Go to...&#10;2. Click on...&#10;3. See error...">{{ old('steps_to_reproduce') }}</textarea>
                        @error('steps_to_reproduce')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Screenshot <span class="text-muted">(optional)</span></label>
                        <div class="custom-file">
                            <input type="file" name="screenshot" class="custom-file-input @error('screenshot') is-invalid @enderror"
                                   id="screenshotInput" accept="image/*">
                            <label class="custom-file-label" for="screenshotInput">Choose file...</label>
                            @error('screenshot')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        <small class="form-text text-muted">Max 5MB. Accepted formats: JPG, PNG, GIF, WEBP</small>
                        <div id="screenshotPreview" class="mt-2" style="display:none;">
                            <img id="previewImg" src="" alt="Preview" style="max-width:300px;max-height:200px;border-radius:8px;border:1px solid #ddd;">
                        </div>
                    </div>

                    <div class="form-group mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-bug mr-1"></i> Submit Bug Report
                        </button>
                        <a href="{{ route('user.dashboard') }}" class="btn btn-light ml-2">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header"><h4 class="card-title">Tips</h4></div>
            <div class="card-body">
                <ul class="pl-3" style="line-height:2;">
                    <li>Be as specific as possible about what went wrong.</li>
                    <li>Include the steps to reproduce the issue if you can.</li>
                    <li>A screenshot helps us identify the problem faster.</li>
                    <li>Mention which page or feature was affected.</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
$('#screenshotInput').on('change', function() {
    var file = this.files[0];
    if (file) {
        $(this).next('.custom-file-label').text(file.name);
        var reader = new FileReader();
        reader.onload = function(e) {
            $('#previewImg').attr('src', e.target.result);
            $('#screenshotPreview').show();
        };
        reader.readAsDataURL(file);
    } else {
        $(this).next('.custom-file-label').text('Choose file...');
        $('#screenshotPreview').hide();
    }
});
</script>
@endpush
