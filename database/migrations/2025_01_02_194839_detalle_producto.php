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
        Schema::create('detalles_productos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('producto_id');
            $table->unsignedBigInteger('color_id');
            $table->unsignedBigInteger('talla_id');
            $table->decimal('precio_base', 10, 2);
            $table->integer('stock')->default(0);
            $table->string('imagen_url')->nullable();
            $table->timestamps();

            // Relaciones
            $table->foreign('producto_id')->references('id')->on('productos')->onDelete('cascade');
            $table->foreign('color_id')->references('id')->on('colores')->onDelete('cascade');
            $table->foreign('talla_id')->references('id')->on('tallas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalles_productos');
    }
};
