<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $rol = strtolower(Auth::user()->rol);

        return match ($rol) {
            'paciente' => view('Paciente.dashboardPaciente'),
            'profesional' => view('profesionales.dashboard'),
            'administrador' => view('admin.dashboard'),
            default => view('dashboard'),
        };
    }
}