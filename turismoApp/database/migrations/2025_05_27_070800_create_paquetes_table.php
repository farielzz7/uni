<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('paquetes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_tipo_paquete');
            $table->string('nombre');
            $table->text('descripcion');
            $table->decimal('precio', 10, 2);
            $table->integer('duracion_dias');
            $table->boolean('disponible');

            $table->foreign('id_tipo_paquete')->references('id')->on('tipos_paquete');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('paquetes');
    }
};
