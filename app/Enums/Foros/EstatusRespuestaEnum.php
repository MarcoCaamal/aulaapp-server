<?php
namespace App\Enums\Foros;

use OpenApi\Attributes as OAT;

#[OAT\Schema()]
enum EstatusRespuestaEnum: int
{
    case ACTIVO = 0;
    case BANEADO = 1;
    case DESHABILITADO = 2;
}
