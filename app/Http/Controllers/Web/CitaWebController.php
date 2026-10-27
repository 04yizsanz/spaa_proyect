<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Cita;
use App\Models\Cliente;
use App\Models\Empleado;
use App\Models\Servicio;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Throwable;

/**
 * Controlador de vistas Blade para el módulo de Citas.
 *
 * Este controlador es DIFERENTE al CitaController existente,
 * que devuelve JSON para la API. Este renderiza vistas Blade.
 *
 * Gestiona: listado, formulario de creación y formulario de edición.
 */
class CitaWebController extends Controller
{
    /**
     * Listado de citas con filtros opcionales por fecha y estado.
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

            // Aplicar filtro de fecha si se proporcionó
            if ($filtroFecha) {
                $query->whereDate('fecha', $filtroFecha);
            }

            // Aplicar filtro de estado si se proporcionó y es válido
            $estadosValidos = ['pendiente', 'confirmada', 'completada', 'cancelada'];
            if ($filtroEstado && in_array($filtroEstado, $estadosValidos)) {
                $query->where('estado', $filtroEstado);
            }

            $citas   = $query->paginate(15)->withQueryString();
            $modoDemo = false;

        } catch (Throwable $e) {
            // Si la base de datos no está lista, modo demostración
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
        try {
            // Cargamos clientes con su información de usuario para mostrar nombre y apellido
            $clientes  = Cliente::with('usuario')->get();
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

        // Estado predeterminado si no se envía
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
            // Buscamos por clave primaria real del modelo
            $cita      = Cita::findOrFail($id);
            $clientes  = Cliente::with('usuario')->get();
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
        $cita = Cita::findOrFail($id);

        $validated = $request->validate([
            'codigo_cita' => [
                'required',
                'integer',
                // Ignoramos la cita actual en la validación unique
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
}
