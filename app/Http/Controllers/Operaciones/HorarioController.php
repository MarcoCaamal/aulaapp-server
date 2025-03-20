<?php

namespace App\Http\Controllers\Operaciones;

use App\Enums\DiaSemanaEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateHorarioRequest;
use App\Services\Interfaces\UserServiceInterface;
use App\Services\Interfaces\HorarioServiceInterface;
use App\Services\Interfaces\MateriaServiceInterface;
use App\Http\Requests\StoreHorarioRequest;
use Illuminate\Http\Request;

class HorarioController extends Controller
{
    private UserServiceInterface $userService;
    private MateriaServiceInterface $materiaService;
    private HorarioServiceInterface $horarioService;
    public function __construct(UserServiceInterface $userService, MateriaServiceInterface $materiaService, HorarioServiceInterface $horarioService)
    {
        $this->userService = $userService;
        $this->materiaService = $materiaService;
        $this->horarioService = $horarioService;
    }

    public function index($idProfesor)
    {
        $profesor = $this->userService->obtenerProfesorPorIdConRelaciones($idProfesor, [
            'materias',
            'horarios' => [
                'materia'
            ]
        ]);

        if ($profesor->getKey() === null) {
            abort(404);
        }

        if ($profesor->materias->isEmpty()) {
            return to_route('profesores.index')->with('error', __('messages.profesor_materias_empty'));
        }

        return view('operaciones.horarios.index', [
            'profesor' => $profesor
        ]);
    }

    public function create($idProfesor)
    {
        $profesor = $this->userService->obtenerProfesorPorIdConRelaciones($idProfesor, ['materias']);
        $diasSemanas = DiaSemanaEnum::toArray();

        if ($profesor->getKey() === null) {
            abort(404);
        }

        if ($profesor->materias->isEmpty()) {
            return to_route('horarios.index', ['idProfesor' => $profesor->id])->with('error', __('messages.profesor_materias_empty'));
        }

        return view('operaciones.horarios.create', [
            'profesor' => $profesor,
            'materias' => $profesor->materias,
            'diasSemana' => $diasSemanas
        ]);
    }

    public function store(StoreHorarioRequest $request, $idProfesor)
    {
        $request->validated();

        $response = $this->horarioService->crear($request->all(), $idProfesor);

        if (!$response->success && $response->statusCode === 404) {
            abort(404, $response->message);
        }

        if (!$response->success && $response->statusCode === 400) {
            return to_route('horarios.create', ['idProfesor' => $idProfesor])->with('error', $response->message)->withInput();
        }

        if (!$response->success) {
            return to_route('horarios.index', ['idProfesor' => $idProfesor])->with('error', $response->message);
        }

        return to_route('horarios.index', ['idProfesor' => $idProfesor])->with('success', $response->message);
    }

    public function edit($idProfesor, $idHorario) {
        $horario = $this->horarioService->obtenerPorId($idHorario);
        $profesor = $this->userService->obtenerProfesorPorIdConRelaciones($idProfesor, ['materias']);

        if($horario->getKey() === null || $profesor->getKey() === null) {
            abort(404);
        }

        if($horario->profesor_id != $profesor->getKey()) {
            abort(404);
        }

        if ($profesor->materias->isEmpty()) {
            return to_route('horarios.index', ['idProfesor' => $profesor->id])->with('error', __('messages.profesor_materias_empty'));
        }

        return view('operaciones.horarios.edit', [
            'horario' => $horario,
            'profesor' => $profesor,
            'materias' => $profesor->materias,
            'diasSemana' => DiaSemanaEnum::toArray()
        ]);
    }

    public function update(UpdateHorarioRequest $request, $idProfesor, $idHorario) {
        $request->validated();

        $response = $this->horarioService->actualizar($request->all(), $idProfesor, $idHorario);

        if($response->success) {
            return to_route('horarios.index', ['idProfesor' => $idProfesor])->with('success', $response->message);
        }

        if(!$response->success && $response->statusCode === 400) {
            return to_route('horarios.create', ['idProfesor' => $idProfesor])->with('error', $response->message)->withInput();
        }

        return to_route('horarios.index', ['idProfesor' => $idProfesor])->with('error', $response->message);
    }

    public function delete(Request $request, $idProfesor, $idHorario) {
        $response = $this->horarioService->eliminar($idProfesor, $idHorario);

        return response()->json($response, $response->statusCode);
    }

    public function diasDisponibles()
    {
        $diasDisponibles = DiaSemanaEnum::toArray();

        return response()->json($diasDisponibles);
    }
}
