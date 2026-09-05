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
        Schema::create('servicios', function (Blueprint $table) {
            $table->id('servicio_id'); // BIGINT UNSIGNED, PK, autoincremental

            $table->string('nombre', 100);

            $table->unsignedSmallInteger('duracion_min'); // Debe ser > 0 (validar en Form Request)

            $table->decimal('precio', 10, 2); // Debe ser >= 0 (validar en Form Request)

            $table->text('descripcion')->nullable();

            $table->boolean('activo')->default(true); // Servicio disponible o no

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('servicios');
    }
};