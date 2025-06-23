<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('transacciones_externas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_pago');
            $table->string('proveedor');
            $table->text('respuesta_raw');

            $table->foreign('id_pago')->references('id')->on('pagos');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('transacciones_externas');
    }
};
