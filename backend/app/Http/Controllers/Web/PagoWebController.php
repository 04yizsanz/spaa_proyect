<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pago\StorePagoRequest;
use App\Http\Requests\Pago\UpdatePagoRequest;
use App\Models\Cita;
use App\Models\Pago;
use App\Services\PagoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

/**
 * Vistas Blade del módulo de Pagos.
 *
 * Reglas de negocio (PagoService / PagoPolicy):
 *  - Un pago nace 'pendiente' y pasa a 'aprobado' o 'rechazado' (definitivo).
 *  - Solo quien tiene 'pagos.aprobar' puede cambiar el estado.
 *  - Los pagos no se eliminan.
 *  - Un Cliente solo ve los pagos de sus propias citas.
 */
class PagoWebController extends Controller
{
    public const METODOS = [
        'efectivo'      => 'Efectivo',
        'tarjeta'       => 'Tarjeta',
        'transferencia' => 'Transferencia',
        'pse'           => 'PSE',
    ];

    public function __construct(
        protected PagoService $pagoService
    ) {}

    /** GET /pagos */
    public function index(Request $request)
    {
        Gate::authorize('viewAny', Pago::class);

        $estado = in_array($request->query('estado'), ['pendiente', 'aprobado', 'rechazado'], true)
            ? $request->query('estado') : null;

        $pagos = $this->pagoService->paginar([
            'visiblePara' => $request->user(),
            'filtros'     => ['estado' => $estado, 'metodo' => $request->query('metodo')],
            'con'         => ['cita.cliente.usuario', 'cita.servicio'],
            'orden'       => ['fecha_hora', 'desc'],
        ]);

        return view('pagos.index', ['pagos' => $pagos, 'metodos' => self::METODOS]);
    }

    /** GET /pagos/nuevo  (acepta ?cita=CODIGO para preseleccionarla) */
    public function create(Request $request)
    {
        Gate::authorize('create', Pago::class);

        return view('pagos.create', [
            'pago'    => new Pago([
                'fecha_hora'  => now(),
                'codigo_cita' => $request->integer('cita') ?: null,
            ]),
            'citas'   => $this->opcionesCita(),
            'metodos' => self::METODOS,
        ]);
    }

    /** POST /pagos */
    public function store(StorePagoRequest $request)
    {
        Gate::authorize('create', Pago::class);

        // El estado inicial siempre es 'pendiente'; se aprueba después desde el detalle.
        $datos = collect($request->validated())->except('estado')->all();
        $pago  = $this->pagoService->store($datos, $request->user());

        return redirect()->route('pagos.show', $pago->pago_id)->with('success', 'Pago registrado como pendiente.');
    }

    /** GET /pagos/{id} */
    public function show(Request $request, int $pago)
    {
        $registro = $this->buscar($pago, $request);

        return view('pagos.show', [
            'pago'    => $registro->load('cita.cliente.usuario', 'cita.servicio'),
            'metodos' => self::METODOS,
        ]);
    }

    /** GET /pagos/{id}/editar */
    public function edit(Request $request, int $pago)
    {
        $registro = $this->buscar($pago, $request);

        if ($respuesta = $this->noEditable($registro)) {
            return $respuesta;
        }

        return view('pagos.edit', [
            'pago'    => $registro,
            'citas'   => $this->opcionesCita(),
            'metodos' => self::METODOS,
        ]);
    }

    /** PUT /pagos/{id} */
    public function update(UpdatePagoRequest $request, int $pago)
    {
        $registro = $this->buscar($pago, $request);

        if ($respuesta = $this->noEditable($registro)) {
            return $respuesta;
        }

        $datos = collect($request->validated())->except('estado')->all();
        $this->pagoService->update($registro, $datos, $request->user());

        return redirect()->route('pagos.show', $registro->pago_id)->with('success', 'Pago actualizado.');
    }

    /** PATCH /pagos/{id}/estado — aprobar o rechazar */
    public function cambiarEstado(Request $request, int $pago)
    {
        $registro = $this->buscar($pago, $request);

        if ($respuesta = $this->noEditable($registro)) {
            return $respuesta;
        }

        Gate::authorize('aprobar', $registro);

        $datos = $request->validate([
            'estado' => ['required', Rule::in(['aprobado', 'rechazado'])],
        ]);

        $this->pagoService->update($registro, $datos, $request->user());

        return redirect()->route('pagos.show', $registro->pago_id)
            ->with('success', $datos['estado'] === 'aprobado' ? 'Pago aprobado.' : 'Pago rechazado.');
    }

    /** Busca el pago solo entre los que el usuario puede ver (si no, 404). */
    private function buscar(int $id, Request $request): Pago
    {
        $pago = $this->pagoService->buscarVisible($id, $request->user()) ?? abort(404);
        Gate::authorize('view', $pago);

        return $pago;
    }

    /**
     * Un pago aprobado o rechazado no se modifica (PagoPolicy::update).
     * En la web se explica con un mensaje en lugar de mostrar un error 409.
     */
    private function noEditable(Pago $pago)
    {
        $decision = Gate::inspect('update', $pago);

        if ($decision->allowed()) {
            return null;
        }

        if ($decision->status() === 409) {
            return redirect()->route('pagos.show', $pago->pago_id)->with('warning', $decision->message());
        }

        abort(403, $decision->message() ?? 'No tienes permiso para modificar pagos.');
    }

    /** @return array<int, string> [codigo_cita => descripción] */
    private function opcionesCita(): array
    {
        return Cita::with(['cliente.usuario', 'servicio'])
            ->orderByDesc('fecha')
            ->limit(200)
            ->get()
            ->mapWithKeys(fn (Cita $c) => [
                $c->codigo_cita => "#{$c->codigo_cita} · "
                    . trim(($c->cliente?->usuario?->nombre ?? 'Cliente') . ' ' . ($c->cliente?->usuario?->apellido ?? ''))
                    . ' · ' . ($c->servicio?->nombre ?? 'Servicio')
                    . ' · ' . $c->fecha?->format('d/m/Y'),
            ])
            ->all();
    }
}
