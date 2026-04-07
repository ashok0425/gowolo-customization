<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <title>@yield('title', 'Customization Portal') — {{ config('app.name') }}</title>
    <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('admin/assets/img/no-image.png') }}" type="image/x-icon">

    <script src="{{ asset('admin/assets/js/plugin/webfont/webfont.min.js') }}"></script>
    <script>
        WebFont.load({
            google: {"families": ["Montserrat:300,400,500,600,700,900"]},
            custom: {
                "families": ["Flaticon", "Font Awesome 5 Solid", "Font Awesome 5 Regular", "Font Awesome 5 Brands", "simple-line-icons"],
                urls: ['{{ asset('admin/assets/css/fonts.min.css') }}']
            },
            active: function () { sessionStorage.fonts = true; }
        });
    </script>

    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('admin/assets/css/demo.css') }}">
    <link rel="stylesheet" href="{{ asset('common/vendor/datatable/datatables.min.css') }}">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.6.2/css/buttons.dataTables.min.css">
    <link rel="stylesheet" href="{{ asset('common/vendor/x-editable/css/bootstrap-editable.css') }}">
    <link rel="stylesheet" href="{{ asset('common/vendor/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('common/vendor/daterangepicker/daterangepicker.css') }}">
    <link rel="stylesheet" href="{{ asset('common/vendor/fileinput/css/jasny-bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/assets/css/atlantis.css') }}">
    <link rel="stylesheet" href="{{ asset('common/css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ekko-lightbox/5.3.0/ekko-lightbox.css">

    <style>
        body, h1, h2, h3, h4, h5, h6, p, .navbar, .brand, .btn-simple, .alert, a, td,
        .form-group label, .form-check label, .page-title {
            font-family: 'Montserrat', sans-serif;
        }
        .wrapper { background-color: white; }
        .modal { background-color: rgba(2,1,1,0.52) !important; }
        .modal-header { background-color: #662c87 !important; color: #fff; }
        .main-header, .sidebar-wrapper { background-color: #662c87 !important; }
        .sidebar.sidebar-style-2 .nav.nav-primary > .nav-item.active > a,
        .sidebar.sidebar-style-2 .nav .nav-item a:hover {
            background: rgba(255,255,255,0.15) !important;
        }
        .logo { height: 60px; position: relative; width: 220px; float: left; }
        .logo img { position: absolute; margin: auto !important; max-height: 100%; top: 0; bottom: 0; right: 0; left: 0; max-width: 100%; }
        .badge-new { background-color: #e74c3c; }
        .badge-progress { background-color: #f39c12; }
        .badge-completed { background-color: #27ae60; }
    </style>

    @stack('css')
</head>
<body>
<div class="wrapper">

    {{-- Top Header --}}
    <div class="main-header">
        <div class="logo-header text-center" data-background-color="blue">
            <a href="#" class="logo text-center">
                <img src="{{ asset('admin/assets/img/no-image.png') }}" alt="Logo" class="navbar-brand" style="filter:brightness(0) invert(1);">
            </a>
            <button class="navbar-toggler sidenav-toggler ml-auto" type="button">
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

                    {{-- Portal user nav --}}
                    @auth('portal')
                    <li class="nav-item dropdown hidden-caret">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                           data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <div class="avatar-sm float-left mr-2">
                                <img src="{{ asset('admin/assets/img/no-image.png') }}" alt="profile" class="avatar-img rounded-circle">
                            </div>
                            <span class="d-none d-sm-block text-white">
                                {{ Auth::guard('portal')->user()->full_name }}
                                <span class="badge badge-light ml-1" style="font-size:9px;">
                                    {{ ucfirst(Auth::guard('portal')->user()->getRoleNames()->first()) }}
                                </span>
                            </span>
                        </a>
                        <ul class="dropdown-menu dropdown-user animated fadeIn" aria-labelledby="userDropdown">
                            <div class="dropdown-user-scroll scrollbar-outer">
                                <li>
                                    <div class="user-box">
                                        <div class="u-text">
                                            <h4>{{ Auth::guard('portal')->user()->full_name }}</h4>
                                            <p class="text-muted">{{ Auth::guard('portal')->user()->email }}</p>
                                        </div>
                                    </div>
                                </li>
                                <li>
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

                    {{-- SSO user nav --}}
                    @if(session()->has('auth_user'))
                    <li class="nav-item dropdown hidden-caret">
                        <a class="nav-link dropdown-toggle" href="#" id="ssoDropdown" role="button"
                           data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="text-white">{{ session('auth_user.name') }}</span>
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
    {{-- End Top Header --}}

    {{-- Sidebar --}}
    <div class="sidebar sidebar-style-2">
        <div class="sidebar-wrapper scrollbar scrollbar-inner">
            <div class="sidebar-content">
                <div class="user-box">
                    <div class="u-text">
                        <h4>{{ config('app.name') }}</h4>
                        <p class="text-muted">Customization Portal</p>
                    </div>
                </div>
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

                {{-- Flash Messages --}}
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
                    {{ date('Y') }}, {{ config('app.name') }}
                </div>
            </div>
        </footer>
    </div>
    {{-- End Main Panel --}}

</div>

{{-- Core JS --}}
<script src="{{ asset('common/vendor/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('common/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('admin/assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js') }}"></script>
<script src="{{ asset('admin/assets/js/atlantis.min.js') }}"></script>
<script src="{{ asset('common/vendor/datatable/datatables.min.js') }}"></script>
<script src="{{ asset('common/vendor/select2/js/select2.full.min.js') }}"></script>
<script src="{{ asset('common/vendor/daterangepicker/moment.min.js') }}"></script>
<script src="{{ asset('common/vendor/daterangepicker/daterangepicker.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/ekko-lightbox/5.3.0/ekko-lightbox.min.js"></script>

<script>
    // CSRF setup for all AJAX
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

    // Date range pickers
    $('.date-range').daterangepicker({ autoUpdateInput: false, locale: { cancelLabel: 'Clear' } });
    $('.date-range').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('MM-DD-YYYY') + ' - ' + picker.endDate.format('MM-DD-YYYY'));
    });
    $('.date-range').on('cancel.daterangepicker', function() { $(this).val(''); });

    // Select2
    $('.select2').select2();
</script>

@stack('js')
</body>
</html>
