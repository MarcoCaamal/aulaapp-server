<?php

namespace App\Http\Controllers\API\Operaciones\Desafios;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Desafios\PostStoreDesafioRequest;
use App\Http\Requests\API\Desafios\PutUpdateDesafioRequest;
use App\Services\Interfaces\Desafios\DesafioServiceInterface;
use App\Services\Interfaces\UserServiceInterface;
use Illuminate\Http\Request;

class APIDesafioController extends Controller
{
    private UserServiceInterface $userService;
    private DesafioServiceInterface $desafioService;

    public function __construct(UserServiceInterface $userService,
        DesafioServiceInterface $desafioService
    ) {
        $this->userService = $userService;
        $this->desafioService = $desafioService;
    }

    public function index()
    {

    }

    public function indexProfesor()
    {

    }

    public function show()
    {

    }

    public function store(PostStoreDesafioRequest $request, int $profesorId)
    {
        $authUser = $this->userService->getAuthenticatedUserByBearerToken();

        if($authUser->id !== $profesorId) {
            abort(403);
        }

        $response = $this->desafioService->crear($request->except(['materia_id']), $request->materia_id, $profesorId);

        return response()->json($response, $response->statusCode);
    }

    public function update(PutUpdateDesafioRequest $request, int $profesorId, int $desafioId)
    {
        $authUser = $this->userService->getAuthenticatedUserByBearerToken();

        if($authUser->id !== $profesorId) {
            abort(403);
        }

        $response = $this->desafioService->editar($request->all(), $profesorId, $desafioId);

        return response()->json($response, $response->statusCode);
    }

    public function delete(int $profesorId, int $desafioId)
    {
        $authUser = $this->userService->getAuthenticatedUserByBearerToken();

        if($authUser->id !== $profesorId) {
            abort(403);
        }

        $response = $this->desafioService->eliminar($desafioId, $profesorId);

        return response()->json($response, $response->statusCode);
    }
}
