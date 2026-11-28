<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<meta name="csrf-token" content="{{ csrf_token() }}">

<title>{{ config('app.name', 'SIGAC') }}</title>

<link rel="icon" href="{{ asset('favicon.ico') }}">

{{-- Bootstrap --}}
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

{{-- Bootstrap Icons --}}
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

{{-- Tipografías institucionales --}}
<link
    href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800&family=Open+Sans:wght@400;500;600&display=swap"
    rel="stylesheet">

{{-- Estilos globales --}}
<link rel="stylesheet" href="{{ asset('css/principal.css') }}">

{{-- Sistema de diseño SIGAC --}}
<link rel="stylesheet" href="{{ asset('css/componentes.css') }}">

{{-- Formularios --}}
<link rel="stylesheet" href="{{ asset('css/formularios.css') }}">

{{-- Tablas y filtros --}}
<link rel="stylesheet" href="{{ asset('css/tablas.css') }}">

{{-- Protección del historial y restauración de páginas --}}
<script src="{{ asset('js/session-security.js') }}"></script>
