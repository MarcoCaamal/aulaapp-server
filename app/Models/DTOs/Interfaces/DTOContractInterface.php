<?php 
namespace App\Models\DTOs\Interfaces;

interface DTOContractInterface
{
    /**
     * Convierte el DTO a array
     *
     * @return array
     */
    public function toArray(array $includeRelations = []): array;
    /**
     * Convierte el DTO a JSON
     *
     * @return string
     */
    public function toJson(array $includeRelations = []): string;
    /**
     * Llena las propiedades del DTO usando un array con las propieades
     *
     * @param array $data
     * @return void
     */
    public function fromArray(array $data, array $includeRelations = []);
    /**
     * Llena las propiedades del DTO usando un JSON
     *
     * @param string $json
     * @return void
     */
    public function fromJson(string $json, $includeRelations = []);
    /**
     * Retorna la propiedad del DTO
     *
     * @param string $property
     * @return mixed
     */
    public function get(string $property): mixed;
    /**
     * Establece el valor a una propiedad del DTO
     *
     * @param string $property
     * @param mixed $value
     * @return void
     */
    public function set(string $property, mixed $value);
    /**
     * Elimina una propiedad del DTO
     *
     * @param string $property
     * @return void
     */
    public function unset(string $property);
    /**
     * Elimina varias propiedades del DTO
     *
     * @param array $properties
     * @return void
     */
    public function unsetProperties(array $properties);
}