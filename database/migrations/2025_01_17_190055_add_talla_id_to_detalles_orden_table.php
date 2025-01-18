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
        Schema::table('detalles_orden', function (Blueprint $table) {
            // Agregar el campo talla_id
            $table->unsignedBigInteger('talla_id')->after('detalles_productos_id');

            // Crear la relación de clave foránea con la tabla tallas
            $table->foreign('talla_id')->references('id')->on('tallas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detalles_orden', function (Blueprint $table) {
            // Eliminar la clave foránea y el campo talla_id
            $table->dropForeign(['talla_id']);
            $table->dropColumn('talla_id');
        });
    }
};
