<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Forzar zona horaria de MySQL para que coincida con la app
        try {
            DB::statement("SET time_zone = 'America/Lima'");
        } catch (\Throwable $e) {
            // Ignorar si el motor/usuario no permite cambiar time_zone
        }
    }
}
