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
        Schema::create('predicciones_ia', function (Blueprint $table) {
            $table->id();

            $table->foreignId('servicio_id')
                ->nullable()
                ->constrained('servicios')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->string('tipo_prediccion', 100);
            $table->date('periodo_inicio');
            $table->date('periodo_fin');
            $table->decimal('valor_predicho', 12, 2);
            $table->text('recomendacion')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('predicciones_ia');
    }
};