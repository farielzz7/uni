<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('comentarios', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_turista');
            $table->unsignedBigInteger('id_destino');
            $table->text('texto');
            $table->integer('calificacion');
            $table->timestamp('fecha');

            $table->foreign('id_turista')->references('id')->on('turistas');
            $table->foreign('id_destino')->references('id')->on('destinos');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('comentarios');
    }
};
