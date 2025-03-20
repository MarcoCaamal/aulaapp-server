<?php
namespace App\Services;

use App\Models\Horario;
use App\Enums\TurnoEnum;
use App\Enums\DiaSemanaEnum;
use Illuminate\Support\Carbon;
use App\Helpers\ResponseHelper;
use Illuminate\Support\Facades\Log;
use App\Repositories\UserRepository;
use App\Repositories\HorarioRepository;
use App\Repositories\MateriaRepository;
use App\Services\Interfaces\HorarioServiceInterface;



class HorarioService implements HorarioServiceInterface
{
    private HorarioRepository $horarioRepository;
    private UserRepository $userRepository;
    private MateriaRepository $materiaRepository;
    public function __construct(HorarioRepository $horarioRepository, UserRepository $userRepository, MateriaRepository $materiaRepository)
    {
        $this->horarioRepository = $horarioRepository;
        $this->userRepository = $userRepository;
        $this->materiaRepository = $materiaRepository;
    }
    /**
     * Servicio que obtiene una colección de horarios
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function obtenerColeccion(): \Illuminate\Database\Eloquent\Collection
    {
        $horarios = new \Illuminate\Database\Eloquent\Collection();
        try {
            $horarios = $this->horarioRepository->obtenerColeccion();
        } catch (\Throwable $th) {
            Log::error($th);
        }
        return $horarios;
    }

    /**
     * Servicio que obtiene una coleccipon de horarios con sus relaciones
     *
     * @param array $relations
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function obtenerColeccionConRelaciones(array $relations): \Illuminate\Database\Eloquent\Collection
    {
        $horarios = new \Illuminate\Database\Eloquent\Collection();
        try {
            $horarios = $this->horarioRepository->obtenerColeccionConRelaciones($relations);
        } catch (\Throwable $th) {
            Log::error($th);
        }
        return $horarios;
    }

    /**
     *
     * @param int $id
     * @return Horario|\Illuminate\Database\Eloquent\Model
     */
    public function obtenerPorId(int $id): Horario|\Illuminate\Database\Eloquent\Model
    {
        $horario = new Horario();
        try {
            $horario = $this->horarioRepository->obtenerPorId($id);
        } catch (\Throwable $th) {
            Log::error($th);
        }
        return $horario;
    }

    /**
     *
     * @param int $id
     * @param array $relations
     * @return Horario|\Illuminate\Database\Eloquent\Model
     */
    public function obtenerPorIdConRelaciones(int $id, array $relations): Horario|\Illuminate\Database\Eloquent\Model
    {
        $horario = new Horario();
        try {
            $horario = $this->horarioRepository->obtenerPorIdConRelaciones($id, $relations);
        } catch (\Throwable $th) {
            Log::error($th);
        }
        return $horario;
    }

    /**
     * Servicio para poder crear un horario
     *
     * @param array $attributes
     * @param int $idProfesor
     * @return ResponseHelper
     */
    public function crear(array $attributes, int $idProfesor): ResponseHelper
    {
        $response = new ResponseHelper();
        try {
            $horario = new Horario();
            $horario->fill($attributes);
            $horario->profesor_id = $idProfesor;

            $profesor = $this->userRepository->obtenerProfesorPorId($idProfesor);
            $materia = $this->materiaRepository->obtenerPorId($attributes['materia_id']);

            if ($profesor->getKey() === null || $materia->getKey() === null) {
                $response->success = false;
                $response->statusCode = 400;
                return $response;
            }

            $horaInicio = strtotime($horario->hora_inicio);
            $horaFin = strtotime($horario->hora_fin);

            if ($horaInicio >= $horaFin) {
                $response->success = false;
                $response->message = __('validation.after_time', ['Attribute' => 'La Hora Fin', 'time' => 'La Hora Incio']);
                $response->statusCode = 400;
                return $response;
            }

            $conflict = $this->exitsConflicts($horario->hora_inicio, $horario->hora_fin, $horario->dia_semana->value);

            if ($conflict) {
                $response->success = false;
                $response->message = __('messages.horarios.conflic_horario');
                $response->statusCode = 400;
                return $response;
            }

            if ($this->horarioRepository->guardar($horario)) {
                $response->success = true;
                $response->message = __('messages.successful_creation', ['name' => 'el Horario']);
                return $response;
            }

        } catch (\Throwable $th) {
            Log::error($th);
            $response->success = false;
            $response->message = __('messages.failed_creation', ['name' => 'el Horario']);
            $response->statusCode = 500;
        }
        return $response;
    }

