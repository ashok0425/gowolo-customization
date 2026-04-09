@extends('layouts.app')
@section('title', 'Change Password')

@section('content')
<div class="page-header">
    <h4 class="page-title">Change Password</h4>
    <ul class="breadcrumbs">
        <li class="nav-home"><a href="{{ route('admin.dashboard') }}"><i class="flaticon-home"></i></a></li>
        <li class="separator"><i class="flaticon-right-arrow"></i></li>
        <li class="nav-item"><a href="#">Change Password</a></li>
    </ul>
</div>

<div class="row">
    <div class="col-md-6 offset-md-3">
        <div class="card">
            <div class="card-header"><h4 class="card-title">Update your password</h4></div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.profile.password.update') }}">
                    @csrf

                    <div class="form-group">
                        <label>Current Password <span class="text-danger">*</span></label>
                        <input type="password" name="current_password"
                               class="form-control @error('current_password') is-invalid @enderror" required>
                        @error('current_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group">
                        <label>New Password <span class="text-danger">*</span></label>
                        <input type="password" name="password"
                               class="form-control @error('password') is-invalid @enderror" required>
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <small class="form-text text-muted">Minimum 8 characters.</small>
                    </div>

                    <div class="form-group">
                        <label>Confirm New Password <span class="text-danger">*</span></label>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>

                    <div class="d-flex justify-content-between mt-3">
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Update Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
