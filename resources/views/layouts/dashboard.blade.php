<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>

    @include('partials.head')

    <link rel="stylesheet" href="{{ asset('css/footer.css') }}">

    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">

    <link rel="stylesheet" href="{{ asset('css/profile.css') }}">

    {{-- FullCalendar --}}
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>

    @stack('styles')

    @livewireStyles

</head>

<body class="bg-light">

    <header class="bg-white shadow-sm">
        @include('partials.headerPaciente')
    </header>

    <main>
        @yield('contenido')
    </main>



    @include('partials.scripts')

    @livewireScripts

    @stack('scripts')

</body>

</html>
