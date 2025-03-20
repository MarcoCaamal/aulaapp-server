<?php

namespace App\Enums;

use OpenApi\Attributes as OAT;

#[OAT\Schema(
    type: 'integer',
    format: 'enum'
)]
enum TurnoEnum: int
{
    case Matutino = 0;
    case Vespertino = 1;

    public static function toArray()
    {
        return [
            ['name' => self::Matutino->name, 'value' => self::Matutino->value],
            ['name' => self::Vespertino->name, 'value' => self::Vespertino->value]
        ];
    }
}