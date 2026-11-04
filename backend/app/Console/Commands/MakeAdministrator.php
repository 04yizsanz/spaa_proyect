<?php

namespace App\Console\Commands;

use App\Models\Rol;
use App\Models\Usuario;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MakeAdministrator extends Command
{
    protected $signature = 'app:make-administrator {email : Correo del usuario registrado que será administrador}';

    protected $description = 'Asigna el único rol de administrador a un usuario existente';

    public function handle(): int
    {
        try {
            $usuario = DB::transaction(function (): Usuario {
                if (Usuario::whereHas('rol', fn ($query) => $query->where('nombre', 'Administrador'))
                    ->lockForUpdate()
                    ->exists()) {
                    throw ValidationException::withMessages([
                        'administrador' => 'Ya existe una cuenta de administrador.',
                    ]);
                }

                $usuario = Usuario::where('email', $this->argument('email'))->lockForUpdate()->first();

                if (! $usuario) {
                    throw ValidationException::withMessages([
                        'email' => 'No existe un usuario registrado con ese correo.',
                    ]);
                }

                $rol = Rol::firstOrCreate(
                    ['nombre' => 'Administrador'],
                    ['descripcion' => 'Administrador general del sistema.', 'estado' => true]
                );

                $usuario->rol_id = $rol->rol_id;
                $usuario->save();

                return $usuario;
            });
        } catch (ValidationException $exception) {
            $this->error($exception->errors()[array_key_first($exception->errors())][0]);

            return self::FAILURE;
        }

        $this->info("{$usuario->email} ahora es el administrador del sistema.");

        return self::SUCCESS;
    }
}
