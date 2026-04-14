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

    <li class="nav-item nav-item1 {{ request()->routeIs('inbox.messages') ? 'active' : '' }}">
        <a href="{{ route('inbox.messages') }}" class="link1">
            <i class="fas fa-envelope"></i>
            <p>Messages</p>
            @php
                $staffUnreadMsgs = \App\Models\PortalNotification::where('notifiable_type', 'staff')
                    ->where(function ($q) { $q->whereNull('notifiable_id')->orWhere('notifiable_id', Auth::guard('portal')->id()); })
                    ->where('type', 'new_chat')->where('is_read', false)->count();
            @endphp
            @if($staffUnreadMsgs > 0)
                <span class="badge badge-warning ml-auto">{{ $staffUnreadMsgs }}</span>
            @endif
        </a>
    </li>
    <li class="nav-item nav-item1 {{ request()->routeIs('inbox.notifications') ? 'active' : '' }}">
        <a href="{{ route('inbox.notifications') }}" class="link1">
            <i class="fas fa-bell"></i>
            <p>Notifications</p>
            @php
                $staffUnreadNotifs = \App\Models\PortalNotification::where('notifiable_type', 'staff')
                    ->where(function ($q) { $q->whereNull('notifiable_id')->orWhere('notifiable_id', Auth::guard('portal')->id()); })
                    ->where('type', '!=', 'new_chat')->where('is_read', false)->count();
            @endphp
            @if($staffUnreadNotifs > 0)
                <span class="badge badge-warning ml-auto">{{ $staffUnreadNotifs }}</span>
            @endif
        </a>
    </li>

    <li class="nav-item nav-item1 {{ request()->routeIs('admin.bug-reports.*') ? 'active' : '' }}">
        <a href="{{ route('admin.bug-reports.index') }}" class="link1">
            <i class="fas fa-bug"></i>
            <p>Bug Reports</p>
            @php $unreadBugs = \App\Models\BugReport::where('is_read', false)->count(); @endphp
            @if($unreadBugs > 0)
                <span class="badge badge-warning ml-auto">{{ $unreadBugs }}</span>
            @endif
        </a>
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
    <li class="nav-item nav-item1">
        <a href="https://dashboard.gowologlobal.com/{{ base64_encode(session('auth_user.email')) }}" class="link1">
            <i class="fas fa-tachometer-alt"></i>
            <p>Dashboard</p>
        </a>
    </li>
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
    <li class="nav-item nav-item1 {{ request()->routeIs('inbox.messages') ? 'active' : '' }}">
        <a href="{{ route('inbox.messages') }}" class="link1">
            <i class="fas fa-envelope"></i>
            <p>Messages</p>
            @php
                $userUnreadMsgs = \App\Models\PortalNotification::where('notifiable_type', 'user')
                    ->where('notifiable_id', session('auth_user.user_id'))
                    ->where('type', 'new_chat')->where('is_read', false)->count();
            @endphp
            @if($userUnreadMsgs > 0)
                <span class="badge badge-warning ml-auto">{{ $userUnreadMsgs }}</span>
            @endif
        </a>
    </li>
    <li class="nav-item nav-item1 {{ request()->routeIs('inbox.notifications') ? 'active' : '' }}">
        <a href="{{ route('inbox.notifications') }}" class="link1">
            <i class="fas fa-bell"></i>
            <p>Notifications</p>
            @php
                $userUnreadNotifs = \App\Models\PortalNotification::where('notifiable_type', 'user')
                    ->where('notifiable_id', session('auth_user.user_id'))
                    ->where('type', '!=', 'new_chat')->where('is_read', false)->count();
            @endphp
            @if($userUnreadNotifs > 0)
                <span class="badge badge-warning ml-auto">{{ $userUnreadNotifs }}</span>
            @endif
        </a>
    </li>
    <li class="nav-item nav-item1 {{ request()->routeIs('user.bug-report.*') ? 'active' : '' }}">
        <a href="{{ route('user.bug-report.index') }}" class="link1">
            <i class="fas fa-bug"></i>
            <p>Bug Reports</p>
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
