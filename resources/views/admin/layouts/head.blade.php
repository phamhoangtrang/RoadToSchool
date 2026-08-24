<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="shortcut icon" href="{{ asset('assets/img/favicon.png') }}">

    <title>@yield('title')</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/sass/admin.scss', 'resources/js/app.js'])
    <script src="{{ asset('build/admin.js') }}"></script>

    {{-- Page-specific styles --}}
    @yield('inline_styles')

    <link href="{{ asset('assets/admin/css/font-awesome.min.css') }}" rel="stylesheet"/>
    <link href="{{ asset('assets/admin/css/materialdesignicons.min.css') }}" rel="stylesheet"/>
    <link href="{{ asset('assets/admin/css/themify-icons.css') }}" rel="stylesheet"/>
    <link href="{{ asset('assets/admin/css/animate.min.css') }}" rel="stylesheet"/>
    <link href="{{ asset('assets/admin/css/app.css') }}" rel="stylesheet"/>
</head>
