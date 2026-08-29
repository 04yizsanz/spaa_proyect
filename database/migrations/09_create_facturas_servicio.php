<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Tabla intermedia (débil) que resuelve la relación N:M entre
     * `facturas` y `servicios`. Requiere que `facturas` (creada en esta
     * misma tanda) y `servicios` (PK `servicio_id`) ya existan.
     */
    public function up(): void
    {
        Schema::create('factura_servicio', function (Blueprint $table) {
            // PK compuesta -> FacturaId + ServicioId
            $table->foreignId('factura_id')
                ->constrained('facturas', 'factura_id')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('servicio_id')
                ->constrained('servicios', 'servicio_id')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->primary(['factura_id', 'servicio_id']);

            $table->unsignedSmallInteger('cantidad'); // > 0

            // Se guarda aparte del precio actual del servicio para no perder histórico
            $table->decimal('precio_unitario', 10, 2); // >= 0

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('factura_servicio');
    }
};