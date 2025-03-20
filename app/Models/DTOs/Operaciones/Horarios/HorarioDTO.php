<?php 
namespace App\Models\DTOs\Operaciones\Horarios;

use App\Enums\DiaSemanaEnum;
use App\Models\DTOs\Interfaces\DTOContractInterface;
use App\Models\DTOs\Operaciones\Materias\MateriaDTO;
use App\Models\DTOs\Operaciones\Personas\ProfesorDTO;
use App\Models\Horario;
use OpenApi\Attributes as OA;

use function PHPUnit\Framework\isEmpty;

#[OA\Schema(
    title: 'Horarios',
    description: 'Horario model'
    
)]
class HorarioDTO implements DTOContractInterface
{
    #[OA\Property(
        description: 'ID del hoario',
        title: 'ID',
        example: 1,
    ),]
    public int $id; //int
    #[OA\Property(
        description: 'Lugar del horario',
        title: 'Lugar',
        example: 'Aula 3'
    )]
    public string $lugar; //String
    #[OA\Property(
        description: 'Dia del horario',
        title: 'Dia de la semana',
        example: 1
    )]
    public ?DiaSemanaEnum $dia_semana; //int
    #[OA\Property(
        description: 'Hora de inicio del horario',
        title: 'Hora de Inicio',
        example: '12:00:00',
        format: 'time'
    )]
    public string $hora_inicio; //String
    #[OA\Property(
        description: 'Hora de fin del horario',
        title: 'Hora de Fin',
        example: '12:00:00',
        format: 'time'
    )]
    public string $hora_fin; //String
    #[OA\Property(
        title: 'Asesor'
    )]
    public ProfesorDTO $asesor;
    #[OA\Property(
        title: 'Materia'
    )]
    public MateriaDTO $materia;
    #[OA\Property(
        description: 'Fecha de creación del horario',
        title: 'Created At',
        example: '04-03-2023 12:00:00',
        format: 'date-time'
    )]
    public string $created_at; //Date
    #[OA\Property(
        description: 'Fecha de edición del horario',
        title: 'Updated At',
        example: '04-03-2023 12:00:00',
        format: 'date-time'
    )]
    public string $updated_at; //Date

    public function __construct(array $args = [], Horario $horario = null) 
    {
        if($horario == null)
        {
            $this->fromArray($args);
        } else {
            $this->fromArray($horario->toArray());
        }
    }
	/**
	 * Convierte el DTO a array
	 *
	 * @param array $includeRelations
	 * @return array
	 */
	public function toArray(array $includeRelations = []): array {
        $data = [
            'id' => $this->id,
            'lugar' => $this->lugar,
            'dia_semana' => $this->dia_semana,
            'hora_inicio' => $this->hora_inicio,
            'hora_fin' => $this->hora_fin,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
        
        if(in_array('asesor', $includeRelations)) {
            $data['asesor'] = $this->asesor;
        }
        if(in_array('materia', $includeRelations)) {
            $data['materia'] = $this->materia;
        }

        return $data;
	}
	
	/**
	 * Convierte el DTO a JSON
	 *
	 * @param array $includeRelations
	 * @return string
	 */
	public function toJson(array $includeRelations = []): string {
        return json_encode($this->toArray($includeRelations));
	}
	
	/**
	 * Llena las propiedades del DTO usando un array con las propieades
	 *
	 * @param array $data
	 * @param array $includeRelations
	 * @return void
	 */
	public function fromArray(array $data, array $includeRelations = array()) {
        foreach($data as $key => $value)
        {
            if(property_exists($this, $key)) {
                if($key === 'dia_semana') {
                    $this->$key = DiaSemanaEnum::from($value);
                    continue;
                }
                if(in_array($key, $includeRelations)) {
                    switch($key) {
                        case 'asesor':
                            $this->asesor = $value;
                        break;
                        case 'materia':
                            $this->materia = $value;
                        break;
                        default:
                        break;
                    }
                }
                if(($key == 'asesor' || $key == 'materia') && empty($includeRelations)) {
                    continue;
                }
                if(!in_array($key, $includeRelations)) {
                    $this->$key = $value;
                }
            }
        }
	}
	
	/**
	 * Llena las propiedades del DTO usando un JSON
	 *
	 * @param string $json
	 * @param mixed $includeRelations
	 * @return void
	 */
	public function fromJson(string $json, $includeRelations = array()) {
        $data = json_decode($json, true);
        $this->fromArray($data, $includeRelations);
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
	/**
	 * Elimina varias propiedades del DTO
	 *
	 * @param array $properties
	 * @return void
	 */
	public function unsetProperties(array $properties) {
        foreach($properties as $property) {
            $this->unset($property);
        }
	}
}