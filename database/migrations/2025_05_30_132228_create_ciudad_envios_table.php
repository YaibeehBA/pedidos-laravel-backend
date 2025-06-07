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
        Schema::create('ciudad_envios', function (Blueprint $table) {
            $table->id();
            $table->string('nombre'); // Quito, Guayaquil, etc.
            $table->decimal('precio_envio', 8, 2); // Precio envío a esa ciudad
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ciudad_envios');
    }
};
