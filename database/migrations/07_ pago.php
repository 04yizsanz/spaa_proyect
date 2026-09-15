<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * NOTA: Esta migración asume que la tabla `citas` ya existe con PK
     * `codigo_cita` (BIGINT UNSIGNED). Si aún no la has creado, crea esa
     * migración primero o esta fallará al construirse la FK.
     */
    public function up(): void
    {
        Schema::create('pagos', function (Blueprint $table) {
            $table->id('pago_id');

            $table->decimal('monto', 10, 2); // > 0

            // AMBIGUO en el diccionario: confirmar catálogo exacto de métodos de pago
            $table->enum('metodo', ['efectivo', 'tarjeta', 'transferencia', 'pse']);

            $table->dateTime('fecha_hora');

            // AMBIGUO en el diccionario: confirmar catálogo exacto de estados
            $table->enum('estado', ['pendiente', 'aprobado', 'rechazado'])
                ->default('pendiente');

            // FK -> TCita.CodigoCita (1:N sugerido; definir si admite pagos parciales)
            $table->foreignId('codigo_cita')
                ->constrained('citas', 'codigo_cita')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
}; 