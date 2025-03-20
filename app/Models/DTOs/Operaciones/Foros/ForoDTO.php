<?php
namespace App\Models\DTOs\Operaciones\Foros;

use App\Enums\Foros\EstatusForoEnum;
use App\Models\DTOs\Interfaces\DTOContractInterface;
use App\Models\DTOs\Operaciones\Materias\MateriaDTO;
use App\Models\DTOs\Operaciones\Personas\AlumnoDTO;
use App\Models\DTOs\Operaciones\Personas\ProfesorDTO;
use App\Models\Foros\Foro;
use OpenApi\Attributes as OAT;

#[OAT\Schema(
    title: 'Foros',
    description: 'Foro model'
)]
class ForoDTO implements DTOContractInterface {
    #[OAT\Property(
        description: 'ID del foro',
        example: 1,
    ),]
    public int $id; // json:id Required
    #[OAT\Property(
        description: 'Titulo del foro',
    ),]
    public string $titulo; // json:titulo Required
    #[OAT\Property(
        description: 'Contenido del foro',
    ),]
    public string $contenido; // json:contenido Required
    #[OAT\Property(
        description: 'Titulo del foro',
    ),]
    public EstatusForoEnum $estatus; // json:estatus Required
    #[OAT\Property(
        description: 'Url De La Imagen del foro',
    ),]
    public ?string $url_imagen; // json:url_imagen Optional
    #[OAT\Property(
        description: 'motivo de baja del foro',
    ),]
    public ?string $motivo_baja; // json:motivo_baja Optional
    #[OAT\Property()]
    public ?MateriaDTO $materia; // json:materia_id Required
    #[OAT\Property()]
    public ProfesorDTO | AlumnoDTO  | null $usuario;
    #[OAT\Property()]
    public string $created_at; // json:created_at Required
    #[OAT\Property()]
    public string $updated_at; // json:updated_at Required

    public function __construct(array $args = [], Foro $foro = null)
    {
        if($foro == null)
        {
            $this->fromArray($args);
        } else {
            $this->fromArray($foro->toArray());
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
            'titulo' => $this->titulo,
            'contenido' => $this->contenido,
            'estatus' => $this->estatus,
            'url_imagen' => $this->url_imagen,
            'motivo_baja' => $this->motivo_baja,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];

        if(in_array('usuario', $includeRelations)) {
            $data['asesor'] = $this->usuario;
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
        $relaciones = ['asesor', 'materia']; // Nombres de las propiedades

        foreach ($data as $key => $value) {
            if (!isset($this->$key)) {
                continue;
            }

            if ($key === 'estatus') {
                $this->$key = EstatusForoEnum::from($value);
            }

            if (in_array($key, $relaciones)) {
                $this->$key = $value;
                continue;
            }

            if (!in_array($key, $includeRelations)) {
                $this->$key = $value;
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
