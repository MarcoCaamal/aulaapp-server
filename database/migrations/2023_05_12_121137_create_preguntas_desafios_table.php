<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('preguntas_desafios', function (Blueprint $table) {
            $table->id();
            $table->string('identificador');
            $table->string('pregunta', 300);
            $table->string('url_imagen')->nullable();
            $table->string('path_imagen')->nullable();
            $table->bigInteger('orden');


            $table->foreignId('desafio_id')->nullable()->constrained('desafios');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('preguntas_desafio');
    }
};
