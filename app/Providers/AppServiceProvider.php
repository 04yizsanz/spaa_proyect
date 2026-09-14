<?php

namespace App\Providers;

use App\Repositories\CitaRepository;
use App\Repositories\Interfaces\CitaInterface;
use App\Interfaces\EmpleadoInterface;
use App\Repositories\EmpleadoRepository;
use App\Interfaces\ServicioInterface;
use App\Repositories\ServicioRepository;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            CitaInterface::class,
            CitaRepository::class
        );

        $this->app->bind(
            EmpleadoInterface::class,
            EmpleadoRepository::class
        );

        $this->app->bind(
            ServicioInterface::class,
            ServicioRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
