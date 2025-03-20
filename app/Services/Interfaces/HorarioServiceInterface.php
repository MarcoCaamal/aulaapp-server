<?php
namespace App\Services\Interfaces;

use App\Models\Horario;
use App\Enums\TurnoEnum;
use App\Helpers\ResponseHelper;

interface HorarioServiceInterface
{
    public function obtenerColeccion(): \Illuminate\Database\Eloquent\Collection;
    public function obtenerColeccionConRelaciones(array $relations): \Illuminate\Database\Eloquent\Collection;
    public function obtenerPorId(int $id): Horario|\Illuminate\Database\Eloquent\Model;
    public function obtenerPorIdConRelaciones(int $id, array $relations): Horario|\Illuminate\Database\Eloquent\Model;
    public function obtenerColeccionConFiltros(TurnoEnum $turno, int $profesorId, array $relations = []): \Illuminate\Database\Eloquent\Collection;
    public function crear(array $attributes, int $idProfesor): ResponseHelper;
    public function actualizar(array $attributes, int $idProfesor, int $idHorario): ResponseHelper;
    public function eliminar(int $idProfesor, int $idHorario): ResponseHelper;
// public function exitsConflicts(string $hora_inicio, string $hora_fin, int $dia_semana): bool;
}
