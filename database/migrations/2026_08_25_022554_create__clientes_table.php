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
        // Crea una tabla llamada "Clientes"
        Schema::create('Clientes', function (Blueprint $table) {

            // Crea un ID único y autoincremental para cada cliente
            $table->id();

            // Crea una clave foránea que relaciona el cliente con un usuario
            $table->foreignId("usuario_id");

            // Guarda la fecha de nacimiento del cliente
            $table->date("fecha_nacimiento");

            // Guarda las preferencias del cliente
            $table->text("preferencias");

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
        // Elimina la tabla "clientes" si existe
        Schema::dropIfExists("clientes");
    }
};