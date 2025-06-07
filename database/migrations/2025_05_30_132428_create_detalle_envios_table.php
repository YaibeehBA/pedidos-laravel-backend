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
        Schema::create('detalle_envios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('orden_id')->constrained('ordenes')->onDelete('cascade');
            $table->enum('tipo_envio', ['Envío Nacional', 'Retiro tienda Física']);
            $table->foreignId('ciudad_envio_id')->nullable()->constrained('ciudad_envios');
            $table->string('direccion')->nullable();
            $table->string('referencia')->nullable();
            $table->decimal('peso_total', 8, 2)->nullable();
            $table->decimal('costo_envio', 8, 2)->nullable();
            $table->enum('estado_envio', ['pendiente', 'enviado', 'entregado'])->default('pendiente');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_envios');
    }
};
