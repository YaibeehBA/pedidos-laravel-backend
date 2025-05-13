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
        Schema::create('ordenes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('usuario_id');
            $table->enum('estado', ['Pagado', 'Entregando', 'Atrasado'])->default('Pagado');
            $table->decimal('monto_total', 10, 2);
            $table->date('fecha_entrega')->nullable();
            $table->enum('estado_pago', ['pendiente', 'completado'])->default('pendiente');
            $table->timestamps();
    
            $table->foreign('usuario_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ordenes');
    }
};
