<?php
namespace App\Services\Interfaces;

use App\Models\Grupo;
use App\Helpers\ResponseHelper;

interface GrupoServiceInterface
{
    public function obtenerColeccion(): \Illuminate\Database\Eloquent\Collection;
    public function obtenerPorId(int $id): Grupo;
    public function obtenerGrupoActualAlumnoPorIdAlumno(int $idAlumno): Grupo|null;
    public function crear(array $attributes): ResponseHelper;
    public function actualizar(array $attributes, int $id): ResponseHelper;
    public function eliminar(int $id): ResponseHelper;
    public function existe(int $id): bool;
}
