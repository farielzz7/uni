<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('destinos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->text('descripcion');
            $table->text('eventos');
            $table->text('atractivos');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('destinos');
    }
};
