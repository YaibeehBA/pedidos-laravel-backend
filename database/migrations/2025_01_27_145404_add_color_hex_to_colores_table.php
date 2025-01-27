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
        Schema::table('colores', function (Blueprint $table) {
            $table->string('codigo_hex', 7)->nullable()->after('nombre'); // Hexadecimal de color (ejemplo: #FFFFFF)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('colores', function (Blueprint $table) {
            $table->dropColumn('codigo_hex');
        });
    }
};
