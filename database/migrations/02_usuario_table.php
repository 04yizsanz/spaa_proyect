<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.erfqesfesffddf
     */
    public function up(): void
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id('usuario_id'); // BIGINT UNSIGNED, PK, autoincremental

            $table->foreignId('rol_id') // rol_id
                ->constrained('roles', 'rol_id')
                ->onUpdate('cascade')
                ->onDelete('restrict');

            $table->string('nombre', 80);
            $table->string('apellido', 80);
            $table->string('documento', 20)->unique();
            $table->string('email', 150)->unique();
            $table->string('telefono', 20)->nullable();
            $table->string('password', 255);
            $table->boolean('estado');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};