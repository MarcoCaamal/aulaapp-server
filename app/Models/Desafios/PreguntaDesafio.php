<?php

namespace App\Models\Desafios;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PreguntaDesafio extends Model
{
    use HasFactory;
    protected $table = 'preguntas_desafios';

    /**
    * The attributes that are mass assignable.
    *
    * @var array<int, string>
    */
    protected $fillable = [
        'identificador',
        'pregunta',
    ];


}
