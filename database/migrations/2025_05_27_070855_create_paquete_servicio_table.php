<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('paquete_servicio', function (Blueprint $table) {
            $table->unsignedBigInteger('id_paquete');
            $table->unsignedBigInteger('id_servicio');
            $table->integer('cantidad');
            $table->decimal('precio_individual', 10, 2);
            $table->timestamps();

            // Claves foráneas
            $table->foreign('id_paquete')->references('id')->on('paquetes');
            $table->foreign('id_servicio')->references('id')->on('servicios');

            // ✅ Clave primaria compuesta
            $table->primary(['id_paquete', 'id_servicio']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('paquete_servicio');
    }
};
    
