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
        Schema::create('cita', function (Blueprint $table) {
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
                ->constrained(); // FK -> clientes.id (TCliente.ClienteId)

            $table->foreignId('empleado_id')
                ->constrained(); // FK -> empleados.empleado_id (TEmpleado.EmpleadoId)
                // Regla de negocio: debe estar disponible en fecha/hora (validar en Service/Request)

            $table->foreignId('servicio_id')
                ->constrained(); // FK -> servicios.servicio_id (TServicio.ServicioId)

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cita');
    }
};