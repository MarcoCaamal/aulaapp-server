<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\Interfaces\UserServiceInterface;

class HomeController extends Controller
{
    private $userService;
    public function __construct(UserServiceInterface $userService)
    {
        $this->userService = $userService;
    }
    public function __invoke(Request $request)
    {
        $userId = Auth::id();
        $user = $this->userService->obtenerUsuarioPorId($userId);

        if($user->hasRole('Administrador')) {
            return to_route('dashboard.admin');
        }

        if(!$user->hasVerifiedEmail()) {
            return to_route('verification.notice');
        }

        if($user->hasRole('Profesor') || $user->hasRole('Alumno')) {
            return to_route('user.not.available');
        }
    }
}
