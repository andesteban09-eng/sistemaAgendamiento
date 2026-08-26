<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\PacienteController;
use App\Http\Controllers\Admin\ProfesionalController;
use App\Http\Controllers\Admin\AuxiliarController;
use App\Http\Controllers\Auxiliar\AgendaController;
use App\Http\Controllers\Admin\TipoServicioController;
use App\Http\Controllers\Admin\ServicioController;
use App\Livewire\Actions\Logout;
use Illuminate\Http\Request;

Route::get('/', function () {
    return view('inicio');
})->name('inicio');
Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');
    Route::get('admin/pacientes', [PacienteController::class, 'index'])
        ->name('admin.pacientes.index');

    Route::get('admin/pacientes/create', [PacienteController::class, 'create'])
        ->name('admin.pacientes.create');

    Route::post('admin/pacientes', [PacienteController::class, 'store'])
        ->name('admin.pacientes.store');
    Route::get('admin/pacientes/{paciente}/edit', [PacienteController::class, 'edit'])
        ->name('admin.pacientes.edit');

    Route::put('admin/pacientes/{paciente}', [PacienteController::class, 'update'])
        ->name('admin.pacientes.update');

    Route::delete('admin/pacientes/{paciente}', [PacienteController::class, 'destroy'])
        ->name('admin.pacientes.destroy');
    Route::patch('admin/pacientes/{paciente}/estado', [PacienteController::class, 'toggleEstado'])
        ->name('admin.pacientes.toggleEstado');

    Route::get('admin/pacientes/{paciente}', [PacienteController::class, 'show'])
        ->name('admin.pacientes.show');

        


    Route::get('admin/profesionales', [ProfesionalController::class, 'index'])
        ->name('admin.profesionales.index');

    Route::get('admin/profesionales/create', [ProfesionalController::class, 'create'])
        ->name('admin.profesionales.create');

    Route::post('admin/profesionales', [ProfesionalController::class, 'store'])
        ->name('admin.profesionales.store');

    Route::get(
        'admin/profesionales/{profesional}/edit',
        [ProfesionalController::class, 'edit']
    )->name('admin.profesionales.edit');

    Route::put('admin/profesionales/{profesional}', [ProfesionalController::class, 'update'])
        ->name('admin.profesionales.update');

    Route::delete(
        'admin/profesionales/{profesional}',
        [ProfesionalController::class, 'destroy']
    )->name('admin.profesionales.destroy');

    Route::patch(
        'admin/profesionales/{profesional}/estado',
        [ProfesionalController::class, 'toggleEstado']
    )->name('admin.profesionales.toggleEstado');

    Route::get('admin/profesionales/{profesional}', [ProfesionalController::class, 'show'])
        ->name('admin.profesionales.show');




    Route::get('admin/auxiliares', [AuxiliarController::class, 'index'])
        ->name('admin.auxiliares.index');

    Route::get('admin/auxiliares/create', [AuxiliarController::class, 'create'])
        ->name('admin.auxiliares.create');

    Route::post('admin/auxiliares', [AuxiliarController::class, 'store'])
        ->name('admin.auxiliares.store');

    Route::get('admin/auxiliares/{auxiliar}', [AuxiliarController::class, 'show'])
        ->name('admin.auxiliares.show');

    Route::get('admin/auxiliares/{auxiliar}/edit', [AuxiliarController::class, 'edit'])
        ->name('admin.auxiliares.edit');

    Route::put('admin/auxiliares/{auxiliar}', [AuxiliarController::class, 'update'])
        ->name('admin.auxiliares.update');

    Route::delete('admin/auxiliares/{auxiliar}', [AuxiliarController::class, 'destroy'])
        ->name('admin.auxiliares.destroy');

    Route::patch('admin/auxiliares/{auxiliar}/estado', [AuxiliarController::class, 'toggleEstado'])
        ->name('admin.auxiliares.toggleEstado');





    Route::get('/auxiliar/agenda', [AgendaController::class, 'index'])
        ->name('auxiliar.agenda.index');

    Route::get('/auxiliar/agenda/create', [AgendaController::class, 'create'])
        ->name('auxiliar.agenda.create');

    Route::post('/auxiliar/agenda', [AgendaController::class, 'store'])
        ->name('auxiliar.agenda.store');

    Route::get('/auxiliar/agenda/{agenda}/edit', [AgendaController::class, 'edit'])
        ->name('auxiliar.agenda.edit');

    Route::put('/auxiliar/agenda/{agenda}', [AgendaController::class, 'update'])
        ->name('auxiliar.agenda.update');

    Route::delete('/auxiliar/agenda/{agenda}', [AgendaController::class, 'destroy'])
        ->name('auxiliar.agenda.destroy');


   Route::get('admin/tiposervicios', [TipoServicioController::class, 'index'])
    ->name('admin.tiposervicios.index');

Route::get('admin/tiposervicios/create', [TipoServicioController::class, 'create'])
    ->name('admin.tiposervicios.create');

Route::post('admin/tiposervicios', [TipoServicioController::class, 'store'])
    ->name('admin.tiposervicios.store');

Route::get('admin/tiposervicios/{tipoServicio}/edit', [TipoServicioController::class, 'edit'])
    ->name('admin.tiposervicios.edit');

Route::put('admin/tiposervicios/{tipoServicio}', [TipoServicioController::class, 'update'])
    ->name('admin.tiposervicios.update');

Route::patch('admin/tiposervicios/{tipoServicio}/estado', [TipoServicioController::class, 'toggleEstado'])
    ->name('admin.tiposervicios.toggleEstado');




Route::get('admin/servicios', [ServicioController::class, 'index'])
    ->name('admin.servicios.index');

Route::get('admin/servicios/create', [ServicioController::class, 'create'])
    ->name('admin.servicios.create');

Route::post('admin/servicios', [ServicioController::class, 'store'])
    ->name('admin.servicios.store');

Route::get('admin/servicios/{servicio}/edit', [ServicioController::class, 'edit'])
    ->name('admin.servicios.edit');

Route::put('admin/servicios/{servicio}', [ServicioController::class, 'update'])
    ->name('admin.servicios.update');

Route::patch('admin/servicios/{servicio}/estado', [ServicioController::class, 'toggleEstado'])
    ->name('admin.servicios.toggleEstado');

    Route::post('logout', function (Request $request, Logout $logout) {
        $logout();

        return redirect('/');
    })->name('logout');

    Route::view('profile', 'profile')
        ->name('profile');
});

require __DIR__ . '/auth.php';
