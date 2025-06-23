<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tipos_servicio', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_servicio');
            $table->string('nombre');
            $table->text('descripcion');

            $table->foreign('id_servicio')->references('id')->on('servicios');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tipos_servicio');
    }
};
