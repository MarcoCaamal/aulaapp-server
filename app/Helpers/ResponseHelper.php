<?php
namespace App\Helpers;

use OpenApi\Attributes as OAT;

#[OAT\Schema(
    title: 'ResponseHelper'
)]
class ResponseHelper {
    #[OAT\Property(
        description: 'Mensaje de la respuesta',
        title: 'message'
    )]
    public string $message;
    #[OAT\Property(
        description: 'Indica si el proceso solicitado fue exitoso',
        title: 'success'
    )]
    public bool $success;
    #[OAT\Property(
        description: 'Código de estado http',
        title: 'statusCode'
    )]
    public int $statusCode = 200;
}
?>