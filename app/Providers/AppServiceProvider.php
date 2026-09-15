<?php

namespace App\Providers;

use App\Repositories\CitaRepository;
use App\Interfaces\CitaInterface;
use App\Interfaces\EmpleadoInterface;
use App\Repositories\EmpleadoRepository;
use App\Interfaces\ServicioInterface;
use App\Repositories\ServicioRepository;
use App\Interfaces\ProveedorInterface;
use App\Repositories\ProveedorRepository;
use App\Interfaces\ProductoInterface;
use App\Repositories\ProductoRepository;

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

        $this->app->bind(
            ProveedorInterface::class,
            ProveedorRepository::class
        );

        $this->app->bind(
            ProductoInterface::class,
           ProductoRepository::class
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
