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
        Schema::create('foros', function (Blueprint $table) {
            $table->id();
            $table->string('titulo', 300);
            $table->text('contenido');
            $table->unsignedTinyInteger('estatus');
            $table->string('url_imagen')->nullable();
            $table->string('path_imagen')->nullable();
            $table->string('motivo_baja', 300)->nullable();
            $table->foreignId('materia_id')->constrained('materias');
            $table->foreignId('user_id')->constrained('users');
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
        Schema::dropIfExists('foros');
    }
};
