<?php

namespace App\Providers;

use App\Repositories\CitaRepository;
use App\Interfaces\CitaInterface;
use App\Interfaces\EmpleadoInterface;
use App\Repositories\EmpleadoRepository;
use App\Interfaces\ServicioInterface;
use App\Repositories\ServicioRepository;
use App\Repositories\RolRepository;
Use App\Interfaces\RolInterface;
use App\Interfaces\ClienteInterface;
use App\Repositories\ClienteRepository;
use App\Interfaces\UsuarioInterface;
use App\Repositories\UsuarioRepository;

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
            RolInterface::class,
            RolRepository::class
        );
         $this->app->bind(
            ClienteInterface::class,
            ClienteRepository::class
        );
         $this->app->bind(
            UsuarioInterface::class,
            UsuarioRepository::class
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
