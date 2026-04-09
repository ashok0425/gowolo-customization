@extends('layouts.app')
@section('title', 'Add Portal User')

@section('content')
<div class="page-header">
    <h4 class="page-title">Add Portal User</h4>
    <ul class="breadcrumbs">
        <li class="nav-home"><a href="{{ route('admin.dashboard') }}"><i class="flaticon-home"></i></a></li>
        <li class="separator"><i class="flaticon-right-arrow"></i></li>
        <li class="nav-item"><a href="{{ route('admin.users.index') }}">Users</a></li>
        <li class="separator"><i class="flaticon-right-arrow"></i></li>
        <li class="nav-item"><a href="#">Add</a></li>
    </ul>
</div>

<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-header"><h4 class="card-title">New User Details</h4></div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.users.store') }}">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>First Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                       value="{{ old('name') }}" required>
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Last Name</label>
                                <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror"
                                       value="{{ old('last_name') }}">
                                @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                       value="{{ old('email') }}" required>
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Phone</label>
                                <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Password <span class="text-danger">*</span></label>
                                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Confirm Password <span class="text-danger">*</span></label>
                                <input type="password" name="password_confirmation" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="d-block mb-2"><strong>Permissions</strong>
                            <small class="text-muted">— Check the actions this user can perform</small>
                        </label>

                        @if($permissions->isEmpty())
                            <div class="alert alert-warning">
                                No permissions found. Run <code>php artisan migrate</code> then <code>php artisan db:seed --class=RolesAndPermissionsSeeder</code>.
                            </div>
                        @else
                            <div class="row perm-grid">
                                @foreach($permissions as $perm)
                                <div class="col-md-6 col-lg-4 mb-2">
                                    <label class="perm-card" for="perm_{{ $perm->name }}">
                                        <input type="checkbox"
                                               name="permissions[]" value="{{ $perm->name }}"
                                               id="perm_{{ $perm->name }}"
                                               {{ in_array($perm->name, old('permissions', [])) ? 'checked' : '' }}>
                                        <span class="perm-check"></span>
                                        <span class="perm-text">{{ ucwords(str_replace('_', ' ', $perm->name)) }}</span>
                                    </label>
                                </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    <div class="d-flex justify-content-between mt-3">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Create User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
