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
           $table->foreignId('proveedor_id')
            ->unique()
            ->constrained('proveedor', 'proveedor_id') 
            ->onUpdate('cascade')
            ->onDelete('cascade');
            $table->timestamps();

            
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
