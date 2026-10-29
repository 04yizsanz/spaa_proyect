<?php

/**
 * ARCHIVO: app/Http/Controllers/Web/CitaWebController.php
 *
 * PROPÓSITO: Controlador de vistas Blade para el módulo de Citas del sistema
 * Asha Spa. Gestiona las peticiones HTTP que renderizarán respuestas en formato
 * HTML mediante el motor de plantillas Blade, a diferencia del CitaController
 * ubicado en App\Http\Controllers, que retorna respuestas JSON para la API.
 *
 * RESPONSABILIDADES:
 *   - Listar citas con filtros opcionales (index).
 *   - Mostrar el formulario de creación (create).
 *   - Persistir una nueva cita en la base de datos (store).
 *   - Mostrar el formulario de edición de una cita existente (edit).
 *   - Actualizar una cita existente en la base de datos (update).
 *
 * MANEJO DE ERRORES: Cada método que realiza consultas a la base de datos
 * está envuelto en un bloque try-catch que captura cualquier excepción de
 * tipo Throwable (clase base de todas las excepciones y errores de PHP).
 * Ante un error, el controlador activa el modo demostración ($modoDemo = true),
 * lo que permite que la vista se renderice con datos vacíos en lugar de
 * mostrar un error técnico al usuario.
 *
 * ACCESO: Las rutas de este controlador están protegidas por el middleware
 * 'auth', definido en routes/web.php, que redirige al usuario a /login
 * si no tiene una sesión activa.
 */

namespace App\Http\Controllers\Web;

// Importa la clase base Controller del espacio de nombres actual.
use App\Http\Controllers\Controller;

// Importa los modelos Eloquent necesarios para las consultas de este módulo.
use App\Models\Cita;
use App\Models\Cliente;
use App\Models\Empleado;
use App\Models\Servicio;
use App\Models\Usuario;

// Importa la clase Request de Laravel para acceder a los datos de la petición HTTP.
use Illuminate\Http\Request;

// Importa la clase Carbon, que extiende la clase DateTime nativa de PHP
// con métodos de manipulación y formato de fechas más convenientes.
use Illuminate\Support\Carbon;

// Importa la interfaz Throwable, que es implementada por todas las excepciones
// (Exception) y errores (Error) de PHP 7+. Permite capturar tanto errores
// de PHP como excepciones de la aplicación en un único bloque catch.
use Throwable;

/**
 * Clase CitaWebController
 *
 * Controlador Blade del módulo de gestión de citas. Todas las respuestas
 * de este controlador son vistas HTML renderizadas mediante view().
 */
