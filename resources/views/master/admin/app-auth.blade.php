<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="description" content="Ward Info BIMC KUTA">
    <meta name="author" content="Ward">
    <meta name="keywords" content="ward, bimc kuta">

    <title>HCDOC APP</title>

    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/admin/favicons/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/admin/favicons/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/admin/favicons/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('assets/admin/favicons/site.webmanifest') }}">
    <link rel="mask-icon" color="#5bbad5" href="{{ asset('assets/admin/favicons/safari-pinned-tab.svg') }}">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="theme-color" content="#ffffff">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet">
    <!-- End fonts -->

    <!-- CSRF Token -->
    <meta name="_token" content="{{ csrf_token() }}">

    <link rel="shortcut icon" href="{{ asset('assets/admin/favicons/favicon.ico') }}">

    <!-- plugin css -->
    <link href="{{ asset('assets/admin/fonts/feather-font/css/iconfont.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/admin/plugins/perfect-scrollbar/perfect-scrollbar.css') }}" rel="stylesheet" />
    <!-- end plugin css -->

    @stack('plugin-styles')

    <!-- common css -->
    <link href="{{ asset('css/admin/app.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/admin/custom.css') }}" rel="stylesheet" />
    <!-- end common css -->

    @stack('style')
</head>

<body data-base-url="{{url('/')}}">

    <script src="{{ asset('assets/admin/js/spinner.js') }}"></script>

    <div class="main-wrapper" id="app">
        <div class="page-wrapper full-page">
            @yield('content')
        </div>
    </div>

    <!-- base js -->
    <script src="{{ asset('js/admin/app.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/feather-icons/feather.min.js') }}"></script>
    <!-- end base js -->

    <!-- plugin js -->
    @stack('plugin-scripts')
    <!-- end plugin js -->

    <!-- common js -->
    <script src="{{ asset('assets/admin/js/template.js') }}"></script>
    <!-- end common js -->

    @stack('custom-scripts')
</body>

</html>
