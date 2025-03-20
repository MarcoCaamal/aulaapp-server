<?php

namespace App\Http\Controllers\API\Personas;

use App\Enums\DiaSemanaEnum;
use App\Enums\TurnoEnum;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\User;
use OpenApi\Attributes as OAT;
use App\Http\Requests\API\GetProfesorByIdRequest;
use App\Models\DTOs\ParamsProfesorApiDTO;
use App\Http\Requests\API\GetProfesoresRequest;
use App\Models\DTOs\Operaciones\Personas\ProfesorDTO;
use App\Models\DTOs\Paginacion\PaginadorDTO;
use App\Services\Interfaces\GrupoServiceInterface;
use App\Services\Interfaces\UserServiceInterface;
use App\Services\Interfaces\HorarioServiceInterface;
use OpenApi\Attributes\JsonContent;

class APIProfesorController extends Controller
{
    private HorarioServiceInterface $horarioService;
    private UserServiceInterface $userService;
    private GrupoServiceInterface $grupoService;
    public function __construct(HorarioServiceInterface $horarioServiceInterface,
        UserServiceInterface $userService,
        GrupoServiceInterface $grupoService) {

        $this->horarioService = $horarioServiceInterface;
        $this->userService = $userService;
        $this->grupoService = $grupoService;
    }
    // #[OAT\Get(
    //     path: '/api/profesores',
    //     tags: ['Profesores'],
    //     summary: 'Obtener una listado de profesores',
    //     description: 'Los profesores pueden ser filtrados por nombre, email, curp y turno, estos valores se pasan por query strings. ',
    //     operationId: 'GetAll',
    //     parameters: [
    //         new OAT\Parameter(
    //             name: 'nombre',
    //             in: 'query',
    //             description: 'Filtrar por el coincidencia de nombres y apellidos.',
    //             required: false,
    //             explode: false,
    //             schema: new OAT\Schema(
    //                 type: 'string',
    //             )
    //             ),
    //             new OAT\Parameter(
    //                 name: 'curp',
    //                 in: 'query',
    //                 description: 'Filtrar por el coincidencia de CURP',
    //                 required: false,
    //                 explode: false,
    //                 schema: new OAT\Schema(
    //                     type: 'string',
    //                 )
    //             ),
    //             new OAT\Parameter(
    //                 name: 'email',
    //                 in: 'query',
    //                 description: 'Filtrar por el coincidencia de email.',
    //                 required: false,
    //                 explode: false,
    //                 schema: new OAT\Schema(
    //                     type: 'string',
    //                 )
    //             ),
    //             new OAT\Parameter(
    //                 name: 'turno',
    //                 in: 'query',
    //                 description: 'Filtrar por turno. Esto solo trae los profesores que tengan horarios en el turno seleccionado (0 = Matutino, 1 = Vespertino)',
    //                 required: false,
    //                 explode: false,
    //                 schema: new OAT\Schema(
    //                     ref: TurnoEnum::class,
    //                 ),
    //                 style: 'form'
    //             )
    //     ],
    //     responses: [
    //         new OAT\Response(
    //             response: 200,
    //             description: 'Ok',
    //             content: new OAT\JsonContent(
    //                 ref: PaginadorDTO::class
    //             )
    //         ),
    //         new OAT\Response(
    //             response: 401,
    //             description: 'Unauthorized',
    //         )
    //     ],
    //     security: [
    //         [
    //             'bearerAuth' => []
    //         ]
    //     ]
    // )]
    // public function get(GetProfesoresRequest $request) {
    //     $request->validated();
    //     $queryParams = new ParamsProfesorApiDTO();
    //     $queryParams->fill($request->query());
    //     $userAuthenticate = $this->userService->getUserAuthenticateByBearerToken();
    //     $queryParams->idAlumno = $userAuthenticate->id;

    //     if(!$userAuthenticate->hasRole('Alumno')) {
    //         abort(403);
    //     }

    //     $profesores = $this->userService->getAllProfesoresWithFilterPaginationRelations($queryParams);

    //     return response()->json($profesores);
    // }

    // #[OAT\Get(
    //     path: '/api/profesores/{id}',
    //     tags: ['Profesores'],
    //     summary: 'Obtener una profesor por su ID',
    //     description: 'Se obtiene un profesor por su id, el cual incluyen sus horarios y materias',
    //     operationId: 'GetById',
    //     parameters: [
    //         new OAT\Parameter(
    //             description: 'ID del profesor a obtener',
    //             in: 'path',
    //             name: 'id',
    //             required: true,
    //             schema: new OAT\Schema(
    //                 type: 'integer',
    //                 format: 'int64'
    //             )
    //         )
    //     ],
    //     responses: [
    //         new OAT\Response(
    //             response: 200,
    //             description: 'Ok',
    //             content: new OAT\JsonContent(
    //                 ref: ProfesorDTO::class
    //             )
    //         ),
    //         new OAT\Response(
    //             response: 401,
    //             description: 'Unauthorized',
    //         )
    //     ],
    //     security: [
    //         [
    //             'bearerAuth' => []
    //         ]
    //     ]
    // )]
    // public function getById(GetProfesorByIdRequest $request, int $id) {
    //     $request->validated();

    //     $userAuthenticate = $this->userService->getUserAuthenticateByBearerToken();

    //     if($userAuthenticate->hasRole('Alumno')) {
    //         $grupoActualAlumno = $this->grupoService->getGrupoActualAlumnoById($userAuthenticate->id);
    //         $profesor = $this->userService->getProfesorByProfesorIdSemestreId($id, $grupoActualAlumno->semestre_id);
    //         return response()->json($profesor);
    //     }

    //     if($userAuthenticate->hasRole('Profesor')) {
    //         $profesor = $this->userService->getProfesorById($id);
    //         return response()->json($profesor);
    //     }

    //     abort(403);
    // }
}
