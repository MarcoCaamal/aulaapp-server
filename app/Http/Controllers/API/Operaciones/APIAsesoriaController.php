<?php

namespace App\Http\Controllers\API\Operaciones;

use App\Enums\TurnoEnum;
use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\GetAllAsesoriasDisponiblesRequest;
use App\Models\DTOs\Paginacion\PaginadorDTO;
use App\Services\Interfaces\UserServiceInterface;
use App\Services\Interfaces\AsesoriaServiceInterface;
use App\Services\Interfaces\GrupoServiceInterface;
use Illuminate\Http\Request;
use OpenApi\Attributes as OAT;

class APIAsesoriaController extends Controller
{
    private UserServiceInterface $userService;
    private AsesoriaServiceInterface $asesoriaService;
    private GrupoServiceInterface $grupoService;
    public function __construct(
        UserServiceInterface $userService, AsesoriaServiceInterface $asesoriaService,
        GrupoServiceInterface $grupoService
    ) {
        $this->userService = $userService;
        $this->asesoriaService = $asesoriaService;
        $this->grupoService = $grupoService;
    }

    #[OAT\Get(
        path: '/api/alumnos/{idAlumno}/asesorias-confirmadas',
        tags: ['Asesorias'],
        summary: 'Obtener Asesorias Confirmadas',
        description: 'Devuelve las asesorias confirmadas del alumno',
        operationId: 'GetAllConfirmadas',
        parameters: [
            new OAT\Parameter(
                description: 'ID del alumno',
                in: 'path',
                name: 'idAlumno',
                required: true,
                schema: new OAT\Schema(
                    type: 'integer',
                    format: 'int64'
                )
            ),
            new OAT\Parameter(
                description: 'Numero de página que por defecto es la 1',
                in: 'query',
                name: 'page',
                required: false,
                schema: new OAT\Schema(
                    type: 'integer',
                    format: 'int64',
                    default: 1
                )
            )
        ],
        responses: [
            new OAT\Response(
                response: 200,
                description: 'Ok',
                content: new OAT\JsonContent(
                    ref: PaginadorDTO::class
                )
            ),
            new OAT\Response(
                response: 401,
                description: 'Unauthorized',
            )
        ],
        security: [
            [
                'bearerAuth' => []
            ]
        ]
    )]
    public function getConfirmadasAlumno(int $idAlumno, Request $request)
    {
        $userAuth = $this->userService->getAuthenticatedUserByBearerToken();
        $paginaActual = $request->query('page') ?? 1;

        if ($userAuth->getKey() !== $idAlumno) {
            abort(404);
        }

        $asesorias = $this->asesoriaService
            ->obtenerColeccionConfirmadosPorAlumnoId($userAuth->getKey(), $paginaActual);

        return $asesorias;
    }

    #[OAT\Get(
        path: '/api/asesorias/horarios-disponibles',
        tags: ['Asesorias'],
        summary: 'Obtener Asesorias Disponibles para el alumno',
        description: 'Devuelve las asesorias disponibles para el alumno',
        operationId: 'GetAllDisponibles',
        parameters: [
            new OAT\Parameter(
                description: 'Filtrar por Hora de Inicio',
                in: 'query',
                name: 'horaInicio',
                required: false,
                schema: new OAT\Schema(
                    type: 'string',
                    format: 'date'
                )
            ),
            new OAT\Parameter(
                description: 'Filtrar por Hora de Fin',
                in: 'query',
                name: 'horaFin',
                required: false,
                schema: new OAT\Schema(
                    type: 'string',
                    format: 'date'
                )
            ),
            new OAT\Parameter(
                description: 'Filtrar por nombre del profesor',
                in: 'query',
                name: 'nombre',
                required: false,
                schema: new OAT\Schema(
                    type: 'string'
                )
            ),
            new OAT\Parameter(
                description: 'Filtrar por turno (0 = Matutino, 1 = Vespertino)',
                in: 'query',
                name: 'turno',
                required: false,
                style: 'form',
                schema: new OAT\Schema(
                    ref: TurnoEnum::class
                )
            ),
            new OAT\Parameter(
                description: 'Número de la página actual que por defecto es 1',
                in: 'query',
                name: 'page',
                required: false,
                schema: new OAT\Schema(
                    type: 'integer',
                    format: 'int64',
                    default: 1
                )
            )
        ],
        responses: [
            new OAT\Response(
                response: 200,
                description: 'Ok',
                content: new OAT\JsonContent(
                    ref: PaginadorDTO::class
                )
            ),
            new OAT\Response(
                response: 401,
                description: 'Unauthorized',
            )
        ],
        security: [
            [
                'bearerAuth' => []
            ]
        ]
    )]
    public function getAllDisponibles(GetAllAsesoriasDisponiblesRequest $request)
    {
        $request->validated();
        $filtros = $request->query();
        $paginaActual = $request->query('page') ?? 1;
        $userAuth = $this->userService->getAuthenticatedUserByBearerToken();

        $result = $this->asesoriaService
            ->obtenerPaginacionHorariosAsesoriasPorFiltrosAlumnoId(
                $userAuth->getKey(),
                $filtros,
                $paginaActual
            );

        return response()->json($result);
    }
    #[OAT\Get(
        path: '/api/asesorias/horarios-disponibles/cantidad-por-turnos',
        tags: ['Asesorias'],
        summary: 'Obtener Cantidad de Asesorias Disponibles por Turnos.',
        description: 'Devuelve el número de asesorias disponibles para el alumno por turnos.',
        operationId: 'GetCantidadAsesoriasDisponiblesPorTurnos',
        responses: [
            new OAT\Response(
                response: 200,
                description: 'Ok',
                content: new OAT\JsonContent(
                    properties: [
                        new OAT\Property(
                            property: 'matutinos',
                            type: 'integer',
                            format: 'int64'
                        ),
                        new OAT\Property(
                            property: 'vespertinos',
                            type: 'integer',
                            format: 'int64'
                        )
                    ]
                )
            ),
            new OAT\Response(
                response: 401,
                description: 'Unauthorized',
            )
        ],
        security: [
            [
                'bearerAuth' => []
            ]
        ]
    )]
    public function getCountPorTurnos()
    {
        $userAuth = $this->userService->getAuthenticatedUserByBearerToken();
        $grupoActualAlumno = $this->grupoService->obtenerGrupoActualAlumnoPorIdAlumno($userAuth->getKey());

        if (!$grupoActualAlumno) {
            return response()->json([
                'message' => __('messages.users.alumno_without_group'),
                'success' => false,
                'statusCode' => 400
            ], 400);
        }

        $response = $this->asesoriaService->obtenerCountMatutinosVespertinos($grupoActualAlumno->semestre_id);

        return response()->json($response);
    }
    #[OAT\Get(
        path: '/api/profesores/{profesorId}/asesorias/finalizadas',
        tags: ['Asesorias'],
        summary: 'Obtener lista paginada de asesorias finalizadas del profesor.',
        description: 'Devuelve una lista paginada de las asesorias finalizadas del profesor.',
        operationId: 'GetAllFinalizadasProfesor',
        parameters: [
            new OAT\Parameter(
                description: 'ID del profesor',
                in: 'path',
                name: 'profesorId',
                required: true,
                schema: new OAT\Schema(
                    type: 'integer',
                    format: 'int64'
                )
            ),
            new OAT\Parameter(
                description: 'Número de la página actual que por defecto es 1',
                in: 'query',
                name: 'page',
                required: false,
                schema: new OAT\Schema(
                    type: 'integer',
                    format: 'int64',
                    default: 1
                )
            )
        ],
        responses: [
            new OAT\Response(
                response: 200,
                description: 'Ok',
                content: new OAT\JsonContent(
                    ref: PaginadorDTO::class
                )
            ),
            new OAT\Response(
                response: 401,
                description: 'Unauthorized',
            )
        ],
        security: [
            [
                'bearerAuth' => []
            ]
        ]
    )]
    public function getAllFinalizadasProfesor(int $profesorId, Request $request)
    {
        $userAuth = $this->userService->getAuthenticatedUserByBearerToken();
        $paginaActual = $request->query('page') ?? 1;

        if ($userAuth->getKey() !== $profesorId) {
            abort(404);
        }

        $response = $this->asesoriaService
            ->obtenerPaginacionFinalizadasAsesorPorProfesorId($profesorId, $paginaActual);

        return response()->json($response);
    }
    #[OAT\Get(
        path: '/api/profesores/{profesorId}/asesorias-confirmadas',
        tags: ['Asesorias'],
        summary: 'Obtener lista paginada de asesorias confirmadas de un profesor.',
        description: 'Devuelve una lista paginada de las asesorias confirmadas de un profesor.',
        operationId: 'GetAllConfirmadasProfesor',
        parameters: [
            new OAT\Parameter(
                description: 'ID del profesor',
                in: 'path',
                name: 'profesorId',
                required: true,
                schema: new OAT\Schema(
                    type: 'integer',
                    format: 'int64'
                )
            ),
            new OAT\Parameter(
                description: 'Número de la página actual que por defecto es 1',
                in: 'query',
                name: 'page',
                required: false,
                schema: new OAT\Schema(
                    type: 'integer',
                    format: 'int64',
                    default: 1
                )
            )
        ],
        responses: [
            new OAT\Response(
                response: 200,
                description: 'Ok',
                content: new OAT\JsonContent(
                    ref: PaginadorDTO::class
                )
            ),
            new OAT\Response(
                response: 401,
                description: 'Unauthorized',
            )
        ],
        security: [
            [
                'bearerAuth' => []
            ]
        ]
    )]
    public function getAllConfirmadasProfesor(int $profesorId, Request $request)
    {
        $userAuth = $this->userService->getAuthenticatedUserByBearerToken();
        $paginaActual = $request->query('page') ?? 1;
        if($userAuth->getKey() !== $profesorId) {
            abort(404);
        }

        $response = $this->asesoriaService
            ->obtenerPaginacionConfirmadasAsesorPorProfesorId($profesorId, $paginaActual);

        return response()->json($response);
    }
    #[OAT\Get(
        path: '/api/profesores/{profesorId}/asesorias-confirmadas/{asesoriaId}/lista-alumnos',
        tags: ['Asesorias'],
        summary: 'Obtener lista paginada de alumnos que tienen confirmada su asistencia a una asesoria.',
        description: 'Devuelve una lista paginada de los alumnos que tiene una asistencia confirmada a la asesoria confirmada.',
        operationId: 'GetListaALumnosAsesoria',
        parameters: [
            new OAT\Parameter(
                description: 'ID del profesor',
                in: 'path',
                name: 'profesorId',
                required: true,
                schema: new OAT\Schema(
                    type: 'integer',
                    format: 'int64'
                )
                ),
                new OAT\Parameter(
                    description: 'ID de la asesoria confirmada',
                    in: 'path',
                    name: 'asesoriaId',
                    required: true,
                    schema: new OAT\Schema(
                        type: 'integer',
                        format: 'int64'
                    )
                ),
                new OAT\Parameter(
                    description: 'Número de la página actual que por defecto es 1',
                    in: 'query',
                    name: 'page',
                    required: false,
                    schema: new OAT\Schema(
                        type: 'integer',
                        format: 'int64',
                        default: 1
                    )
                )
        ],
        responses: [
            new OAT\Response(
                response: 200,
                description: 'Ok',
                content: new OAT\JsonContent(
                    ref: PaginadorDTO::class
                )
            ),
            new OAT\Response(
                response: 401,
                description: 'Unauthorized',
            )
        ],
        security: [
            [
                'bearerAuth' => []
            ]
        ]
    )]
    public function getListaAlumnosAsesoria(int $profesorId, int $asesoriaId,
        Request $request)
    {
        $userAuth = $this->userService->getAuthenticatedUserByBearerToken();
        $paginaActual = $request->query('page') ?? 1;

        if($userAuth->getKey() !== $profesorId) {
            abort(404);
        }

        $response = $this->asesoriaService
            ->obtenerPaginacionListaAlumnosAsesoriaPorAsesoriaId(
            $asesoriaId,
            $paginaActual);

        return response()->json($response);
    }
    #[OAT\Post(
        path: '/api/alumnos/{alumnoId}/horarios-disponibles/{horarioId}/agendar',
        tags: ['Asesorias'],
        summary: 'Agenda una asesoria',
        description: 'El alumno agenda una asesoria al la cual quiere asistir. Devuelve una objeto con la respuesta de que si la asesoria se agendo con exito o hubo errores.',
        operationId: 'PostAgendarAsesoriaAlumno',
        parameters: [
            new OAT\Parameter(
                description: 'ID del alumno que quiere agendar una asesoria',
                in: 'path',
                name: 'alumnoId',
                required: true,
                schema: new OAT\Schema(
                    type: 'integer',
                    format: 'int64'
                )
            ),
            new OAT\Parameter(
                description: 'ID del horario de la asesoria a agendar',
                in: 'path',
                name: 'horarioId',
                required: true,
                schema: new OAT\Schema(
                    type: 'integer',
                    format: 'int64'
                )
            ),
        ],
        responses: [
            new OAT\Response(
                response: 200,
                description: 'Ok',
                content: new OAT\JsonContent(
                    ref: ResponseHelper::class
                )
            ),
            new OAT\Response(
                response: 401,
                description: 'Unauthorized',
            )
        ],
        requestBody: new OAT\RequestBody(
            content: new OAT\JsonContent(
                properties: [
                    new OAT\Property(
                        property: 'fecha',
                        type: 'string',
                        format: 'date'
                    )
                ]
            )
        ),
        security: [
            [
                'bearerAuth' => []
            ]
        ]
    )]
    public function postAgendarAlumno(Request $request, int $alumnoId, int $horarioId)
    {
        $request->validate([
            'fecha' => ['nullable', 'date_format:Y-m-d']
        ]);

        $userAuth = $this->userService->getAuthenticatedUserByBearerToken();

        if ($userAuth->getKey() !== $alumnoId) {
            abort(404);
        }

        $attributes = [];
        if ($request->fecha !== null) {
            $attributes['fecha'] = $request->fecha;
        }
        $attributes['horario_id'] = $horarioId;
        $attributes['alumno_id'] = $alumnoId;

        $response = $this->asesoriaService->crear($attributes);

        return response()->json($response, $response->statusCode);
    }
    #[OAT\Put(
        path: '/api/alumnos/{alumnoId}/asesorias-confirmadas/{asesoriaId}/cancelar',
        tags: ['Asesorias'],
        summary: 'Cancela la una asesoria confirmada del alumno',
        description: 'Cancela la asesoria a la cual el alumno yo quiere asistir. El método devuelve una respuesta de que si la operación fue correcta o algo salio mal.',
        operationId: 'putCancelarAsesoriaConfirmadaAlumno',
        parameters: [
            new OAT\Parameter(
                description: 'ID del alumno que quiere cancelar una asistencia a una asesoria',
                in: 'path',
                name: 'alumnoId',
                required: true,
                schema: new OAT\Schema(
                    type: 'integer',
                    format: 'int64'
                )
            ),
            new OAT\Parameter(
                description: 'ID de la asesoria confirmada a cancelar',
                in: 'path',
                name: 'asesoriaId',
                required: true,
                schema: new OAT\Schema(
                    type: 'integer',
                    format: 'int64'
                )
            ),
        ],
        responses: [
            new OAT\Response(
                response: 200,
                description: 'Ok',
                content: new OAT\JsonContent(
                    ref: ResponseHelper::class
                )
            ),
            new OAT\Response(
                response: 401,
                description: 'Unauthorized',
            )
        ],
        requestBody: new OAT\RequestBody(
            content: new OAT\JsonContent(
                properties: [
                    new OAT\Property(
                        property: 'justificacion',
                        type: 'string',
                    )
                ]
            )
        ),
        security: [
            [
                'bearerAuth' => []
            ]
        ]
    )]
    public function putCancelarAsesoriaAlumno(int $alumnoId, int $asesoriaId, Request $request)
    {
        $request->validate([
            'justificacion' => ['required', 'string', 'max:500']
        ]);
        $userAuth = $this->userService->getAuthenticatedUserByBearerToken();

        if ($userAuth->getKey() !== $alumnoId) {
            abort(404);
        }

        $response = $this->asesoriaService
            ->cancelarAsesoriaConfirmadaAlumno($alumnoId, $asesoriaId, $request->justificacion);

        return response()->json($response, $response->statusCode);
    }
    #[OAT\Put(
        path: '/api/profesores/{profesorId}/asesorias-confirmadas/{asesoriaId}/cancelar',
        tags: ['Asesorias'],
        summary: 'Cancela la una asesoria confirmada del alumno',
        description: 'Cancela la asesoria a la cual el alumno yo quiere asistir. El método devuelve una respuesta de que si la operación fue correcta o algo salio mal.',
        operationId: 'putCancelarAsesoriaConfirmadaAsesor',
        parameters: [
            new OAT\Parameter(
                description: 'ID del profesor que quiere cancelar una asesoria',
                in: 'path',
                name: 'profesorId',
                required: true,
                schema: new OAT\Schema(
                    type: 'integer',
                    format: 'int64'
                )
            ),
            new OAT\Parameter(
                description: 'ID de la asesoria confirmada a cancelar',
                in: 'path',
                name: 'asesoriaId',
                required: true,
                schema: new OAT\Schema(
                    type: 'integer',
                    format: 'int64'
                )
            ),
        ],
        responses: [
            new OAT\Response(
                response: 200,
                description: 'Ok',
                content: new OAT\JsonContent(
                    ref: ResponseHelper::class
                )
            ),
            new OAT\Response(
                response: 401,
                description: 'Unauthorized',
            )
        ],
        security: [
            [
                'bearerAuth' => []
            ]
        ]
    )]
    public function putCancelarAsesoriaProfesor(int $asesoriaId, int $profesorId)
    {
        $userAuth = $this->userService->getAuthenticatedUserByBearerToken();

        if($userAuth->getKey() !== $profesorId) {
            abort(404);
        }

        $response = $this->asesoriaService
            ->cancelarAsesoriaConfirmadaAsesor($profesorId, $asesoriaId);

        return response()->json($response, $response->statusCode);
    }

}
