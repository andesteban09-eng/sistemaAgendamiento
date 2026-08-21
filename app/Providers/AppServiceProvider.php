<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $appUrl = config('app.url');

        if ($appUrl) {
            // Fuerza que route(), url(), redirect(), etc. usen APP_URL como raíz,
            // sin depender de cómo Apache reporte el subdirectorio.
            URL::forceRootUrl($appUrl);

            if (str_starts_with($appUrl, 'https://')) {
                URL::forceScheme('https');
            }
        }

        // Prefijo dinámico (ej. "/sistemaAgendamiento/public") sacado de APP_URL,
        // en vez de tenerlo escrito a mano.
        $basePath = rtrim((string) parse_url($appUrl, PHP_URL_PATH), '/');

        Livewire::setScriptRoute(function ($handle) use ($basePath) {
            return Route::get("{$basePath}/livewire/livewire.js", $handle);
        });

        Livewire::setUpdateRoute(function ($handle) use ($basePath) {
            return Route::post("{$basePath}/livewire/update", $handle);
        });
    }
}