<?php
namespace App\Repositories\Foros;

use App\Enums\Foros\EstatusForoEnum;
use App\Enums\Foros\EstatusRespuestaEnum;
use App\Models\Foros\Respuesta;

class RespuestaRepository {
    private Respuesta $model;

    public function __construct() {
        $this->model = new Respuesta();
    }

    public function obtenerColeccion(): \Illuminate\Database\Eloquent\Collection {
        return $this->model->all();
    }

    /**
     * Repositorio que obtiene una colección de respuestas de un foro para paginación
     * @param int $foroId
     * @param int $paginaActual
     * @param int $numeroRegistrosPorPagina
     * @return array{total:int,data:\Illuminate\Database\Eloquent\Collection<Respuesta>}
     */
    public function obtenerColeccionPaginadaPorForoId(
        int $foroId,
        int $paginaActual = 1,
        int $numeroRegistrosPorPagina = 15
    ): array
    {
        $sql = $this->model->query();
        $resultado = [];

        $sql->where([
            ['foro_id', $foroId],
            ['estatus', EstatusRespuestaEnum::ACTIVO]
        ]);

        $sql->whereHas('foro', function ($query) {
            $query->where('foros.estatus', EstatusForoEnum::ACTIVO);
        });

        $resultado['total'] = $sql->count();
        $resultado['data'] = $sql
            ->skip(($paginaActual - 1) * $numeroRegistrosPorPagina)
            ->take($numeroRegistrosPorPagina)
            ->get();

        return $resultado;
    }

    public function obtenerPorId(int $id): Respuesta {
        return $this->model->find($id) ?? new Respuesta();
    }

    public function obtenerPorRepuestaIdUserId(int $respuestaId, int $userId): Respuesta {
        return $this->model
            ->where([
                ['respuestas.id', $respuestaId],
                ['respuestas.user_id', $userId]
            ])
            ->first() ?? new Respuesta();
    }

    public function guardar(Respuesta $respuesta): bool {
        return $respuesta->save();
    }

    public function eliminar(Respuesta $respuesta): bool {
        return $respuesta->delete();
    }
}
