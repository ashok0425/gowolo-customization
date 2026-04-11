<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <title>@yield('title', 'Customization Portal') — {{ config('app.name') }}</title>
    <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('admin/assets/img/no-image.png') }}" type="image/x-icon">

    <link href='https://fonts.googleapis.com/css?family=Poppins' rel='stylesheet'>

    {{-- Fonts and icons --}}
    <script src="{{ asset('admin/assets/js/plugin/webfont/webfont.min.js') }}"></script>
    <script>
        WebFont.load({
            google: { "families": ["Montserrat:300,400,500,600,700,900"] },
            custom: {
                "families": ["Flaticon", "Font Awesome 5 Solid", "Font Awesome 5 Regular", "Font Awesome 5 Brands", "simple-line-icons"],
                urls: ['{{ asset('admin/assets/css/fonts.min.css') }}']
            },
            active: function () { sessionStorage.fonts = true; }
        });
    </script>

    {{-- CSS Files (mirrors dashboardv2 user master) --}}
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('admin/assets/css/demo.css') }}">
    <link rel="stylesheet" href="{{ asset('common/vendor/datatable/datatables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('common/vendor/x-editable/css/bootstrap-editable.css') }}">
    <link rel="stylesheet" href="{{ asset('common/vendor/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('common/vendor/daterangepicker/daterangepicker.css') }}">
    <link rel="stylesheet" href="{{ asset('common/vendor/fileinput/css/jasny-bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/assets/css/atlantis.css') }}">
    <link rel="stylesheet" href="{{ asset('common/css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ekko-lightbox/5.3.0/ekko-lightbox.css">

    <style>
        :root {
            --primary-color: #662c87;
            --secondary-color: #f3e8fb;
        }

        body, h1, .h1, h2, .h2, h3, .h3, h4, .h4, h5, .h5, h6, .h6, p,
        .navbar, .brand, .btn-simple, .alert, a, .td-name, td, button.close,
        .form-group label, .form-check label, .page-title {
            font-family: 'Poppins', 'Montserrat', sans-serif;
        }

        .wrapper { background-color: #ffffff; }

        .modal { background-color: rgba(2,1,1,0.52) !important; }
        .modal-header { background-color: var(--primary-color) !important; color: #fff; }

        /* Header / Sidebar — purple */
        .main-header,
        .sidebar-wrapper,
        .logo-header {
            background-color: var(--primary-color) !important;
        }
        .main-header {
            box-shadow: none !important;
            border-radius: 0px 0px 20px 20px;
        }
        .logo-header { height: 49px; }
        .logo-header .nav-toggle { right: 0 !important; }

        /* Sidebar wrapper — rounded floating card */
        .sidebar, .sidebar[data-background-color="white"] {
            width: 230px;
            margin-top: 72px !important;
        }
        .sidebar.sidebar-style-2 {
            margin: 10px;
            background: none;
            height: fit-content;
        }
        .sidebar .sidebar-wrapper,
        .sidebar[data-background-color="white"] .sidebar-wrapper {
            border-radius: 11px !important;
            height: fit-content;
        }

        /* Sidebar items — dashboardv2 nav-item1 pattern. White text always. */
        .sidebar-content .nav-item1 a,
        .sidebar-content .nav-item1 button.link1,
        .sidebar-content .nav-item1 a p,
        .sidebar-content .nav-item1 button.link1 p,
        .sidebar-content .nav-item1 a i,
        .sidebar-content .nav-item1 button.link1 i,
        .sidebar-content .nav-item1 a .caret {
            color: #ffffff !important;
        }
        .sidebar-content .nav-item1 a,
        .sidebar-content .nav-item1 button.link1 {
            font-weight: 500;
            display: flex;
            align-items: center;
            padding: 10px 18px;
            border-radius: 8px;
            margin: 4px 8px;
            width: calc(100% - 16px);
            background: transparent;
            border: none;
            cursor: pointer;
            text-align: left;
            text-decoration: none;
            font-family: inherit;
        }
        .sidebar-content .nav-item1 a p,
        .sidebar-content .nav-item1 button.link1 p {
            margin: 0 0 0 12px;
            font-size: 13px !important;
        }
        .sidebar-content .nav-item1 a i,
        .sidebar-content .nav-item1 button.link1 i {
            font-size: 13px;
            width: 18px;
            text-align: center;
        }
        .sidebar-content .nav-item1 a .caret {
            margin-left: auto;
        }
        /* Logout form wrapper — kill its box-model so button aligns like other items */
        .sidebar-content .nav-item1 form {
            margin: 0;
            padding: 0;
            display: block;
        }
        /* All icons in sidebar + header → white by default, every state */
        .sidebar i, .sidebar .fa, .sidebar .fas, .sidebar .far, .sidebar .fab,
        .main-header i, .main-header .fa, .main-header .fas, .main-header .far, .main-header .fab,
        .navbar-header i, .navbar-header .fa, .navbar-header .fas, .navbar-header .far, .navbar-header .fab {
            color: #ffffff !important;
        }
        /* Override Atlantis blue (#1572E8) on active sidebar items.
           Match its full selector specificity so this wins. */
        .sidebar .nav.nav-primary > .nav-item.active a i,
        .sidebar[data-background-color="white"] .nav.nav-primary > .nav-item.active a i,
        .sidebar .nav.nav-primary > .nav-item a:hover i,
        .sidebar .nav.nav-primary > .nav-item a:focus i,
        .sidebar .nav.nav-primary > .nav-item a[data-toggle=collapse][aria-expanded=true] i,
        .sidebar .nav.nav-primary > .nav-item.active a:before,
        .sidebar .nav.nav-primary > .nav-item.active > a p,
        .sidebar .nav.nav-primary .nav-collapse li.active a,
        .sidebar .nav.nav-primary .nav-collapse li.active a i,
        .sidebar .nav.nav-primary .nav-collapse li.active a p,
        .sidebar .nav.nav-primary .nav-collapse li a:hover,
        .sidebar .nav.nav-primary .nav-collapse li a:hover i,
        .sidebar .nav.nav-primary .nav-collapse li a:hover p {
            color: #ffffff !important;
        }
        /* Hover on parent */
        .sidebar-content .nav-item1 > a:hover {
            background-color: rgba(255,255,255,0.12) !important;
        }
        /* Active / open parent — darker purple, still white text */
        .sidebar.sidebar-style-2 .nav .nav-item.active > a,
        .sidebar.sidebar-style-2 .nav .nav-item > a[data-toggle=collapse][aria-expanded=true],
        .drop_down_open {
            background-color: #4f1f6c !important;
            border-radius: 8px 8px 0 0 !important;
            margin-bottom: 0 !important;
        }
        .sidebar.sidebar-style-2 .nav .nav-item.active > a,
        .sidebar.sidebar-style-2 .nav .nav-item.active > a p,
        .sidebar.sidebar-style-2 .nav .nav-item.active > a i,
        .sidebar.sidebar-style-2 .nav .nav-item > a[data-toggle=collapse][aria-expanded=true],
        .sidebar.sidebar-style-2 .nav .nav-item > a[data-toggle=collapse][aria-expanded=true] p,
        .sidebar.sidebar-style-2 .nav .nav-item > a[data-toggle=collapse][aria-expanded=true] i,
        .drop_down_open,
        .drop_down_open p,
        .drop_down_open i {
            color: #ffffff !important;
            font-weight: 600;
        }

        /* Sidebar sub-menu (collapse) — transparent, inherits sidebar purple */
        .sidebar .nav-collapse,
        .sidebar-content .nav-item1 .nav-collapse {
            background: transparent !important;
            margin: 0;
            padding: 4px 0;
            border-radius: 0;
            list-style: none;
            box-shadow: none;
        }
        .sidebar .nav-collapse li,
        .sidebar-content .nav-item1 .nav-collapse li {
            list-style: none;
        }
        .sidebar .nav-collapse li a,
        .sidebar-content .nav-item1 .nav-collapse li a {
            padding: 9px 0 9px 32px !important;
            margin: 1px 0 !important;
            color: #ffffff !important;
            font-size: 12.5px;
            border-radius: 0;
            display: flex;
            align-items: center;
            font-weight: 400;
            background: transparent !important;
        }
        .sidebar .nav-collapse li a p {
            font-size: 12.5px !important;
            margin: 0 0 0 10px !important;
            color: #ffffff !important;
            font-weight: 400;
            line-height: 1.3;
        }
        .sidebar .nav-collapse li a i {
            font-size: 12px;
            width: 16px;
            text-align: center;
            color: #ffffff !important;
            flex-shrink: 0;
        }
        .sidebar .nav-collapse li a:hover,
        .sidebar .nav-collapse li.active a {
            background-color: rgba(255,255,255,0.12) !important;
        }
        .sidebar .nav-collapse li a:hover,
        .sidebar .nav-collapse li a:hover p,
        .sidebar .nav-collapse li a:hover i,
        .sidebar .nav-collapse li.active a,
        .sidebar .nav-collapse li.active a p,
        .sidebar .nav-collapse li.active a i {
            color: #ffffff !important;
            font-weight: 600;
        }

        /* Logo */
        .logo { height: 49px; position: relative; width: 220px; float: left; }
        .logo b { color: #fff !important; font-size: 16px; line-height: 49px; }

        /* Top-bar icons / dropdowns */
        .navbar-header .nav-link i { color: #fff !important; }
        .profile-pic span { color: #fff; font-size: 14px; font-weight: 500; }

        /* Main panel — white card content */
        .main-panel > .content { background-color: #f4f5f7; min-height: calc(100vh - 130px); }
        .page-inner { padding: 25px 30px; }
        .card { border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); border: none; }
        .card-header { background-color: #fff; border-bottom: 1px solid #f1f1f4; border-radius: 12px 12px 0 0 !important; padding: 18px 22px; }
        .card-title { font-weight: 600; color: #333; }

        /* Buttons — purple primary */
        .btn-primary, .btn-info, .listgth {
            background-color: var(--primary-color) !important;
            border-color: var(--primary-color) !important;
            color: #fff !important;
        }
        .btn-primary:hover, .btn-info:hover, .listgth:hover {
            background-color: #4f1f6c !important;
            border-color: #4f1f6c !important;
        }

        /* Tables */
        table.dataTable thead th { background-color: #fafafa; color: #333; font-weight: 600; }
        .table-bordered, .table-bordered td, .table-bordered th { border-color: #ececec !important; }

        /* Badges from old palette */
        .badge-new { background-color: #e74c3c; color: #fff; }
        .badge-progress { background-color: #f39c12; color: #fff; }
        .badge-completed { background-color: #27ae60; color: #fff; }

        /* Reset list bullets */
        .nav, .nav-primary, .nav-collapse { list-style: none; padding-left: 0; }

        .nav-section h4.text-section { color: rgba(255,255,255,0.7); font-size: 11px; letter-spacing: 1px; text-transform: uppercase; padding: 12px 18px 4px; margin: 0; }

        /* Notification / message badge */
        .notif-count, .msg-count {
            position: absolute; top: 8px; right: 4px;
            background: #e74c3c; color: #fff; font-size: 10px;
            width: 18px; height: 18px; border-radius: 50%;
            text-align: center; line-height: 18px; font-weight: 700;
        }

        /* Right offcanvas panel */
        .offcanvas-right {
            position: fixed; top: 0; right: -400px; width: 380px;
            height: 100vh; background: #fff; z-index: 99999;
            box-shadow: -4px 0 20px rgba(0,0,0,0.12);
            transition: right 0.3s ease;
            display: flex; flex-direction: column;
        }
        .offcanvas-right.open { right: 0; }
        .offcanvas-backdrop {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.35); z-index: 99998;
            display: none;
        }
        .offcanvas-backdrop.show { display: block; }
        .offcanvas-header {
            display: flex; align-items: center; justify-content: space-between;
            padding: 18px 20px; border-bottom: 1px solid #f0f0f3;
            background: #fafbfc; flex-shrink: 0;
        }
        .offcanvas-header h5 { margin: 0; font-size: 15px; font-weight: 700; color: #333; }
        .offcanvas-header .close-panel {
            background: none; border: none; font-size: 22px; color: #999;
            cursor: pointer; padding: 0; line-height: 1;
        }
        .offcanvas-header .close-panel:hover { color: #333; }
        .offcanvas-header .clear-link {
            font-size: 12px; color: #662c87; text-decoration: none; font-weight: 600;
        }
        .offcanvas-header .clear-link:hover { text-decoration: underline; }
        .offcanvas-body { flex: 1; overflow-y: auto; }
        /* Notification card */
        .notif-card {
            display: flex; gap: 12px; padding: 14px 16px;
            border-bottom: 1px solid #f1f1f4; transition: background 0.15s;
            cursor: default;
        }
        .notif-card:hover { background: #f9f3fc; }
        .notif-icon {
            width: 40px; height: 40px; border-radius: 50%;
            background: #f3e8fb; color: #662c87;
            display: flex; align-items: center; justify-content: center;
            font-size: 16px; flex-shrink: 0;
        }
        .notif-icon.chat { background: #e8f5e9; color: #27ae60; }
        .notif-body { flex: 1; min-width: 0; }
        .notif-header { display: flex; align-items: center; gap: 6px; margin-bottom: 2px; }
        .notif-source { font-size: 11px; color: #662c87; font-weight: 600; }
        .notif-time { font-size: 11px; color: #aaa; }
        .notif-title { font-size: 13px; font-weight: 600; color: #333; margin-bottom: 2px; }
        .notif-text { font-size: 12px; color: #666; line-height: 1.4; margin-bottom: 6px; }
        .notif-actions { display: flex; gap: 16px; }
        .notif-actions a, .notif-actions button {
            font-size: 12px; font-weight: 600; background: none; border: none;
            cursor: pointer; padding: 0;
        }
        .notif-dismiss { color: #999; }
        .notif-dismiss:hover { color: #333; }
        .notif-action { color: #662c87; text-decoration: none; }
        .notif-action:hover { text-decoration: underline; }
        .notif-close {
            background: none; border: none; color: #ccc; font-size: 16px;
            cursor: pointer; padding: 0; line-height: 1; flex-shrink: 0;
        }
        .notif-close:hover { color: #333; }

        /* Permission selector cards (user create / edit) */
        .perm-card {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 14px;
            border: 2px solid #e4e6ef;
            border-radius: 10px;
            background: #fafbfc;
            cursor: pointer;
            transition: all 0.15s ease;
            margin: 0;
            width: 100%;
        }
        .perm-card:hover {
            border-color: var(--primary-color);
            background: #f9f3fc;
        }
        .perm-card input[type="checkbox"] {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }
        .perm-card .perm-check {
            width: 22px;
            height: 22px;
            border: 2px solid #c2c5d2;
            border-radius: 6px;
            background: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: all 0.15s ease;
        }
        .perm-card .perm-check::after {
            content: "\2713";
            color: #fff;
            font-size: 14px;
            font-weight: 700;
            line-height: 1;
            opacity: 0;
        }
        .perm-card input[type="checkbox"]:checked ~ .perm-check {
            background: var(--primary-color);
            border-color: var(--primary-color);
        }
        .perm-card input[type="checkbox"]:checked ~ .perm-check::after {
            opacity: 1;
        }
        .perm-card input[type="checkbox"]:checked ~ .perm-text {
            color: var(--primary-color);
            font-weight: 600;
        }
        .perm-card:has(input[type="checkbox"]:checked) {
            border-color: var(--primary-color);
            background: #f9f3fc;
        }
        .perm-card .perm-text {
            font-size: 13px;
            color: #4a4a4a;
            font-weight: 500;
        }
    </style>

    @stack('css')
</head>

<body>
<div class="wrapper">

    {{-- Header --}}
    <div class="main-header">
        <div class="logo-header text-center" data-background-color="blue">
            <a href="{{ Auth::guard('portal')->check() ? route('admin.dashboard') : url('/') }}" class="logo text-center">
                <img src="{{ asset('common/img/goWOLO_Logo_White.png') }}" alt="GoWolo" style="max-height:35px;">
            </a>
            <button class="navbar-toggler sidenav-toggler ml-auto" type="button" data-toggle="collapse"
                    data-target="collapse" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"><i class="icon-menu"></i></span>
            </button>
            <button class="topbar-toggler more"><i class="icon-options-vertical"></i></button>
            <div class="nav-toggle">
                <button class="btn btn-toggle toggle-sidebar"><i class="icon-menu"></i></button>
            </div>
        </div>

        <nav class="navbar navbar-header navbar-expand-lg" data-background-color="">
            <div class="container-fluid">
                <ul class="navbar-nav topbar-nav ml-md-auto align-items-center">

                    {{-- Messages Icon (dropdown) --}}
                    <li class="nav-item dropdown hidden-caret" id="msgDropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="position:relative;">
                            <i class="fas fa-comment-dots"></i>
                            <span class="notification msg-count" style="display:none;">0</span>
                        </a>
                        <ul class="dropdown-menu notif-box animated fadeIn" aria-labelledby="msgDropdown" style="width:420px;max-height:500px;overflow-y:auto;right:0;left:auto;">
                            <li>
                                <div class="dropdown-title d-flex justify-content-between align-items-center">
                                    <span><i class="fas fa-comment-dots mr-1" style="color:#662c87;"></i> Messages</span>
                                    <a href="#" onclick="clearType('new_chat');return false;" style="font-size:11px;color:#662c87;">Clear All</a>
                                </div>
                            </li>
                            <li>
                                <div id="msgList">
                                    <div class="text-center text-muted py-3" style="font-size:13px;">
                                        <i class="fas fa-comment-slash d-block mb-1" style="font-size:20px;color:#ddd;"></i>
                                        No new messages
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </li>

                    {{-- Notification Bell (offcanvas) --}}
                    <li class="nav-item hidden-caret">
                        <a class="nav-link" href="#" onclick="togglePanel('notifPanel');return false;" style="position:relative;">
                            <i class="fas fa-bell"></i>
                            <span class="notification notif-count" style="display:none;">0</span>
                        </a>
                    </li>

                    @auth('portal')
                    @php $portalUserNav = Auth::guard('portal')->user(); @endphp
                    <li class="nav-item dropdown hidden-caret">
                        <a class="dropdown-toggle profile-pic" data-toggle="dropdown" href="#" aria-expanded="false">
                            <div class="avatar-sm float-left mr-2">
                                <img src="{{ asset('admin/assets/img/no-image.png') }}" alt="..." class="avatar-img rounded-circle">
                            </div>
                            <span>{{ $portalUserNav->full_name }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-user animated fadeIn">
                            <div class="dropdown-user-scroll scrollbar-outer">
                                <li>
                                    <div class="user-box">
                                        <div class="avatar-lg">
                                            <img src="{{ asset('admin/assets/img/no-image.png') }}" alt="..." class="avatar-img rounded-circle">
                                        </div>
                                        <div class="u-text">
                                            <h4>{{ $portalUserNav->full_name }}</h4>
                                            <p class="text-muted">{{ $portalUserNav->email }}</p>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="dropdown-divider"></div>
                                    <a href="{{ route('admin.profile.password') }}" class="dropdown-item">
                                        <i class="fas fa-key mr-2"></i>Change Password
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    <form method="POST" action="{{ route('portal.logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item"><i class="fas fa-sign-out-alt mr-2"></i>Logout</button>
                                    </form>
                                </li>
                            </div>
                        </ul>
                    </li>
                    @endauth

                    @if(session()->has('auth_user'))
                    <li class="nav-item dropdown hidden-caret">
                        <a class="dropdown-toggle profile-pic" data-toggle="dropdown" href="#" aria-expanded="false">
                            <span>{{ session('auth_user.name') }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-user animated fadeIn">
                            <li>
                                <form method="POST" action="{{ route('sso.logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item"><i class="fas fa-sign-out-alt mr-2"></i>Logout</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                    @endif
                </ul>
            </div>
        </nav>
    </div>
    {{-- End Header --}}

    {{-- Sidebar --}}
    <div class="sidebar sidebar-style-2">
        <div class="sidebar-wrapper scrollbar scrollbar-inner">
            <div class="sidebar-content">
                <ul class="nav nav-primary">
                    @include('partials.sidebar')
                </ul>
            </div>
        </div>
    </div>
    {{-- End Sidebar --}}

    {{-- Main Panel --}}
    <div class="main-panel">
        <div class="content">
            <div class="page-inner">

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                    </div>
                @endif

                @yield('content')
            </div>
        </div>

        <footer class="footer">
            <div class="container-fluid">
                <div class="copyright ml-auto">
                    &copy; {{ date('Y') }}, {{ config('app.name') }}
                </div>
            </div>
        </footer>
    </div>
    {{-- End Main Panel --}}

</div>

{{-- Offcanvas backdrop --}}
<div class="offcanvas-backdrop" id="offcanvasBackdrop" onclick="closeAllPanels()"></div>

{{-- Notifications offcanvas --}}
<div class="offcanvas-right" id="notifPanel">
    <div class="offcanvas-header">
        <h5><i class="fas fa-bell mr-2" style="color:#662c87;"></i> Notifications</h5>
        <div>
            <a href="#" class="clear-link" onclick="clearType('new_request');return false;">Clear All</a>
            <button class="close-panel ml-3" onclick="closeAllPanels()">&times;</button>
        </div>
    </div>
    <div class="offcanvas-body" id="notifList">
        <div class="text-center text-muted py-5">
            <i class="fas fa-bell-slash d-block mb-2" style="font-size:32px;color:#ddd;"></i>
            No new notifications
        </div>
    </div>
</div>

{{-- Core JS — js/app.js is the compiled Mix bundle with jQuery + Bootstrap + Popper --}}
<script src="{{ asset('js/app.js') }}"></script>
<script src="{{ asset('admin/assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js') }}"></script>
<script src="{{ asset('admin/assets/js/atlantis.js') }}"></script>
<script src="{{ asset('common/vendor/datatable/datatables.min.js') }}"></script>
<script src="{{ asset('common/vendor/select2/js/select2.full.min.js') }}"></script>
<script src="{{ asset('common/vendor/moment/moment.min.js') }}"></script>
<script src="{{ asset('common/vendor/daterangepicker/daterangepicker.js') }}"></script>

<script>
    // Sidebar collapse toggle (matches dashboardv2)
    function callmenutoggle(id, lid) {
        $('.lftmenu').attr('aria-expanded', 'false');
        $('.menudiv').removeClass('show');
        $('.navmenuli').removeClass('submenu');
        $('.lftmenu').removeClass('drop_down_open');
        $('#' + id).addClass('show');
        $('#' + lid).addClass('submenu');
    }

    // CSRF setup for all AJAX
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

    $(function() {
        // Date range pickers — matches dashboardv2 configuration
        $(".date-range").daterangepicker({
            opens: 'left',
            autoUpdateInput: false,
            locale: {
                format: 'YYYY-MM-DD',
                cancelLabel: 'Clear'
            },
            maxDate: moment(),
            ranges: {
                'Today':              [moment(), moment()],
                'Yesterday':          [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'This Week':          [moment().subtract(6, 'days'), moment()],
                'This Week to-date':  [moment().startOf('week'), moment()],
                'This Month':         [moment().subtract(1, 'months'), moment()],
                'This Month to-date': [moment().startOf('month'), moment()],
                'This Quarter':       [moment().subtract(3, 'months'), moment()],
                'This Quarter to-date': [moment().startOf('quarter'), moment()],
                'This Year':          [moment().subtract(1, 'year'), moment()],
                'This Year to-date':  [moment().startOf('year'), moment()],
                'Last Year':          [moment().subtract(1, 'year').add(1,'day'), moment()]
            }
        }, function(start, end, label) {
            // optional callback
        });

        $('.date-range').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
        });

        $('.date-range').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });

        // Select2
        $('.select2').select2();
    });
</script>

@stack('js')

<script>
// Offcanvas panel toggle + notification polling
(function() {
    var POLL_INTERVAL = 10000;
    var TOKEN = $('meta[name="csrf-token"]').attr('content');

    // Panel toggle
    window.togglePanel = function(id) {
        var $panel = $('#' + id);
        var $backdrop = $('#offcanvasBackdrop');
        if ($panel.hasClass('open')) {
            $panel.removeClass('open');
            $backdrop.removeClass('show');
        } else {
            $('.offcanvas-right').removeClass('open');
            $panel.addClass('open');
            $backdrop.addClass('show');
        }
    };
    window.closeAllPanels = function() {
        $('.offcanvas-right').removeClass('open');
        $('#offcanvasBackdrop').removeClass('show');
    };

    // Build a notification card
    function buildCard(n) {
        var isChat = (n.type === 'new_chat');
        var iconClass = isChat ? 'notif-icon chat' : 'notif-icon';
        var faIcon    = isChat ? 'fas fa-comment' : 'fas fa-bell';
        var emoji     = isChat
            ? '<i class="fas fa-envelope" style="color:#662c87;"></i> '
            : '<i class="fas fa-exclamation-triangle" style="color:#e67e22;"></i> ';

        return '<div class="notif-card" data-id="' + n.id + '" style="position:relative;">'
            + '<div class="' + iconClass + '"><i class="' + faIcon + '"></i></div>'
            + '<div class="notif-body">'
            +   '<div class="notif-header">'
            +     '<span class="notif-source">Customization</span>'
            +     '<span class="notif-time">&bull; ' + n.time_ago + '</span>'
            +   '</div>'
            +   '<div class="notif-title">' + emoji + n.title + '</div>'
            +   '<div class="notif-text">' + n.body + '</div>'
            +   '<div class="notif-actions">'
            +     '<button class="notif-dismiss" onclick="dismissNotif(' + n.id + ')">Dismiss</button>'
            +     (n.action_url ? '<a class="notif-action" href="' + n.action_url + '"><strong>' + n.action_label + '</strong></a>' : '')
            +   '</div>'
            + '</div>'
            + '<button class="notif-close" onclick="dismissNotif(' + n.id + ')">&times;</button>'
            + '</div>';
    }

    function emptyState(icon, text) {
        return '<div class="text-center text-muted py-5"><i class="' + icon + ' d-block mb-2" style="font-size:32px;color:#ddd;"></i>' + text + '</div>';
    }

    // Split notifications into messages vs requests and render
    function renderSplit(data) {
        var msgs = [], notifs = [];
        $.each(data.notifications, function(i, n) {
            if (n.type === 'new_chat') msgs.push(n);
            else notifs.push(n);
        });

        // Messages panel — latest 4
        var $msgList = $('#msgList'), $msgCount = $('.msg-count');
        if (msgs.length) {
            $msgCount.text(msgs.length).show();
            var html = '';
            $.each(msgs.slice(0, 5), function(i, n) { html += buildCard(n); });
            $msgList.html(html);
        } else {
            $msgCount.hide();
            $msgList.html(emptyState('fas fa-comment-slash', 'No new messages'));
        }

        // Notifications panel — all requests
        var $notifList = $('#notifList'), $notifCount = $('.notif-count');
        if (notifs.length) {
            $notifCount.text(notifs.length).show();
            var html2 = '';
            $.each(notifs, function(i, n) { html2 += buildCard(n); });
            $notifList.html(html2);
        } else {
            $notifCount.hide();
            $notifList.html(emptyState('fas fa-bell-slash', 'No new notifications'));
        }
    }

    function fetchNotifications() {
        $.getJSON('{{ route("api.notifications") }}', function(data) {
            renderSplit(data);
        });
    }

    // Dismiss single notification
    window.dismissNotif = function(id) {
        var $card = $('.notif-card[data-id="' + id + '"]');
        var $body = $card.closest('.offcanvas-body');
        $.post('/api/notifications/' + id + '/dismiss', { _token: TOKEN }, function() {
            $card.fadeOut(200, function() {
                $(this).remove();
                var remaining = $body.find('.notif-card').length;
                var isMsg = ($body.attr('id') === 'msgList');
                var $badge = isMsg ? $('.msg-count') : $('.notif-count');
                if (remaining) {
                    $badge.text(remaining).show();
                } else {
                    $badge.hide();
                    $body.html(emptyState(
                        isMsg ? 'fas fa-comment-slash' : 'fas fa-bell-slash',
                        isMsg ? 'No new messages' : 'No new notifications'
                    ));
                }
            });
        });
    };

    // Clear all per type
    window.clearType = function(type) {
        var isMsg = (type === 'new_chat');
        var $body = isMsg ? $('#msgList') : $('#notifList');
        var ids = [];
        $body.find('.notif-card').each(function() { ids.push($(this).data('id')); });
        if (!ids.length) return;
        $.each(ids, function(i, id) {
            $.post('/api/notifications/' + id + '/dismiss', { _token: TOKEN });
        });
        var $badge = isMsg ? $('.msg-count') : $('.notif-count');
        $badge.hide();
        $body.html(emptyState(
            isMsg ? 'fas fa-comment-slash' : 'fas fa-bell-slash',
            isMsg ? 'No new messages' : 'No new notifications'
        ));
    };

    // Initial fetch + poll
    fetchNotifications();
    setInterval(fetchNotifications, POLL_INTERVAL);
})();
</script>
</body>
</html>
