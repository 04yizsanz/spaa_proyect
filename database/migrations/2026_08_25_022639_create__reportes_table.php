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
        Schema::create('reportes', function (Blueprint $table) {
            $table->id();
            $table->foreignld("usuario_id");
            $table->enum("tipo");
            $table->date("fecha_inicio");
            $table->date("fecha_fin");
            $table->json("parametros");
            $table->datetime("generado_en");
            $table->timestamps();


        });
    }

    /**
     * Reverse the migrations..
     */
    public function down(): void
    {
      Schema::dropIfExists("reportes");
    }
};
