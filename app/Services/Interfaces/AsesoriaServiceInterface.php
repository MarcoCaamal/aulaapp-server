<?php
namespace App\Services\Interfaces;

use App\Models\Asesoria;
use App\Helpers\ResponseHelper;
use App\Enums\TurnoEnum;

interface AsesoriaServiceInterface
{
    public function obtenerColeccion(): \Illuminate\Database\Eloquent\Collection;
    public function obtenerColeccionConfirmadosPorAlumnoId(int $alumnoId,
    int $paginaActual): \Illuminate\Pagination\LengthAwarePaginator|null;
    public function obtenerPaginacionHorariosAsesoriasPorFiltrosAlumnoId(int $alumnoId,
    array $filtros = [], int $paginaActual): \Illuminate\Pagination\LengthAwarePaginator|null;
    public function obtenerPaginacionListaAlumnosAsesoriaPorAsesoriaId(
        int $asesoriaId,
        int $paginaActual): \Illuminate\Pagination\LengthAwarePaginator|null;
    public function obtenerPaginacionFinalizadasAsesorPorProfesorId(int $profesorId,
        int $paginaActual): \Illuminate\Pagination\LengthAwarePaginator|null;
    public function obtenerPaginacionConfirmadasAsesorPorProfesorId(int $profesorId,
    int $paginaActual): \Illuminate\Pagination\LengthAwarePaginator|null;
    public function obtenerPorId(int $id): Asesoria;
    public function obtenerCountMatutinosVespertinos(int $semestreId): array;
    public function crear(array $attributes): ResponseHelper;
    public function cancelarAsesoriaConfirmadaAlumno(int $alumnoId, int $asesoriaId, string $justificacion): ResponseHelper;
    public function cancelarAsesoriaConfirmadaAsesor(int $profesorId, int $asesoriaId): ResponseHelper;
}
