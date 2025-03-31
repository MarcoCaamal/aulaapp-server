<?php

namespace App\Http\Controllers\API\Auth;

use App\Models\DTOs\UserDTO;
use OpenApi\Annotations as OA;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\API\LoginRequest;
use App\Services\Interfaces\UserServiceInterface;

class APICuentaController extends Controller
{
    private UserServiceInterface $userService;
    public function __construct(UserServiceInterface $userService) {
        $this->userService = $userService;
    }

    /**
     * @OA\Post(
     *     path="/api/cuentas/login",
     *     tags={"Cuentas"},
     *     summary="Autenticarse en la API",
     *     description="Aquí el usuario puede autenticarse en la API.",
     *     operationId="login",
     *     @OA\Response(
     *         response="200",
     *         description="ok"
     *     ),
     *     @OA\RequestBody(
     *         description="Credenciales del usuario",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/CredencialesUsuario")
     *     )
     * )
     */
    public function login(LoginRequest $request) {
        $request->validated();

        $user = $this->userService->obtenerUsuarioPorEmail($request->email);

        if($user->getKey() === null || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'statusCode' => 400,
                'message' => 'Las credenciales ingresadas son incorrectas.'
            ], 400);
        }

        $userDTO = $this->userService->mapUserToUserDTO($user);

        if($user->hasRole('Profesor')) {
            return response()->json([
                'success' => true,
                'message' => '¡Login Correcto!',
                'statusCode' => 200,
                'data' => [
                    'user' => $userDTO,
                    'token' => $user->createToken('token', ['profesor'])->plainTextToken,
                ]
            ]);
        }

        if($user->hasRole('Alumno')) {
            return response()->json([
                'success' => true,
                'message' => '¡Login Correcto!',
                'statusCode' => 200,
                'data' => [
                    'user' => $userDTO,
                    'token' => $user->createToken('token', ['alumno'])->plainTextToken,
                ]
            ]);
        }

        return response()->json([
            'success' => false,
            'statusCode' => 400,
            'message' => '¡Login Incorrecto!'
        ], 400);
    }

    public function usuario() {
        $user = $this->userService->getAuthenticatedUserByBearerToken();
        $user->load('roles');
        
        return [
            'success' => true,
            'message' => 'Usuario recuperado correctamente',
            'statusCode' => 200,
            'data' => $this->userService->mapUserToUserDTO($user)
        ];
    }
}
