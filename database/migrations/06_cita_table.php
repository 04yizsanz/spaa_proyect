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
        Schema::create('citas', function (Blueprint $table) {
            $table->id('codigo_cita'); // BIGINT UNSIGNED, PK, autoincremental

            $table->date('fecha'); // Debe ser >= fecha actual al crearse (validar en Form Request)
            $table->time('hora'); // Debe estar dentro del horario del spa (validar en Form Request)

            $table->enum('estado', [
                'pendiente',
                'confirmada',
                'completada',
                'cancelada',
            ])->default('pendiente'); // AMBIGUO: catálogo de estados pendiente de confirmar

            $table->foreignId('cliente_id')
                ->constrained('clientes')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreignId('empleado_id')
                ->constrained('empleados', 'empleado_id')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreignId('servicio_id')
                ->constrained('servicios', 'servicio_id')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('citas');
    }
};