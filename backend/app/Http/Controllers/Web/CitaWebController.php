<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Cita;
use App\Models\Cliente;
use App\Models\Empleado;
use App\Models\Servicio;
use App\Models\Usuario;
use App\Services\CitaService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;
use InvalidArgumentException;

/**
 * Vistas Blade del módulo de Citas.
 *
 * Es distinto del CitaController de la API (que devuelve JSON), pero usa las
 * MISMAS reglas: CitaService (disponibilidad del empleado, código asignado por
 * el servidor, cliente propio) y CitaPolicy (quién puede ver, editar, cambiar
 * el estado o reasignar).
 *
 *  - Cliente: ve y agenda solo sus citas; nacen 'pendiente'; puede editar una
 *    cita mientras esté pendiente y cancelarla.
 *  - Recepcionista / Administrador ('citas.gestionar'): gestionan toda la agenda.
 *  - Estilista / Esteticista ('citas.ver_todas'): solo consultan la agenda.
 */
class CitaWebController extends Controller
{
    public const ESTADOS = [
        'pendiente'  => 'Pendiente',
        'confirmada' => 'Confirmada',
        'completada' => 'Completada',
        'cancelada'  => 'Cancelada',
    ];

    public const HORA_APERTURA = '09:00';
    public const HORA_CIERRE   = '21:00';

    public function __construct(
        protected CitaService $citaService
    ) {}

    /** GET /citas — agenda con filtros por fecha y estado */
    public function index(Request $request)
    {
        $filtroFecha  = $this->fechaValida($request->query('fecha'));
        $filtroEstado = array_key_exists((string) $request->query('estado'), self::ESTADOS) ? $request->query('estado') : null;

        $citas = $this->citaService->paginar([
            'visiblePara' => $request->user(),
            'filtros'     => ['fecha' => $filtroFecha, 'estado' => $filtroEstado],
            'con'         => ['cliente.usuario', 'servicio', 'empleado'],
            'orden'       => ['fecha', 'desc'],
        ]);

        return view('appointments.index', [
            'citas'        => $citas,
            'filtroFecha'  => $filtroFecha,
            'filtroEstado' => $filtroEstado,
            'estados'      => self::ESTADOS,
        ]);
    }

    /** GET /citas/nueva */
    public function create(Request $request)
    {
        $usuario = $request->user();

        if (! $this->gestionaAgenda($usuario) && ! $usuario->cliente) {
            return redirect()->route('citas.index')->with('error', 'Tu cuenta no tiene un perfil de cliente asociado.');
        }

        return view('appointments.create', $this->datosFormulario($usuario) + [
            'cita'        => new Cita(['estado' => 'pendiente']),
            'fechaMinima' => now()->toDateString(),
            'zonaHoraria' => config('app.timezone'),
        ]);
    }

