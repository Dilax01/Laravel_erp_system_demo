<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Favicons -->
    <link rel="apple-touch-icon" href="{{ asset('assets/img/favicon/apple-touch-icon.png') }}" sizes="180x180">
    <link rel="icon" href="{{ asset('assets/img/favicon/favicon-32x32.png') }}" sizes="32x32" type="image/png">
    <link rel="icon" href="{{ asset('assets/img/favicon/favicon-16x16.png') }}" sizes="16x16" type="image/png">
    <link rel="mask-icon" href="{{ asset('assets/img/favicon/safari-pinned-tab.svg') }}" color="#563d7c">
    <link rel="icon" href="{{ asset('assets/img/favicon/favicon.ico') }}">
    <meta name="msapplication-config" content="{{ asset('assets/img/favicons/browserconfig.xml') }}">
    <meta name="theme-color" content="#563d7c">
    <meta name="viewport" content="width=device-width,initial-scale=1,shrink-to-fit=no">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Fontawesome -->
    <link href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet">

    <!-- Sweet Alert -->
    <link href="{{ asset('vendor/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet">

    <!-- Volt CSS -->
    <link href="{{ asset('css/volt.css') }}" rel="stylesheet">

    <!-- Theme CSS -->
    <link href="{{ asset('css/theme.css') }}" rel="stylesheet">

    @livewireStyles

    <title>{{ config('app.name') }} | @yield("title")</title>
</head>

<body>
    @php
        $route = request()->route()->getName();
        $simpleRoutes = ['login', 'register', 'forgot-password', 'reset-password'];
    @endphp

    @if (in_array($route, $simpleRoutes))
        {{-- No layout needed --}}
        @yield("content")
        @include('layouts.footer2')
    @else
        {{-- Full layout --}}
        @include('layouts.nav')
        @include('layouts.sidenav')

        <main class="content">
            <livewire:top-bar />
            @yield("content")
        </main>
    @endif

    @livewireScripts

    <!-- JS Scripts -->
    <script src="{{ asset('js/theme.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.min.js"></script>
    <script src="{{ asset('assets/js/on-screen.umd.min.js') }}"></script>
    <script src="{{ asset('assets/js/smooth-scroll.polyfills.min.js') }}"></script>
    <script src="{{ asset('assets/js/volt.js') }}"></script>
    <script src="{{ asset('js/sweetalert2.min.js') }}"></script>

    @stack('scripts')

    {{-- SweetAlert2 Notification --}}
    @if (Session::has('message'))
        <script>
            Swal.fire({
                timer: 2500,
                icon: "{{ Session::get('icon') }}",
                title: "{{ Session::get('title') }}",
                text: "{{ Session::get('message') }}",
            });
        </script>
    @endif
</body>
</html>
