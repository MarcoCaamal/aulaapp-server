<?php

namespace App\Http\Controllers\API\Operaciones;

use App\Enums\EstatusAsesoriaEnum;
use App\Enums\EstatusAsistenciaEnum;
use App\Http\Controllers\Controller;
use App\Models\Asesoria;
use App\Models\Asistencia;
use App\Models\Horario;
use App\Services\Interfaces\UserServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class APIHorarioController extends Controller
{
    public function __construct(
        private UserServiceInterface $_userService
    )
    {
        $this->_userService = $_userService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = $this->_userService->getAuthenticatedUserByBearerToken();
        if(!$user) {
            abort(401);
        }
        $userRoles = $user->roles()->get();

        if($userRoles->some('name', '=', 'Alumno')) {
            $grupoAlumoActivo = $user
                ->grupos()
                ->wherePivot('is_activo', '=', true)
                ->first();
            return Horario::with(['profesor', 'materia'])
                ->whereRelation('materia', 'semestre_id', $grupoAlumoActivo->semestre_id)
                ->paginate();
        }

        if($userRoles->some('name', '=', 'Profesor')) {
            return Horario::with('materia')
                ->where('profesor_id', '=', $user->id)
                ->paginate();
        }
        return Horario::with(['profesor', 'materia'])
            ->paginate();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, int $id)
{
    $user = $this->_userService->getAuthenticatedUserByBearerToken();
    if (!$user) {
        abort(401);
    }
    
    $userRoles = $user->roles()->get();
    if (!$userRoles->contains('name', 'Alumno')) {
        abort(401);
    }
    
    // Obtener el horario
    $horario = Horario::findOrFail($id);
    
    // Calcular la fecha de la asesoría más cercana según el día de la semana
    $hoy = Carbon::now();
    $diaSemana = $horario->dia_semana; // Día de la semana de la asesoría (0 = Lunes, 4 = Viernes)
    
    $fechaAsesoria = $hoy->copy()->next($diaSemana->value + 1); // Busca el siguiente día que coincida con el horario
    
    // Verificar si ya existe una asesoría para este horario en esa fecha
    $asesoria = Asesoria::where('horario_id', $id)
        ->where('fecha', '>=', $hoy->toDateString())
        ->orderBy('fecha', 'asc')
        ->first();
    
    // Si no hay asesoría, crear una nueva
    if (!$asesoria) {
        $asesoria = Asesoria::create([
            'fecha' => $fechaAsesoria->toDateString(),
            'materia_asesor_id' => $horario->materia_id, // Se asume que materia_asesor_id es el mismo de materia_id
            'horario_id' => $id,
            'estado' => EstatusAsesoriaEnum::PENDIENTE,
            'is_activo' => true
        ]);
    }
    
    // Comprobar si el alumno ya tiene una asistencia registrada para esta asesoría
    $asistencia = Asistencia::where('alumno_id', $user->id)
        ->where('asesoria_id', $asesoria->id)
        ->first();
    
    if ($asistencia) {
        return response()->json([
            'success' => false,
            'statusCode' => 400,
            'message' => 'El alumno ya tiene asistencia registrada en esta asesoría'
        ], 400);
    }
    
    // Crear la asistencia con estatus PENDIENTE
    Asistencia::create([
        'asesoria_id' => $asesoria->id,
        'alumno_id' => $user->id,
        'estatus' => EstatusAsistenciaEnum::PENDIENTE,
    ]);
    
    return response()->json([
        'success' => true,
        'statusCode' => 200,
        'message' => 'Asistencia registrada correctamente'
    ]);
}

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        return Horario::with(['materia', 'profesor'])->where('id', '=', $id)->limit(1)->paginate();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
