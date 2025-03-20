<?php
namespace App\Services\Foros;

use App\Enums\Foros\EstatusForoEnum;
use App\Enums\Foros\EstatusRespuestaEnum;
use App\Helpers\ResponseHelper;
use App\Models\Foros\Respuesta;
use App\Repositories\Foros\ForoRepository;
use App\Repositories\Foros\RespuestaRepository;
use App\Services\Interfaces\Foros\RespuestaServiceInterface;
use Illuminate\Support\Facades\Log;

class RespuestaService implements RespuestaServiceInterface {
    private RespuestaRepository $respuestaRepository;
    private ForoRepository $foroRepository;

    private int $numeroRegistrosPorPagina = 15;

    public function __construct(
        ForoRepository $foroRepository,
        RespuestaRepository $respuestaRepository
    ) {
        $this->foroRepository = $foroRepository;
        $this->respuestaRepository = $respuestaRepository;
    }

    /**
     * Servicio para obtener una paginación de respuestas de un foro
     *
     * @param int $foroId
     * @param int $paginaActual
     * @return \Illuminate\Pagination\LengthAwarePaginator|null
     */
    public function obtenerPaginacion(int $foroId, int $paginaActual): \Illuminate\Pagination\LengthAwarePaginator|null
    {
        $paginador = null;
        try {
            $respuestasDB = $this->respuestaRepository->obtenerColeccionPaginadaPorForoId(
                $foroId,
                $paginaActual,
                $this->numeroRegistrosPorPagina
            );

            $paginador = new \Illuminate\Pagination\LengthAwarePaginator(
                items: $respuestasDB['data'],
                total: $respuestasDB['total'],
                perPage: $this->numeroRegistrosPorPagina,
                currentPage: $paginaActual,
                options: [
                    'path' => \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPath()
                ]
            );
        } catch (\Throwable $th) {
            Log::error($th);
        }
        return $paginador;
    }
    /**
     * Servicio para crear una respuesta a un foro
     *
     * @param array<string> $atributos
     * @param int $foroId
     * @return \App\Helpers\ResponseHelper
     */
    public function crear(array $atributos, int $foroId, int $userId): ResponseHelper
    {
        $response = new ResponseHelper();
        try {
            $foro = $this->foroRepository->obtenerPorId($foroId);

            if ($foro->getKey() === null) {
                $response->success = false;
                $response->message = __('foros/foros_messages.error_not_found');
                $response->statusCode = 404;
                return $response;
            }

            if ($foro->estatus == EstatusForoEnum::BANEADO) {
                $response->success = false;
                $response->message = __('foros/foros_messages.error_banned');
                $response->statusCode = 400;
                return $response;
            }

            $respuesta = new Respuesta();
            $respuesta->fill($atributos);
            $respuesta->estatus = EstatusRespuestaEnum::ACTIVO;
            $respuesta->foro_id = $foroId;
            $respuesta->user_id = $userId;

            if ($this->respuestaRepository->guardar($respuesta)) {
                $response->success = true;
                $response->message = __('foros/respuestas_messages.successful_create');
                return $response;
            }

            $response->success = false;
            $response->message = __('foros/respuestas_messages.error_create');
            $response->statusCode = 400;
        } catch (\Throwable $th) {
            Log::error($th);
            $response->success = false;
            $response->message = __('foros/respuestas_messages.error_create');
            $response->statusCode = 500;
        }
        return $response;
    }

    /**
     * Servicio para actulizar una respuesta
     *
     * @param array<string> $atributos
     * @param int $respuestaId
     * @param int $userId
     * @return ResponseHelper
     */
    public function actualizar(array $atributos, int $respuestaId, int $userId): ResponseHelper
    {
        $response = new ResponseHelper();
        try {
            $respuesta = $this->respuestaRepository->obtenerPorRepuestaIdUserId($respuestaId, $userId);

            if ($respuesta->getKey() === null) {
                $response->success = false;
                $response->message = __('foros/respuestas_messages.error_not_found');
                $response->statusCode = 404;
                return $response;
            }

            if ($respuesta->estatus === EstatusRespuestaEnum::BANEADO) {
                $response->success = false;
                $response->message = __('foros/respuestas_messages.error_banned');
                $response->statusCode = 400;
                return $response;
            }

            $respuesta->fill($atributos);

            if ($this->respuestaRepository->guardar($respuesta)) {
                $response->success = true;
                $response->message = __('foros/respuestas_messages.successful_update');
                return $response;
            }

            $response->success = false;
            $response->message = __('foros/respuestas_messages.error_update');
            $response->statusCode = 400;
        } catch (\Throwable $th) {
            Log::error($th);
            $response->success = false;
            $response->message = __('foros/respuestas_messages.error_update');
            $response->statusCode = 500;
        }
        return $response;
    }
    /**
     * Servicio para eliminar una respuesta
     *
     * @param int $respuestaId
     * @param int $userId
     * @return ResponseHelper
     */
    public function eliminar(int $respuestaId, int $userId): ResponseHelper
    {
        $response = new ResponseHelper();
        try {
            $respuesta = $this->respuestaRepository->obtenerPorRepuestaIdUserId($respuestaId, $userId);

            if($respuesta->getKey() !== null) {
                $response->success = false;
                $response->message = __('foros/respuestas_messages.error_not_found');
                $response->statusCode = 404;
                return $response;
            }

            if ($respuesta->estatus === EstatusRespuestaEnum::BANEADO) {
                $response->success = false;
                $response->message = __('foros/respuestas_messages.error_banned');
                $response->statusCode = 400;
                return $response;
            }

            if($this->respuestaRepository->eliminar($respuesta)) {
                $response->success = true;
                $response->message = __('foros/respuestas_messages.successful_delete');
                return $response;
            }

            $response->success = false;
            $response->message = __('foros/respuestas_messages.error_delete');
            $response->statusCode = 400;
        } catch (\Throwable $th) {
            Log::error($th);
            $response->success = false;
            $response->message = __('foros/respuestas_messages.error_delete');
            $response->statusCode = 500;
        }
        return $response;
    }
}
