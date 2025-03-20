<?php
namespace App\Repositories;

use App\Models\Grupo;
use App\Models\User;

class GrupoRepository
{
    private Grupo $model;
    private User $user;

    public function __construct()
    {
        $this->model = new Grupo();
        $this->user = new User();
    }

    /**
     * Repositorio que obtiene una colección de grupos
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function obtenerColeccion(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->model->with('semestre')->get();
    }

    /**
     * Repositorio que obtiene un grupo por su id
     * @param int $id
     * @return Grupo
     */
    public function obtenerPorId(int $id): Grupo
    {
        return $this->model->find($id);
    }

    /**
     * Repositorio que crea o actualiza un grupo
     * @param Grupo $grupo
     * @return bool
     */
    public function guardar(Grupo $grupo): bool
    {
        return $grupo->save();
    }

    /**
     * Repositorio que elimina un grupo
     * @param Grupo $grupo
     * @return bool
     */
    public function eliminar(Grupo $grupo): bool
    {
        return $grupo->delete();
    }

    public function existe(int $id): bool
    {
        return $this->model->where('id', $id)->exists();
    }
}
