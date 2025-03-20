<?php

namespace App\Http\Controllers\Operaciones\Personas;

use Illuminate\View\View;
use App\Http\Controllers\Controller;
use App\Services\Interfaces\UserServiceInterface;
use App\Services\Interfaces\GrupoServiceInterface;
use App\Http\Requests\StoreAlumnoRequest;
use App\Http\Requests\UpdateAlumnoRequest;

class AlumnoController extends Controller
{
    private UserServiceInterface $userService;
    private GrupoServiceInterface $grupoService;
    public function __construct(UserServiceInterface $userService, GrupoServiceInterface $grupoService) {
        $this->userService = $userService;
        $this->grupoService = $grupoService;
    }
    public function index(): View {
        $alumnos = $this->userService->obtenerColeccionAlumnos();

        return view('operaciones.personas.alumnos.index', [
            'alumnos' => $alumnos
        ]);
    }

    public function create(): View {
        $grupos = $this->grupoService->obtenerColeccion();

        return view('operaciones.personas.alumnos.create', [
            'grupos' => $grupos
        ]);
    }

    public function store(StoreAlumnoRequest $request) {
        $request->validated();

        $response = $this->userService->crearAlumno($request->all());
        if(!$response->success && $response->statusCode === 404) {
            abort(404, $response->message);
        }

        if(!$response->success && $response->statusCode === 400) {
            return to_route('alumnos.create')->with('error', $response->message)->withInput();
        }

        if(!$response->success) {
            return to_route('alumnos.index')->with('error', $response->message);
        }

        return to_route('alumnos.index')->with('success', $response->message);
    }

    public function edit($id): View {
        $alumno = $this->userService->obtenerAlumnoPorId($id);
        $grupos = $this->grupoService->obtenerColeccion();
        $grupoAlumno = $alumno->grupos->where('pivot.is_activo', true)->first();
        return view('operaciones.personas.alumnos.edit', [
            'alumno' => $alumno,
            'grupos' => $grupos,
            'grupoAlumno' => $grupoAlumno
        ]);
    }

    public function update(UpdateAlumnoRequest $request, $id) {
        $request->validated();

        $response = $this->userService->actualizarAlumno($request->all(), $id);

        if(!$response->success && $response->statusCode === 404) {
            abort(404, $response->message);
        }

        if(!$response->success && $response->statusCode === 400) {
            return to_route('alumnos.create')->with('error', $response->message)->withInput();
        }

        if(!$response->success) {
            return to_route('alumnos.index')->with('error', $response->message);
        }

        return to_route('alumnos.index')->with('success', $response->message);
    }

    public function delete($id)
    {
        $response = $this->userService->eliminar($id);

        if(!$response->success) {
            return response()->json($response, $response->statusCode);
        }

        return response()->json($response);
    }
}
