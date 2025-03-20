<?php
namespace App\Repositories;
use App\Models\Semestre;

class SemestreRepository {
    private $model;

    public function __construct()
    {
        $this->model = new Semestre();
    }

    /**
     * Repositorio que obtiene una colección de Semestres
     * @return \Illuminate\Database\Eloquent\Collection<Semestre>
     */
    public function obtenerColeccion(): \Illuminate\Database\Eloquent\Collection {
        return $this->model->all();
    }

    /**
     * Repositorio que obtiene un semestre por su id
     * @param int $id
     * @return Semestre
     */
    public function obtenerPorId(int $id): Semestre {
        return $this->model->find($id);
    }

    /**
     * Repositorio que guarda o actualiza un semestre en la base de datos
     * @param Semestre $semestre
     * @return bool
     */
    public function guardar(Semestre $semestre): bool {
        return $semestre->save();
    }

    /**
     * Repositorio que elimina un semestre de la base de datos
     * @param Semestre $semestre
     * @return bool
     */
    public function eliminar(Semestre $semestre): bool {
        return $semestre->delete();
    }

    /**
     * Repositorio que verifica si existe un semestre en la base de datos
     * @param int $id
     * @return bool
     */
    public function existe(int $id): bool {
        return $this->model->where('id', $id)->existe();
    }
}
?>
