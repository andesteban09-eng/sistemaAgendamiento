<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Http\Controllers\Profesional\ProfesionalDashboardController;

class DashboardController extends Controller
{
    public function index(): View
    {
        $rol = strtolower(Auth::user()->rol);

        return match ($rol) {
            'paciente' => view('Paciente.dashboardPaciente'),
            'profesional'   => app(ProfesionalDashboardController::class)->index(),
            'administrador' => view('admin.dashboard-admin'),
            'auxiliar' => view('auxiliar.dashboard-auxiliar'),
            default => view('dashboard'),
        };
    }
}
