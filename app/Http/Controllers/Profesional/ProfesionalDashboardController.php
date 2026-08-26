<?php

namespace App\Http\Controllers\Profesional;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ProfesionalDashboardController extends Controller
{
public function index(): View
    {
        $userId = Auth::id();

        // Consulta de citas asignadas al profesional en Oracle
        $citasRaw = DB::table('CITA')
            ->select([
                'ID_CITA as idCita',
                'DETALLE as title',
                'FECHA_CITA as start',
                'ESTADO_CITA as estadoCita'
            ])
            ->where('ID_PROFESIONAL', $userId)
            ->get();

        // Mapeo de colores según el estado
        $citas = $citasRaw->map(function ($cita) {
            $color = match ($cita->estadoCita) {
                'Pendiente'             => '#ffc107',
                'Realizada', 'Atendida' => '#198754',
                'Cancelada'             => '#dc3545',
                default                 => '#0d6efd',
            };

            return [
                'idCita'     => $cita->idCita,
                'title'      => $cita->title,
                'start'      => $cita->start,
                'estadoCita' => $cita->estadoCita,
                'color'      => $color,
            ];
        });

        // Apunta a resources/views/profesional/dashboardProfesional.blade.php
        return view('profesional.dashboardProfesional', compact('citas'));
    }
}
