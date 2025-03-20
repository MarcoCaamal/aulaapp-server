<?php 
namespace App\Swagger;

/**
 * @OA\Info(
 *     description="Documentación de la API de Asesora-T",
 *     version="1.0.0",
 *     title="Swagger Asesora-T API Doc",
 *
 *     @OA\Contact(
 *         email="caamalmarco99@gmail.com"
 *     )
 * )
 * @OA\Tag(
 *     name="Cuentas",
 *     description="Todo sobre las cuentas",
 * )
 * @OA\Tag(
 *     name="Profesores",
 *     description="Todo sobre los profesores",
 * )
 * @OA\Tag(
 *     name="Asesorias",
 *     description="Todo sobre las asesorias",
 * )
 * @OA\Server(
 *     description="SwaggerHUB API Mocking",
 *     url=SERVER_URL
 * )
 */
class OpenApiInfo
{
}