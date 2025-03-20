<?php

namespace App\Http\Controllers\Operaciones;

use App\Models\Semestre;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\SemestreRequest;
use App\Services\Interfaces\SemestreServiceInterface;

class SemestreController extends Controller
{
    private $semestreService;
    public function __construct(SemestreServiceInterface $semestreService)
    {
        $this->semestreService = $semestreService;
    }

    public function index() {
        return view('operaciones.semestres.index', [
            'semestres' => $this->semestreService->obtenerColeccion()
        ]);
    }

    public function create() {
        return view('operaciones.semestres.create');
    }

    public function store(SemestreRequest $request) {
        $request->validated();

        $response = $this->semestreService->crear($request->all());

        if($response->success) {
            session()->flash('success', $response->message);
            return to_route('semestres.index');
        }

        session()->flash('error', $response->message);
        return to_route('ciclos.create');
    }

    public function edit(Request $request, $id) {
        $semestre = Semestre::find($id);

        return view('operaciones.semestres.edit', [
            'semestre' => $semestre
        ]);
    }

    public function update(SemestreRequest $request, $id) {
        $request->validated();

        $response = $this->semestreService->actualizar($request->all(), $id);

        if($response->success) {
            session()->flash('success', $response->message);
            return to_route('semestres.index');
        }

        session()->flash('error', $response->message);
        return to_route('semestres.index');
    }

    public function delete(Request $request, $id) {
        $response = $this->semestreService->eliminar($id);

        if($response->success) {
            return response()->json($response);
        }

        return response()->json($response, $response->statusCode);
    }
}
