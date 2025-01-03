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
    Schema::create('productos', function (Blueprint $table) {
        $table->id(); // id será UNSIGNED BIGINT por defecto
       
        $table->string('nombre');
        $table->text('descripcion')->nullable();
        $table->decimal('precio_base', 10, 2);
        $table->timestamps();
       
        
        
       
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
