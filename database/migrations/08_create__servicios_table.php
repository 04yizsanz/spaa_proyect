<?php

// Importa las clases necesarias de Laravel para crear y manejar la migración
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Crea una clase de migración anónima
return new class extends Migration
{
    /**
     * Ejecuta la migración.
     * Este método se utiliza para crear la tabla en la base de datos.
     */
    public function up(): void
    {
        // Crea una tabla llamada "servicios"
        Schema::create('servicios', function (Blueprint $table) {

            // Crea un ID único y autoincremental para cada servicio
            $table->id();

            // Guarda el nombre del servicio
            $table->string("nombre");

            // Guarda una descripción detallada del servicio
            $table->text("descripcion");

            // Guarda el precio del servicio como un número decimal
            $table->decimal("precio");

            // Guarda la duración del servicio como un número entero positivo
            $table->unsignedInteger("duracion");

            // Guarda el estado del servicio: verdadero (activo) o falso (inactivo)
            $table->boolean("estado");

            // Crea automáticamente las fechas created_at y updated_at
            $table->timestamps();
        });
    }

    /**
     * Revierte la migración.
     * Este método elimina la tabla cuando se deshace la migración.
     */
    public function down(): void
    {
        // Elimina la tabla "servicios" si existe
        Schema::dropIfExists("servicios");
    }
};