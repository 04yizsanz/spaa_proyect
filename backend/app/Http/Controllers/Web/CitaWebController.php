<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Cita;
use App\Models\Cliente;
use App\Models\Empleado;
use App\Models\Servicio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Throwable;

/**
 * Controlador de vistas Blade para el módulo de Citas.
 *
 * Es DIFERENTE al CitaController de la API, que devuelve JSON.
 * Un usuario con rol Cliente solo ve y gestiona sus propias citas.
 */
class CitaWebController extends Controller
{
    /**
     * Citas: listado con filtros opcionales por fecha y estado.
     * GET /citas
     */
    public function index(Request $request)
    {
        $filtroFecha  = $request->query('fecha');
        $filtroEstado = $request->query('estado');

        try {
            $query = Cita::with(['cliente.usuario', 'servicio', 'empleado'])
                ->orderBy('fecha', 'desc')
                ->orderBy('hora', 'asc');

            // Un Cliente solo ve sus propias citas
            $usuario = Auth::user();
            if ($usuario->esCliente()) {
                $query->where('cliente_id', $usuario->cliente?->cliente_id ?? 0);
            }

            if ($filtroFecha) {
                $query->whereDate('fecha', $filtroFecha);
            }

            $estadosValidos = ['pendiente', 'confirmada', 'completada', 'cancelada'];
            if ($filtroEstado && in_array($filtroEstado, $estadosValidos)) {
                $query->where('estado', $filtroEstado);
            }

            $citas    = $query->paginate(15)->withQueryString();
            $modoDemo = false;

        } catch (Throwable $e) {
            $citas    = collect([]);
            $modoDemo = true;
        }

        return view('appointments.index', compact(
            'citas',
            'filtroFecha',
            'filtroEstado',
            'modoDemo'
        ));
    }

    /**
     * Formulario para crear una nueva cita.
     * GET /citas/nueva
     */
    public function create()
    {
        $usuario = Auth::user();

        try {
            if ($usuario->esCliente()) {
                $cliente = $usuario->cliente;

                if (! $cliente) {
                    return redirect()
                        ->route('citas.index')
                        ->with('error', 'Tu cuenta no tiene un perfil de cliente asociado.');
                }

                // El Cliente solo puede agendar a su propio nombre
                $clientes = Cliente::with('usuario')
                    ->where('cliente_id', $cliente->cliente_id)
                    ->get();
            } else {
                $clientes = Cliente::with('usuario')->get();
            }

            $empleados = Empleado::where('disponibilidad', true)->orderBy('nombre')->get();
            $servicios = Servicio::where('activo', true)->orderBy('nombre')->get();
            $modoDemo  = false;
        } catch (Throwable $e) {
            $clientes  = collect([]);
            $empleados = collect([]);
            $servicios = collect([]);
            $modoDemo  = true;
        }

        return view('appointments.create', compact('clientes', 'empleados', 'servicios', 'modoDemo'));
    }

    /**
     * Guarda una nueva cita en la base de datos.
     * POST /citas
     */
    public function store(Request $request)
    {
        $usuario = Auth::user();

        // Un Cliente siempre agenda a su propio nombre, aunque manipulen el formulario
        if ($usuario->esCliente()) {
            $cliente = $usuario->cliente;

            if (! $cliente) {
                return redirect()
                    ->route('citas.index')
                    ->with('error', 'Tu cuenta no tiene un perfil de cliente asociado.');
            }

            $request->merge(['cliente_id' => $cliente->cliente_id]);
        }

        $validated = $request->validate([
            'codigo_cita' => ['required', 'integer', 'unique:citas,codigo_cita'],
            'fecha'       => ['required', 'date'],
            'hora'        => ['required', 'date_format:H:i'],
            'estado'      => ['nullable', 'in:pendiente,confirmada,completada,cancelada'],
            'cliente_id'  => ['required', 'integer', 'exists:clientes,cliente_id'],
            'empleado_id' => ['required', 'integer', 'exists:empleados,empleado_id'],
            'servicio_id' => ['required', 'integer', 'exists:servicios,servicio_id'],
        ], [
            'codigo_cita.required' => 'El código de cita es obligatorio.',
            'codigo_cita.unique'   => 'Ya existe una cita con ese código.',
            'fecha.required'       => 'La fecha es obligatoria.',
            'hora.required'        => 'La hora es obligatoria.',
            'hora.date_format'     => 'La hora debe tener el formato HH:MM.',
            'cliente_id.required'  => 'Debes seleccionar un cliente.',
            'cliente_id.exists'    => 'El cliente seleccionado no existe.',
            'empleado_id.required' => 'Debes seleccionar un empleado.',
            'empleado_id.exists'   => 'El empleado seleccionado no existe.',
            'servicio_id.required' => 'Debes seleccionar un servicio.',
            'servicio_id.exists'   => 'El servicio seleccionado no existe.',
        ]);

        $validated['estado'] = $validated['estado'] ?? 'pendiente';

        Cita::create($validated);

        return redirect()
            ->route('citas.index')
            ->with('success', 'La cita se ha guardado correctamente.');
    }

