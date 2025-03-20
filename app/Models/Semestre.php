<?php

namespace App\Models;

use App\Models\Grupo;
use App\Models\Materia;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Semestre extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nombre'
    ];

    // Relaciones
    public function grupos()
    {
        return $this->hasMany(Grupo::class);
    }

    public function materias()
    {
        return $this->hasMany(Materia::class);
    }
}