<?php

namespace App\Http\Controllers\Operaciones;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGrupoRequest;
use App\Http\Requests\UpdateGrupoRequest;
use App\Services\Interfaces\GrupoServiceInterface;
use App\Services\Interfaces\SemestreServiceInterface;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class GrupoController extends Controller
{
    private GrupoServiceInterface $grupoService;
    private SemestreServiceInterface $semestreService;
    public function __construct(GrupoServiceInterface $grupoService, SemestreServiceInterface $semestreService)
    {
        $this->grupoService = $grupoService;
        $this->semestreService = $semestreService;
    }

    public function index(): View
    {
        $grupos = $this->grupoService->obtenerColeccion();

        return view('operaciones.grupos.index', [
            'grupos' => $grupos
        ]);
    }

    public function create(): View
    {
        $semestres = $this->semestreService->obtenerColeccion();

        return view('operaciones.grupos.create', [
            'semestres' => $semestres
        ]);
    }
    public function store(StoreGrupoRequest $request): RedirectResponse {
        $request->validated();

        $response = $this->grupoService->crear($request->all());

        if($response->statusCode = 404 && !$response->success) {
            abort(404, $response->message);
        }

        if(!$response->success && $response->statusCode = 400) {
            return to_route('grupos.create')->with('error', $response->message);
        }

        if(!$response->success && $response->statusCode == 500) {
            return to_route('grupos.index')->with('error', $response->message);
        }

        return to_route('grupos.index')->with('success', $response->message);
    }

    public function edit($id) {
        $grupo = $this->grupoService->obtenerPorId($id);
        $semestres = $this->semestreService->obtenerColeccion();

        return view('operaciones.grupos.edit', [
            'grupo' => $grupo,
            'semestres' => $semestres
        ]);
    }
    public function update(UpdateGrupoRequest $request, int $id): RedirectResponse
    {
        $request->validated();

        $response = $this->grupoService->actualizar($request->all(), $id);

        if($response->statusCode = 404 && !$response->success) {
            abort(404, $response->message);
        }

        if(!$response->success && $response->statusCode = 400) {
            return to_route('grupos.edit')->with('error', $response->message);
        }

        if(!$response->success && $response->statusCode == 500) {
            return to_route('grupos.index')->with('error', $response->message);
        }

        return to_route('grupos.index')->with('success', $response->message);
    }
    public function delete($id): JsonResponse
    {
        $response = $this->grupoService->eliminar($id);

        if(!$response->success) {
            return response()->json($response, $response->statusCode);
        }

        return response()->json($response);
    }
}
