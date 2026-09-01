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

    $table->unsignedBigInteger('cliente_id');
    $table->unsignedBigInteger('empleado_id');
    $table->unsignedBigInteger('servicio_id');

    $table->timestamps();
});

    }


    public function down(): void
    {
        Schema::dropIfExists('citas');
    }
};