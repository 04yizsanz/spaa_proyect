<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Usuario\StoreUsuarioRequest;
use App\Http\Requests\Usuario\UpdateUsuarioRequest;
use App\Models\Cliente;
use App\Models\Rol;
use App\Models\Usuario;
use App\Services\UsuarioService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Gestión de usuarios y roles (solo Administrador).
 *
 * Así se le dan permisos al personal:
 *  1. El registro público SIEMPRE crea cuentas con rol Cliente.
 *  2. El primer administrador se asigna por consola:
 *       php artisan app:make-administrator correo@ejemplo.com
 *  3. Desde esta pantalla el administrador crea las cuentas del personal
 *     (Recepcionista, Estilista, Esteticista) o cambia el rol de una cuenta.
 *
 * Reglas:
 *  - Solo puede existir UN administrador (StoreUsuarioRequest / UpdateUsuarioRequest).
 *  - Las cuentas no se eliminan: se desactivan (estado = inactivo).
 *  - El administrador no puede quitarse a sí mismo el rol ni desactivarse,
 *    para que el sistema nunca se quede sin administrador.
 *
 * La ruta exige 'can:administrar-sistema' y los Form Requests lo verifican otra vez.
 */
class UsuarioWebController extends Controller
{
    public function __construct(
        protected UsuarioService $usuarioService
    ) {}

    /** GET /usuarios */
    public function index(Request $request)
    {
        $usuarios = $this->usuarioService->paginar([
            'buscar'  => $request->query('buscar'),
            'filtros' => ['rol_id' => $request->query('rol'), 'estado' => $request->query('estado')],
            'con'     => ['rol'],
            'orden'   => ['nombre', 'asc'],
        ]);

        return view('users.index', [
            'usuarios' => $usuarios,
            'roles'    => Rol::orderBy('nombre')->pluck('nombre', 'rol_id')->all(),
        ]);
    }

    /** GET /usuarios/nuevo */
    public function create()
    {
        return view('users.create', [
            'usuario' => new Usuario(['estado' => true]),
            'roles'   => $this->opcionesRol(),
        ]);
    }

    /** POST /usuarios */
    public function store(StoreUsuarioRequest $request)
    {
        $usuario = DB::transaction(function () use ($request) {
            $usuario = $this->usuarioService->create($request->validated());
            $this->asegurarFichaCliente($usuario);

            return $usuario;
        });

        return redirect()->route('usuarios.index')
            ->with('success', "Cuenta creada para {$usuario->nombre} {$usuario->apellido}.");
    }

    /** GET /usuarios/{id}/editar */
    public function edit(int $usuario)
    {
        $registro = $this->buscar($usuario);

        return view('users.edit', [
            'usuario'    => $registro,
            'roles'      => $this->opcionesRol($registro),
            'esMiCuenta' => $registro->usuario_id === auth()->id(),
        ]);
    }

    /** PUT /usuarios/{id} */
    public function update(UpdateUsuarioRequest $request, int $usuario)
    {
        $registro = $this->buscar($usuario);
        $datos    = $request->validated();

        // Protección contra quedarse sin administrador.
        if ($registro->usuario_id === $request->user()->usuario_id) {
            if ((int) $datos['rol_id'] !== (int) $registro->rol_id || ! (bool) $datos['estado']) {
                return back()->withInput()
                    ->with('error', 'No puedes cambiar tu propio rol ni desactivar tu propia cuenta.');
            }
        }

        DB::transaction(function () use ($registro, $datos) {
            $actualizado = $this->usuarioService->update($registro->usuario_id, $datos);
            $this->asegurarFichaCliente($actualizado);
        });

        return redirect()->route('usuarios.index')->with('success', 'Cuenta actualizada.');
    }

    private function buscar(int $id): Usuario
    {
        return $this->usuarioService->getById($id) ?? abort(404);
    }

    /**
     * Roles activos para el selector. El rol Administrador solo se ofrece
     * si todavía no hay administrador o si es el de la cuenta que se edita.
     *
     * @return array<int, string>
     */
    private function opcionesRol(?Usuario $editando = null): array
    {
        $idAdministrador = Rol::where('nombre', 'Administrador')->value('rol_id');
        $hayAdministrador = $idAdministrador && Usuario::where('rol_id', $idAdministrador)->exists();

        return Rol::where('estado', true)
            ->orderBy('nombre')
            ->get()
            ->reject(fn (Rol $rol) => $rol->rol_id === $idAdministrador
                && $hayAdministrador
                && $editando?->rol_id !== $idAdministrador)
            ->pluck('nombre', 'rol_id')
            ->all();
    }

    /** Una cuenta con rol Cliente necesita su ficha en la tabla clientes. */
    private function asegurarFichaCliente(Usuario $usuario): void
    {
        if ($usuario->fresh('rol')->esCliente() && ! $usuario->cliente()->exists()) {
            Cliente::create(['usuario_id' => $usuario->usuario_id]);
        }
    }
}
