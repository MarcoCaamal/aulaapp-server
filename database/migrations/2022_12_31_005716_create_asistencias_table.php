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
        Schema::create('asistencias', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('asesoria_id');
            $table->unsignedBigInteger('alumno_id');
            $table->unsignedTinyInteger('estatus');
            $table->string('justificacion', 500)->nullable();

            $table->foreign('asesoria_id')
                ->references('id')
                ->on('asesorias');

            $table->foreign('alumno_id')
                ->references('id')
                ->on('users');

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
        Schema::dropIfExists('asistencias');
    }
};
