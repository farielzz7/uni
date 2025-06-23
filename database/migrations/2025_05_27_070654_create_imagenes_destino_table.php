<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('imagenes_destino', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_destino');
            $table->string('url_imagen');
            $table->boolean('es_principal');
            $table->string('descripcion');

            $table->foreign('id_destino')->references('id')->on('destinos');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('imagenes_destino');
    }
};
