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
        Schema::create('visualizaciones_estilos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('cliente_id')
                ->nullable()
                ->constrained('clientes')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->foreignId('servicio_id')
                ->nullable()
                ->constrained('servicios')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->string('imagen_original', 255);
            $table->string('resultado', 255)->nullable();
            $table->enum('estado', ['pendiente', 'procesado', 'error'])->default('pendiente');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visualizaciones_estilos');
    }
};