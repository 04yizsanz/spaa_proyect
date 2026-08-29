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
        Schema::create('movimientoinventario', function (Blueprint $table) {
            $table->id();
            $table->enum('tipo',['entrada','salida']);
            $table->unsignedInteger('cantidad');
            $table->dateTime('fecha_hora');
           $table->string('motivo',100)->nullable();
           $table->unsignedInteger('producto_id');
            $table->timestamps();

            $table->foreign('producto_id')->references('id')->on('producto')->onDelete('cascade');
        });
    }

    /**c
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("movimientoinventario");
    }
};
