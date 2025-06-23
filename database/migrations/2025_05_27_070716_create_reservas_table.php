<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('reservas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_turista');
            $table->unsignedBigInteger('id_hotel');
            $table->date('fecha_entrada');
            $table->date('fecha_salida');
            $table->integer('numero_personas');
            $table->string('estado');

            $table->foreign('id_turista')->references('id')->on('turistas');
            $table->foreign('id_hotel')->references('id')->on('hoteles');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('reservas');
    }
};