    /** POST /citas */
    public function store(Request $request)
    {
        $usuario = $request->user();

        // Un Cliente siempre agenda a su nombre, aunque manipulen el formulario.
        if (! $this->gestionaAgenda($usuario)) {
            $request->merge(['cliente_id' => $usuario->cliente?->cliente_id, 'estado' => 'pendiente']);
        }

        $datos = $request->validate($this->reglas($request, nueva: true), $this->mensajes());

        try {
            $this->citaService->crear($datos, $usuario);
        } catch (InvalidArgumentException|AuthorizationException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->route('citas.index')->with('success', 'La cita se agendó correctamente.');
    }

    /** GET /citas/{id}/editar */
    public function edit(Request $request, int $id)
    {
        $cita = $this->buscar($id, $request);

        $decision = Gate::inspect('update', $cita);
        if ($decision->denied()) {
            return redirect()->route('citas.index')->with('warning', $decision->message());
        }

        return view('appointments.edit', $this->datosFormulario($request->user(), $cita) + [
            'cita'        => $cita,
            'fechaMinima' => min(now()->toDateString(), $cita->fecha->toDateString()),
            'zonaHoraria' => config('app.timezone'),
        ]);
    }

    /** PUT /citas/{id} */
    public function update(Request $request, int $id)
    {
        $cita    = $this->buscar($id, $request);
        $usuario = $request->user();
        Gate::authorize('update', $cita);

        if (! $this->gestionaAgenda($usuario)) {
            $request->merge(['cliente_id' => $cita->cliente_id]);
        }

        $datos = $request->validate($this->reglas($request, nueva: false, cita: $cita), $this->mensajes());

        try {
            $this->citaService->actualizar($cita, $datos, $usuario);
        } catch (InvalidArgumentException|AuthorizationException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->route('citas.index')->with('success', 'La cita se actualizó correctamente.');
    }

    /** PATCH /citas/{id}/cancelar — atajo para que el cliente cancele su cita */
    public function cancelar(Request $request, int $id)
    {
        $cita = $this->buscar($id, $request);
        Gate::authorize('update', $cita);

        try {
            $this->citaService->actualizar($cita, ['estado' => 'cancelada'], $request->user());
        } catch (AuthorizationException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'La cita fue cancelada.');
    }

    // ── Auxiliares ───────────────────────────────────────────────────────────

    private function buscar(int $id, Request $request): Cita
    {
        $cita = $this->citaService->buscarVisible($id, $request->user()) ?? abort(404);
        Gate::authorize('view', $cita);

        return $cita;
    }

    private function gestionaAgenda(Usuario $usuario): bool
    {
        return $usuario->tienePermiso('citas.gestionar');
    }

    /** Listas para los selectores del formulario. */
    private function datosFormulario(Usuario $usuario, ?Cita $cita = null): array
    {
        $gestiona = $this->gestionaAgenda($usuario);

        $clientes = $gestiona
            ? Cliente::with('usuario')->get()
            : Cliente::with('usuario')->where('cliente_id', $usuario->cliente?->cliente_id ?? 0)->get();

        $empleados = Empleado::where('disponibilidad', true)
            ->when($cita, fn ($q) => $q->orWhere('empleado_id', $cita->empleado_id))
            ->orderBy('nombre')->get();

        $servicios = Servicio::where('activo', true)
            ->when($cita, fn ($q) => $q->orWhere('servicio_id', $cita->servicio_id))
            ->orderBy('nombre')->get();

        // Estados que este usuario puede asignar (CitaPolicy).
        $estados = $gestiona
            ? self::ESTADOS
            : array_intersect_key(self::ESTADOS, array_flip(array_filter([$cita?->estado ?? 'pendiente', $cita ? 'cancelada' : null])));

        return compact('clientes', 'empleados', 'servicios', 'estados', 'gestiona');
    }

    /** Reglas de validación (horario de atención 09:00–21:00, sin fechas pasadas). */
    private function reglas(Request $request, bool $nueva, ?Cita $cita = null): array
    {
        $cambiaFechaHora = $nueva
            || $request->input('fecha') !== $cita?->fecha?->toDateString()
            || substr((string) $request->input('hora'), 0, 5) !== substr((string) $cita?->hora, 0, 5);

        return [
            'fecha' => array_filter([
                'required', 'date_format:Y-m-d',
                $cambiaFechaHora ? 'after_or_equal:' . now()->toDateString() : null,
            ]),
            'hora' => [
                'required',
                'date_format:H:i',
                function ($atributo, $valor, $falla) use ($request, $cambiaFechaHora): void {
                    if (! is_string($valor) || ! preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/', $valor)) {
                        return;
                    }
                    if ($valor < self::HORA_APERTURA || $valor > self::HORA_CIERRE) {
                        $falla('La hora de la cita debe estar entre las ' . self::HORA_APERTURA . ' y las ' . self::HORA_CIERRE . '.');

                        return;
                    }
                    $fecha = $this->fechaValida($request->input('fecha'));
                    if ($cambiaFechaHora && $fecha === now()->toDateString()
                        && Carbon::createFromFormat('!Y-m-d H:i', "{$fecha} {$valor}")->lessThanOrEqualTo(now())) {
                        $falla('Para hoy, selecciona una hora posterior a la hora actual.');
                    }
                },
            ],
            'estado'      => ['nullable', 'in:' . implode(',', array_keys(self::ESTADOS))],
            'cliente_id'  => ['required', 'integer', 'exists:clientes,cliente_id'],
            'empleado_id' => ['required', 'integer', 'exists:empleados,empleado_id'],
            'servicio_id' => ['required', 'integer', 'exists:servicios,servicio_id'],
        ];
    }

    private function mensajes(): array
    {
        return [
            'fecha.required'       => 'La fecha es obligatoria.',
            'fecha.after_or_equal' => 'No puedes agendar una cita en una fecha anterior a hoy.',
            'fecha.date_format'    => 'La fecha debe tener un formato válido.',
            'hora.required'        => 'La hora es obligatoria.',
            'hora.date_format'     => 'La hora debe tener el formato HH:MM.',
            'cliente_id.required'  => 'Debes seleccionar un cliente.',
            'empleado_id.required' => 'Debes seleccionar quién te atiende.',
            'servicio_id.required' => 'Debes seleccionar un servicio.',
        ];
    }

    /** Devuelve la fecha si tiene formato AAAA-MM-DD real; si no, null. */
    private function fechaValida(mixed $fecha): ?string
    {
        if (! is_string($fecha) || ! preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
            return null;
        }
        $objeto = \DateTimeImmutable::createFromFormat('!Y-m-d', $fecha);

        return $objeto && $objeto->format('Y-m-d') === $fecha ? $fecha : null;
    }
}
