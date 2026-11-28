<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Paciente;
use App\Models\ProfesionalSalud;
use App\Models\Sede;
use App\Models\Servicio;
use App\Models\Cita;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        abort_unless(
            $user?->rol === 'administrador' &&
            $user?->estado === 'activo' &&
            $user?->email_verified_at !== null,
            403
        );

        // Usuarios del sistema.

        $usuariosActivos = User::query()
            ->where('estado', 'activo')
            ->count();

        $pacientesRegistrados = Paciente::query()
            ->count();

        $profesionalesActivos = ProfesionalSalud::query()
            ->where('estadoprofesionalsalud', 'Activo')
            ->whereHas('user', function ($query) {
                $query->where('estado', 'activo')
                    ->where('rol', 'profesional');
            })
            ->count();

        $auxiliaresActivos = User::query()
            ->where('rol', 'auxiliar')
            ->where('estado', 'activo')
            ->count();

        // Infraestructura y servicios.

        $sedesActivas = Sede::query()
            ->where('estadosede', 'Activo')
            ->count();

        $serviciosRegistrados = Servicio::query()
            ->count();

        // Actividad mensual de citas.

        $estadosMes = Cita::query()
            ->where(
                'fechacita',
                '>=',
                DB::raw("TRUNC(SYSDATE, 'MM')")
            )
            ->where(
                'fechacita',
                '<',
                DB::raw("ADD_MONTHS(TRUNC(SYSDATE, 'MM'), 1)")
            )
            ->select('estadocita')
            ->selectRaw('COUNT(*) AS total')
            ->groupBy('estadocita')
            ->pluck('total', 'estadocita');

        $citasMes = (int) $estadosMes->sum();

        return view('admin.dashboard-admin', compact(
            'usuariosActivos',
            'pacientesRegistrados',
            'profesionalesActivos',
            'auxiliaresActivos',
            'sedesActivas',
            'serviciosRegistrados',
            'estadosMes',
            'citasMes'
        ));
    }
}
