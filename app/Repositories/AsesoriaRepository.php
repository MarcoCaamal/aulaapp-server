<?php
namespace App\Repositories;

use App\Enums\EstatusAsesoriaEnum;
use App\Enums\EstatusAsistenciaEnum;
use App\Models\Asesoria;
use App\Models\Asistencia;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AsesoriaRepository
{
    private $model;

    public function __construct()
    {
        $this->model = new Asesoria();
    }

    public function obtenerColeccion(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->model->all();
    }

    /**
     * Repositorio que obtiene una colección de Asesorias confirmadas del alumno
     *
     * @param integer $alumnoId
     * @param array $relations
     * @param int $paginaActual
     * @param int $numeroRegistrosPorPagina
     *
     * @return array{total: int, data: \Illuminate\Database\Eloquent\Collection}
     */
    public function obtenerColeccionPaginadaConfirmadosByAlumnoId(int $alumnoId, array $relations = [],
    int $paginaActual = 1, int $numeroRegistrosPorPagina = 15): array
    {
        $resultado = [];
        $hoy = Carbon::now();
        $sql = $this->model->query();

        $sql->with($relations);

        $sql
            ->join('asistencias', 'asesorias.id', '=', 'asistencias.asesoria_id')
            ->join('horarios', 'asesorias.horario_id', '=', 'horarios.id')
            ->where([
                ['estado', EstatusAsesoriaEnum::PENDIENTE],
                ['asistencias.alumno_id', $alumnoId],
                ['asistencias.estatus', EstatusAsistenciaEnum::PENDIENTE]
            ])
            ->where(function ($query) use ($hoy) {
                $query->where('asesorias.fecha', '>', $hoy->toDateString())
                    ->orWhere(function ($query) use ($hoy) {
                        $query->where('asesorias.fecha', $hoy->toDateString())
                            ->where('horarios.hora_inicio', '>', $hoy->toTimeString());
                    });
            })
            ->select('asesoria.*');

        $resultado['total'] = $sql->count();

        $resultado['data'] = $sql
            ->skip(($paginaActual - 1) * $numeroRegistrosPorPagina)
            ->take($numeroRegistrosPorPagina)
            ->get();

        return $resultado;
    }
    /**
     * Repositorio que obtiene una colección de asesorias finalizadas de un profesor.
     *
     * @param integer $profesorId
     * @return array{total: int, data: \Illuminate\Support\Collection<Asesoria>}
     */
    public function obtenerColleccionFinalizadasAsesorPorProfesorId(int $profesorId,
    int $paginaActual = 1,
    int $numeroRegistrosPorPagina = 15): array
    {
        $sql = $this->model->query();
        $relations = ['horario', 'materia_asesor'];
        $resultado = [];

        $sql->with($relations);

        $sql
            ->join('materias_asesores', 'asesorias.materia_asesor_id', '=', 'materias_asesores.id')
            ->where([
                ['materias_asesores.user_id', $profesorId],
                ['asesorias.estado', EstatusAsesoriaEnum::FINALIZADA]
            ])
            ->orderByDesc('asesorias.fecha')
            ->select('asesorias.*');

        $resultado['total'] = $sql->count();
        $resultado['data'] = $sql
            ->skip(($paginaActual - 1) * $numeroRegistrosPorPagina)
            ->take($numeroRegistrosPorPagina)
            ->get();
        return $resultado;
    }
    /**
     * Repositorio que obtiene una collección para paginación de las asesorias confirmadas del profesor.
     *
     * El valor devuelto sera un array asociativo con los siguiente valores:
     * $result['total'] es el total de registros que se encontraron en la base de datos
     * $result['data'] es la collección de asesorias que se encontraron en la base de datos.
     * La collección que se obtiene es utilizada para páginación, es decir, que no se traen todos los registros de la DB.
     *
     * @param integer $profesorId
     * @param integer $paginaActual
     * @param integer $numeroRegistrosPorPagina
     * @return array{total: int, data: \Illuminate\Database\Eloquent\Collection<Asesoria>}
     */
    public function obtenerColeccionConfirmadasAsesorPorProfesorId(int $profesorId,
        int $paginaActual = 1, int $numeroRegistrosPorPagina = 2): array
    {
        $sql = $this->model->query();
        $resultado = [];
        $relations = ['horario', 'materia_asesor'];
        $now = Carbon::now();

        $sql->with($relations);

        $sql
            ->join('materias_asesores', 'asesorias.materia_asesor_id', '=', 'materias_asesores.id')
            ->join('horarios', 'asesorias.horario_id', '=', 'horarios.id')
            ->where([
                ['materias_asesores.user_id', $profesorId],
                ['asesorias.estado', EstatusAsesoriaEnum::PENDIENTE],
            ])
            ->where(function ($query) use ($now) {
                $query->where('asesorias.fecha', '>', $now->toDateString())
                    ->orWhere(function ($query) use ($now) {
                            $query->where('asesorias.fecha', $now->toDateString())
                                ->where('horarios.hora_inicio', '>', $now->toTimeString());
                        });
            })
            ->orderByDesc('asesorias.fecha')
            ->select('asesorias.*');

        $resultado['total'] = $sql->count();
        $resultado['data'] = $sql
            ->skip(($paginaActual - 1) * $numeroRegistrosPorPagina)
            ->take($numeroRegistrosPorPagina)
            ->get();

        return $resultado;
    }
    /**
     * Repositorio que obtiene una asesoria por su Id
     *
     * @param integer $id
     * @return Asesoria
     */
    public function getById(int $id): Asesoria|null
    {
        return $this->model->find($id);
    }
    /**
     * Repositorio que obtiene una Asesoria filtrado por horarioId y una fecha
     *
     * @param integer $horarioId
     * @param string $fecha
     * @param array $realtions
     * @return Asesoria|null
     */
    public function getByHorarioIdFecha(int $horarioId, string $fecha, array $realtions = []): Asesoria|null
    {
        $sql = $this->model->query();

        if (count($realtions) > 0) {
            $sql->with($realtions);
        }
        $sql->where('fecha', $fecha);

        $sql->where('horario_id', $horarioId);

        return $sql->first();
    }

    /**
     * Repositorio que guarda o actuliza una Asesoria
     *
     * @param Asesoria $asesoria
     * @param Asistencia $asistencia
     * @return boolean
     */
    public function guardar(Asesoria &$asesoria, Asistencia &$asistencia): bool
    {
        $result = false;
        DB::transaction(function () use (&$asesoria, &$asistencia, &$result) {
            $asesoria->save();
            $asistencia->asesoria_id = $asesoria->id;
            $asistencia->save();
            $result = true;
        });
        return $result;
    }
    /**
     * Repositorio que guarda o actualiza una asesoria
     *
     * @param Asesoria $asesoria
     * @return boolean
     */
    public function actualizar(Asesoria $asesoria): bool
    {
        return $asesoria->save();
    }

    public function existe(int $id): bool
    {
        $sql = $this->model->query();

        return $sql->where('id', $id)->exists();
    }

    /**
     * Repositorio que elimina una Asesoria
     *
     * @param Asesoria $asesoria
     * @return boolean
     */
    public function eliminar(Asesoria $asesoria): bool
    {
        return $asesoria->delete();
    }

}
