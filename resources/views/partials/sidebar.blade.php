@auth('portal')
    @php $portalUser = Auth::guard('portal')->user(); @endphp
    @php $isTech = $portalUser->hasRole('technician'); @endphp

    <li class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        <a href="{{ route('admin.dashboard') }}">
            <i class="fas fa-tachometer-alt"></i>
            <p>{{ $isTech ? 'My Dashboard' : 'Dashboard' }}</p>
        </a>
    </li>

    <li class="nav-item {{ request()->routeIs('admin.requests.*') ? 'active' : '' }}">
        <a href="{{ route('admin.requests.index') }}">
            <i class="fas fa-list-alt"></i>
            <p>{{ $isTech ? 'My Assignments' : 'All Requests' }}</p>
        </a>
    </li>

    @if($portalUser->hasAnyRole(['super_admin','admin','supervisor']))
    <li class="nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
        <a href="{{ route('admin.users.index') }}">
            <i class="fas fa-users"></i>
            <p>Portal Users</p>
        </a>
    </li>
    @endif

    <li class="nav-item">
        <form method="POST" action="{{ route('portal.logout') }}">
            @csrf
            <button type="submit" class="btn btn-link nav-link" style="padding-left:25px">
                <i class="fas fa-sign-out-alt"></i>
                <p>Logout</p>
            </button>
        </form>
    </li>
@endauth

@if(session()->has('auth_user'))
    <li class="nav-item {{ request()->routeIs('user.dashboard') ? 'active' : '' }}">
        <a href="{{ route('user.dashboard') }}">
            <i class="fas fa-home"></i>
            <p>My Requests</p>
        </a>
    </li>

    <li class="nav-item {{ request()->routeIs('user.request.create') ? 'active' : '' }}">
        <a href="{{ route('user.request.create') }}">
            <i class="fas fa-plus-circle"></i>
            <p>New Request</p>
        </a>
    </li>

    <li class="nav-item">
        <form method="POST" action="{{ route('sso.logout') }}">
            @csrf
            <button type="submit" class="btn btn-link nav-link" style="padding-left:25px">
                <i class="fas fa-sign-out-alt"></i>
                <p>Logout</p>
            </button>
        </form>
    </li>
@endif
