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
        Schema::create('itinerarios_personalizados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_usuario')->constrained('users')->onDelete('cascade');
            $table->foreignId('id_paquete')->nullable()->constrained('paquetes')->onDelete('set null');
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->decimal('presupuesto_total', 10, 2);
            $table->json('detalles_json')->nullable(); // Para almacenar la estructura del itinerario personalizado
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('itinerarios_personalizados');
    }
};
