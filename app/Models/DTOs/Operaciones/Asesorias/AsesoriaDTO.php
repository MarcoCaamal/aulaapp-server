<?php
namespace App\Models\DTOs\Operaciones\Asesorias;

use App\Enums\EstatusAsesoriaEnum;
use App\Models\Asesoria;
use App\Models\DTOs\Interfaces\DTOContractInterface;
use App\Models\DTOs\Operaciones\Horarios\HorarioDTO;
use App\Models\DTOs\Operaciones\Materias\MateriaDTO;
use App\Models\DTOs\Operaciones\Personas\ProfesorDTO;
use OpenApi\Attributes as OAT;
use OpenApi\Attributes\Property;

#[OAT\Schema(
    title: 'AsesoriasConfirmadasAlumno',
    description: 'AsesoriaConfirmadaAlumno Model'
)]
class AsesoriaDTO implements DTOContractInterface
{
    #[Property(
        description: 'ID de la Asesoria',
        title: 'ID Asesoria',
        example: 1
    )]
    public int $id;
    #[Property(
        description: 'Estado de la Asesoria',
        title: 'Estado',
        example: 0
    )]
    public ?EstatusAsesoriaEnum $estado;
    #[OAT\Property(
        description: 'Fecha de la Asesoria',
        title: 'Fecha',
        example: '04-03-2023 12:00:00',
        format: 'date-time'
    )]
    public null|string $fecha;
    #[OAT\Property(
        description: 'Evidencias de la asesoria',
        title: 'Evidencias',
        example: '04-03-2023 12:00:00'
    )]
    public null|string $evidencias;

    // Relaciones

    #[OAT\Property(
        description: 'ID del horario',
        title: 'Fecha',
        example: '04-03-2023 12:00:00',
        format: 'date-time'
    )]
    public null|ProfesorDTO $asesor;
    public null|MateriaDTO $materia;
    public null|HorarioDTO $horario;
    // Auditoria

    #[OAT\Property(
        description: 'Fecha de creación',
        title: 'Created At',
        example: '04-03-2023 12:00:00',
        format: 'date-time'
    )]
    public null|string $created_at;
    #[OAT\Property(
        description: 'Fecha de edición',
        title: 'Updated At',
        example: '04-03-2023 12:00:00',
        format: 'date-time'
    )]
    public null|string $updated_at;

    public function __construct(array $args = [], Asesoria $asesoria = null)
    {
        if($asesoria == null)
        {
            $this->fromArray($args);
        } else {
            $this->fromArray($asesoria->toArray());
        }
    }

	/**
	 * Convierte el DTO a array
	 * @return array
	 */
	public function toArray(array $includes = []): array {

        $data =  [
            'id' => $this->id,
            'estado' => $this->estado,
            'fecha' => $this->fecha,
            'evidencias' => $this->evidencias,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];

        if(in_array('asesor', $includes)) {
            $data['asesor'] = $this->asesor;
        }
        if(in_array('materia', $includes)) {
            $data['materia'] = $this->materia;
        }
        if(in_array('horario', $includes)) {
            $data['horario'] = $this->horario;
        }

        return $data;
	}

	/**
	 * Convierte el DTO a JSON
	 * @return string
	 */
	public function toJson($includes = []): string {
        return json_encode($this->toArray($includes));
	}

	/**
	 * Llena las propiedades del DTO usando un array con las propieades
	 *
	 * @param array $data
	 * @return void
	 */
	public function fromArray(array $data, $includes = []) {
        foreach($data as $key => $value)
        {
            if(property_exists($this, $key)) {
                if($key === 'estado') {
                    $this->$key = EstatusAsesoriaEnum::from($value);
                    continue;
                }
                if(in_array($key, $includes)) {
                    switch($key) {
                        case 'asesor':
                            $this->asesor = $value;
                        break;
                        case 'materia':
                            $this->materia = $value;
                        break;
                        case 'horario':
                            $this->horario = $value;
                        break;
                        default:
                        break;
                    }
                }
                if(!in_array($key, $includes) && !($key == 'horario' || $key == 'materia' || $key == 'asesor') ) {
                    $this->$key = $value;
                }
            }
        }
	}

	/**
	 * Llena las propiedades del DTO usando un JSON
	 *
	 * @param string $json
	 * @return void
	 */
	public function fromJson(string $json, $includes = []) {
        $data = json_decode($json, true);
        $this->fromArray($data, $includes);
	}

	/**
	 * Retorna la propiedad del DTO
	 *
	 * @param string $property
	 * @return mixed
	 */
	public function get(string $property): mixed {
        if(property_exists($this, $property)) {
            return $this->$property;
        }
        return null;
	}

	/**
	 * Establece el valor a una propiedad del DTO
	 *
	 * @param string $property
	 * @param mixed $value
	 * @return void
	 */
	public function set(string $property, mixed $value) {
        if(property_exists($this, $property)) {
            $this->$property = $value;
        }
	}

	/**
	 * Elimina una propiedad del DTO
	 *
	 * @param string $property
	 * @return void
	 */
	public function unset(string $property) {
        if(property_exists($this, $property)) {
            unset($this->$property);
        }
	}

    public function unsetProperties(array $properties) {
        foreach($properties as $property) {
            $this->unset($property);
        }
    }
}
