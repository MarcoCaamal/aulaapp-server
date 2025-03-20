<?php
namespace App\Services;
use App\Models\Asesoria;
use App\Models\Asistencia;
use App\Models\DTOs\Operaciones\Horarios\HorarioDTO;
use App\Models\DTOs\Operaciones\Materias\MateriaDTO;
use App\Models\DTOs\Operaciones\Personas\AlumnoDTO;
use App\Models\DTOs\Operaciones\Personas\ProfesorDTO;
use App\Models\User;
use App\Services\Interfaces\GrupoServiceInterface;
use App\Services\Interfaces\Mapping\MapperServiceInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use App\Helpers\ResponseHelper;
use App\Enums\EstatusAsesoriaEnum;
use App\Models\DTOs\Operaciones\Asesorias\AsesoriaDTO;
use App\Models\Horario;
use Illuminate\Support\Facades\Log;
use App\Repositories\HorarioRepository;
use App\Repositories\AsesoriaRepository;
use App\Repositories\AsistenciaRepository;
use App\Repositories\MateriaAsesorRepository;
use App\Services\Interfaces\UserServiceInterface;
use App\Services\Interfaces\AsesoriaServiceInterface;
use App\Enums\EstatusAsistenciaEnum;
use App\Repositories\UserRepository;

class AsesoriaService implements AsesoriaServiceInterface
{
    private AsesoriaRepository $asesoriaRepository;
    private HorarioRepository $horarioRepository;
    private MateriaAsesorRepository $materiaAsesorRepository;
    private UserServiceInterface $userService;
    private AsistenciaRepository $asistenciaRepository;
    private GrupoServiceInterface $grupoService;
    private UserRepository $userRepository;
    private MapperServiceInterface $mapperService;

    private int $numeroRegistrosPorPagina = 2;
    public function __construct(
        AsesoriaRepository $asesoriaRepository, HorarioRepository $horarioRepository,
        MateriaAsesorRepository $materiaAsesorRepository, UserServiceInterface $userService,
        AsistenciaRepository $asistenciaRepository,
        MapperServiceInterface $mapperService,
        GrupoServiceInterface $grupoService,
        UserRepository $userRepository,
    ) {
        $this->asesoriaRepository = $asesoriaRepository;
        $this->horarioRepository = $horarioRepository;
        $this->materiaAsesorRepository = $materiaAsesorRepository;
        $this->userService = $userService;
        $this->asistenciaRepository = $asistenciaRepository;
        $this->mapperService = $mapperService;
        $this->grupoService = $grupoService;
        $this->userRepository = $userRepository;
    }

    /**
     * Servicio que obtiene una colección de asesorias
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function obtenerColeccion(): \Illuminate\Database\Eloquent\Collection
    {
        $asesorias = new \Illuminate\Database\Eloquent\Collection();
        try {
            $asesorias = $this->asesoriaRepository->obtenerColeccion();
        }
        catch (\Throwable $th) {
            Log::error($th);
        }
        return $asesorias;
    }

    /**
     * Servicio para obtener una asesoria por su Id
     *
     * @param int $id
     * @return Asesoria
     */
    public function obtenerPorId(int $id): Asesoria
    {
        $asesoria = new Asesoria();
        try {
            $asesoria = $this->asesoriaRepository->getById($id);
        }
        catch (\Throwable $th) {
            Log::error($th);
        }
        return $asesoria;
    }

