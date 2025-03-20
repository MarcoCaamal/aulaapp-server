<?php

namespace App\Models;

use App\Models\User;
use App\Models\Materia;
use App\Enums\DiaSemanaEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Horario extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'lugar',
        'dia_semana',
        'hora_inicio',
        'hora_fin',
        'profesor_id',
        'materia_id'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'dia_semana' => DiaSemanaEnum::class
    ];

    // Relaciones
    public function profesor()
    {
        return $this->belongsTo(User::class, 'profesor_id');
    }

    public function materia() {
        return $this->belongsTo(Materia::class);
    }

    public function asesorias()
    {
        return $this->hasMany(Asesoria::class);
    }
}