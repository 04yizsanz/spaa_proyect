<?php

/**
 * ARCHIVO: app/Http/Controllers/UsuarioController.php
 *
 * PROPÓSITO: Controlador HTTP de la API RESTful para el recurso Usuario.
 * Gestiona las peticiones HTTP entrantes relacionadas con el módulo de
 * usuarios, valida los datos de entrada mediante Form Requests y delega
 * toda la lógica de negocio al servicio UsuarioService.
 *
 * PATRÓN DE DISEÑO: Este controlador forma parte de la arquitectura en capas
 * del proyecto, siguiendo el patrón Repository-Service-Controller:
 *   - UsuarioController: recibe y responde peticiones HTTP.
 *   - UsuarioService: contiene la lógica de negocio.
 *   - UsuarioRepository: abstrae el acceso a datos.
 *
 * RESPUESTAS: Todos los métodos retornan instancias de JsonResponse,
 * que Laravel serializa automáticamente a respuestas HTTP con cabecera
 * Content-Type: application/json.
 */

namespace App\Http\Controllers;

// Importa las clases de validación de formulario para las operaciones
// de creación y actualización. Cada clase define las reglas de validación
// específicas para su operación y el método authorize() que controla el acceso.
use App\Http\Requests\Usuario\StoreUsuarioRequest;
use App\Http\Requests\Usuario\UpdateUsuarioRequest;

// Importa el servicio que encapsula la lógica de negocio del módulo Usuario.
// UsuarioService actúa como intermediario entre el controlador y el repositorio.
use App\Services\UsuarioService;

// Importa el tipo JsonResponse de Symfony, que es la clase concreta que
// retorna el método response()->json() de Laravel. Se declara en las
// firmas de los métodos para mejorar la legibilidad y la verificación de tipos.
use Illuminate\Http\JsonResponse;

/**
 * Clase UsuarioController
 *
 * Controlador de la API REST para el módulo de usuarios del sistema Asha Spa.
 * Expone los endpoints necesarios para las operaciones CRUD sobre la entidad
 * Usuario, así como consultas filtradas por rol, estado y nombre.
 */
class UsuarioController extends Controller
{
    /**
     * Constructor de la clase UsuarioController.
     *
     * PROPÓSITO: Inyecta la dependencia del servicio UsuarioService mediante
     * el mecanismo de inyección de dependencias del contenedor de servicios
     * de Laravel. Al declarar el parámetro con su tipo, Laravel resuelve
     * automáticamente la instancia correcta del servicio al instanciar
     * el controlador.
     *
     * La palabra reservada 'protected' indica que la propiedad $usuarioService
     * solo es accesible desde esta clase y sus subclases.
     * La palabra reservada 'readonly' (implícita en la sintaxis de promoción
     * de propiedades de PHP 8.x) garantiza que la propiedad no pueda ser
     * reasignada después de la construcción del objeto.
     *
     * @param UsuarioService $usuarioService Instancia del servicio de usuarios
     *        resuelta automáticamente por el contenedor de Laravel.
     */
    public function __construct(
        protected UsuarioService $usuarioService
    ) {}

    /**
     * Retorna todos los usuarios registrados en el sistema.
     *
     * MÉTODO HTTP: GET
     * RUTA: /api/usuarios
     * NOMBRE: api.usuarios.index
     *
     * PROCESAMIENTO: Invoca el método getAll() del servicio, que a su vez
     * llama al repositorio para obtener todos los registros de la tabla
     * 'usuarios' mediante el método all() de Eloquent.
     *
     * @return JsonResponse Respuesta HTTP 200 con un arreglo JSON que contiene
     *         todos los registros de usuarios. Los campos declarados en $hidden
     *         del modelo (password, remember_token) son excluidos
     *         automáticamente por Eloquent en la serialización.
     */
    public function index(): JsonResponse
    {
        // $this->usuarioService->getAll() invoca el servicio para obtener
        // la colección completa de usuarios desde la base de datos.
        // response()->json() convierte el resultado a una respuesta HTTP
        // con código de estado 200 (OK) y Content-Type: application/json.
        return response()->json($this->usuarioService->getAll());
    }

