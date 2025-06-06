<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('servicio_proveedor', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_servicio');
            $table->unsignedBigInteger('id_proveedor');

            $table->foreign('id_servicio')->references('id')->on('servicios');
            $table->foreign('id_proveedor')->references('id')->on('proveedores');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('servicio_proveedor');
    }
};
