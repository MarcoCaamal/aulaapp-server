<?php
namespace App\Services\Interfaces\Foros;

use App\Enums\Foros\TipoLikeEnum;
use App\Helpers\ResponseHelper;
use App\Models\Foros\Foro;

interface ForoServiceInterface
{
    /**
     * Servicio para obtener una páginacion de todos los foros que tenga la materia solicitada
     * @param int $materiaId
     * @param int $oaginaActual
     * @return \Illuminate\Pagination\LengthAwarePaginator|null
     */
    public function obtenerPaginacionPorMateriaId(
        int $materiaId,
        int $oaginaActual
    ): \Illuminate\Pagination\LengthAwarePaginator|null;
    /**
     * Servicio para obtener una paginación de los foros de un usuario por su id
     * @param int $userId
     * @param int $paginaActual
     * @return \Illuminate\Pagination\LengthAwarePaginator|null
     */
    public function obtenerPaginacionPorUserId(
        int $userId,
        int $paginaActual
    ): \Illuminate\Pagination\LengthAwarePaginator|null;
    public function obtenerPorId(int $id, int $userId): Foro;
    /**
     * Servicio para crear un foro desde el controlador
     * @param array{titulo:string,contenido:string,materia_id:int,imagen:null|\Illuminate\Http\UploadedFile} $atributos
     * @param int $userId
     * @return ResponseHelper
     */
    public function crear(array $atributos, int $userId): ResponseHelper;
    /**
     * Servicio que actuliza un foro por su id y el id del usuario al que pertenece el foro
     * @param array{titulo:string,contenido:string,imagen:null|\Illuminate\Http\UploadedFile} $atributos
     * @param mixed $foroId
     * @param mixed $userId
     * @return ResponseHelper
     */
    public function editar(array $atributos, $foroId, $userId): ResponseHelper;
    /**
     * Repositorio para borrar un foro por su id y el id del usuario del foro
     * @param int $id
     * @param int $userId
     * @return ResponseHelper
     */
    public function eliminar(int $id, int $userId): ResponseHelper;
    /**
     * Servicio para reporta un foro
     * @param int $foroId
     * @param int $userReportanteId
     * @param string $motivo
     * @return ResponseHelper
     */
    public function reportar(int $foroId, int $userReportanteId, string $motivo = ""): ResponseHelper;
    /**
     * Servicio para dar like(de cualquier tipo) a un foro.
     * Se recibe el ID del foro al cual se dio like, el ID del usuario que dio like, y el tipo de like que se dio
     * @param int $foroId
     * @param int $userLikedId
     * @param TipoLikeEnum $tipoLike
     * @return ResponseHelper
     */
    public function darLike(int $foroId, int $userLikedId, TipoLikeEnum $tipoLike): ResponseHelper;
}
