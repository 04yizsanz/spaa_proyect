<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * NOTA: Esta migración asume que la tabla `clientes` ya existe con PK
     * `cliente_id` (BIGINT UNSIGNED). Si aún no la has creado, crea esa
     * migración primero o esta fallará al construirse la FK.
     */
    public function up(): void
    {
        Schema::create('facturas', function (Blueprint $table) {
            $table->id('factura_id');

            $table->dateTime('fecha_hora');

            $table->decimal('subtotal', 10, 2); // >= 0
            $table->decimal('impuestos', 10, 2); // >= 0
            $table->decimal('total', 10, 2); // Total = Subtotal + Impuestos

            $table->string('pdf_url', 255)->nullable();

            // FK -> TCliente.ClienteId
            $table->foreignId('cliente_id')
                ->constrained('clientes', 'cliente_id')
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
        Schema::dropIfExists('facturas');
    }
};