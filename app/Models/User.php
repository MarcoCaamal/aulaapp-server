<?php

namespace App\Models;

use App\Models\Desafios\Desafio;
use App\Models\DTOs\Operaciones\Personas\ProfesorDTO;
use App\Models\Foros\Foro;
use App\Models\Foros\Respuesta;
use App\Models\Grupo;
use App\Models\Horario;
use App\Models\Materia;
use App\Models\MateriaAsesor;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nombre',
        'apellido_paterno',
        'apellido_materno',
        'curp',
        'email',
    ];

    protected $guarded = ['materias'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'email_verified_at'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // Relaciones
    public function materias() {
        return $this->belongsToMany(Materia::class, 'materias_asesores', 'user_id', 'materia_id')->withTimestamps()->withPivot('id')->using(MateriaAsesor::class);
    }

    public function grupos() {
        return $this->belongsToMany(Grupo::class, 'grupos_alumnos', 'alumno_id', 'grupo_id')->withPivot(['is_activo', 'ciclo_id'])->withTimestamps();
    }

    public function horarios()
    {
        return $this->hasMany(Horario::class, 'profesor_id');
    }

    public function asistencias()
    {
        return $this->hasMany(Asistencia::class, 'alumno_id');
    }

    public function foros()
    {
        return $this->hasMany(Foro::class);
    }

    public function respuestas()
    {
        return $this->hasMany(Respuesta::class);
    }

    public function desafios(): HasMany
    {
        return $this->hasMany(Desafio::class, 'profesor_id');
    }

    // Mutadores y accesores
    public function nombre(): Attribute {
        return Attribute::make(
            set: fn($value) => trim(ucwords($value))
        );
    }

    public function apellido_paterno(): Attribute {
        return Attribute::make(
            set: fn($value) => trim(ucfirst($value))
        );
    }

    public function apellido_materno(): Attribute {
        return Attribute::make(
            set: fn($value) => trim(ucfirst($value))
        );
    }

    // Métodos
    public function getFullName() {
        return "{$this->nombre} {$this->apellido_paterno} {$this->apellido_materno}";
    }
}
