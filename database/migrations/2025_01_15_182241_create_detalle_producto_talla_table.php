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
    Schema::create('detalle_producto_talla', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('detalle_producto_id');
        $table->unsignedBigInteger('talla_id');
        $table->timestamps();

        // Relaciones
        $table->foreign('detalle_producto_id')->references('id')->on('detalles_productos')->onDelete('cascade');
        $table->foreign('talla_id')->references('id')->on('tallas')->onDelete('cascade');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_producto_talla');
    }
};