    /**
     * Servicio que obtiene una array con las cantidades de asesorias disponibles por semestre
     *
     * @param integer $semestreId
     * @return array
     */
    public function obtenerCountMatutinosVespertinos(int $semestreId): array
    {
        $counts = [];
        try {
            $counts = $this->horarioRepository->obtenerCountMatutinosVespertinosPorSemestreId($semestreId);
        }
        catch (\Throwable $th) {
            Log::error($th);
        }
        return $counts;
    }
    /**
     * Servicio que devuelve una lista paginada de los horarios de asesorias para el alumno
     *
     * @param integer $alumnoId
     * @param array $filtros
     * @param int $paginaActual
     * @return LengthAwarePaginator|null
     */
    public function obtenerPaginacionHorariosAsesoriasPorFiltrosAlumnoId(int $alumnoId, array $filtros = [], int $paginaActual): LengthAwarePaginator|null
    {
        $horariosPaginados = null;
        try {
            $grupoAlumnoActual = $this->grupoService
                ->obtenerGrupoActualAlumnoPorIdAlumno($alumnoId);
            $filtros['semestre_id'] = $grupoAlumnoActual->semestre_id;
            $horariosDB = $this->horarioRepository
                ->obtenerColeccionPorFiltros(
                    $filtros,
                    $paginaActual,
                    $this->numeroRegistrosPorPagina
                );
            $horariosDTO = $horariosDB['data']->map(function (Horario $horario) {
                $horarioDTO = new HorarioDTO(horario: $horario);
                $asesorDTO = new ProfesorDTO(profesor: $horario->profesor);
                $asesorDTO->unsetProperties(['email', 'curp', 'created_at', 'updated_at']);
                $materiaDTO = new MateriaDTO(materia: $horario->materia);
                $materiaDTO->unsetProperties(['created_at', 'updated_at']);
                $horarioDTO->asesor = $asesorDTO;
                $horarioDTO->materia = $materiaDTO;
                return $horarioDTO;
            });
            $horariosPaginados = new LengthAwarePaginator(
                $horariosDTO,
                $horariosDB['total'],
                $this->numeroRegistrosPorPagina,
            LengthAwarePaginator::resolveCurrentPage(), [
                'path' => LengthAwarePaginator::resolveCurrentPath()
            ]);
        } catch (\Throwable $th) {
            Log::error($th);
        }
        return $horariosPaginados;
    }

    public function obtenerPaginacionListaAlumnosAsesoriaPorAsesoriaId(int $asesoriaId, int $paginaActual): LengthAwarePaginator|null
    {
        $paginacionAlumnos = null;
        try {
            $alumnosDB = $this->userRepository
                ->obtenerColeccionAlumnosPorAsesoriaId(
                    $asesoriaId,
                    $paginaActual,
                    $this->numeroRegistrosPorPagina
                );
            $alumnosMap = $alumnosDB['data']->map(function(User $alumno) {
                $alumnoDTO = new AlumnoDTO(alumno: $alumno);
                $alumnoDTO->unsetProperties(['curp', 'email', 'created_at', 'updated_at']);
                return $alumnoDTO;
            });
            $paginacionAlumnos = new LengthAwarePaginator(
                $alumnosMap,
                $alumnosDB['total'],
                $this->numeroRegistrosPorPagina,
                LengthAwarePaginator::resolveCurrentPage(),
                [
                    'path' => LengthAwarePaginator::resolveCurrentPath()
                ]
            );
        } catch (\Throwable $th) {
            Log::error($th);
        }
        return $paginacionAlumnos;
    }

