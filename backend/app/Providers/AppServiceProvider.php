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
use App\Models\Usuario;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

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
        // Gestión de roles y usuarios: solo con el permiso explícito (denegación por defecto).
        Gate::define(
            'administrar-sistema',
            fn (Usuario $usuario): bool => $usuario->tienePermiso('sistema.administrar')
        );

        // Inicio de sesión (web y API): por correo + IP y, además, por IP.
        RateLimiter::for('login', function (Request $request) {
            $correo = Str::lower((string) $request->input('email'));

            return [
                Limit::perMinute(5)->by('login:'.$correo.'|'.$request->ip()),
                Limit::perMinute(20)->by('login-ip:'.$request->ip()),
            ];
        });

        // Registro público de cuentas.
        RateLimiter::for('registro', fn (Request $request) => Limit::perMinute(5)->by('registro:'.$request->ip()));

        // API (usado por throttleApi()): por usuario autenticado o, si no hay, por IP.
        RateLimiter::for('api', function (Request $request) {
            $usuarioId = $request->user()?->getAuthIdentifier();

            return Limit::perMinute(120)->by($usuarioId ? 'api-usuario:'.$usuarioId : 'api-ip:'.$request->ip());
        });

        // Política de contraseñas para cuentas nuevas y cambios de contraseña.
        Password::defaults(function () {
            $regla = Password::min(8)->letters()->mixedCase()->numbers();

            return $this->app->isProduction() ? $regla->uncompromised() : $regla;
        });
    }
}