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
        Schema::create('grupos_alumnos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grupo_id')
                ->nullable()
                ->constrained('grupos')
                ->onDelete('set null')
                ->onUpdate('cascade');
            $table->foreignId('alumno_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null')
                ->onUpdate('cascade');
            $table->foreignId('ciclo_id')->constrained('ciclos');
            $table->boolean('is_activo');
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
        Schema::dropIfExists('grupos_users');
    }
};
