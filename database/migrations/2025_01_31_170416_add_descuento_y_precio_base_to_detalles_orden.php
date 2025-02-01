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
            $table->decimal('descuento_unitario', 10, 2)->after('precio_unitario')->default(0);
            $table->decimal('precio_base', 10, 2)->after('precio_unitario')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detalles_orden', function (Blueprint $table) {
            $table->dropColumn(['descuento_unitario', 'precio_base']);
        });
    }
};
