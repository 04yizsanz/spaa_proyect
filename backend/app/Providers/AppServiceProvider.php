<?php

namespace App\Providers;

use App\Repositories\CitaRepository;
use App\Interfaces\CitaInterface;
use App\Interfaces\EmpleadoInterface;
use App\Repositories\EmpleadoRepository;
use App\Interfaces\ServicioInterface;
use App\Repositories\ServicioRepository;
use App\Interfaces\FacturaInterface;
use App\Repositories\FacturaRepository;
use App\Interfaces\PagoInterface;
use App\Repositories\PagoRepository;
use App\Interfaces\FacturaServicioInterface;
use App\Repositories\FacturaServicioRepository;
use App\Interfaces\RolInterface;
use App\Repositories\RolRepository;
use App\Interfaces\UsuarioInterface;
use App\Repositories\UsuarioRepository;
use App\Interfaces\ClienteInterface;
use App\Repositories\ClienteRepository;
use App\Interfaces\ProveedorInterface;
use App\Repositories\ProveedorRepository;
use App\Interfaces\ProductoInterface;
use App\Repositories\ProductoRepository;
use App\Interfaces\MovimientoInventarioInterface;
use App\Repositories\MovimientoInventarioRepository;


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
            FacturaInterface::class,
            FacturaRepository::class
        );

        $this->app->bind(
            PagoInterface::class,
            PagoRepository::class
        );

        $this->app->bind(
            FacturaServicioInterface::class,
            FacturaServicioRepository::class
        );

        $this->app->bind(
            RolInterface::class,
            RolRepository::class
        );

       $this->app->bind(
            UsuarioInterface::class,
            UsuarioRepository::class
        );

       $this->app->bind(
            ClienteInterface::class,
            ClienteRepository::class
        );

         
    
        $this->app->bind(
            ProveedorInterface::class,
            ProveedorRepository::class
    
        );

        $this->app->bind(
            ProductoInterface::class,
            ProductoRepository::class
    
        );

         $this->app->bind(
            MovimientoInventarioInterface::class,
            MovimientoInventarioRepository::class
    
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