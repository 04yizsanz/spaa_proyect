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
        Schema::create('producto', function (Blueprint $table) {
            $table->id("producto_id");

            $table->string('nombre',100);
            $table->unsignedInteger('cantidad')->default (0);
            $table->decimal('precio',10,2);
            $table->date('fecha_registro');
           $table->foreignId('proveedor_id')
                ->unique()
                ->constrained('proveedor', 'proveedor_id') 
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->timestamps();

            
        });
    }
// EEEEEEE
    /**c
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("producto");
    }
};