<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_turista');
            $table->unsignedBigInteger('id_paquete');
            $table->timestamp('fecha_pago');
            $table->decimal('monto', 10, 2);
            $table->string('estado');
            $table->string('referencia_pago');
            $table->unsignedBigInteger('id_metodo_pago');

            $table->foreign('id_turista')->references('id')->on('turistas');
            $table->foreign('id_paquete')->references('id')->on('paquetes');
            $table->foreign('id_metodo_pago')->references('id')->on('metodos_pago');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pagos');
    }
};
