<?php
namespace App\Services\Interfaces\Foros;

use App\Helpers\ResponseHelper;

interface RespuestaServiceInterface
{
    /**
     * Servicio para obtener una paginación de respuestas de un foro
     * @param int $foroId
     * @return \Illuminate\Pagination\LengthAwarePaginator|null
     */
    public function obtenerPaginacion(int $foroId, int $paginaActual): \Illuminate\Pagination\LengthAwarePaginator|null;
    /**
     * Servicio para crear una respuesta a un foro
     *
     * @param array{contenido:string} $atributos
     * @param int $foroId
     * @param int $userId
     * @return ResponseHelper
     */
    public function crear(array $atributos, int $foroId, int $userId): ResponseHelper;

    /**
     * Servicio para actulizar una respuesta
     * @param array{contenido:string} $atributos
     * @param int $respuestaId
     * @param int $userId
     * @return ResponseHelper
     */
    public function actualizar(array $atributos, int $respuestaId, int $userId): ResponseHelper;
    /**
     * Servicio para eliminar una respuesta
     *
     * @param int $respuestaId
     * @param int $userId
     * @return ResponseHelper
     */
    public function eliminar(int $respuestaId, int $userId): ResponseHelper;
}
