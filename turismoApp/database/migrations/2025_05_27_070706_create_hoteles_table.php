<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('hoteles', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('direccion');
            $table->string('telefono');
            $table->string('email');
            $table->unsignedBigInteger('id_destino');

            $table->foreign('id_destino')->references('id')->on('destinos');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('hoteles');
    }
};
