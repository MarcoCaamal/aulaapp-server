<?php

namespace App\Models;

use App\Models\Horario;
use App\Models\User;
use App\Models\Asistencia;
use App\Models\MateriaAsesor;
use App\Enums\EstatusAsesoriaEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Asesoria extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'fecha',
        'materia_asesor_id',
        'horario_id',
        'is_activo'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'estado' => EstatusAsesoriaEnum::class
    ];

    public function asistencias() {
        return $this->hasMany(Asistencia::class);
    }

    public function materia_asesor() {
        return $this->belongsTo(MateriaAsesor::class, 'materia_asesor_id');
    }

    public function horario()
    {
        return $this->belongsTo(Horario::class);
    }
}
