<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('empleados', function (Blueprint $table) {
            $table->id('empleado_id'); // BIGINT UNSIGNED, PK, autoincremental

            $table->string('nombre', 50);
            $table->string('apellido', 50);

            $table->string('documento', 20)->unique(); // Documento de identidad, único

            $table->string('correo', 100)->nullable();
            $table->string('telefono', 20)->nullable();

            $table->enum('rol', [
                'estilista',
                'Estetisista',
                'recepcionista',
                'admin',
            ]); // AMBIGUO: catálogo de roles pendiente de confirmar con negocio

            $table->decimal('salario', 10, 2); // Debe ser > 0 (validar en Form Request)

            $table->date('fecha_contratacion'); // Debe ser <= fecha actual (validar en Form Request)

            $table->boolean('disponibilidad')->default(true)->nullable();
            // AMBIGUO: formato pendiente de confirmar. Se usó boolean simple
            // (disponible / no disponible). Si se requiere indicar "cuándo"
            // (días/horarios), reemplazar por $table->json('disponibilidad')->nullable()
            // o mover esa lógica a una tabla relacionada tipo `horarios`.

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empleados');
    }
};