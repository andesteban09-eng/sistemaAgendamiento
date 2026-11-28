<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>

    @include('partials.head')

    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">

    @stack('styles')

    @livewireStyles

</head>

<body class="bg-light">

    @include('partials.admin-header')

    <main>
        @yield('contenido')
    </main>

    @include('partials.scripts')


    @livewireScripts

    @stack('scripts')



</body>

</html>
