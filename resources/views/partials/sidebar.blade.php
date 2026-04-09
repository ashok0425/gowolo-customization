@auth('portal')
    @php $portalUser = Auth::guard('portal')->user(); @endphp

    <li class="nav-item nav-item1 {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        <a href="{{ route('admin.dashboard') }}" class="link1">
            <i class="fas fa-tachometer-alt"></i>
            <p>Dashboard</p>
        </a>
    </li>


    <li class="nav-item nav-item1 navmenuli {{ request()->routeIs('admin.requests.*') ? 'active submenu' : '' }}" id="reqnav">
        <a data-toggle="collapse" href="#requestsMenu"
           aria-expanded="{{ request()->routeIs('admin.requests.*') ? 'true' : 'false' }}"
           class="lftmenu">
            <i class="fas fa-list-alt"></i>
            <p>Requests</p>
            <span class="caret"></span>
        </a>
        <div class="collapse menudiv {{ request()->routeIs('admin.requests.*') ? 'show' : '' }}" id="requestsMenu">
            <ul class="nav nav-collapse">
                <li class="nav-item nav-item1 {{ request()->routeIs('admin.requests.index') && !request('status') ? 'active' : '' }}">
                    <a href="{{ route('admin.requests.index') }}" class="link1">
                        <i class="fas fa-th-list"></i>
                        <p>{{ $portalUser->hasPermissionTo('view_all_requests') ? 'All Requests' : 'My Assignments' }}</p>
                    </a>
                </li>
                <li class="nav-item nav-item1 {{ request('status') === '0' ? 'active' : '' }}">
                    <a href="{{ route('admin.requests.index', ['status' => 0]) }}" class="link1">
                        <i class="fas fa-clock"></i>
                        <p>Pending</p>
                    </a>
                </li>
                <li class="nav-item nav-item1 {{ request('status') === '1' ? 'active' : '' }}">
                    <a href="{{ route('admin.requests.index', ['status' => 1]) }}" class="link1">
                        <i class="fas fa-user-check"></i>
                        <p>Assigned</p>
                    </a>
                </li>
                <li class="nav-item nav-item1 {{ request('status') === '2' ? 'active' : '' }}">
                    <a href="{{ route('admin.requests.index', ['status' => 2]) }}" class="link1">
                        <i class="fas fa-search"></i>
                        <p>In Review</p>
                    </a>
                </li>
                <li class="nav-item nav-item1 {{ request('status') === '3' ? 'active' : '' }}">
                    <a href="{{ route('admin.requests.index', ['status' => 3]) }}" class="link1">
                        <i class="fas fa-paper-plane"></i>
                        <p>Sent for Review</p>
                    </a>
                </li>
                <li class="nav-item nav-item1 {{ request('status') === '4' ? 'active' : '' }}">
                    <a href="{{ route('admin.requests.index', ['status' => 4]) }}" class="link1">
                        <i class="fas fa-check-circle"></i>
                        <p>Approved</p>
                    </a>
                </li>
                <li class="nav-item nav-item1 {{ request('status') === '5' ? 'active' : '' }}">
                    <a href="{{ route('admin.requests.index', ['status' => 5]) }}" class="link1">
                        <i class="fas fa-check-double"></i>
                        <p>Completed</p>
                    </a>
                </li>
            </ul>
        </div>
    </li>

    @if($portalUser->hasPermissionTo('manage_portal_users'))
    <li class="nav-section">
        <h4 class="text-section">Administration</h4>
    </li>

    <li class="nav-item nav-item1 navmenuli {{ request()->routeIs('admin.users.*') ? 'active submenu' : '' }}" id="usersnav">
        <a data-toggle="collapse" href="#usersMenu"
           aria-expanded="{{ request()->routeIs('admin.users.*') ? 'true' : 'false' }}"
           class="lftmenu">
            <i class="fas fa-users"></i>
            <p>Portal Users</p>
            <span class="caret"></span>
        </a>
        <div class="collapse menudiv {{ request()->routeIs('admin.users.*') ? 'show' : '' }}" id="usersMenu">
            <ul class="nav nav-collapse">
                <li class="nav-item nav-item1 {{ request()->routeIs('admin.users.index') ? 'active' : '' }}">
                    <a href="{{ route('admin.users.index') }}" class="link1">
                        <i class="fas fa-list"></i>
                        <p>All Users</p>
                    </a>
                </li>
                <li class="nav-item nav-item1 {{ request()->routeIs('admin.users.create') ? 'active' : '' }}">
                    <a href="{{ route('admin.users.create') }}" class="link1">
                        <i class="fas fa-user-plus"></i>
                        <p>Add User</p>
                    </a>
                </li>
            </ul>
        </div>
    </li>
    @endif

    <li class="nav-item nav-item1">
        <form method="POST" action="{{ route('portal.logout') }}" style="margin:0">
            @csrf
            <button type="submit" class="link1">
                <i class="fas fa-sign-out-alt"></i>
                <p>Logout</p>
            </button>
        </form>
    </li>
@endauth

@if(session()->has('auth_user'))
    <li class="nav-item nav-item1 {{ request()->routeIs('user.dashboard') ? 'active' : '' }}">
        <a href="{{ route('user.dashboard') }}" class="link1">
            <i class="fas fa-home"></i>
            <p>My Requests</p>
        </a>
    </li>
    <li class="nav-item nav-item1 {{ request()->routeIs('user.request.create') ? 'active' : '' }}">
        <a href="{{ route('user.request.create') }}" class="link1">
            <i class="fas fa-plus-circle"></i>
            <p>New Request</p>
        </a>
    </li>
    <li class="nav-item nav-item1">
        <form method="POST" action="{{ route('sso.logout') }}" style="margin:0">
            @csrf
            <button type="submit" class="link1">
                <i class="fas fa-sign-out-alt"></i>
                <p>Logout</p>
            </button>
        </form>
    </li>
@endif
