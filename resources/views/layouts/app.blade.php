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
                <b>GoWolo</b>
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
</body>
</html>