class CitaWebController extends Controller
{
    /**
     * Muestra el listado de citas con filtros opcionales por fecha y estado.
     *
     * MÉTODO HTTP: GET
     * RUTA: /citas
     * NOMBRE: citas.index
     * VISTA RENDERIZADA: resources/views/appointments/index.blade.php
     *
     * FILTROS DISPONIBLES (parámetros de consulta en la URL):
     *   - fecha:  filtra las citas cuya columna 'fecha' coincide con la fecha indicada.
     *   - estado: filtra las citas cuya columna 'estado' coincide con el valor indicado.
     *
     * PAGINACIÓN: Los resultados se paginan en grupos de 15 registros mediante
     * el método paginate(15). El método withQueryString() preserva los parámetros
     * de filtro activos en los enlaces de paginación generados por Blade.
     *
     * @param Request $request Objeto que encapsula la petición HTTP actual.
     *        Permite acceder a los parámetros de consulta (query string),
     *        datos del formulario, cabeceras y otros datos de la petición.
     *
     * @return \Illuminate\View\View Vista Blade renderizada con las variables
     *         $citas, $filtroFecha, $filtroEstado y $modoDemo.
     */
    public function index(Request $request)
    {
        // $request->query('fecha'): lee el parámetro 'fecha' de la URL.
        // Retorna null si el parámetro no está presente en la petición.
        $filtroFecha  = $request->query('fecha');

        // $request->query('estado'): lee el parámetro 'estado' de la URL.
        // Retorna null si el parámetro no está presente en la petición.
        $filtroEstado = $request->query('estado');

        // El bloque try inicia el intento de ejecución. Si cualquier instrucción
        // dentro de este bloque lanza una excepción o un error de PHP, la
        // ejecución salta inmediatamente al bloque catch.
        try {
            // Cita::with([...]): construye una consulta Eloquent con carga ansiosa
            // (eager loading) de las relaciones indicadas, lo que evita el problema
            // N+1 de consultas al precargar los datos relacionados en una sola consulta.
            // Se cargan: cliente y su usuario asociado, servicio y empleado.
            // ->orderBy('fecha', 'desc'): ordena los resultados de mayor a menor fecha.
            // ->orderBy('hora', 'asc'): dentro de la misma fecha, ordena por hora ascendente.
            $query = Cita::with(['cliente.usuario', 'servicio', 'empleado'])
                ->orderBy('fecha', 'desc')
                ->orderBy('hora', 'asc');

            // Bloque condicional: aplica el filtro de fecha solo si el parámetro
            // está presente y tiene un valor no vacío.
            if ($filtroFecha) {
                // whereDate(): añade una cláusula WHERE que compara únicamente
                // la parte de la fecha (sin la hora) de la columna 'fecha'.
                $query->whereDate('fecha', $filtroFecha);
            }

            // Arreglo que define los valores válidos para el filtro de estado.
            $estadosValidos = ['pendiente', 'confirmada', 'completada', 'cancelada'];

            // Bloque condicional: aplica el filtro de estado solo si el parámetro
            // está presente y su valor es uno de los estados válidos.
            // in_array() verifica si $filtroEstado existe dentro del arreglo $estadosValidos.
            if ($filtroEstado && in_array($filtroEstado, $estadosValidos)) {
                // where(): añade una cláusula WHERE exacta sobre la columna 'estado'.
                $query->where('estado', $filtroEstado);
            }

            // paginate(15): ejecuta la consulta SQL y retorna un objeto LengthAwarePaginator
            // que contiene los primeros 15 resultados de la página actual.
            // withQueryString(): incluye los parámetros de la URL actual en los
            // enlaces de paginación, preservando los filtros activos.
            $citas    = $query->paginate(15)->withQueryString();

            // $modoDemo = false: indica a la vista que los datos son reales y
            // no deben mostrarse con indicaciones de modo demostración.
            $modoDemo = false;

        } catch (Throwable $e) {
            // Si ocurre cualquier error (por ejemplo, la tabla no existe o
            // hay un problema de conexión), se utilizan datos vacíos.

            // collect([]): crea una colección Eloquent vacía que la vista puede
            // recorrer sin causar errores adicionales.
            $citas    = collect([]);

            // $modoDemo = true: indica a la vista que active el aviso de demostración.
            $modoDemo = true;
        }

        // view(): retorna una instancia de View que Blade renderizará.
        // Primer argumento: ruta de la vista relativa al directorio resources/views.
        // compact(): crea un arreglo asociativo a partir de las variables locales
        // con los nombres indicados como cadenas de texto. Es equivalente a:
        // ['citas' => $citas, 'filtroFecha' => $filtroFecha, ...]
        return view('appointments.index', compact(
            'citas',
            'filtroFecha',
            'filtroEstado',
            'modoDemo'
        ));
    }

