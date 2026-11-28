<?php

namespace App\Http\Controllers\Auxiliar;

use App\Http\Controllers\Controller;
use App\Models\Agenda;
use App\Models\Cita;
use App\Models\ProfesionalSalud;
use App\Models\Sede;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AuxiliarDashboardController extends Controller
{
    public function index(): View
    {
        abort_unless(
            Auth::user()?->rol === 'auxiliar' &&
            Auth::user()?->estado === 'activo' &&
            Auth::user()?->email_verified_at !== null,
            403
        );

        // Capacidad operativa de la IPS.

        $profesionalesActivos = ProfesionalSalud::query()
            ->where('estadoprofesionalsalud', 'Activo')
            ->whereHas('user', function ($query) {
                $query->where('estado', 'activo')
                    ->where('rol', 'profesional');
            })
            ->count();

        $sedesActivas = Sede::query()
            ->where('estadosede', 'Activo')
            ->count();

        $horariosHoy = Agenda::query()
            ->where('fecha', '>=', DB::raw('TRUNC(SYSDATE)'))
            ->where('fecha', '<', DB::raw('TRUNC(SYSDATE) + 1'))
            ->count();

        // Actividad de la jornada actual.

        $estadosHoy = Cita::query()
            ->where('fechacita', '>=', DB::raw('TRUNC(SYSDATE)'))
            ->where('fechacita', '<', DB::raw('TRUNC(SYSDATE) + 1'))
            ->select('estadocita')
            ->selectRaw('COUNT(*) AS total')
            ->groupBy('estadocita')
            ->pluck('total', 'estadocita');

        $citasHoy = (int) $estadosHoy->sum();

        $citasPendientes = (int) $estadosHoy->get(
            'Pendiente', 0
        );

        $citasConfirmadas = (int) $estadosHoy->get(
            'Confirmada', 0
        );

        $citasRealizadas = (int) $estadosHoy->get(
            'Realizada', 0
        );

        $citasCanceladas = (int) $estadosHoy->get(
            'Cancelada', 0
        );

        $porcentajeRealizadas = $citasHoy > 0
            ? round(($citasRealizadas / $citasHoy) * 100, 1)
            : 0;

        return view('auxiliar.dashboard-auxiliar', compact(
            'profesionalesActivos',
            'sedesActivas',
            'horariosHoy',
            'citasHoy',
            'citasPendientes',
            'citasConfirmadas',
            'citasRealizadas',
            'citasCanceladas',
            'porcentajeRealizadas'
        ));
    }
}
