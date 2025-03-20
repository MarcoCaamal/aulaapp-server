<?php
use App\Models\Desafios\Opcion;

class OpcionRepository {
    private $model;
    public function __construct()
    {
        $this->model = new Opcion();
    }
    /**
     * Repositorio que retorna una Opcion
     * @param int $id
     * @return Opcion
     */
    public function obtenerPorId(int $id): Opcion
    {
        return $this->model->find($id);
    }
    /**
     * Repositorio que retorna una colección de Opcion
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function obtenerColeccion(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->model->all();
    }
    /**
     * Repositorio que crea o actualiza una Opcion
     * @param Opcion $model
     * @return bool
     */
    public function guardar(Opcion $model): bool
    {
        return $model->save();
    }
    /**
     * Repositorio que elimina una Opcion
     * @param Opcion $model
     * @return bool
     */
    public function eliminar(Opcion $model): bool
    {
        return $model->delete();
    }
}
