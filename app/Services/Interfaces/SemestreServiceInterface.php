<?php
namespace App\Services\Interfaces;

use App\Models\Semestre;
use App\Helpers\ResponseHelper;

interface SemestreServiceInterface
{
    public function obtenerColeccion(): \Illuminate\Database\Eloquent\Collection;
    public function obtenerPorId(int $id): Semestre;
    public function crear(array $attributes): ResponseHelper;
    public function actualizar(array $attributes, int $id): ResponseHelper;
    public function eliminar(int $id): ResponseHelper;
    public function existe(int $id): bool;
}
