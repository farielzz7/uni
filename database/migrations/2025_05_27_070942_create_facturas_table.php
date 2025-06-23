<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('facturas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_pago');
            $table->string('numero_factura');
            $table->string('rfc_cliente')->nullable();
            $table->string('nombre_cliente');
            $table->string('direccion_cliente');
            $table->timestamp('fecha_emision');
            $table->decimal('subtotal', 10, 2);
            $table->decimal('iva', 10, 2);
            $table->decimal('total', 10, 2);

            $table->foreign('id_pago')->references('id')->on('pagos');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('facturas');
    }
};
