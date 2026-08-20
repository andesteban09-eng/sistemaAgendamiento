<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>
        {{ config('app.name', 'Laboratorios Carvajal') }}
    </title>

    <!-- Bootstrap -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <!-- Bootstrap Icons -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
        rel="stylesheet"
    >

    <!-- Google Fonts -->
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800&family=Open+Sans:wght@400;500;600&display=swap"
        rel="stylesheet"
    >

    <!-- CSS propio -->
    <link
        rel="stylesheet"
        href="{{ asset('css/principal.css') }}"
    >

    <link
        rel="stylesheet"
        href="{{ asset('css/footer.css') }}"
    >

    @livewireStyles

</head>

<body class="font-sans antialiased">

    @include('partials.menu')

    {{ $slot }}

    @include('partials.footer')


    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
    </script>

    @livewireScripts

</body>

</html>
