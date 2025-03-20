<?php 
namespace App\Services\Interfaces\Mapping;

interface MapperServiceInterface
{
    public function mapAsesoriaToAsesoriaConfirmadaDTOCollection(
        \Illuminate\Database\Eloquent\Collection $asesorias,
        array $includes = []
    ): \Illuminate\Support\Collection;
}