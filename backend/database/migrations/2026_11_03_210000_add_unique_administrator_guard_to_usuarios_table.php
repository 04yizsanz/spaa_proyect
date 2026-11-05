<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $administradorId = DB::table('roles')
            ->where('nombre', 'Administrador')
            ->value('rol_id');

        if ($administradorId !== null && DB::table('usuarios')->where('rol_id', $administradorId)->count() > 1) {
            throw new \RuntimeException(
                'Hay más de un usuario administrador. Conserva uno antes de ejecutar esta migración.'
            );
        }

        Schema::table('usuarios', function (Blueprint $table) {
            $table->unsignedTinyInteger('administrador_unico')->nullable();
        });

        if ($administradorId !== null) {
            DB::table('usuarios')
                ->where('rol_id', $administradorId)
                ->update(['administrador_unico' => 1]);
        }

        Schema::table('usuarios', function (Blueprint $table) {
            $table->unique('administrador_unico', 'usuarios_administrador_unico_unique');
        });
    }

    public function down(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->dropUnique('usuarios_administrador_unico_unique');
            $table->dropColumn('administrador_unico');
        });
    }
};
