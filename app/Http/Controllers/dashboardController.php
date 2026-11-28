<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Profesional\ProfesionalDashboardController;
use App\Http\Controllers\Auxiliar\AuxiliarDashboardController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Models\Cita;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;





class DashboardController extends Controller
{


    public function index(): View
    {

        $usuario = Auth::user();

        $rol = strtolower($usuario->rol);

        if ($rol === 'paciente') {

            $paciente = $usuario->paciente;

            $totalPendientes = 0;
            $proximaCita = null;

            if ($paciente) {

                $totalPendientes = Cita::where(
                    'idpaciente',
                    $paciente->idpaciente
                )
                    ->where(
                        'estadocita',
                        'Pendiente'
                    )
                    ->where(
                        'fechacita',
                        '>=',
                        DB::raw('SYSDATE')
                    )
                    ->count();

                $proximaCita = Cita::with([
                    'servicio',
                    'tipoServicio',
                    'agenda.profesional.user',
                    'agenda.sede',
                ])
                    ->where(
                        'idpaciente',
                        $paciente->idpaciente
                    )
                    ->where(
                        'estadocita',
                        'Pendiente'
                    )
                    ->where(
                        'fechacita',
                        '>=',
                        DB::raw('SYSDATE')
                    )
                    ->orderBy('fechacita', 'asc')
                    ->first();
            }

            return view(
                'Paciente.dashboardPaciente',
                compact(
                    'paciente',
                    'totalPendientes',
                    'proximaCita'
                )
            );
        }

        return match ($rol) {

            'profesional' =>
            app(
                ProfesionalDashboardController::class
            )->index(),

            'administrador' =>
            app(
                AdminDashboardController::class
            )->index(),

            'auxiliar' =>
            app(
                AuxiliarDashboardController::class
            )->index(),

            default => abort(403, 'Rol no autorizado.'),
        };
    }
}
