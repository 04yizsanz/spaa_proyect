
1 de 726
trabajo (trabajo)
Recibidos

cristian Paez
Archivos adjuntos
9:52 p.m. (hace 31 minutos)
para mí

Traducido: inglés
español
El Traductor puede cometer errores, así que verifica las traducciones

 29 archivos adjuntos
  •  Analizado por Gmail
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
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
                ->constrained('clientes', 'cliente_id')
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
            $table->softDeletes();
            
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('citas');
    }
};