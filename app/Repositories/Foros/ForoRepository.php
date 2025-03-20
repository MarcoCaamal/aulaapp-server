<?php
namespace App\Repositories\Foros;

use App\Enums\Foros\EstatusForoEnum;
use App\Models\Foros\Foro;

class ForoRepository {
    private Foro $model;

    public function __construct()
    {
        $this->model = new Foro();
    }

    /**
     * Repositorio que obtiene una coleccion de todos Foros
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function obtenerColeccion(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->model->all();
    }
    /**
     * Repositorio que obtiene una colección pagina de un usuario por su id y el número de registros que tiene en un array
     * @param int $userId
     * @param int $paginaActual
     * @param int $numeroRegistrosPorPagina
     * @return array{total:int,data:\Illuminate\Database\Eloquent\Collection<Foro>}
     */
    public function obtenerColeccionPaginadaPorUserId(
        int $userId,
        int $paginaActual = 1,
        int $numeroRegistrosPorPagina = 15
    ): array {
        $sql = $this->model->query();
        $resultado = [];

        $sql->where('user_id', $userId);

        $resultado['total'] = $sql->count();
        $resultado['data'] = $sql->skip(($paginaActual - 1) * $numeroRegistrosPorPagina)
            ->take($numeroRegistrosPorPagina)
            ->get();

        return $resultado;

    }
    /**
     * Repositorio que obtiene una colección paginada de foros de una materia en especifico.
     *
     * @param int $materiaId
     * @param int $paginaActual
     * @param int $numeroRegistrosPorPagina
     * @return array{total:int,data:\Illuminate\Database\Eloquent\Collection<Foro>}
     */
    public function obtenerColeccionPaginadaPorMateriaId(
        int $materiaId,
        int $paginaActual = 1,
        int $numeroRegistrosPorPagina = 15
    ): array {
        $sql = $this->model->query();
        $resultado = [];

        $sql->where('materia_id', $materiaId)
            ->where('estatus', EstatusForoEnum::ACTIVO);

        $resultado['total'] = $sql->count();
        $resultado['data'] = $sql->skip(($paginaActual - 1) * $numeroRegistrosPorPagina)
            ->take($numeroRegistrosPorPagina)
            ->get();

        return $resultado;
    }
    /**
     * Repositorio que obtiene un foro por su id
     * @param int $id
     * @return Foro
     */
    public function obtenerPorId(int $id): Foro
    {
        return $this->model->find($id) ?? new Foro();
    }
    /**
     * Repositorio que obtiene un Foro por su id y el id del usuario que creo el foro
     *
     * @param int $id
     * @return Foro
     */
    public function obtenerPorForoIdUsuarioId(int $id, int $userId): Foro
    {
        return $this->model->where('user_id', $userId)->find($id) ?? new Foro();
    }
    /**
     * Repositorio que crea o actualiza un Foro en la DB
     * @param Foro $foro
     * @return bool
     */
    public function guardar(Foro $foro)
    {
        return $foro->save();
    }
    /**
     * Repositorio que elimina un Foro de la DB
     * @param Foro $foro
     * @return bool|null
     */
    public function eliminar(Foro $foro)
    {
        return $foro->delete();
    }
}
