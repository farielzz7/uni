<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('destino_categoria', function (Blueprint $table) {
            $table->unsignedBigInteger('id_destino');
            $table->unsignedBigInteger('id_categoria');

            $table->foreign('id_destino')->references('id')->on('destinos');
            $table->foreign('id_categoria')->references('id')->on('categorias_destino');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('destino_categoria');
    }
};
