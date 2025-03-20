<?php
namespace App\Services\Interfaces;

use App\Models\Ciclo;
use App\Helpers\ResponseHelper;

interface CicloServiceInterface
{
    public function obtenerColeccion(): \Illuminate\Database\Eloquent\Collection;

    public function obtenerPorId(int $id): Ciclo;
    public function obtenerCicloActivo(): Ciclo|null;
    public function crear(array $attributes): ResponseHelper;
    public function actualizar(array $attributes, int $id): ResponseHelper;
    public function eliminar(int $id): ResponseHelper;
    public function activar(int $idCiclo): ResponseHelper;
    public function desactivar(int $idCiclo): ResponseHelper;
}
