<?php

namespace App\Http\Controllers\Operaciones;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Interfaces\MateriaServiceInterface;
use App\Http\Requests\StoreMateriaRequest;
use App\Services\Interfaces\SemestreServiceInterface;
use App\Http\Requests\UpdateMateriaRequest;

class MateriaController extends Controller
{
    private $materiaService;
    private $semestreService;
    public function __construct(MateriaServiceInterface $materiaService, SemestreServiceInterface $semestreService)
    {
        $this->materiaService = $materiaService;
        $this->semestreService = $semestreService;
    }
    public function index() {
        return view('operaciones.materias.index', [
            'materias' => $this->materiaService->obtenerColeccionConRelaciones(['semestre'])
        ]);
    }

    public function create() {
        return view('operaciones.materias.create', [
            'semestres' => $this->semestreService->obtenerColeccion()
        ]);
    }

    public function store(StoreMateriaRequest $request) {
        $request->validated();
        $response = $this->materiaService->crear($request->all());

        if(!$response->success && $request->statusCode === 404) {
            abort(404);
        }

        if(!$response->success && $request->statusCode === 400) {
            return back()->withInput()->with('error', $response->message);
        }

        if($response->success) {
            return to_route('materias.index')->with('success', $response->message);
        }

        return to_route('materias.index')->with('error', $response->message);
    }

    public function edit(Request $request, $id) {
        return view('operaciones.materias.edit', [
            'materia' => $this->materiaService->obtenerPorId($id),
            'semestres' => $this->semestreService->obtenerColeccion()
        ]);
    }

    public function update(UpdateMateriaRequest $request, $id) {
        $request->validated();

        $response = $this->materiaService->actualizar($request->all(), $id);

        if(!$response->success && $request->statusCode == 404) {
            abort(404);
        }

        if(!$response->success && $request->statusCode === 400) {
            return back()->withInput()->with('error', $response->message);
        }

        if($response->success) {
            return to_route('materias.index')->with('success', $response->message);
        }

        return to_route('materias.index')->with('error', $response->message);
    }

    public function delete(Request $request, $id) {
        $response = $this->materiaService->eliminar($id);

        if($response->success) {
            return response()->json($response);
        }

        return response()->json($response, $response->statusCode);
    }
}
