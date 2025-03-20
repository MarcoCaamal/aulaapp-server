<?php
namespace App\Repositories;

use App\Models\Ciclo;

class CicloRepository {
    private $model;

    public function __construct()
    {
        $this->model = new Ciclo();
    }

	/**
     * Repositorio que obtiene una colección de ciclos
     *
	 * @return \Illuminate\Database\Eloquent\Collection<\App\Models\Ciclo>
	 */
	public function obtenerColeccion(): \Illuminate\Database\Eloquent\Collection {
        return $this->model->all();
	}

	/**
	 * Repositorio que obtiene un ciclo por su id
     *
	 * @param int $id
	 * @return Ciclo
	 */
	public function obtenerPorId(int $id): Ciclo|null {
        return $this->model->find($id);
	}

	public function obtenerCicloActivo(): Ciclo|null {
		return $this->model->where('is_activo', true)->first();
	}

	/**
     * Repositorio que guarda o actualiza un ciclo
     *
	 * @return bool
	 */
	public function guardar(Ciclo $ciclo): bool {
        return $ciclo->save();
	}

	/**
     * Repositorio que elimina un ciclo
     *
	 * @return bool
	 */
	public function eliminar(Ciclo $ciclo): bool {
		return $ciclo->delete();
	}
}
