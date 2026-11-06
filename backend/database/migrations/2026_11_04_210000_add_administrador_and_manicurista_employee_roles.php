<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const ROLES = [
        'estilista',
        'Estetisista',
        'recepcionista',
        'admin',
        'administrador',
        'manicurista',
    ];

    public function up(): void
    {
        Schema::table('empleados', function (Blueprint $table): void {
            $table->enum('rol', self::ROLES)->change();
        });
    }

    public function down(): void
    {
        if (DB::table('empleados')->whereIn('rol', ['administrador', 'manicurista'])->exists()) {
            throw new RuntimeException(
                'No se pueden quitar los roles administrador y manicurista mientras haya empleados con esos roles.'
            );
        }

        Schema::table('empleados', function (Blueprint $table): void {
            $table->enum('rol', [
                'estilista',
                'Estetisista',
                'recepcionista',
                'admin',
            ])->change();
        });
    }
};
