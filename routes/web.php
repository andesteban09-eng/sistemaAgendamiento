<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Livewire\Actions\Logout;
use Illuminate\Http\Request;

Route::get('/', function () {
    return view('inicio');
})->name('inicio');

Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    Route::post('logout', function (Request $request, Logout $logout) {
        $logout();

        return redirect('/');
    })->name('logout');

    Route::view('profile', 'profile')
        ->name('profile');
});

require __DIR__.'/auth.php';
