<?php
namespace App\Repositories;

use App\Enums\DiaSemanaEnum;
use App\Enums\TurnoEnum;
use App\Models\Horario;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class HorarioRepository
{
    private $model;
    public function __construct()
    {
        $this->model = new Horario();
    }

    /**
     * Repositorio que obtiene una colección de horarios
     *
     * @return \Illuminate\Database\Eloquent\Collection<Horario>
     */
    public function obtenerColeccion(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->model->all();
    }
    /**
     * Repositorio que obtiene una colección de horarios con las relaciones
     *
     * @param array $relations
     * @return \Illuminate\Database\Eloquent\Collection<Horario>
     */
    public function obtenerColeccionConRelaciones(array $relations): \Illuminate\Database\Eloquent\Collection
    {
        return $this->model->with($relations)->get();
    }

    /**
     * Repositorio que obtiene una colección de horarios que tengan un choque de horarios
     * por una hora de inicio, hora fin y dia de la semana
     *
     * @param string $hora_inicio
     * @param string $hora_fin
     * @param int $dia_semana
     *
     * @return \Illuminate\Database\Eloquent\Collection<Horario>
     */
    public function ObtenerColeccionPorHoraInicioHoraFinDiaSemana(string $hora_inicio, string $hora_fin, int $dia_semana): \Illuminate\Database\Eloquent\Collection
    {
        return $this->model->where('dia_semana', $dia_semana)
            ->where(function ($query) use ($hora_inicio, $hora_fin) {
                $query->whereBetween('hora_inicio', [$hora_inicio, $hora_fin])
                    ->orWhereBetween('hora_fin', [$hora_inicio, $hora_fin]);
            })
            ->get();
    }
    /**
     * Repositorio que obtiene un horario por su id
     *
     * @param int $id
     * @return Horario|\Illuminate\Database\Eloquent\Model
     */
    public function obtenerPorId(int $id): Horario|\Illuminate\Database\Eloquent\Model|null
    {
        return $this->model->find($id);
    }
    /**
     * Repositorio que obtiene un horario por su id con relaciones
     *
     * @param int $id
     * @param array $relations
     * @return Horario|\Illuminate\Database\Eloquent\Model
     */
    public function obtenerPorIdConRelaciones(int $id, array $relations): Horario|\Illuminate\Database\Eloquent\Model
    {
        return $this->model->with($relations)->find($id);
    }

    /**
     * Repositorio que obtiene una colección de horario filtrados por el turno
     *
     * @param TurnoEnum $turno
     * @param array $relations
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function obtenerColeccionPorTurnoProfesorId(TurnoEnum $turno, int $profesorId, array $relations = []): \Illuminate\Database\Eloquent\Collection
    {
        $sql = $this->model->query();

        if (count($relations) > 0) {
            $sql->with($relations);
        }

        if ($turno != null) {
            if ($turno == TurnoEnum::Matutino) {
                $sql->where('hora_fin', '<=', '14:00:00');
            }
            else {
                $sql->where('hora_inicio', '>=', '14:00:00');
            }
        }

        if ($profesorId != null) {
            $sql->where('profesor_id', $profesorId);
        }

        return $sql->get();
    }

    /**
     * Repositorio que obtiene una colección de horarios paginados y filtrados
     * Se pueden filtrar por:
     * - semestre_id (este es requerido)
     * - turno (opcional)
     * - nombre (opcional)
     * - hora_inicio (opcional o requerido si va filtrar por rango de horas)
     * - hora_fin (opcional o requerido si va filtrar por rango de horas)
     * @param array $filtros
     *
     * @return array{total: int, data: \Illuminate\Support\Collection<Horario>}
     */
    public function obtenerColeccionPorFiltros($filtros = [],
    $paginaActual = 1,
    $numeroRegistrosPorPagina = 15): array
    {
        $sql = $this->model->query();
        $resultado = [];
        $corteHorario = '14:00:00';
        $semestreId = $filtros['semestre_id'];
        $turno = isset($filtros['turno']) ? TurnoEnum::from($filtros['turno']) : false;
        $nombre = $filtros['nombre'] ?? false;
        $hora_inicio = isset($filtros['horaInicio']) ? Carbon::parse($filtros['horaInicio']) : false;
        $hora_fin = isset($filtros['horaInicio']) ? Carbon::parse($filtros['horaFin']) : false;
        $fecha = isset($filtros['fecha']) ? Carbon::parse($filtros['fecha']) : false;

        $relacciones = [
            'materia' => function ($query) use ($semestreId) {
                $query->where('semestre_id', $semestreId);
            },
            'profesor'
        ];

        $sql->with($relacciones);

        // Se filtra los horarios que solo tengan relacionada una materia con el semestre del alumno
        $sql->whereHas('materia', function ($query) use ($semestreId) {
            $query->where('semestre_id', $semestreId);
        });

        // Si hay hora de inicio y fin se filtran por ese rango de horarios
        if($hora_inicio && $hora_fin) {
            if($hora_inicio->lessThan($hora_fin)) {
                $sql->whereTime('hora_inicio', '>=', $hora_inicio->format('H:i:s'))
                    ->whereTime('hora_fin', '<=', $hora_fin->format('H:i:s'));
            }
        }

        // Si hay nombre se filtra los horarios que tengan un profesor que coincida con ese nombre
        if ($nombre) {
            $sql->whereHas('profesor', function ($query) use ($nombre) {
                $query->where('nombre', 'like', "%$nombre%")
                    ->orWhere('apellido_paterno', 'like', "%$nombre%")
                    ->orWhere('apellido_materno', 'like', "%$nombre%");
            });
        }

        // Si hay turno de filtra por turno
        if ($turno) {

            if ($turno === TurnoEnum::Matutino) {
                $sql->whereTime('hora_fin', '<=', $corteHorario);
            }
            if ($turno === TurnoEnum::Vespertino) {
                $sql->whereTime('hora_inicio', '>=', $corteHorario);
            }
        }

        if($fecha) {
            if(!($fecha->dayOfWeek === Carbon::SATURDAY || $fecha->dayOfWeek === Carbon::SUNDAY)) {
                $diaSemana = DiaSemanaEnum::from($fecha->dayOfWeek - 1);
                $sql->where('dia_semana', $diaSemana->value);
            }
        }

        $sql->select('horarios.*');

        $resultado['total'] = $sql->count();
        $resultado['data'] = $sql
            ->skip(($paginaActual - 1) * $numeroRegistrosPorPagina)
            ->take($numeroRegistrosPorPagina)
            ->get();

        return $resultado;
    }

    /**
     * Repositorio que obtiene una array con las cantidades de asesorias disponibles por semestre
     *
     * @param integer $semestreId
     * @return array
     */
    public function obtenerCountMatutinosVespertinosPorSemestreId(int $semestreId): array
    {
        $counts = [];
        $corteHorarios = '14:00:00';

        $counts['matutinos'] = $this->model->where('hora_fin', '<=', $corteHorarios)
            ->whereHas('materia', function ($query) use ($semestreId) {
                $query->where('semestre_id', $semestreId);
            })->count();

        $counts['vespertinos'] = $this->model->where('hora_inicio', '>=', $corteHorarios)
            ->whereHas('materia', function ($query) use ($semestreId) {
                $query->where('semestre_id', $semestreId);
            })->count();

        return $counts;
    }

    /**
     * Repositorio que guarda un horario
     * @param Horario $horario
     * @return bool
     */
    public function guardar(Horario $horario): bool
    {
        return $horario->save();
    }
    /**
     * Repositorio que elimina un horario
     *
     * @param Horario $horario
     * @return bool
     */
    public function eliminar(Horario $horario): bool
    {
        return $horario->delete();
    }

}