    /**
     * Servicio de devuelve una lista de asesorias finalizadas de un asesor
     *
     * @param integer $profesorId
     * @return LengthAwarePaginator|null
     */
    public function obtenerPaginacionFinalizadasAsesorPorProfesorId(int $profesorId, int $paginaActual): LengthAwarePaginator|null
    {
        $asesoriasFinalizadasPaginadas = null;
        try {
            $asesoriasFinalizadasDB = $this->asesoriaRepository
                ->obtenerColleccionFinalizadasAsesorPorProfesorId(
                    $profesorId,
                    $paginaActual,
                    $this->numeroRegistrosPorPagina
                );
            $asesoriasFinalizadasMap = $asesoriasFinalizadasDB['data']->map(function(Asesoria $asesoria) {
                $asesoriaDTO = new AsesoriaDTO(asesoria: $asesoria);
                $horarioDTO = new HorarioDTO(horario: $asesoria->horario);
                $materiaDTO = new MateriaDTO(materia: $asesoria->materia_asesor->materia);
                // $profesorDTO = new ProfesorDTO(profesor: $asesoria->materia_asesor->asesor);
                $asesoriaDTO->horario = $horarioDTO;
                $asesoriaDTO->materia = $materiaDTO;
                // $asesoriaDTO->asesor = $profesorDTO;
                return $asesoriaDTO;
            });
            $asesoriasFinalizadasPaginadas = new LengthAwarePaginator(
                $asesoriasFinalizadasMap,
                $asesoriasFinalizadasDB['total'],
                $this->numeroRegistrosPorPagina,
                LengthAwarePaginator::resolveCurrentPage(),
                [
                    'path' => LengthAwarePaginator::resolveCurrentPath()
                ]
            );
        } catch (\Throwable $th) {
            Log::error($th);
        }
        return $asesoriasFinalizadasPaginadas;
    }
    /**
     * Serivicio que devuelve una lista de asesorias confirmadas de un asesor
     *
     * @param integer $profesorId
     * @return LengthAwarePaginator|null
     */
    public function obtenerPaginacionConfirmadasAsesorPorProfesorId(
        int $profesorId,
        int $paginaActual): LengthAwarePaginator|null
    {
        $asesoriasPaginadas = null;
        try {
            $asesoriasConfirmadasDB = $this->asesoriaRepository
            ->obtenerColeccionConfirmadasAsesorPorProfesorId(
                $profesorId,
                $paginaActual,
                $this->numeroRegistrosPorPagina
            );
            $asesoriasMap = $asesoriasConfirmadasDB['data']->map(function(Asesoria $asesoria) {
                $asesoriaDTO = new AsesoriaDTO(asesoria: $asesoria);
                $horarioDTO = new HorarioDTO(horario: $asesoria->horario);
                $materiaDTO = new MateriaDTO(materia: $asesoria->materia_asesor->materia);
                $asesoriaDTO->horario = $horarioDTO;
                $asesoriaDTO->materia = $materiaDTO;
                return $asesoriaDTO;
            });
            $asesoriasPaginadas = new LengthAwarePaginator(
                $asesoriasMap,
                $asesoriasConfirmadasDB['total'],
                $this->numeroRegistrosPorPagina,
                LengthAwarePaginator::resolveCurrentPage(),
                [
                    'path' => LengthAwarePaginator::resolveCurrentPath(),
                    'pageName' => 'page'
                ]
            );
        } catch (\Throwable $th) {
            Log::error($th);
        }
        return $asesoriasPaginadas;
    }

