<?php
namespace App\Enums\Foros;

use OpenApi\Attributes as OAT;

#[OAT\Schema()]
enum EstatusForoEnum: int
{
    case ACTIVO = 0;
    case DESHABILITADO = 1;
    case BANEADO = 2;
}
