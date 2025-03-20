<?php
namespace App\Enums\Foros;

use OpenApi\Attributes as OAT;

#[OAT\Schema()]
enum TipoLikeEnum: int
{
    case LIKE = 0;
    case DISLIKE = 1;
}