    /**
     * Servicio para crear una asesoria
     *
     * @param array $attributes
     * @return ResponseHelper
     */
    public function crear(array $attributes): ResponseHelper
    {
        $response = new ResponseHelper();
        try {
            $hoy = Carbon::now();
            $fecha = Carbon::now();

            if (array_key_exists('fecha', $attributes)) {
                $fecha = Carbon::parse($attributes['fecha']);
                if ($fecha->dayOfWeek == Carbon::SUNDAY || $fecha->dayOfWeek == Carbon::SATURDAY) {
                    $response->success = false;
                    $response->message = __('messages.asesorias.no_weekends');
                    $response->statusCode = 400;
                    return $response;
                }
            }

            // if ($fecha->dayOfWeek == Carbon::SUNDAY && $fecha->dayOfYear == Carbon::SATURDAY) {
            //     $response->success = false;
            //     $response->message = __('messages.asesorias.no_weekends');
            //     $response->statusCode = 400;
            //     return $response;
            // }

            if ($fecha->lessThan($hoy)) {
                $response->success = false;
                $response->message = __('messages.date_less_than_today', ['attribute' => 'fecha']);
                $response->statusCode = 400;
                return $response;
            }

            $horario = $this->horarioRepository->obtenerPorId($attributes['horario_id']);

            if (!$horario) {
                $response->message = __('messages.not_found', ['name' => 'el Horario']);
                $response->success = false;
                $response->statusCode = 404;
                return $response;
            }

            // Si no ingreso una fecha el usuario entonces se busca la fecha más cercana al dia de la semana del horario del asesor
            if (!array_key_exists('fecha', $attributes)) {
                $i = $fecha->dayOfWeek;
                while ($i != ($horario->dia_semana->value + 1)) {
                    $fecha->addDay();
                    $i = $fecha->dayOfWeek;
                }
            }

            $materiaAsesor = $this->materiaAsesorRepository->obtenerPorMateriaIdAsesorId($horario->materia_id, $horario->profesor_id);

            if (!$materiaAsesor) {
                $response->message = __('messages.not_found', ['name' => 'la Materia del Asesor del Horario seleccionado']);
                $response->success = false;
                $response->statusCode = 400;
                return $response;
            }

            $asesoria = $this->asesoriaRepository->getByHorarioIdFecha($horario->id, $fecha->format('Y-m-d'));

            if (($horario->dia_semana->value + 1) != $fecha->dayOfWeek) {
                $response->success = false;
                $response->message = __('messages.asesorias.mismatched_days');
                $response->statusCode = 400;
                return $response;
            }

            $userAutheticate = $this->userService->getAuthenticatedUserByBearerToken();

            if (!$userAutheticate->hasRole('Alumno')) {
                abort(401);
            }

            $asistencia = new Asistencia();
            $asistencia->alumno_id = $userAutheticate->id;
            $asistencia->estatus = EstatusAsistenciaEnum::PENDIENTE;

            if (!$asesoria) {
                $asesoria = new Asesoria();
                $asesoria->estado = EstatusAsesoriaEnum::PENDIENTE;
                $asesoria->fecha = $fecha->format('Y-m-d');
                $asesoria->materia_asesor_id = $materiaAsesor->id;
                $asesoria->horario_id = $horario->id;

                if ($this->asesoriaRepository->guardar($asesoria, $asistencia)) {
                    $response->success = true;
                    $response->message = __('messages.successful_creation', ['name' => 'la Asesoria.']);
                    return $response;
                }
            }
            else {
                if ($this->userService->verificarAsesoriaYaConfirmada($asesoria->id, $userAutheticate->id)) {
                    $response->success = false;
                    $response->message = __('messages.asesorias.already_confirmed');
                    $response->statusCode = 400;
                    return $response;
                }
                $asistencia->asesoria_id = $asesoria->id;

                if ($this->asistenciaRepository->guardar($asistencia)) {
                    $response->success = true;
                    $response->message = __('messages.succesfull_creation', ['name' => 'la Asesoria']);
                    return $response;
                }
            }
        }
        catch (\Throwable $th) {
            $response->success = false;
            $response->message = __('messages.failed_creation', ['name' => 'la Asesoria']);
            $response->statusCode = 400;
            Log::error($th);
        }
        return $response;
    }
    /**
     * Servicio para que el alumno pueda cancelar una asesoria a la cual ya no quiera asistir
     *
     * @param integer $alumnoId
     * @param integer $asesoriaId
     * @param string $justificacion
     * @return ResponseHelper
     */
    public function cancelarAsesoriaConfirmadaAlumno(int $alumnoId, int $asesoriaId, string $justificacion): ResponseHelper
    {
        $response = new ResponseHelper();
        try {
            $existeAsesoria = $this->asesoriaRepository->existe($asesoriaId);

            if(!$existeAsesoria) {
                $response->success = false;
                $response->message = __('messages.not_found', ['name' => 'la Asesoria a cancelar']);
                $response->statusCode = 404;
                return $response;
            }

            $asistenciaAlumno = $this->asistenciaRepository
                ->obtenerPorAsesoriaIdAlumnoId($asesoriaId, $alumnoId);

            if($asistenciaAlumno === null) {
                $response->message = __('messages.alumnos.no_asesoria_confirmed');
                $response->success = false;
                $response->statusCode = 400;
            }

            $asistenciaAlumno->justificacion = $justificacion;
            $asistenciaAlumno->estatus = EstatusAsistenciaEnum::CANCELADO;

            if($this->asistenciaRepository->guardar($asistenciaAlumno)) {
                $response->success = true;
                $response->message = __('messages.asesorias.asistencia_cancelled_successful');
                return $response;
            }

            $response->success = false;
            $response->message = __('messages.asesorias.asistencia_cancelled_failed');
            $response->statusCode = 400;
        } catch (\Throwable $th) {
            $response->success = false;
            $response->message = __('messages.asesorias.asistencia_cancelled_failed');
            $response->statusCode = 500;
            Log::error($th);
        }
        return $response;
    }

