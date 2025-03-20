<?php 
namespace App\Swagger\Models;

/**
 * Class CredencialesUsuario
 *
 * @author  Marco Caamal <caamalmarco99@gmail.com>
 *
 * @OA\Schema(
 *     title="Credenciales Usuario",
 *     description="Credenciales del usuario necesarias para poder iniciar sesión.",
 * )
 */
class CredencialesUsuario
{
    /**
     * @OA\Property(
     *     description="Email",
     *     title="Email",
     * )
     *
     * @var string
     */
    private string $email;
    /**
     * @OA\Property(
     *     description="Password",
     *     title="Password",
     * )
     *
     * @var string
     */
    private string $password;
}