<?php

namespace App\Http\Controllers\API\Operaciones\Foros;

use App\Enums\Foros\TipoLikeEnum;
use App\Helpers\ResponseHelper;
use Illuminate\Http\Request;
use OpenApi\Attributes as OAT;
use App\Http\Controllers\Controller;
use App\Models\DTOs\Paginacion\PaginadorDTO;
use App\Models\DTOs\Operaciones\Foros\ForoDTO;
use App\Http\Requests\API\Foros\StoreForoRequest;
use App\Services\Interfaces\UserServiceInterface;
use App\Http\Requests\API\Foros\UpdateForoRequest;
use App\Http\Requests\API\Foros\PostDarLikeRequest;
use App\Http\Requests\API\Foros\PostReportarRequest;
use App\Services\Interfaces\Foros\ForoServiceInterface;

use function Termwind\style;

class APIForoController extends Controller
{
    private ForoServiceInterface $foroService;
    private UserServiceInterface $userService;
    public function __construct(ForoServiceInterface $foroService,
    UserServiceInterface $userService)
    {
        $this->foroService = $foroService;
        $this->userService = $userService;
    }
    #[OAT\Get(
        path: '/api/foros',
        tags: ['Foros'],
        summary: 'Obtener Foros',
        description: 'Devuelve un listado de todos los foros paginados por materia',
        operationId: 'GetAllForos',
        parameters: [
            new OAT\Parameter(
                description: 'Número de pagina',
                in: 'query',
                name: 'page',
                required: false,
                schema: new OAT\Schema(
                    type: 'integer',
                    format: 'int64'
                )
            ),
            new OAT\Parameter(
                description: 'ID materia',
                in: 'query',
                name: 'materia_id',
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
                description: 'OK',
                content: new OAT\JsonContent(
                    ref: PaginadorDTO::class
                )
            )
        ],
        security: [
            [
                'bearerAuth' => []
            ]
        ]
    )]
    public function index(Request $request)
    {
        $request->validate([
            'materia_id' => ['required', 'numeric']
        ]);

        if(!$request->query('materia_id')) {
            return response()->json([
                'success' => false,
                'message' => 'Se debe ingresar una ID de una materia',
                'statusCode' => 400
            ], 400);
        }

        $materiaId = $request->query('materia_id');
        $paginaActual = $request->query('page') ?? 1;

        $response = $this->foroService->obtenerPaginacionPorMateriaId($materiaId, $paginaActual);

        return response()->json($response);
    }
    #[OAT\Get(
        path: '/api/users/{userId}/foros',
        tags: ['Foros'],
        summary: 'Obtener Foros de un usuario',
        description: 'Devuelve una lista de foros paginados del usuario',
        operationId: 'GetAllForosUsuario',
        parameters: [
            new OAT\Parameter(
                description: 'ID del usuario',
                in: 'path',
                name: 'userId',
                required: true,
                schema: new OAT\Schema(
                    type: 'integer',
                    format: 'int64'
                )
            ),
            new OAT\Parameter(
                description: 'Número de página',
                in: 'path',
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
                description: 'OK',
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
    public function indexUser(int $userId, Request $request)
    {
        $authUser = $this->userService->getAuthenticatedUserByBearerToken();
        $paginaActual = $request->query('page') ?? 1;

        if($authUser->getKey() !== $userId) {
            abort(403);
        }

        $response = $this->foroService->obtenerPaginacionPorUserId($userId, $paginaActual);

        return response()->json($response);
    }
    #[OAT\Get(
        path: '/api/users/{userId}/foros/{foroId}',
        tags: ['Foros'],
        summary: 'Obtener un foro de un usuario',
        description: 'Devuelve un foro de un usuario',
        operationId: 'GetForoUsuario',
        parameters: [
            new OAT\Parameter(
                description: 'ID usuario',
                in: 'path',
                name: 'userId',
                required: true,
                schema: new OAT\Schema(
                    type: 'integer',
                    format: 'int64'
                )
            ),
            new OAT\Parameter(
                description: 'ID foro',
                in: 'path',
                name: 'foroId',
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
                description: 'OK',
                content: new OAT\JsonContent(
                    ref: ForoDTO::class
                )
            ),
        ],
        security: [
            [
                'bearerAuth' => []
            ]
        ]
    )]
    public function show(int $userId, int $foroId)
    {
        $userAuth = $this->userService->getAuthenticatedUserByBearerToken();

        if($userAuth->getKey() !== $userId) {
            abort(403);
        }

        $foro = $this->foroService->obtenerPorId($foroId, $userId);

        if($foro->getKey() === null) {
            abort(404);
        }

        return response()->json($foro);
    }

    #[OAT\Post(
        path: '/api/users/{userId}/foros',
        tags: ['Foros'],
        summary: 'Publicar foro',
        description: 'Crear un foro',
        operationId: 'PostForo',
        parameters: [
            new OAT\Parameter(
                description: 'ID usuario',
                in: 'path',
                name: 'userId',
                required: true,
                schema: new OAT\Schema(
                    type: 'integer',
                    format: 'int64'
                )
            )
        ],
        requestBody: new OAT\RequestBody(
            content: new OAT\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OAT\Schema(
                    properties: [
                        new OAT\Property(
                            property: 'titulo',
                            type: 'string',
                            required: ['true']
                        ),
                        new OAT\Property(
                            property: 'contenido',
                            type: 'string',
                            required: ['true']
                        ),
                        new OAT\Property(
                            property: 'imagen',
                            type: 'string',
                            format: 'binary'
                        ),
                        new OAT\Property(
                            property: 'materia_id',
                            type: 'integer',
                            format: 'int64',
                        )
                    ],
                    required: ['titulo', 'contenido', 'materia_id']
                )
            )
        ),
        responses: [
            new OAT\Response(
                response: 200,
                description: 'OK',
                content: new OAT\JsonContent(
                    ref: ResponseHelper::class
                )
            )
        ],
        security: [
            [
                'bearerAuth' => []
            ]
        ]
    )]
    public function store(StoreForoRequest $request, int $userId)
    {
        $request->validated();

        $response = $this->foroService->crear($request->all(), $userId);

        return response()->json($response, $response->statusCode);
    }

    #[OAT\Post(
        path: '/api/foros/{foroId}/darLike',
        tags: ['Foros'],
        summary: 'Dar like a un foro',
        description: 'Dar like un foro de un usuario (0 = LIKE, 1 = DISLIKE)',
        operationId: 'PostDarLike',
        parameters: [
            new OAT\Parameter(
                description: 'ID foro',
                in: 'path',
                name: 'foroId',
                required: true,
                schema: new OAT\Schema(
                    type: 'integer',
                    format: 'int64'
                ),
            )
        ],
        requestBody: new OAT\RequestBody(
            content: new OAT\JsonContent(
                properties: [
                    new OAT\Property(
                        property: 'tipo_like',
                        type: 'integer',
                        format: 'int64',
                        default: 0
                    )
                ]
            )
        ),
        responses: [
            new OAT\Response(
                response: 200,
                description: 'OK',
                content: new OAT\JsonContent(
                    ref: ResponseHelper::class
                )
            )
        ],
        security: [
            [
                'bearerAuth' => []
            ]
        ]
    )]
    public function darLike(int $foroId, PostDarLikeRequest $request)
    {
        $request->validated();
        $userAuth = $this->userService->getAuthenticatedUserByBearerToken();
        $tipo_like = $request->input('tipo_like');

        $response = $this->foroService->darLike($foroId, $userAuth->getKey(), $tipo_like);

        return response()->json($response, $response->statusCode);
    }
    #[OAT\Post(
        path: '/api/foros/{foroId}',
        tags: ['Foros'],
        summary: 'Reportar un foro',
        description: 'Reportar foro que el usuario cree inapropiado',
        operationId: 'PostReportar',
        parameters: [
            new OAT\Parameter(
                description: 'ID foro',
                in: 'path',
                name: 'foroId',
                required: true,
                schema: new OAT\Schema(
                    type: 'integer',
                    format: 'int64'
                ),
            )
        ],
        requestBody: new OAT\RequestBody(
            content: new OAT\JsonContent(
                properties: [
                    new OAT\Property(
                        property: 'motivo',
                        type: 'string',
                    )
                ]
            )
        ),
        responses: [
            new OAT\Response(
                response: 200,
                description: 'OK',
                content: new OAT\JsonContent(
                    ref: ResponseHelper::class
                )
            )
        ],
        security: [
            [
                'bearerAuth' => []
            ]
        ]
    )]
    public function reportar(int $foroId, PostReportarRequest $request)
    {
        $request->validated();
        $userAuth = $this->userService->getAuthenticatedUserByBearerToken();
        $motivo = $request->input('motivo');

        $response = $this->foroService->reportar($foroId, $userAuth->getKey(), $motivo);

        return response()->json($response);
    }
    #[OAT\Post(
        path: '/api/users/{userId}/foros/{foroId}',
        tags: ['Foros'],
        summary: 'Actualizar un foro',
        description: 'Actuliza un foro de un usuario',
        operationId: 'PutForo',
        parameters: [
            new OAT\Parameter(
                description: 'method',
                in: 'query',
                name: '_method',
                required: true,
                schema: new OAT\Schema(
                    type: 'string',
                    default: 'PUT'
                )
            ),
            new OAT\Parameter(
                description: 'ID usuario',
                in: 'path',
                name: 'userId',
                required: true,
                schema: new OAT\Schema(
                    type: 'integer',
                    format: 'int64'
                )
            ),
            new OAT\Parameter(
                description: 'ID foro',
                in: 'path',
                name: 'foroId',
                required: true,
                schema: new OAT\Schema(
                    type: 'integer',
                    format: 'int64'
                ),
            )
        ],
        requestBody: new OAT\RequestBody(
            content: new OAT\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OAT\Schema(
                    properties: [
                        new OAT\Property(
                            property: 'titulo',
                            type: 'string',
                            required: ['true']
                        ),
                        new OAT\Property(
                            property: 'contenido',
                            type: 'string',
                            required: ['true']
                        ),
                        new OAT\Property(
                            property: 'imagen',
                            type: 'string',
                            format: 'binary'
                        )
                    ],
                    required: ['titulo', 'contenido']
                )
            )
        ),
        responses: [
            new OAT\Response(
                response: 200,
                description: 'OK',
                content: new OAT\JsonContent(
                    ref: ResponseHelper::class
                )
            )
        ],
        security: [
            [
                'bearerAuth' => []
            ]
        ]
    )]
    public function update(UpdateForoRequest $request, int $userId, int $foroId)
    {
        $request->validated();

        $response = $this->foroService->editar($request->all(), $foroId, $userId);

        return response()->json($response, $response->statusCode);
    }
    #[OAT\Delete(
        path: '/users/{userId}/foros/{foroId}',
        tags: ['Foros'],
        summary: 'Elimina un foro',
        description: 'Elimina un foro de un usuario',
        operationId: 'DeleteForo',
        parameters: [
            new OAT\Parameter(
                description: 'ID usuario',
                in: 'path',
                name: 'userId',
                required: true,
                schema: new OAT\Schema(
                    type: 'integer',
                    format: 'int64'
                )
            ),
            new OAT\Parameter(
                description: 'ID foro',
                in: 'path',
                name: 'foroId',
                required: true,
                schema: new OAT\Schema(
                    type: 'integer',
                    format: 'int64'
                ),
            )
        ],
        responses: [
            new OAT\Response(
                response: 200,
                description: 'OK',
                content: new OAT\JsonContent(
                    ref: ResponseHelper::class
                )
            )
        ],
        security: [
            [
                'bearerAuth' => []
            ]
        ]
    )]
    public function delete(int $userId, int $foroId)
    {
        $userAuth = $this->userService->getAuthenticatedUserByBearerToken();

        if($userAuth->getKey() !== $userId) {
            abort(403);
        }

        $response = $this->foroService->eliminar($foroId, $userId);

        return response()->json($response, $response->statusCode);
    }
}
