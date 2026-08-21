<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Livewire\Actions\Logout;

Route::get('/', function () {
    return view('inicio');
})->name('inicio');


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

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';
