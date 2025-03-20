<?php

namespace App\Models;

use App\Models\User;
use App\Models\Semestre;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Grupo extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nombre',
        'clave',
        'id_semestre'
    ];

    // Relaciones
    public function semestre() {
        return $this->belongsTo(Semestre::class);
    }

    public function alumnos() {
        return $this->belongsToMany(User::class, 'grupos_alumnos', 'grupo_id', 'alumno_id')->withTimestamps()->withPivot('is_activo');
    }
}
