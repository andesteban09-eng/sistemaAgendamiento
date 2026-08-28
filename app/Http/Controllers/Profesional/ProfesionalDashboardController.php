<?php

namespace App\Http\Controllers\Profesional;

use App\Http\Controllers\Controller;
use App\Models\Cita;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProfesionalDashboardController extends Controller
{
    public function index(): View
    {
        $profesional = Auth::user()->profesional;

        if (!$profesional) {
            return view('profesional.dashboardProfesional', ['citas' => collect()]);
        }

        // Consultar citas con relaciones
        $citasRaw = Cita::whereHas('agenda', function ($query) use ($profesional) {
                $query->where('idprofesionalsalud', $profesional->idprofesionalsalud);
            })
            ->with([
                'servicio',
                'paciente.user',
                'agenda.sede',
            ])
            ->orderBy('fechacita', 'asc')
            ->get();

        $citas = $citasRaw->map(function ($cita) {
            $color = match ($cita->estadocita) {
                'Pendiente'            => '#ffc107',
                'Realizada', 'Atendida' => '#198754',
                'Cancelada'            => '#dc3545',
                default                => '#0d6efd',
            };

            $pacienteNombre = $cita->paciente->user->name ?? 'Paciente no asignado';
            $servicioNombre = $cita->servicio->nombre ?? 'Consulta General';

            return [
                'id'         => $cita->idcita,
                'title'      => "{$pacienteNombre} - {$servicioNombre}",
                'start'      => $cita->fechacita ? $cita->fechacita->format('Y-m-d H:i:s') : null,
                'color'      => $color,
                'paciente'   => $pacienteNombre,
                'servicio'   => $servicioNombre,
                'estadoCita' => $cita->estadocita,
                'observacion'=> $cita->detalle,
                'hora'       => $cita->fechacita ? $cita->fechacita->format('g:i A') : 'Sin hora',
            ];
        });

        return view('profesional.dashboardProfesional', compact('citas'));
    }
}