<?php 
namespace App\Services\Mapping;

use App\Models\Asesoria;
use App\Models\DTOs\Operaciones\Asesorias\AsesoriaDTO;
use App\Models\DTOs\Operaciones\Horarios\HorarioDTO;
use App\Models\DTOs\Operaciones\Materias\MateriaDTO;
use App\Models\DTOs\Operaciones\Personas\ProfesorDTO;
use App\Models\Horario;
use App\Services\Interfaces\Mapping\MapperServiceInterface;

class MapperService implements MapperServiceInterface
{
    
    public function mapAsesoriaToAsesoriaConfirmadaDTOCollection(
        \Illuminate\Database\Eloquent\Collection $asesorias,
        array $includes = []
    ): \Illuminate\Support\Collection
    {
        $collection = $asesorias->map(function(Asesoria $asesoria) use($includes) {
            $asesoriaConfirmadaDTO = new AsesoriaDTO(asesoria: $asesoria);
            if(in_array('horario', $includes)) {
                $asesoriaConfirmadaDTO->horario = new HorarioDTO(horario: $asesoria->horario);
            }
            if(in_array('asesor', $includes)) {
                $asesoriaConfirmadaDTO->asesor = new ProfesorDTO(profesor: $asesoria->materia_asesor->asesor);
            }
            if(in_array('materia', $includes)) {
                $asesoriaConfirmadaDTO->materia = new MateriaDTO(materia: $asesoria->materia_asesor->materia);
            }
            return $asesoriaConfirmadaDTO;
        });

        return $collection;
    }
    
}