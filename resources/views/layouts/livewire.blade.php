<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>

    @include('partials.head')

    <link rel="stylesheet" href="{{ asset('css/footer.css') }}">

    <link rel="stylesheet" href="{{ asset('css/auth-layout.css') }}">

    <link rel="stylesheet" href="{{ asset('css/menu-compacto.css') }}">

    @stack('styles')

    @livewireStyles

</head>

<body class="font-sans antialiased">

    @include('partials.menu')

    {{ $slot }}

    @if (request()->routeIs('login', 'register'))
        @include('partials.auth-footer')
    @else
        @include('partials.footer')
    @endif

    @include('partials.scripts')

    @livewireScripts

    @stack('scripts')

</body>

</html>
