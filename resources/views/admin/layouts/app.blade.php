<!DOCTYPE html>
<html lang="en">
<head>
    <?php
    if (!isset($title)) {
        $title = '';
    }
    ?>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ ($title ? $title.' | ' : '').config('app.name') }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <meta name="base-url" content="{{ rtrim(url('/'),'/') }}">
    <meta name="admin-url" content="{{ rtrim(url('/admin'),'/') }}">
    <meta name="rfm-key" content="@filemanager_get_key()">

    <link rel="shortcut icon" href="{{ asset('images/favicon.png') }}" type='image/x-icon'>
    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="{{ asset('assets/plugins/fontawesome/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/toastr/toastr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/jquery-confirm/jquery-confirm.min.css') }}">

    <link rel="stylesheet" href="{{ asset('assets/plugins/datatables/datatables.min.css') }}">

    <link rel="stylesheet" href="{{ asset('assets/css/adminlte.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    @yield('style')
    @stack('style')
</head>
<body class="sidebar-mini layout-fixed control-sidebar-slide-open layout-navbar-fixed">

<div class="wrapper">
    <div class="loading-spinner">
        <div class="spinner-border" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </div>
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
                <a href="{{ url('/') }}" class="nav-link">Home</a>
            </li>
        </ul>

        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                    <i class="fas fa-expand-arrows-alt"></i>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ url('logout') }}" role="button">
                    <i class="fas fa-power-off"></i>
                </a>
            </li>
        </ul>
    </nav>

    @includeIf('admin.layouts.inc.sidebar')

    <div class="content-wrapper">
        @if($title)
            <section class="content-header">
                <div class="container-fluid">
                    @hasSection('breadcrumb')
                        @yield('breadcrumb')
                    @else
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <h1>{{ $title }}</h1>
                            </div>
                            <div class="col-sm-6">
                                @if(!in_array($title,['Dashboard','Home']))
                                    <ol class="breadcrumb float-sm-right">
                                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                                        <li class="breadcrumb-item active">{{ $title }}</li>
                                    </ol>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </section>
        @endif

        <section class="content">
            @yield('content')
        </section>
    </div>

    {{--<footer class="main-footer text-sm">
        <div class="float-right d-none d-sm-block">

        </div>
        <strong>Copyright &copy; {{ date('Y') }} <a href="{{ url('/') }}">AdminLTE.io</a>.</strong> All rights reserved.
    </footer>--}}
</div>

<script src="{{ asset('assets/plugins/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('assets/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/plugins/toastr/toastr.min.js') }}"></script>
<script src="{{ asset('assets/plugins/jquery-confirm/jquery-confirm.min.js') }}"></script>
<script src="{{ asset('assets/plugins/datatables/datatables.min.js') }}"></script>

<script src="{{ asset('assets/plugins/jquery-validation/jquery.validate.min.js') }}"></script>
<script src="{{ asset('assets/plugins/jquery-validation/additional-methods.min.js') }}"></script>
<script src="{{ asset('assets/js/adminlte.min.js') }}"></script>
<script src="{{ asset('assets/js/filemanager.js?v='.time()) }}"></script>
<script src="{{ asset('assets/js/script.js?v='.time()) }}"></script>
@yield('script')
@stack('script')
@includeIf('admin.layouts.inc.alert')

</body>
</html>
