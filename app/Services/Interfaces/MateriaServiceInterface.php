<?php
namespace App\Services\Interfaces;

use App\Models\Materia;
use App\Helpers\ResponseHelper;

interface MateriaServiceInterface
{
    public function obtenerColeccion(): \Illuminate\Database\Eloquent\Collection;
    public function obtenerColeccionConRelaciones(array $relations): \Illuminate\Database\Eloquent\Collection;
    public function obtenerColeccionPorProfesorId(int $idProfesor): \Illuminate\Database\Eloquent\Collection;
    public function obtenerPorId(int $id): Materia;
    public function crear(array $attributes): ResponseHelper;
    public function actualizar(array $attributes, int $id): ResponseHelper;
    public function eliminar(int $id): ResponseHelper;
    public function existeMateriasPorIds(array $materiasIds): bool;
}
