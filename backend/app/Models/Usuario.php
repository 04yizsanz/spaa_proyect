<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Usuario extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table      = 'usuarios';
    protected $primaryKey = 'usuario_id';

    // Decirle a Laravel qué columna usar para autenticación
    public function getAuthIdentifierName(): string
    {
        return 'usuario_id';
    }

    public function getAuthIdentifier(): mixed
    {
        return $this->usuario_id;
    }

    protected $fillable = [
        'rol_id',
        'nombre',
        'apellido',
        'documento',
        'email',
        'telefono',
        'password',
        'estado',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'estado' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saving(function (Usuario $usuario): void {
            $esAdministrador = Rol::query()
                ->where('rol_id', $usuario->rol_id)
                ->where('nombre', 'Administrador')
                ->exists();

            $usuario->administrador_unico = $esAdministrador ? 1 : null;
        });
    }

    public function rol()
    {
        return $this->belongsTo(Rol::class, 'rol_id', 'rol_id');
    }

    public function cliente()
    {
        return $this->hasOne(Cliente::class, 'usuario_id', 'usuario_id');
    }

    public function esCliente(): bool
    {
        return $this->rol?->nombre === 'Cliente';
    }

    /**
     * La cuenta puede operar solo si el usuario está activo, tiene un rol,
     * ese rol está activo y está definido en config/permisos.php.
     * Cualquier otro caso (sin rol, rol inactivo, rol desconocido o
     * renombrado) se deniega por defecto.
     */
    public function estaHabilitado(): bool
    {
        $rol = $this->rol;

        return (bool) $this->estado
            && $rol !== null
            && (bool) $rol->estado
            && array_key_exists($rol->nombre, config('permisos.roles', []));
    }

    /**
     * Permisos efectivos según el rol. Vacío si la cuenta no está habilitada.
     *
     * @return array<int, string>
     */
    public function permisos(): array
    {
        if (! $this->estaHabilitado()) {
            return [];
        }

        return config('permisos.roles', [])[$this->rol->nombre] ?? [];
    }

    public function tienePermiso(string $permiso): bool
    {
        $permisos = $this->permisos();

        return in_array('*', $permisos, true) || in_array($permiso, $permisos, true);
    }

    public function empleado()
    {
        return $this->hasOne(Empleado::class, 'usuario_id', 'usuario_id');
    }
}