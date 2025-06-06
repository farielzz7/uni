<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('sesiones', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_usuario');
            $table->string('token');
            $table->string('ip');
            $table->string('navegador');
            $table->dateTime('fecha_inicio'); 
            $table->dateTime('fecha_expiracion'); 
            $table->timestamps();

            $table->foreign('id_usuario')->references('id')->on('users');
        });
    }

    public function down()
    {
        Schema::dropIfExists('sesiones');
    }
};
