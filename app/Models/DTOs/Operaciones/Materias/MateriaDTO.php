<?php 
namespace App\Models\DTOs\Operaciones\Materias;

use App\Models\DTOs\Interfaces\DTOContractInterface;
use App\Models\Materia;
use Illuminate\Database\Eloquent\Relations\Pivot;
use OpenApi\Attributes as OAT;

#[OAT\Schema(
    title: 'Materias',
    description: 'Materia model'
)]
class MateriaDTO implements DTOContractInterface
{
    #[OAT\Property(
        description: 'ID de la materia',
        title: 'ID materia',
        example: 1
    )]
    public int $id; //int
    #[OAT\Property(
        description: 'Nombre de la materia',
        title: 'Nombre',
        example: 'QUÍMICA'
    )]
    public string $nombre; //String
    #[OAT\Property(
        description: 'Fecha de creación de la materia',
        title: 'Created At',
        example: '04-03-2023 12:00:00',
        format: 'date-time'
    )]
    public string $created_at; //Date
    #[OAT\Property(
        description: 'Fecha de edición del horario',
        title: 'Updated At',
        example: '04-03-2023 12:00:00',
        format: 'date-time'
    )]
    public string $updated_at; //Date
    
    public function __construct($args = [], Materia $materia = null)
    {
        if($materia == null) {
            $this->id = $args['id'] ?? 0;
            $this->nombre = $args['nombre'];
            $this->created_at = $args['created_at'] ?? '';
            $this->updated_at = $args['updated_at'] ?? '';
        } else 
        {
            $this->id = $materia->id;
            $this->nombre = $materia->nombre;
            $this->created_at = $materia->created_at ;
            $this->updated_at = $materia->updated_at ;
        }
    }
	/**
	 * Convierte el DTO a array
	 *
	 * @param array $includeRelations
	 * @return array
	 */
	public function toArray(array $includeRelations = array()): array {
		$data = [
			'id' => $this->id,
			'nombre' => $this->nombre,
			'created_at' => $this->created_at,
			'updated_at' => $this->updated_at,
		];

		return $data;
	}
	
	/**
	 * Convierte el DTO a JSON
	 *
	 * @param array $includeRelations
	 * @return string
	 */
	public function toJson(array $includeRelations = array()): string {
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
                
                if(in_array($key, $includeRelations)) {
                    switch($key) {
                        // case 'asesor':
                        //     $this->asesor = $value;
                        // break;
                        // case 'materia':
                        //     $this->materia = $value;
                        // break;
                        default:
                        break;
                    }
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
