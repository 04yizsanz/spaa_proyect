<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Empleado\StoreEmpleadoRequest;
use App\Http\Requests\Empleado\UpdateEmpleadoRequest;
use App\Models\Empleado;
use App\Services\EmpleadoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/**
 * Vistas Blade del módulo de Personal (empleados).
 *
 * Privacidad por permisos (config/permisos.php):
 *  - empleados.ver           nombre, rol y disponibilidad (todos los roles)
 *  - empleados.ver_contacto  documento, correo y teléfono
 *  - empleados.ver_salario   salario
 *  - empleados.gestionar     crear, editar y eliminar
 */
class EmpleadoWebController extends Controller
{
    /** Valores de la columna 'rol' (enum de la migración) y su texto en pantalla. */
    public const ROLES = [
        'estilista'     => 'Estilista',
        'Estetisista'   => 'Esteticista',
        'manicurista'   => 'Manicurista',
        'recepcionista' => 'Recepcionista',
        'administrador' => 'Administrador',
    ];

    public function __construct(
        protected EmpleadoService $empleadoService
    ) {}

    /** GET /empleados */
    public function index(Request $request)
    {
        Gate::authorize('viewAny', Empleado::class);

        $rol = array_key_exists((string) $request->query('rol'), self::ROLES) ? $request->query('rol') : null;

        $empleados = $this->empleadoService->paginar([
            'buscar'  => $request->query('buscar'),
            'filtros' => ['rol' => $rol],
            'orden'   => ['nombre', 'asc'],
        ]);

        return view('staff.index', $this->permisosVista($request) + [
            'empleados' => $empleados,
            'roles'     => self::ROLES,
        ]);
    }

    /** GET /empleados/nuevo */
    public function create(Request $request)
    {
        Gate::authorize('create', Empleado::class);

        return view('staff.create', [
            'empleado' => new Empleado(['disponibilidad' => true, 'fecha_contratacion' => now()]),
            'roles'    => self::ROLES,
        ]);
    }

    /** POST /empleados */
    public function store(StoreEmpleadoRequest $request)
    {
        Gate::authorize('create', Empleado::class);

        $datos = $request->validated() + ['disponibilidad' => $request->boolean('disponibilidad')];
        $empleado = $this->empleadoService->create($datos);

        return redirect()->route('empleados.show', $empleado->empleado_id)->with('success', 'Empleado registrado.');
    }

    /** GET /empleados/{id} */
    public function show(Request $request, int $empleado)
    {
        $registro = $this->buscar($empleado);
        Gate::authorize('view', $registro);

        return view('staff.show', $this->permisosVista($request) + [
            'empleado' => $registro,
            'roles'    => self::ROLES,
        ]);
    }

    /** GET /empleados/{id}/editar */
    public function edit(int $empleado)
    {
        $registro = $this->buscar($empleado);
        Gate::authorize('update', $registro);

        return view('staff.edit', ['empleado' => $registro, 'roles' => self::ROLES]);
    }

    /** PUT /empleados/{id} */
    public function update(UpdateEmpleadoRequest $request, int $empleado)
    {
        $registro = $this->buscar($empleado);
        Gate::authorize('update', $registro);

        $datos = $request->validated() + ['disponibilidad' => $request->boolean('disponibilidad')];
        $this->empleadoService->update($registro->empleado_id, $datos);

        return redirect()->route('empleados.show', $registro->empleado_id)->with('success', 'Datos del empleado actualizados.');
    }

    /** DELETE /empleados/{id} */
    public function destroy(int $empleado)
    {
        $registro = $this->buscar($empleado);
        Gate::authorize('delete', $registro);

        $this->empleadoService->delete($registro->empleado_id);

        return redirect()->route('empleados.index')->with('success', 'Empleado eliminado.');
    }

    private function buscar(int $id): Empleado
    {
        return $this->empleadoService->getById($id) ?? abort(404);
    }

    private function permisosVista(Request $request): array
    {
        $usuario = $request->user();

        return [
            'verContacto'    => $usuario->tienePermiso('empleados.ver_contacto'),
            'verSalario'     => $usuario->tienePermiso('empleados.ver_salario'),
            'puedeGestionar' => $usuario->can('create', Empleado::class),
        ];
    }
}
