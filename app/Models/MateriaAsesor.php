<?php
namespace App\Models;

use App\Models\User;
use App\Models\Materia;
use Illuminate\Database\Eloquent\Relations\Pivot;

class MateriaAsesor extends Pivot
{
    protected $table = 'materias_asesores';


    public function asesor()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function materia()
    {
        return $this->belongsTo(Materia::class);
    }
}