    /**
     * Retorna un usuario específico identificado por su clave primaria.
     *
     * MÉTODO HTTP: GET
     * RUTA: /api/usuarios/{id}
     * NOMBRE: api.usuarios.show
     *
     * PROCESAMIENTO: Busca el usuario por su identificador numérico.
     * Si no existe, retorna un error 404. Si existe, retorna sus datos.
     *
     * @param int $id Identificador numérico del usuario a consultar,
     *        extraído del segmento de ruta correspondiente por Laravel.
     *
     * @return JsonResponse Respuesta HTTP 200 con los datos del usuario,
     *         o respuesta HTTP 404 con un mensaje de error si no se encuentra.
     */
    public function show(int $id): JsonResponse
    {
        // Invoca el servicio para buscar el usuario por su identificador.
        // El servicio delega al repositorio, que utiliza el método find()
        // de Eloquent y retorna null si no encuentra el registro.
        $usuario = $this->usuarioService->getById($id);

        // Estructura condicional if que evalúa si la variable $usuario
        // es null (el operador ! niega el valor booleano del resultado).
        // Si $usuario es null, la condición es verdadera y se ejecuta el bloque.
        if (! $usuario) {
            // Retorna una respuesta HTTP 404 (Not Found) con un mensaje
            // descriptivo en formato JSON. El segundo argumento de json()
            // establece el código de estado HTTP de la respuesta.
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        // Si el usuario existe, retorna sus datos con código de estado 200.
        return response()->json($usuario);
    }

    /**
     * Crea y persiste un nuevo usuario en la base de datos.
     *
     * MÉTODO HTTP: POST
     * RUTA: /api/usuarios
     * NOMBRE: api.usuarios.store
     *
     * VALIDACIÓN: El parámetro $request es una instancia de StoreUsuarioRequest,
     * una clase Form Request que valida automáticamente la petición antes de
     * que el método sea ejecutado. Si la validación falla, Laravel retorna
     * una respuesta 422 (Unprocessable Entity) con los errores de validación
     * sin llegar a ejecutar el cuerpo de este método.
     *
     * @param StoreUsuarioRequest $request Objeto que encapsula la petición HTTP
     *        y contiene los datos validados del nuevo usuario.
     *
     * @return JsonResponse Respuesta HTTP 201 (Created) con los datos del
     *         usuario recién creado, incluyendo su clave primaria generada.
     */
    public function store(StoreUsuarioRequest $request): JsonResponse
    {
        // $request->validated() retorna únicamente los campos que pasaron
        // las reglas de validación definidas en StoreUsuarioRequest, como
        // un arreglo asociativo. Los campos no declarados en las reglas
        // son descartados automáticamente.
        // El servicio recibe este arreglo y lo pasa al repositorio, que
        // crea el registro en la base de datos y retorna la instancia creada.
        $usuario = $this->usuarioService->create($request->validated());

        // Retorna el usuario creado con código 201 (Created), que es el
        // código HTTP estándar para indicar que un recurso fue creado exitosamente.
        return response()->json($usuario, 201);
    }

    /**
     * Actualiza los datos de un usuario existente.
     *
     * MÉTODO HTTP: PUT / PATCH
     * RUTA: /api/usuarios/{id}
     * NOMBRE: api.usuarios.update
     *
     * VALIDACIÓN: El parámetro $request es una instancia de UpdateUsuarioRequest
     * que valida los datos de actualización antes de ejecutar el método.
     *
     * @param UpdateUsuarioRequest $request Objeto con los datos validados
     *        del usuario a actualizar.
     * @param int $id Identificador numérico del usuario a modificar.
     *
     * @return JsonResponse Respuesta HTTP 200 con mensaje de confirmación,
     *         o respuesta HTTP 404 si el usuario no existe.
     */
    public function update(UpdateUsuarioRequest $request, int $id): JsonResponse
    {
        // Invoca el servicio con los datos validados y el identificador.
        // El servicio verifica la existencia del usuario antes de actualizar.
        // Retorna null si el usuario no existe, o la instancia actualizada si existe.
        $updated = $this->usuarioService->update($id, $request->validated());

        // Si el servicio retorna null, el usuario no fue encontrado.
        if (! $updated) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        // Retorna un mensaje de confirmación con código 200 (OK).
        return response()->json(['message' => 'Usuario actualizado correctamente']);
    }

    /**
     * Elimina un usuario de la base de datos.
     *
     * MÉTODO HTTP: DELETE
     * RUTA: /api/usuarios/{id}
     * NOMBRE: api.usuarios.destroy
     *
     * @param int $id Identificador numérico del usuario a eliminar.
     *
     * @return JsonResponse Respuesta HTTP 200 con mensaje de confirmación,
     *         o respuesta HTTP 404 si el usuario no existe.
     */
    public function destroy(int $id): JsonResponse
    {
        // Invoca el servicio para eliminar el usuario. El servicio verifica
        // la existencia antes de delegar al repositorio.
        // Retorna null si no existe, o true si la eliminación fue exitosa.
        $deleted = $this->usuarioService->delete($id);

        // Evalúa si la eliminación fue exitosa.
        if (! $deleted) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        // Retorna confirmación de eliminación con código 200 (OK).
        return response()->json(['message' => 'Usuario eliminado correctamente']);
    }

    /**
     * Retorna todos los usuarios que tienen asignado un rol específico.
     *
     * MÉTODO HTTP: GET
     * RUTA: Debe registrarse manualmente en routes/api.php (no incluida
     *       en el apiResource estándar).
     *
     * @param int $rolId Identificador del rol por el que se filtran los usuarios.
     *
     * @return JsonResponse Respuesta HTTP 200 con la colección de usuarios
     *         que pertenecen al rol indicado. Puede retornar una colección vacía
     *         si ningún usuario tiene ese rol.
     */
    public function porRol(int $rolId): JsonResponse
    {
        // Delega al servicio la consulta filtrada por rol.
        return response()->json($this->usuarioService->getByRol($rolId));
    }

    /**
     * Retorna todos los usuarios filtrados por su estado (activo o inactivo).
     *
     * MÉTODO HTTP: GET
     * RUTA: Debe registrarse manualmente en routes/api.php.
     *
     * @param bool $estado Valor booleano que indica el estado a filtrar:
     *        true para usuarios activos, false para inactivos.
     *
     * @return JsonResponse Respuesta HTTP 200 con la colección de usuarios
     *         cuyo campo 'estado' coincide con el valor proporcionado.
     */
    public function porEstatus(bool $estado): JsonResponse
    {
        // Delega al servicio la consulta filtrada por estado booleano.
        return response()->json($this->usuarioService->getByEstatus($estado));
    }

    /**
     * Retorna todos los usuarios cuyo nombre coincide exactamente con el valor indicado.
     *
     * MÉTODO HTTP: GET
     * RUTA: Debe registrarse manualmente en routes/api.php.
     *
     * LIMITACIÓN: La búsqueda es exacta (WHERE nombre = ?), no parcial.
     * No admite búsquedas por coincidencia parcial ni insensibles a mayúsculas
     * salvo que el motor de base de datos lo aplique por configuración.
     *
     * @param string $nombre Nombre exacto por el que se filtra la búsqueda.
     *
     * @return JsonResponse Respuesta HTTP 200 con la colección de usuarios
     *         cuyo campo 'nombre' coincide con el valor proporcionado.
     */
    public function porNombre(string $nombre): JsonResponse
    {
        // Delega al servicio la consulta filtrada por nombre.
        return response()->json($this->usuarioService->getByNombre($nombre));
    }
}