    public function cancelarAsesoriaConfirmadaAsesor(int $profesorId, int $asesorId): ResponseHelper
    {
        $response = new ResponseHelper();
        try {
            $profesor = $this->userService->obtenerProfesorPorId($profesorId);
            $asesoriaDB = $this->asesoriaRepository->getById($asesorId);
            if($profesor->getKey() === null) {
                $response->success = false;
                $response->message = __('messages.not_found', ['name' => 'el Profesor']);
                $response->statusCode = 404;
                return $response;
            }
            if(!$asesoriaDB) {
                $response->success = false;
                $response->message = __('messages.not_found', ['name' => 'la Asesoria']);
                $response->statusCode = 404;
                return $response;
            }

            if($asesoriaDB->estado === EstatusAsesoriaEnum::CANCELADO) {
                $response->success = false;
                $response->message = __('messages.asesorias.already_cancelled');
                $response->statusCode = 400;
                return $response;
            }

            $asesoriaDB->estado = EstatusAsesoriaEnum::CANCELADO;

            if($this->asesoriaRepository->actualizar($asesoriaDB)) {
                $response->success = true;
                $response->message = __('messages.asesorias.successful_cancelled');
                return $response;
            }

            $response->success = false;
            $response->message = __('messages.asesorias.error_cancelled');
            $response->statusCode = 400;
        } catch (\Throwable $th) {
            Log::error($th);
            $response->success = false;
            $response->message = __('messages.asesorias.error_cancelled');
            $response->statusCode = 400;
        }
        return $response;
    }

    /**
     * Servicio que obtiene una lista de las asesoria que el alumno ya tiene confirmadas
     *
     * @param int $alumnoId
     * @return \Illuminate\Pagination\LengthAwarePaginator|null
     */
    public function obtenerColeccionConfirmadosPorAlumnoId(int $alumnoId, int $paginaActual): LengthAwarePaginator|null
    {
        $paginador = null;
        try {
            $relations = [
                'materia_asesor' => ['asesor', 'materia'],
                'asistencias' => function ($query) use ($alumnoId) {
                    $query->where('alumno_id', $alumnoId);
                },
            ];
            $asesorias = $this->asesoriaRepository
            ->obtenerColeccionPaginadaConfirmadosByAlumnoId(
                $alumnoId,
                $relations,
                $paginaActual,
                $this->numeroRegistrosPorPagina
            );
            $result = $this->mapperService->mapAsesoriaToAsesoriaConfirmadaDTOCollection($asesorias['data'], ['horario', 'asesor', 'materia']);
            $result->each(function (AsesoriaDTO $asesoriaConfirmadaDTO) {
                $asesoriaConfirmadaDTO->asesor->unsetProperties(['email', 'curp', 'created_at', 'updated_at']);
            });
            $paginador = new LengthAwarePaginator(
                $result,
                $asesorias['total'],
                $this->numeroRegistrosPorPagina,
                LengthAwarePaginator::resolveCurrentPage(), [
                'path' => LengthAwarePaginator::resolveCurrentPath()
            ]);
        }
        catch (\Throwable $th) {
            Log::error($th);
        }
        return $paginador;
    }
}
