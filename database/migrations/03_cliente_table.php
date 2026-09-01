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
        Schema::create('clientes', function (Blueprint $table) {
            $table->id('cliente_id'); // BIGINT UNSIGNED, PK, autoincremental

            $table->foreignId('usuario_id')
                ->unique()
                ->constrained('usuarios', 'usuario_id')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->date('fecha_nacimiento')->nullable();
            $table->text('preferencias')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};