    /**
     * Muestra el formulario de creación de una nueva cita.
     *
     * MÉTODO HTTP: GET
     * RUTA: /citas/nueva
     * NOMBRE: citas.create
     * VISTA RENDERIZADA: resources/views/appointments/create.blade.php
     *
     * PROPÓSITO: Carga los datos necesarios para poblar los selectores del
     * formulario (clientes, empleados disponibles y servicios activos) y
     * los envía a la vista para que el usuario pueda seleccionarlos.
     *
     * @return \Illuminate\View\View Vista Blade con las variables
     *         $clientes, $empleados, $servicios y $modoDemo.
     */
    public function create()
    {
        try {
            // Cliente::with('usuario'): carga todos los clientes con su
            // usuario asociado para poder mostrar nombre y apellido en el selector.
            $clientes  = Cliente::with('usuario')->get();

            // Empleado::where('disponibilidad', true): filtra solo los empleados
            // con disponibilidad activa. orderBy('nombre'): ordena alfabéticamente.
            $empleados = Empleado::where('disponibilidad', true)->orderBy('nombre')->get();

            // Servicio::where('activo', true): filtra solo los servicios activos.
            $servicios = Servicio::where('activo', true)->orderBy('nombre')->get();

            $modoDemo  = false;
        } catch (Throwable $e) {
            // Ante cualquier error, se proveen colecciones vacías para que
            // la vista pueda renderizarse sin generar errores adicionales.
            $clientes  = collect([]);
            $empleados = collect([]);
            $servicios = collect([]);
            $modoDemo  = true;
        }

        return view('appointments.create', compact('clientes', 'empleados', 'servicios', 'modoDemo'));
    }

