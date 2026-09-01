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
    $table->id('citas_id');
    $table->unsignedBigInteger('codigo_cita')->unique();

    $table->date('fecha');
    $table->time('hora');

    $table->enum('estado', [
        'pendiente',
        'confirmada',
        'completada',
        'cancelada'
    ])->default('pendiente');

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


    public function down(): void
    {
        Schema::dropIfExists('citas');
    }
};