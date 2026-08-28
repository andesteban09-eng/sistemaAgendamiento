<?php

namespace App\Http\Controllers\Profesional;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ProfesionalDashboardController extends Controller
{
    public function index(): View
    {
        $userId = Auth::id();

        $citasRaw = DB::table('cita')
            ->join(
                'agenda',
                'cita.idhorariodispo',
                '=',
                'agenda.idhorariodispo'
            )
            ->select([
                'cita.idcita as idcita',
                'cita.detalle as title',
                'cita.fechacita as start',
                'cita.estadocita as estadocita',
            ])
            ->where('agenda.idprofesionalsalud', $userId)
            ->get();

        $citas = $citasRaw->map(function ($cita) {
            $color = match ($cita->estadocita) {
                'Pendiente' => '#ffc107',
                'Realizada', 'Atendida' => '#198754',
                'Cancelada' => '#dc3545',
                default => '#0d6efd',
            };

            return [
                'idcita' => $cita->idcita,
                'title' => $cita->title,
                'start' => $cita->start,
                'estadocita' => $cita->estadocita,
                'color' => $color,
            ];
        });

        return view(
            'profesional.dashboardProfesional',
            compact('citas')
        );
    }
}
