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
        Schema::table('detalles_productos', function (Blueprint $table) {
            $table->dropForeign(['talla_id']); // Eliminar la relación
            $table->dropColumn('talla_id'); // Eliminar la columna talla_id
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detalles_productos', function (Blueprint $table) {
            $table->unsignedBigInteger('talla_id'); // Agregar la columna talla_id nuevamente
            $table->foreign('talla_id')->references('id')->on('tallas')->onDelete('cascade'); // Restaurar la relación
        });
    }
};
