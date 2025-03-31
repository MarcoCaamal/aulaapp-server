<?php

namespace App\Http\Controllers\API\Operaciones;

use App\Http\Controllers\Controller;
use App\Models\Asesoria;
use App\Services\Interfaces\UserServiceInterface;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;

class APIAsistenciaController extends Controller
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
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $user = $this->_userService->getAuthenticatedUserByBearerToken();

        if(!$user) {
            abort(404);
        }

        return $user->roles()->get();
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
