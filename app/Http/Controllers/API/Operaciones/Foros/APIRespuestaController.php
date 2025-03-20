<?php

namespace App\Http\Controllers\API\Operaciones\Foros;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Foros\PostStoreRespuestaRequest;
use App\Http\Requests\API\Foros\UpdateRespuestaRequest;
use App\Services\Interfaces\Foros\RespuestaServiceInterface;
use App\Services\Interfaces\UserServiceInterface;
use Illuminate\Http\Request;

class APIRespuestaController extends Controller {
    private RespuestaServiceInterface $respuestaService;
    private UserServiceInterface $userService;

    public function __construct(
        UserServiceInterface $userService,
        RespuestaServiceInterface $respuestaService
    ) {
        $this->userService = $userService;
        $this->respuestaService = $respuestaService;
    }

    public function index(Request $request, int $foroId)
    {
        $paginaActual = $request->query('page') ?? 1;

        $response = $this->respuestaService->obtenerPaginacion($foroId, $paginaActual);

        return response()->json($response);
    }

    public function store(PostStoreRespuestaRequest $request, int $foroId)
    {
        $request->validated();
        $authUser = $this->userService->getAuthenticatedUserByBearerToken();

        $response = $this->respuestaService->crear($request->all(), $foroId, $authUser->id);

        return response()->json($response, $response->statusCode);
    }

    public function update(UpdateRespuestaRequest $request, int $foroId, int $respuestaId)
    {
        $request->validated();
        $authUser = $this->userService->getAuthenticatedUserByBearerToken();

        $response = $this->respuestaService->actualizar($request->all(), $respuestaId, $authUser->id);

        return response()->json($response, $response->statusCode);
    }

    public function delete(int $foroId, int $respuestaId)
    {
        $authUser = $this->userService->getAuthenticatedUserByBearerToken();

        $response = $this->respuestaService->eliminar($respuestaId, $authUser->id);

        return response()->json($response, $response->statusCode);
    }
}
