<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>

    @include('partials.head')

    <link rel="stylesheet" href="{{ asset('css/footer.css') }}">

    @stack('styles')

    @livewireStyles

</head>

<body class="font-sans antialiased">

    @include('partials.menu')

    @yield('contenido')

    @include('partials.footer')

    @include('partials.scripts')

    @livewireScripts

    @stack('scripts')

</body>

</html>
