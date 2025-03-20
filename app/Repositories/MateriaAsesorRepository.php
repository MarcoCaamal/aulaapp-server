<?php
namespace App\Repositories;

use App\Models\MateriaAsesor;

class MateriaAsesorRepository {
    private $model;
    public function __construct()
    {
        $this->model = new MateriaAsesor();
    }

    public function obtenerPorMateriaIdAsesorId(int $materiaId, int $asesorId): MateriaAsesor
    {
        return $this->model
            ->where('materia_id', $materiaId)
            ->where('user_id', $asesorId)
            ->first();
    }
}
