<?php

namespace App\Models;

use App\Models\Desafios\Desafio;
use App\Models\Foros\Foro;
use App\Models\Horario;
use App\Models\Semestre;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User;

class Materia extends Model {
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nombre',
        'semestre_id'
    ];

    // Relaciones
    public function profesores()
    {
        return $this->belongsToMany(User::class, 'materias_asesores', 'materia_id', 'user_id')->withTimestamps()->using(MateriaAsesor::class);
    }

    public function semestre()
    {
        return $this->belongsTo(Semestre::class);
    }

    public function horarios()
    {
        return $this->hasMany(Horario::class);
    }

    public function foros()
    {
        return $this->hasMany(Foro::class);
    }

    public function desafios()
    {
        return $this->hasMany(Desafio::class);
    }
}
