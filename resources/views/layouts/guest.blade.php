<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>

    @include('partials.head')

    <link rel="stylesheet" href="{{ asset('css/footer.css') }}">

    <link rel="stylesheet" href="{{ asset('css/auth-layout.css') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')

    @livewireStyles

</head>

<body class="font-sans antialiased">

    {{ $slot }}

    @livewireScripts

    @stack('scripts')

</body>

</html>
