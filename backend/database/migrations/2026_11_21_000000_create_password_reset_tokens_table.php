<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tabla de tokens para recuperar la contraseña ("olvidé mi contraseña").
 *
 * - email      : correo de la cuenta que pidió el enlace (uno por cuenta).
 * - token      : el token se guarda CIFRADO (hash); el original solo viaja en el correo.
 * - created_at : sirve para que el enlace caduque (60 minutos, config/auth.php).
 *
 * El nombre y las columnas son los que espera el "password broker" de Laravel.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email', 150)->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('password_reset_tokens');
    }
};
