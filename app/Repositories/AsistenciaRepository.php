<?php
namespace App\Repositories;

use App\Enums\EstatusAsistenciaEnum;
use App\Models\Asistencia;

class AsistenciaRepository {
    private $model;
    public function __construct() {
        $this->model = new Asistencia();
    }

    /**
     * Repositorio que obtiene una colección de Asistencias
     *
     * @return \Illuminate\Database\Eloquent\Collection<Asistencia>
     */
    public function obtenerColeccion(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->model->all();
    }

    /**
     * Repositorio que obtiene una asistencia por su Id
     *
     * @param integer $id
     * @return Asistencia
     */
    public function obtenerPorId(int $id): Asistencia
    {
        return $this->model->find($id);
    }

    public function obtenerPorAsesoriaIdAlumnoId(int $asesoriaId, int $alumnoId): Asistencia|null
    {
        $sql = $this->model->query();

        $sql
            ->where('asesoria_id', $asesoriaId)
            ->where('alumno_id', $alumnoId)
            ->where('estatus', EstatusAsistenciaEnum::PENDIENTE);

        return $sql->first();
    }

    /**
     * Reposiotrio que guarda o actualiza una asistencia
     *
     * @param Asistencia $asistencia
     * @return boolean
     */
    public function guardar(Asistencia $asistencia): bool
    {
        return $asistencia->save();
    }

    /**
     * Repositorio que elimina una asistencia
     *
     * @param Asistencia $asistencia
     * @return boolean
     */
    public function eliminar(Asistencia $asistencia): bool
    {
        return $asistencia->delete();
    }
}
