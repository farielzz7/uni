<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('pago_paquete', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_paquete');
            $table->decimal('monto', 10, 2);
            $table->timestamp('fecha_pago');
            $table->string('estado_pago');
            $table->unsignedBigInteger('id_metodo_pago');
            $table->string('referencia_pago');

            $table->foreign('id_paquete')->references('id')->on('paquetes');
            $table->foreign('id_metodo_pago')->references('id')->on('metodos_pago');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pago_paquete');
    }
};
