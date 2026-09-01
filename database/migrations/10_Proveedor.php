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
        Schema::create('proveedor', function (Blueprint $table) {
            $table->id(proveedor_id);
            $table->string("nombre",100);
            $table->string("contacto",50)->nullable();
            $table->string("email",100)->nullable();
            $table->string("registro_tributario",20)->unique();
            $table->timestamps();

        });
    }

    /**c
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("proveedor");
    }
};
