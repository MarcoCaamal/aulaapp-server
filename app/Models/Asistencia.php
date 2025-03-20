<?php

namespace App\Models;

use App\Enums\EstatusAsistenciaEnum;
use App\Models\Asesoria;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Asistencia extends Model
{
    use HasFactory;

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'estatus' => EstatusAsistenciaEnum::class
    ];

    public function asesoria() {
        return $this->belongsTo(Asesoria::class);
    }

    public function alumno()
    {
        $this->belongsTo(User::class, 'alumno_id');
    }
}
