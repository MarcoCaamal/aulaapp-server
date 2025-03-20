<?php

namespace App\Models\Foros;

use App\Enums\Foros\EstatusRespuestaEnum;
use App\Models\Foros\Foro;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Respuesta extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'contenido',
    ];
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'estatus' => EstatusRespuestaEnum::class
    ];

    // Relaciones

    public function foro()
    {
        return $this->belongsTo(Foro::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
