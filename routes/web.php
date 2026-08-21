<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\dashboardController;
use App\Livewire\Actions\Logout;
use Illuminate\Http\Request;

Route::get('/', function () {
    return view('inicio');  
})->name('inicio');

Route::get('dashboard', [dashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth', 'role:Administrador'])->group(function () {

    Route::get('/admin', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    Route::get('/admin/usuarios', function () {
        return view('admin.usuarios');
    })->name('admin.usuarios');

});

Route::post('logout', function (Request $request, Logout $logout) {
    $logout();

    return redirect('/');
})->middleware('auth')->name('logout');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';




