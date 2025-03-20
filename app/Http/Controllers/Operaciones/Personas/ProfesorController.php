<?php

namespace App\Http\Controllers\Operaciones\Personas;

use App\Imports\ProfesorImport;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Interfaces\UserServiceInterface;
use App\Services\Interfaces\MateriaServiceInterface;
use App\Http\Requests\StoreProfesorRequest;
use App\Http\Requests\UpdateProfesorRequest;
use Maatwebsite\Excel\Facades\Excel;

class ProfesorController extends Controller
{
    private $userService;
    private $materiaService;

    public function __construct(UserServiceInterface $userService, MateriaServiceInterface $materiaService)
    {
        $this->userService = $userService;
        $this->materiaService = $materiaService;
    }

    public function index() {
        $profesores = $this->userService->obtenerColeccionProfesores();
        return view('operaciones.personas.profesores.index', [
            'profesores' => $profesores
        ]);
    }

    public function create() {
        $materias = $this->materiaService->obtenerColeccion();
        return view('operaciones.personas.profesores.create', [
            'materias' => $materias
        ]);
    }

    public function store(StoreProfesorRequest $request) {
        $request->validated();

        $response = $this->userService->crearProfesor($request->all());

        if($response->success) {
            return to_route('profesores.index')->with('success', $response->message);
        }

        if(!$response->success && $response->statusCode === 400) {
            return to_route('profesores.create')->with('success', $response->message)->withInput();
        }

        return to_route('profesores.index')->with('error', $response->message);
    }
    //TODO: Terminar la vista de carga masiva
    public function getCargaMasiva()
    {
        return view('operaciones.personas.profesores.carga-masiva');
    }
    //TODO: Terminar Post de carga masiva
    public function postCargaMasiva(Request $request)
    {
        Excel::import(new ProfesorImport, $request->file('archivo_carga_masiva'), null, \Maatwebsite\Excel\Excel::CSV);

    }

    public function edit(Request $request, $id) {
        $profesor = $this->userService->obtenerProfesorPorId($id);
        $materias = $this->materiaService->obtenerColeccion();

        return view('operaciones.personas.profesores.edit', [
            'profesor' => $profesor,
            'materias' => $materias
        ]);
    }

    public function update(UpdateProfesorRequest $request, $id) {
        $request->validated();

        $response = $this->userService->actualizarProfesor($request->all(), $id);

        if($response->success) {
            return to_route('profesores.index')->with('success', $response->message);
        }

        return to_route('profesores.index')->with('error', $response->message);
    }

    public function delete($id) {
        $response = $this->userService->eliminar($id);

        if($response->success) {
            return response()->json($response);
        }

        return response()->json($response, $response->statusCode);
    }
}
