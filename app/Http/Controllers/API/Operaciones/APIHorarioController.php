<?php

namespace App\Http\Controllers\API\Operaciones;

use App\Http\Controllers\Controller;
use App\Models\Asesoria;
use App\Models\Ciclo;
use App\Models\Horario;
use App\Services\Interfaces\UserServiceInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

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
        if(!$user) {
            abort(401);
        }
        $userRoles = $user->roles()->get();

        if(!$userRoles->some('name', '=', 'Alumno')) {
            abort(401);
        }

        $asesoria = Asesoria::where('horario_id', '=', $id)->firstOrFail();

        
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        return Horario::where('id', '=', $id)->limit(1)->paginate();
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
