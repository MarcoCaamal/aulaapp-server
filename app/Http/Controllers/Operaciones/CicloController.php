<?php

namespace App\Http\Controllers\Operaciones;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\CicloRequest;
use App\Services\Interfaces\CicloServiceInterface;

class CicloController extends Controller
{
    private $cicloService;

    public function __construct(CicloServiceInterface $cicloService)
    {
        $this->cicloService = $cicloService;
    }

    public function index()
    {
        // $ciclos = Ciclo::all();

        return view('operaciones.ciclos.index', [
            'ciclos' => $this->cicloService->obtenerColeccion()
        ]);
    }

    public function create()
    {
        return view('operaciones.ciclos.create');
    }

    public function store(CicloRequest $request)
    {
        $request->validated();

        $response = $this->cicloService->crear($request->all());

        if ($response->success) {
            session()->flash('success', $response->message);
            return to_route('ciclos.index');
        }

        return to_route('ciclos.index')->with('error', $response->message);
    }

    public function edit(Request $request, $id)
    {
        $ciclo = $this->cicloService->obtenerPorId($id);

        if ($ciclo->is_activo) {
            session()->flash('error', 'No se puede editar un ciclo activo.');
            return to_route('ciclos.index');
        }

        return view('operaciones.ciclos.edit', [
            'ciclo' => $ciclo
        ]);
    }

    public function update(CicloRequest $request, $id)
    {
        $request->validated();

        $response = $this->cicloService->actualizar($request->all(), $id);

        if($response->success) {
            return to_route('ciclos.index')->with('success', $response->message);
        }

        return to_route('ciclos.index')->with('error', $response->message);
    }

    public function delete(Request $request, $id)
    {
        $response = $this->cicloService->eliminar($id);

        return response()->json($response, $response->statusCode);
    }

    public function activate(Request $request, $id)
    {
        $response = $this->cicloService->activar($id);

        if($response->success) {
            return to_route('ciclos.index')->with('success', $response->message);
        }

        return to_route('ciclos.index')->with('error', $response->message);
    }

    public function desactivate(Request $request, $id)
    {
        $response = $this->cicloService->desactivar($id);

        if($response->success) {
            return to_route('ciclos.index')->with('success', $response->message);
        }

        return to_route('ciclos.index')->with('error', $response->message);
    }
}
