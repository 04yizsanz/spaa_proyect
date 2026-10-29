<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\SoftDeletes;

    /**
     * Modelo Eloquent que representa un producto del inventario del sistema.
     *
     * Gestiona los artículos o insumos que forman parte del inventario,
     * incluyendo su disponibilidad en cantidad, precio unitario, fecha de
     * registro y el proveedor que los suministra. Cada movimiento de entrada
     * o salida del inventario queda registrado mediante {@see MovimientoInventario}.
     *
     * Relaciones definidas:
     *  - Pertenece a un {@see Proveedor} (belongsTo).
     *  - Posee múltiples movimientos de inventario {@see MovimientoInventario} (hasMany).
     *
     * @package App\Models
     *
     * @property int             $producto_id     Clave primaria autoincremental del producto.
     * @property string          $nombre          Nombre descriptivo del producto.
     * @property int             $cantidad        Unidades disponibles en el inventario.
     * @property float           $precio          Precio unitario del producto.
     * @property string          $fecha_registro  Fecha en que el producto fue dado de alta.
     * @property int             $proveedor_id    Clave foránea que vincula el producto con su proveedor.
     * @property \Carbon\Carbon|null $created_at  Marca temporal de creación gestionada por Eloquent.
     * @property \Carbon\Carbon|null $updated_at  Marca temporal de última actualización.
     * @property \Carbon\Carbon|null $deleted_at  Marca temporal de borrado lógico (SoftDeletes).
     */
    class Producto extends Model
    {
        /**
         * HasFactory habilita el uso del factory asociado a este modelo,
         * permitiendo la generación de instancias sintéticas durante las
         * pruebas automatizadas y la siembra de la base de datos mediante
         * Database\Factories\ProductoFactory.
         */
        use HasFactory; //Hasfactory se va a encargar de crear datos de prueba

        /**
         * SoftDeletes implementa el borrado lógico («soft delete»): en lugar
         * de eliminar físicamente el registro de la tabla, Eloquent establece
         * la columna `deleted_at` con la marca temporal actual. Los registros
         * marcados quedan excluidos de las consultas ordinarias, pero pueden
         * recuperarse con withTrashed() o restore(), preservando los datos
         * históricos de movimientos de inventario vinculados.
         */
       use SoftDeletes;

        // Nombre explícito de la tabla en la base de datos. Se declara en
        // singular («producto») en lugar del plural que Eloquent inferiría
        // por convención («productos»), reflejando el esquema real de la BD.
        protected $table="producto";

        // Clave primaria personalizada; sobreescribe el valor por defecto "id"
        // que Eloquent asumiría de no indicarse esta propiedad.
        protected $primaryKey = "producto_id";

        // $fillable lista los atributos que pueden asignarse de forma masiva
        // (mass assignment) mediante métodos como create() o fill(). Todos los
        // campos no incluidos aquí serán ignorados en asignaciones masivas,
        // protegiendo el modelo contra vulnerabilidades de tipo mass-assignment.
        protected $fillable = [ 
            "nombre",          // Nombre o denominación comercial del producto.
            "cantidad",        // Stock actual disponible en el inventario.
            "precio",          // Precio unitario de venta del producto.
            "fecha_registro",  // Fecha en que el producto fue incorporado al inventario.
            "proveedor_id",    // Referencia al proveedor que suministra el producto.
        ];

        /**
         * Obtiene el proveedor que suministra este producto.
         *
         * Define una relación de cardinalidad N:1: múltiples productos pueden
         * ser suministrados por un mismo proveedor. Eloquent infiere la clave
         * foránea como `proveedor_id` en la tabla `producto`, siguiendo la
         * convención de nomenclatura del framework.
         *
         * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
         */
            public function proveedor(){
                return $this->belongsTo(Proveedor::class);
            }

        /**
         * Obtiene los movimientos de inventario registrados para este producto.
         *
         * Define una relación de cardinalidad 1:N: un producto puede tener
         * múltiples movimientos de inventario (entradas y salidas). Eloquent
         * infiere la clave foránea como `producto_id` en la tabla
         * `movimientoinventario`, siguiendo la convención del framework.
         *
         * @return \Illuminate\Database\Eloquent\Relations\HasMany
         */
            public function movimiento_inventario()
        {
            return $this->hasMany(MovimientoInventario::class,);
        }
            
            // hasMany: muchos 
    // hasOne: relacion de 1 a 1 
    // belognsTo: tiene un 
            
    }
