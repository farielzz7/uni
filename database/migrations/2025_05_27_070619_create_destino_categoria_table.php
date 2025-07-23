<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('destino_categoria', function (Blueprint $table) {
    $table->unsignedBigInteger('id_destino');
    $table->unsignedBigInteger('id_categoria');
    $table->timestamps();

    $table->primary(['id_destino', 'id_categoria']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('destino_categoria');
    }
};
