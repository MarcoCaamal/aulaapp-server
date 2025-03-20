<?php

namespace App\Enums;

use OpenApi\Attributes as OAT;

#[OAT\Schema()]
enum EstatusAsesoriaEnum: int
{
    case PENDIENTE = 0;
    case FINALIZADA = 1;
    case CANCELADO = 2;

    public static function toArray(): array
    {
        return [
            ['name' => self::PENDIENTE->name, 'value' => self::PENDIENTE->value],
            ['name' => self::FINALIZADA->name, 'value' => self::FINALIZADA->value],
            ['name' => self::CANCELADO->name, 'value' => self::CANCELADO->value],
        ];
    }

    public static function getArrayPendiente(): array
    {
        return ['name' => self::PENDIENTE->name, 'value' => self::PENDIENTE->value];
    }

    public static function getArrayFinalizado(): array
    {
        return ['name' => self::FINALIZADA->name, 'value' => self::FINALIZADA->value];
    }
}