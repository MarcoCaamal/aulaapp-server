<?php
namespace App\Repositories;
use App\Models\Materia;

class MateriaRepository {
    private $model;
    public function __construct()
    {
        $this->model = new Materia();
    }

    /**
     * Repositorio que obtiene una colección de materias
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function obtenerColeccion(): \Illuminate\Database\Eloquent\Collection {
        return $this->model->all();
    }

    /**
     * Repositorio que obtiene una colección de materias con sus relaciones
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function obtenerColeccionConRelaciones(array $relations): \Illuminate\Database\Eloquent\Collection {
        return $this->model->with($relations)->get();
    }

    // public function getAllByProfesorId(int $idProfesor): \Illuminate\Database\Eloquent\Collection
    // {
    //     return $this->model->
    // }

    /**
     * Repositorio que obtiene una materia por su id
     * @param int $id
     * @return Materia
     */
    public function obtenerPorId(int $id): Materia|null {
        return $this->model->find($id);
    }

    /**
     * Repositorio que guarda y actualiza un materias
     *
     * @param Materia $materia
     * @return bool
     */
    public function guardar(Materia $materia): bool {
        return $materia->save();
    }

    /**
     * Repositorio que elimina una materia
     * @param Materia $materia
     * @return bool
     */
    public function eliminar(Materia $materia): bool {
        return $materia->delete();
    }

    /**
     * Repositorio que obtiene una colección de materias por sus ids
     * @param array $materias
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function obtenerColeccionMateriasByIds(array $materias): \Illuminate\Database\Eloquent\Collection {
        return Materia::whereIn('id', $materias)->get();
    }
    /**
     * Repositorio que verifica si el profesor cuenta con una determinada materia
     * @param int $profesorId
     * @param int $materiaId
     * @return bool
     */
    public function verificarMateriaProfesor(int $profesorId, int $materiaId): bool
    {
        $sql = $this->model->query();

        $sql->whereHas('profesores', function ($query) use ($profesorId, $materiaId) {
            $query->where([
                ['materias_asesores.user_id', $profesorId],
                ['materias_asesores.materia_id', $materiaId]
            ]);
        });

        return $sql->exists();
    }
}
?>
