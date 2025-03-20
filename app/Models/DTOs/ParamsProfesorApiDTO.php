<?php
namespace App\Models\DTOs;

use App\Enums\TurnoEnum;

class ParamsProfesorApiDTO {
    // Propiedades que son del query string de la petición
    public ?string $nombre;
    public ?string $curp;
    public ?string $email;
    public ?TurnoEnum $turno;

    // Propiedades que no son del query string
    public int $idAlumno;
    public int $idSemestreAlumno;

    public function fill(array $attributes)
    {
        $this->nombre = $attributes['nombre'] ?? null;
        $this->curp = $attributes['curp'] ?? null;
        $this->email = $attributes['email'] ?? null;
        if(isset($attributes['turno'])) {
            $this->turno = TurnoEnum::from($attributes['turno']) ?? null;
        }
    }

}
