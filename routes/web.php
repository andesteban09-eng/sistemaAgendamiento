<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\dashboardController;
use App\Livewire\Actions\Logout;
use Illuminate\Http\Request;
use App\Livewire\Actions\Logout;

Route::get('/', function () {
    return view('inicio');
})->name('inicio');

Route::get('dashboard', [dashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth', 'role:Administrador'])->group(function () {

Route::post('logout', Logout::class)
    ->middleware('auth')
    ->name('logout');
Route::get('dashboard', function () {

    $user = Auth::user();

    return match ($user->rol) {
        'administrador' => view('dashboards.administrador'),
        'profesional' => view('dashboards.profesional'),
        'paciente' => view('dashboards.paciente'),
        default => abort(403),
    };



})->middleware(['auth', 'verified'])->name('dashboard');

Route::post('logout', function (Request $request, Logout $logout) {
    $logout();

    return redirect('/');
})->middleware('auth')->name('logout');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';




