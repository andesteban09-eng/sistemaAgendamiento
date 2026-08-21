@extends('layouts.dashboard')

@section('contenido')

@php
    use App\Models\Cita;
    use Illuminate\Support\Facades\DB;

    $paciente = auth()->user()->paciente; // relación User -> Paciente

    $totalPendientes = Cita::where('IDPACIENTE', $paciente?->IDPACIENTE)
        ->where('ESTADOCITA', 'Pendiente')
        ->where('FECHACITA', '>=', DB::raw('SYSDATE'))
        ->count();

    $proximaCita = Cita::with(['profesional'])
        ->where('IDPACIENTE', $paciente?->IDPACIENTE)
        ->where('FECHACITA', '>=', DB::raw('SYSDATE'))
        ->orderBy('FECHACITA')
        ->first();
@endphp

<div class="container mt-4 mb-4">
    <section class="hero shadow justify-content-around border-0 rounded-4 mb-2 p-4 p-lg-5 shadow-sm">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="fw-bold">Hola, {{ auth()->user()->name }}</h1>
                <p class="lead">Bienvenido al Portal de Pacientes de Carvajal Laboratorios IPS</p>
                <p>Desde aquí podrás gestionar tus citas, consultar resultados y realizar seguimiento a tus procedimientos.</p>
            </div>
            <div class="col-md-4 text-center">
                <i class="bi bi-person-heart display-1"></i>
            </div>
        </div>
    </section>
</div>

<div class="row d-flex gap-4 m-4 justify-content-center flex-lg-row flex-column">

    <div class="card border-0 shadow-sm m-3 col-lg-3">
        <div class="card-body text-center">
            @if($totalPendientes > 0)
                <div class="alert text-center">
                    <i class="bi bi-exclamation-triangle"></i>
                    Tienes {{ $totalPendientes }} citas pendientes.
                </div>
            @else
                <div class="alert">
                    <i class="bi bi-check-circle-fill text-success"></i>
                    <h2>{{ $totalPendientes }}</h2>
                    <small>Solicitudes nuevas</small>
                </div>
            @endif
        </div>
    </div>

    <div class="card border-0 shadow-sm m-3 col-lg-3">
        <div class="card-body text-center">
            <i class="bi bi-file-earmark-medical text-primary"></i>
            <h2>{{ $totalPendientes }}</h2>
            <small>Resultados pendientes</small>
        </div>
    </div>

    <div class="card border-0 shadow-sm m-3 col-lg-3">
        <div class="card-body text-center">
            <i class="bi bi-journal-medical text-primary"></i>
            <h2>{{ $totalPendientes }}</h2>
            <small>Solicitudes nuevas</small>
        </div>
    </div>
</div>

<div class="row g-4 mx-4">

    <div class="col-md-4">
        <a href="{{ url('gestionarcitas') }}" class="text-decoration-none">
            <div class="card shadow-sm border-0 h-100 text-center">
                <div class="card-body p-4">
                    <i class="bi bi-calendar-check display-2 text-primary"></i>
                    <h4 class="mt-3">Gestionar Citas</h4>
                    <p class="text-muted">Agenda, consulta o cancela tus citas médicas.</p>
                </div>
            </div>
        </a>
    </div>

    <div class="col-md-4">
        <a href="{{ url('examenes') }}" class="text-decoration-none">
            <div class="card shadow-sm border-0 h-100 text-center">
                <div class="card-body p-4">
                    <i class="bi bi-clipboard-data display-2 text-success"></i>
                    <h4 class="mt-3">Solicitar Exámenes</h4>
                    <p class="text-muted">Consulta y solicita procedimientos de laboratorio.</p>
                </div>
            </div>
        </a>
    </div>

    <div class="col-md-4">
        <a href="{{ url('reportecitas') }}" target="_blank" class="text-decoration-none">
            <div class="card shadow-sm border-0 h-100 text-center">
                <div class="card-body p-4">
                    <i class="bi bi-file-earmark-medical display-2 text-danger"></i>
                    <h4 class="mt-3">Ver Resultados</h4>
                    <p class="text-muted">Consulta los resultados de tus exámenes.</p>
                </div>
            </div>
        </a>
    </div>

    <div class="card border-0 shadow-sm mx-auto col-lg-11">
        <div class="card-body">
            <h5><i class="bi bi-calendar-event"></i> Próxima cita</h5>

            @if($proximaCita)
                <div class="alert alert-info">
                    <p><strong>Fecha:</strong> {{ \Carbon\Carbon::parse($proximaCita->FECHACITA)->format('d/m/Y h:i A') }}</p>
                    <p><strong>Profesional:</strong> {{ $proximaCita->profesional?->NOMBRE }} {{ $proximaCita->profesional?->APELLIDO }}</p>
                </div>
            @else
                <p class="mb-0">No tienes citas programadas.</p>
            @endif
        </div>
    </div>
</div>

@endsection