<?php
namespace App\Repositories\Foros;

use App\Models\Foros\Reporte;

class ReporteRepository
{
    private Reporte $model;

    public function __construct() {
        $this->model = new Reporte();
    }
    /**
     * Repositorio que obtiene un reporte por su id
     * @param int $id
     * @return Reporte
     */
    public function obtenerPorId(int $id): Reporte
    {
        return $this->model->find($id) ?? new Reporte();
    }
    /**
     * Repositorio que crea o actualiza un foro
     * @param Reporte $reporte
     * @return bool
     */
    public function guardar(Reporte $reporte): bool
    {
        return $reporte->save();
    }
    /**
     * Repositorio que elimina un foro
     * @param Reporte $reporte
     * @return bool|null
     */
    public function eliminar(Reporte $reporte)
    {
        return $reporte->delete();
    }
}
