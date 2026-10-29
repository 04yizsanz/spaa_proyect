<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Modelo Eloquent que representa a un proveedor de productos del sistema.
 *
 * Un proveedor es la entidad externa (persona física o jurídica) que
 * suministra los productos gestionados en el inventario. Almacena los
 * datos de contacto y el registro tributario necesarios para la
 * trazabilidad fiscal y comercial.
 *
 * Relaciones definidas:
 *  - Posee múltiples productos {@see Producto} (hasMany).
 *
 * @package App\Models
 *
 * @property int             $proveedor_id        Clave primaria autoincremental del proveedor.
 * @property string          $nombre              Nombre o razón social del proveedor.
 * @property string          $contacto            Nombre de la persona de contacto.
 * @property string          $email               Correo electrónico del proveedor.
 * @property string          $registro_tributario Número de identificación fiscal o tributaria.
 * @property \Carbon\Carbon|null $created_at      Marca temporal de creación gestionada por Eloquent.
 * @property \Carbon\Carbon|null $updated_at      Marca temporal de última actualización.
 * @property \Carbon\Carbon|null $deleted_at      Marca temporal de borrado lógico (SoftDeletes).
 */
class Proveedor extends Model
{
    /**
     * HasFactory habilita el uso del factory asociado a este modelo,
     * permitiendo la generación de instancias sintéticas durante las
     * pruebas automatizadas y la siembra de la base de datos mediante
     * Database\Factories\ProveedorFactory.
     */
     use HasFactory; //Hasfactory se va a encargar de crear datos de prueba

    /**
     * SoftDeletes implementa el borrado lógico («soft delete»): en lugar
     * de eliminar físicamente el registro de la tabla, Eloquent establece
     * la columna `deleted_at` con la marca temporal actual. Los registros
     * marcados quedan excluidos de las consultas ordinarias, pero pueden
     * recuperarse con withTrashed() o restore(), preservando la integridad
     * referencial con los productos asociados.
     */
    use SoftDeletes;

    // Nombre explícito de la tabla en la base de datos. Se declara en
    // singular («proveedor») en lugar del plural que Eloquent inferiría
    // por convención («proveedors»), reflejando el esquema real de la BD.
     protected $table="proveedor";

    // Clave primaria personalizada; sobreescribe el valor por defecto "id"
    // que Eloquent asumiría de no indicarse esta propiedad.
     protected $primaryKey = "proveedor_id";

    // $fillable lista los atributos que pueden asignarse de forma masiva
    // (mass assignment) mediante métodos como create() o fill(). Todos los
    // campos no incluidos aquí serán ignorados en asignaciones masivas,
    // protegiendo el modelo contra vulnerabilidades de tipo mass-assignment.
     protected $fillable = [ 
        "nombre",              // Razón social o nombre comercial del proveedor.
        "contacto",            // Persona o área de contacto en la empresa proveedora.
        "email",               // Dirección de correo electrónico de contacto.
        "registro_tributario", // Identificador fiscal o registro tributario del proveedor.
     ];

    /**
     * Obtiene los productos suministrados por este proveedor.
     *
     * Define una relación de cardinalidad 1:N: un proveedor puede
     * abastecer múltiples productos. Eloquent infiere la clave foránea
     * como `proveedor_id` en la tabla `producto`, siguiendo la convención
     * de nomenclatura del framework.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
         public function producto(){
            return $this->hasMany(Producto::class);
         }

         
}