    /**
     * Servicio que actualiza un horario
     *
     * @param array $attributes
     * @param int $idProfesor
     * @param int $idHorario
     * @return ResponseHelper
     */
    public function actualizar(array $attributes, int $idProfesor, int $idHorario): ResponseHelper
    {
        $response = new ResponseHelper();
        try {
            $horarioDB = $this->horarioRepository->obtenerPorId($idHorario);
            $profesorDB = $this->userRepository->obtenerProfesorPorId($idProfesor);
            $materiaDB = $this->materiaRepository->obtenerPorId($attributes['materia_id']);

            if ($horarioDB === null || $profesorDB === null || $materiaDB === null) {
                abort(404);
            }

            $horarioDB->fill($attributes);

            $conflict = $this->exitsConflicts($horarioDB->hora_inicio,
                $horarioDB->hora_fin,
                $horarioDB->dia_semana->value,
                $horarioDB->id
            );

            if ($conflict) {
                $response->success = false;
                $response->message = __('messages.horarios.conflic_horario');
                $response->statusCode = 400;
                return $response;
            }

            if($this->horarioRepository->guardar($horarioDB)) {
                $response->success = true;
                $response->message = __('messages.successful_update', ['name' => 'el Horario']);
                return $response;
            }
        } catch (\Throwable $th) {
            Log::error($th);
            $response->success = false;
            $response->message = __('messages.failed_update', ['name' => 'el Horario']);
            $response->statusCode = 500;
        }
        return $response;
    }

    /**
     * Servicio que elimina un horario
     *
     * @param int $idProfesor
     * @param int $idHorario
     * @return ResponseHelper
     */
    public function eliminar(int $idProfesor, int $idHorario): ResponseHelper
    {
        $response = new ResponseHelper();
        try {
            $horario = $this->horarioRepository->obtenerPorId($idHorario);

            if($horario === null) {
                abort(404);
            }

            if($this->horarioRepository->eliminar($horario)) {
                $response->success = true;
                $response->message = __('messages.successful_deletion', ['name' => 'el Horario']);
                return $response;
            }
        } catch (\Throwable $th) {
            Log::error($th);
            $response->success = false;
            $response->message = __('messages.failed_deletion', ['name' => 'el Horario']);
            $response->statusCode = 500;
        }
        return $response;
    }

    /**
     * Método que busca si hay confilctos de horarios
     *
     * @param string $hora_inicio
     * @param string $hora_fin
     * @param integer $dia_semana
     * @param integer|null $idHorario
     * @return boolean
     */
    private function exitsConflicts(string $hora_inicio, string $hora_fin, int $dia_semana, int $idHorario = null): bool
    {
        $conflict = false;
        try {
            $horarios = $this->horarioRepository->ObtenerColeccionPorHoraInicioHoraFinDiaSemana($hora_inicio, $hora_fin, $dia_semana);

            if ($horarios->isEmpty()) {
                return $conflict;
            }

            $hora_inicio_nuevo = Carbon::parse($hora_inicio);
            $hora_fin_nuevo = Carbon::parse($hora_fin);

            foreach ($horarios as $horario) {
                $isEqualHorario = $idHorario === $horario->getKey() ? true : false;

                if ($isEqualHorario) {
                    continue;
                }

                $hora_inicio_db = Carbon::parse($horario->hora_inicio);
                $hora_fin_db = Carbon::parse($horario->hora_fin);

                if (
                    $hora_inicio_db->lessThan($hora_fin_nuevo) &&
                    ($hora_fin_db->greaterThan($hora_inicio_nuevo) && $hora_fin_db->greaterThanOrEqualTo($hora_fin_nuevo))
                ) {
                    $conflict = true;
                    break;
                }

                if (
                    $hora_fin_db->greaterThan($hora_inicio_nuevo) &&
                    ($hora_inicio_db->lessThanOrEqualTo($hora_inicio_nuevo) && $hora_inicio_db->lessThan($hora_fin_nuevo))
                ) {
                    $conflict = true;
                    break;
                }

            }

        } catch (\Throwable $th) {
            $conflict = true;
            Log::error($th);
        }

        return $conflict;
    }
	/**
     * Servicio que obtiene una colección de horarios filtrados por un turno
     *
	 * @param TurnoEnum $turno
	 * @param array $relations
	 * @return mixed
	 */
	public function obtenerColeccionConFiltros(TurnoEnum $turno, int $profesorId, array $relations = array()): \Illuminate\Database\Eloquent\Collection {
        $horarios = new \Illuminate\Database\Eloquent\Collection();
        try {
            $horarios = $this->horarioRepository->obtenerColeccionPorTurnoProfesorId($turno, $profesorId, $relations);
        } catch (\Throwable $th) {
            Log::error($th);
        }
        return $horarios;
	}
}
