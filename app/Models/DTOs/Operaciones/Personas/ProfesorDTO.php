<?php 
namespace App\Models\DTOs\Operaciones\Personas;

use App\Models\DTOs\Interfaces\DTOContractInterface;
use App\Models\DTOs\Operaciones\Horarios\HorarioDTO;
use App\Models\DTOs\Operaciones\Materias\MateriaDTO;
use App\Models\User;
use OpenApi\Attributes as OAT;

#[OAT\Schema(
    title: 'Profesores',
    description: 'Profesor model'
    
)]
class ProfesorDTO implements DTOContractInterface
{
    #[OAT\Property(
        description: 'ID del profesor',
        title: 'ID profesor',
        example: 1
    )]
    public int $id; //int
    #[OAT\Property(
        description: 'Nombre del profesor',
        title: 'Nombre',
        example: 'Jhon'
    )]
    public string $nombre; //String
    #[OAT\Property(
        description: 'Apellido Paterno del profesor',
        title: 'Apellido Paterno',
        example: 'Perez'
    )]
    public string $apellido_paterno; //String
    #[OAT\Property(
        description: 'Apellido Materno del profesor',
        title: 'Apellido Marterno',
        example: 'Perez'
    )]
    public string $apellido_materno; //String
    #[OAT\Property(
        description: 'CURP del profesor',
        title: 'CURP',
        example: 'TORM770826MQTRMG65'
    )]
    public string $curp; //String
    #[OAT\Property(
        description: 'Email del profesor',
        title: 'Email',
        example: 'jhon@example.com'
    )]
    public string $email; //String
    #[OAT\Property(
        description: 'Fecha de creación del horario',
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

    public function __construct(array $args = [], User $profesor = null)
    {
        if($profesor == null) {
            $this->id = $args['id'] ?? 0;
            $this->nombre = $args['nombre'] ?? '';
            $this->apellido_paterno = $args['apellido_paterno'] ?? '';
            $this->apellido_materno = $args['apellido_materno'] ?? '';
            $this->curp = $args['curp'] ?? '';
            $this->email = $args['email'] ?? '';
            $this->created_at = $args['created_at'] ?? '';
            $this->updated_at = $args['updated_at'] ?? '';
        } else {
            $this->id = $profesor->id ?? 0;
            $this->nombre = $profesor->nombre ?? '';
            $this->apellido_paterno = $profesor->apellido_paterno ?? '';
            $this->apellido_materno = $profesor->apellido_materno ?? '';
            $this->curp = $profesor->curp ?? '';
            $this->email = $profesor->email ?? '';
            $this->created_at = $profesor->created_at ?? '';
            $this->updated_at = $profesor->updated_at ?? '';
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
            'apellido_paterno' => $this->apellido_paterno,
            'apellido_materno' => $this->apellido_materno,
            'curp' => $this->curp,
            'email' => $this->email,
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

    public function unsetProperties(array $properties) {
        foreach($properties as $property) {
            $this->unset($property);
        }
    }
}