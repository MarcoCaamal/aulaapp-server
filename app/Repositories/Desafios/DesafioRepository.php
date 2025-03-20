<?php
namespace App\Repositories\Desafios;

use App\Models\Desafios\Desafio;

class DesafioRepository {
    private $model;

    public function __construct()
    {
        $this->model = new Desafio();
    }

    /**
     * Repositorio que returna un Desafio por su id
     * @param mixed $id
     * @return \App\Models\Desafios\Desafio
     */
    public function obtenerPorId(int $id): Desafio
    {
        return $this->model->find($id);
    }

    public function buscarPorDesafioIdProfesorId(int $desafioId, int $profesorId): Desafio
    {
        $sql = $this->model->query();
        $sql->whereHas('profesor', function ($query) use ($desafioId, $profesorId) {
            $query->where([
                ['users.id', $profesorId],
                ['desafios.id', $desafioId]
            ]);
        });

        return $sql->first() ?? new Desafio();
    }

    /**
     * Repositorio que retorna una colección de Desafios
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function obtenerColeccion(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->model->all();
    }

    /**
     * Repositorio que obtiene array con los datos para la paginación
     * @param mixed $profesorId
     * @param mixed $paginaActual
     * @param mixed $numeroRegistrosPorPagina
     * @return array{total:int,data:\Illuminate\Database\Eloquent\Collection<Desafio>}
     */
    public function obtenerColeccionPaginadaPorProfesorId(
        int $profesorId,
        int $paginaActual = 1,
        int $numeroRegistrosPorPagina = 15
    ): array
    {
        $sql = $this->model->query();
        $resultado = [];

        $sql->where('profesor_id', $profesorId);

        $resultado['total'] = $sql->count();
        $resultado['data'] = $sql
            ->skip(($paginaActual - 1) * $numeroRegistrosPorPagina)
            ->take($numeroRegistrosPorPagina)
            ->get();

        return $resultado;
    }

    /**
     * Repositorio que crea o actualiza un Desafio
     * @param \App\Models\Desafios\Desafio $desafio
     * @return bool|null
     */
    public function guardar(Desafio $desafio): bool
    {
        return $desafio->save();
    }

    /**
     * Repositorio que elimina un Desafio
     * @param \App\Models\Desafios\Desafio $desafio
     * @return bool
     */
    public function eliminar(Desafio $desafio): bool
    {
        return $desafio->delete();
    }
}
