<?php 
namespace App\Enums;

enum EstatusAsistenciaEnum: Int
{
    case ASISTENCIA = 0;
    case INASISTENCIA = 1;
    case PENDIENTE = 2;
    case CANCELADO = 3;
}