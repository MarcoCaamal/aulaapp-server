<?php

namespace App\Enums;

use OpenApi\Attributes as OA;

#[OA\Schema()]
enum DiaSemanaEnum: int
{
    case Lunes = 0;
    case Martes = 1;
    case Miercoles = 2;
    case Jueves = 3;
    case Viernes = 4;

    public static function toArray()
    {
        return [
            ['name' => self::Lunes->name, 'value' => self::Lunes->value],
            ['name' => self::Martes->name, 'value' => self::Martes->value],
            ['name' => self::Miercoles->name, 'value' => self::Miercoles->value],
            ['name' => self::Jueves->name, 'value' => self::Jueves->value],
            ['name' => self::Viernes->name, 'value' => self::Viernes->value],
        ];
    }

    
}