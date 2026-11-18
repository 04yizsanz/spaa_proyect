<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cliente\UpdateClienteRequest;
use App\Models\Cita;
use App\Models\Cliente;
use App\Models\Rol;
use App\Models\Usuario;
use App\Services\ClienteService;
use App\Services\UsuarioService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

/**
 * Vistas Blade del módulo de Clientes.
 *
 * Un cliente son DOS registros: su cuenta (tabla usuarios, con rol Cliente)
 * y su ficha (tabla clientes: fecha de nacimiento y preferencias).
 * Cuando el personal registra un cliente en el mostrador se crean ambos en
 * una sola transacción, igual que en el registro público.
 *
 * Permisos (ClientePolicy): clientes.ver / clientes.gestionar.
 */
class ClienteWebController extends Controller
{
    public function __construct(
        protected ClienteService $clienteService,
        protected UsuarioService $usuarioService
    ) {}

    /** GET /clientes */
    public function index(Request $request)
    {
        Gate::authorize('viewAny', Cliente::class);

        $clientes = $this->clienteService->paginar([
            'visiblePara' => $request->user(),
            'buscar'      => $request->query('buscar'),
            'con'         => ['usuario'],
        ]);

        return view('clients.index', compact('clientes'));
    }

    /** GET /clientes/nuevo */
    public function create()
    {
        Gate::authorize('create', Cliente::class);

        return view('clients.create', ['cliente' => new Cliente(), 'usuario' => new Usuario()]);
    }

    /** POST /clientes */
    public function store(Request $request)
    {
        Gate::authorize('create', Cliente::class);

        $datos = $request->validate($this->reglasCuenta() + [
            'password'         => ['required', 'string', Password::defaults()],
            'fecha_nacimiento' => ['nullable', 'date', 'before:today'],
            'preferencias'     => ['nullable', 'string', 'max:1000'],
        ], $this->mensajes());

        $cliente = DB::transaction(function () use ($datos) {
            $rol = Rol::firstOrCreate(
                ['nombre' => 'Cliente'],
                ['descripcion' => 'Cliente registrado que puede gestionar sus citas.', 'estado' => true]
            );

            // UsuarioRepository cifra la contraseña.
            $usuario = $this->usuarioService->create([
                'rol_id'    => $rol->rol_id,
                'nombre'    => $datos['nombre'],
                'apellido'  => $datos['apellido'],
                'documento' => $datos['documento'],
                'email'     => $datos['email'],
                'telefono'  => $datos['telefono'] ?? null,
                'password'  => $datos['password'],
                'estado'    => true,
            ]);

            return $this->clienteService->create([
                'usuario_id'       => $usuario->usuario_id,
                'fecha_nacimiento' => $datos['fecha_nacimiento'] ?? null,
                'preferencias'     => $datos['preferencias'] ?? null,
            ]);
        });

        return redirect()->route('clientes.show', $cliente->cliente_id)->with('success', 'Cliente registrado.');
    }

    /** GET /clientes/{id} */
    public function show(Request $request, int $cliente)
    {
        $registro = $this->buscar($cliente, $request);

        $citas = Cita::with(['servicio', 'empleado'])
            ->where('cliente_id', $registro->cliente_id)
            ->orderByDesc('fecha')->orderByDesc('hora')
            ->limit(10)->get();

        return view('clients.show', ['cliente' => $registro->load('usuario'), 'citas' => $citas]);
    }

    /** GET /clientes/{id}/editar */
    public function edit(Request $request, int $cliente)
    {
        $registro = $this->buscar($cliente, $request);
        Gate::authorize('update', $registro);

        return view('clients.edit', ['cliente' => $registro, 'usuario' => $registro->usuario]);
    }

    /** PUT /clientes/{id} */
    public function update(Request $request, int $cliente)
    {
        $registro = $this->buscar($cliente, $request);
        Gate::authorize('update', $registro);

        $datos = $request->validate($this->reglasCuenta($registro->usuario_id) + [
            'fecha_nacimiento' => ['nullable', 'date', 'before:today'],
            'preferencias'     => ['nullable', 'string', 'max:1000'],
        ], $this->mensajes());

        DB::transaction(function () use ($registro, $datos) {
            $this->usuarioService->update($registro->usuario_id, collect($datos)
                ->only(['nombre', 'apellido', 'documento', 'email', 'telefono'])->all());

            $this->clienteService->update($registro->cliente_id, collect($datos)
                ->only(['fecha_nacimiento', 'preferencias'])->all());
        });

        return redirect()->route('clientes.show', $registro->cliente_id)->with('success', 'Datos del cliente actualizados.');
    }

    /** DELETE /clientes/{id} — eliminación lógica (soft delete) de la ficha */
    public function destroy(Request $request, int $cliente)
    {
        $registro = $this->buscar($cliente, $request);
        Gate::authorize('delete', $registro);

        $this->clienteService->delete($registro->cliente_id);

        return redirect()->route('clientes.index')->with('success', 'Cliente eliminado.');
    }

    private function buscar(int $id, Request $request): Cliente
    {
        $cliente = $this->clienteService->buscarVisible($id, $request->user()) ?? abort(404);
        Gate::authorize('view', $cliente);

        return $cliente;
    }

    /** Reglas de los datos de la cuenta (tabla usuarios). */
    private function reglasCuenta(?int $usuarioId = null): array
    {
        return [
            'nombre'    => ['required', 'string', 'max:80'],
            'apellido'  => ['required', 'string', 'max:80'],
            'documento' => ['required', 'string', 'max:20', Rule::unique('usuarios', 'documento')->ignore($usuarioId, 'usuario_id')],
            'email'     => ['required', 'email', 'max:150', Rule::unique('usuarios', 'email')->ignore($usuarioId, 'usuario_id')],
            'telefono'  => ['nullable', 'string', 'max:20'],
        ];
    }

    private function mensajes(): array
    {
        return [
            'documento.unique'        => 'Ya existe una cuenta con ese documento.',
            'email.unique'            => 'Ya existe una cuenta con ese correo.',
            'fecha_nacimiento.before' => 'La fecha de nacimiento debe ser anterior a hoy.',
        ];
    }
}