    /**
     * Valida y persiste una nueva cita en la base de datos.
     *
     * MÉTODO HTTP: POST
     * RUTA: /citas
     * NOMBRE: citas.store
     *
     * VALIDACIÓN: El método validate() de Laravel verifica que los datos
     * del formulario cumplan las reglas definidas. Si la validación falla,
     * Laravel redirige automáticamente al formulario anterior con los errores
     * de validación disponibles en la variable $errors de Blade.
     *
     * @param Request $request Petición HTTP que contiene los datos del formulario.
     *
     * @return \Illuminate\Http\RedirectResponse Redirección a la lista de citas
     *         con un mensaje de éxito en la sesión flash.
     */
    public function store(Request $request)
    {
        // validate(): valida los datos del formulario contra el conjunto de reglas.
        // Si la validación pasa, retorna un arreglo con los datos validados.
        // Si falla, lanza una ValidationException y Laravel redirige automáticamente.
        $validated = $request->validate([
            // 'required': el campo no puede estar vacío.
            // 'integer': el valor debe ser un número entero.
            // 'unique:citas,codigo_cita': el valor no debe existir en la columna
            //   'codigo_cita' de la tabla 'citas' (garantiza unicidad del código).
            'codigo_cita' => ['required', 'integer', 'unique:citas,codigo_cita'],

            // 'date': el valor debe ser una fecha válida reconocida por PHP.
            'fecha'       => ['required', 'date'],

            // 'date_format:H:i': el valor debe tener exactamente el formato hora:minuto.
            'hora'        => ['required', 'date_format:H:i'],

            // 'nullable': el campo puede enviarse vacío o no enviarse.
            // 'in:...': el valor debe ser uno de los enumerados.
            'estado'      => ['nullable', 'in:pendiente,confirmada,completada,cancelada'],

            // 'exists:clientes,cliente_id': el valor debe existir como clave
            // primaria en la tabla 'clientes' (integridad referencial).
            'cliente_id'  => ['required', 'integer', 'exists:clientes,cliente_id'],
            'empleado_id' => ['required', 'integer', 'exists:empleados,empleado_id'],
            'servicio_id' => ['required', 'integer', 'exists:servicios,servicio_id'],
        ], [
            // Arreglo de mensajes de error personalizados que reemplazan
            // los mensajes predeterminados de Laravel para cada regla.
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

        // Si el campo 'estado' no fue enviado en el formulario, se asigna
        // el valor por defecto 'pendiente' usando el operador de coalescencia
        // nula ??. Si $validated['estado'] existe y no es null, se mantiene su valor.
        $validated['estado'] = $validated['estado'] ?? 'pendiente';

        // Cita::create(): ejecuta un INSERT en la tabla 'citas' con los datos
        // del arreglo $validated. Solo persiste los campos declarados en el
        // atributo $fillable del modelo Cita.
        Cita::create($validated);

        // redirect()->route(): genera una respuesta de redirección HTTP 302
        // hacia la ruta nombrada 'citas.index'.
        // ->with('success', ...): almacena un mensaje en la sesión flash,
        // disponible en la siguiente petición a través de session('success').
        return redirect()
            ->route('citas.index')
            ->with('success', 'La cita se ha guardado correctamente.');
    }

    /**
     * Muestra el formulario de edición de una cita existente.
     *
     * MÉTODO HTTP: GET
     * RUTA: /citas/{id}/editar
     * NOMBRE: citas.edit
     * VISTA RENDERIZADA: resources/views/appointments/edit.blade.php
     *
     * @param int $id Identificador numérico de la cita a editar,
     *        extraído del segmento de ruta por Laravel.
     *
     * @return \Illuminate\View\View Vista con los datos de la cita a editar,
     *         o redirección al listado si la cita no existe.
     */
    public function edit(int $id)
    {
        try {
            // findOrFail(): busca la cita por su clave primaria. Si no la encuentra,
            // lanza una excepción ModelNotFoundException que es capturada por el
            // bloque catch y resulta en una redirección con mensaje de error.
            $cita      = Cita::findOrFail($id);

            $clientes  = Cliente::with('usuario')->get();

            // Para la edición se listan todos los empleados, no solo los disponibles,
            // ya que puede que el empleado original esté marcado como no disponible.
            $empleados = Empleado::orderBy('nombre')->get();
            $servicios = Servicio::where('activo', true)->orderBy('nombre')->get();
            $modoDemo  = false;
        } catch (Throwable $e) {
            // Si la cita no existe o hay un error de base de datos, se redirige
            // al listado con un mensaje de error en la sesión flash.
            return redirect()
                ->route('citas.index')
                ->with('error', 'No se encontró la cita solicitada.');
        }

        return view('appointments.edit', compact('cita', 'clientes', 'empleados', 'servicios', 'modoDemo'));
    }

    /**
     * Valida y actualiza una cita existente en la base de datos.
     *
     * MÉTODO HTTP: PUT
     * RUTA: /citas/{id}
     * NOMBRE: citas.update
     *
     * @param Request $request Petición HTTP con los datos del formulario de edición.
     * @param int $id Identificador numérico de la cita a actualizar.
     *
     * @return \Illuminate\Http\RedirectResponse Redirección al listado con mensaje
     *         de confirmación, o respuesta 404 si la cita no existe.
     */
    public function update(Request $request, int $id)
    {
        // findOrFail(): lanza ModelNotFoundException si la cita no existe.
        // Esta excepción no está capturada en este método, por lo que Laravel
        // la manejará según su configuración de excepciones (respuesta 404).
        $cita = Cita::findOrFail($id);

        $validated = $request->validate([
            'codigo_cita' => [
                'required',
                'integer',
                // La regla unique excluye el registro actual de la verificación
                // de unicidad para permitir que se guarde con el mismo código.
                // Formato: 'unique:tabla,columna,id_a_ignorar,nombre_columna_pk'
                'unique:citas,codigo_cita,' . $id . ',citas_id',
            ],
            'fecha'       => ['required', 'date'],
            'hora'        => ['required', 'date_format:H:i'],

            // 'required' en lugar de 'nullable': el estado es obligatorio en
            // la edición porque la cita ya tiene un estado previo.
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

        // $cita->update(): ejecuta un UPDATE en la base de datos sobre el
        // registro representado por la instancia $cita, aplicando únicamente
        // los campos del arreglo $validated que están en el $fillable del modelo.
        $cita->update($validated);

        return redirect()
            ->route('citas.index')
            ->with('success', 'La cita se ha actualizado correctamente.');
    }
}
