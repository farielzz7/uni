<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('usuarioxrol', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_usuario');
            $table->unsignedBigInteger('id_rol');

            $table->foreign('id_usuario')->references('id')->on('users');
            $table->foreign('id_rol')->references('id')->on('roles');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('usuarioxrol');
    }
};