    /**
     * Formulario para editar una cita existente.
     * GET /citas/{id}/editar
     */
    public function edit(int $id)
    {
        try {
            $cita = $this->citaVisible($id);

            if (Auth::user()->esCliente()) {
                $clientes = Cliente::with('usuario')
                    ->where('cliente_id', $cita->cliente_id)
                    ->get();
            } else {
                $clientes = Cliente::with('usuario')->get();
            }

            $empleados = Empleado::orderBy('nombre')->get();
            $servicios = Servicio::where('activo', true)->orderBy('nombre')->get();
            $modoDemo  = false;
        } catch (Throwable $e) {
            return redirect()
                ->route('citas.index')
                ->with('error', 'No se encontró la cita solicitada.');
        }

        return view('appointments.edit', compact('cita', 'clientes', 'empleados', 'servicios', 'modoDemo'));
    }

    /**
     * Actualiza una cita existente.
     * PUT /citas/{id}
     */
    public function update(Request $request, int $id)
    {
        $cita = $this->citaVisible($id);

        // Un Cliente no puede pasar su cita a otro cliente
        if (Auth::user()->esCliente()) {
            $request->merge(['cliente_id' => $cita->cliente_id]);
        }

        $validated = $request->validate([
            'codigo_cita' => [
                'required',
                'integer',
                'unique:citas,codigo_cita,' . $id . ',citas_id',
            ],
            'fecha'       => ['required', 'date'],
            'hora'        => ['required', 'date_format:H:i'],
            'estado'      => ['required', 'in:pendiente,confirmada,completada,cancelada'],
            'cliente_id'  => ['required', 'integer', 'exists:clientes,cliente_id'],
            'empleado_id' => ['required', 'integer', 'exists:empleados,empleado_id'],
            'servicio_id' => ['required', 'integer', 'exists:servicios,servicio_id'],
        ], [
            'codigo_cita.required' => 'El código de cita es obligatorio.',
            'codigo_cita.unique'   => 'Ya existe otra cita con ese código.',
            'fecha.required'       => 'La fecha es obligatoria.',
            'hora.required'        => 'La hora es obligatoria.',
            'hora.date_format'     => 'La hora debe tener el formato HH:MM.',
            'estado.required'      => 'El estado es obligatorio.',
            'estado.in'            => 'El estado seleccionado no es válido.',
            'cliente_id.required'  => 'Debes seleccionar un cliente.',
            'empleado_id.required' => 'Debes seleccionar un empleado.',
            'servicio_id.required' => 'Debes seleccionar un servicio.',
        ]);

        $cita->update($validated);

        return redirect()
            ->route('citas.index')
            ->with('success', 'La cita se ha actualizado correctamente.');
    }

    /**
     * Busca una cita por id. Si el usuario es Cliente, solo encuentra las suyas.
     */
    private function citaVisible(int $id): Cita
    {
        $query   = Cita::query();
        $usuario = Auth::user();

        if ($usuario->esCliente()) {
            $query->where('cliente_id', $usuario->cliente?->cliente_id ?? 0);
        }

        return $query->findOrFail($id);
    }
}