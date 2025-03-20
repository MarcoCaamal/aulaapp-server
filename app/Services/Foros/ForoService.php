<?php
namespace App\Services\Foros;

use App\Enums\Foros\EstatusForoEnum;
use App\Enums\Foros\TipoLikeEnum;
use App\Helpers\ResponseHelper;
use App\Models\Foros\Foro;
use App\Models\Foros\Like;
use App\Models\Foros\Reporte;
use App\Repositories\Foros\ForoRepository;
use App\Repositories\Foros\LikeRepository;
use App\Repositories\Foros\ReporteRepository;
use App\Repositories\MateriaRepository;
use App\Services\Interfaces\Foros\ForoServiceInterface;
use App\Services\Interfaces\UserServiceInterface;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ForoService implements ForoServiceInterface {
    private UserServiceInterface $userService;
    private ForoRepository $foroRepository;
    private ReporteRepository $reporteRepository;
    private LikeRepository $likeRepository;
    private MateriaRepository $materiaRepository;

    private int $numeroRegistrosPorPagina = 15;

    public function __construct(
        UserServiceInterface $userService,
        ForoRepository $foroRepository,
        ReporteRepository $reporteRepository,
        LikeRepository $likeRepository,
        MateriaRepository $materiaRepository
    ) {
        $this->userService = $userService;
        $this->foroRepository = $foroRepository;
        $this->reporteRepository = $reporteRepository;
        $this->likeRepository = $likeRepository;
        $this->materiaRepository = $materiaRepository;
    }

    /**
     * Servicio para obtener una páginacion de todos los foros que tenga la materia solicitada
     *
     * @param int $materiaId
     * @param int $oaginaActual
     * @return \Illuminate\Pagination\LengthAwarePaginator|null
     */
    public function obtenerPaginacionPorMateriaId(int $materiaId, int $paginaActual): \Illuminate\Pagination\LengthAwarePaginator|null
    {
        $paginacionForos = null;
        try {
            $forosDB = $this->foroRepository->obtenerColeccionPaginadaPorMateriaId(
                $materiaId,
                $paginaActual,
                $this->numeroRegistrosPorPagina
            );

            $paginacionForos = new \Illuminate\Pagination\LengthAwarePaginator(
                items: $forosDB['data'],
                total: $forosDB['total'],
                perPage: $this->numeroRegistrosPorPagina,
                currentPage: $paginaActual,
                options: [
                    'path' => \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPath()
                ]
            );
        } catch (\Throwable $th) {
            Log::error($th);
        }
        return $paginacionForos;
    }
    /**
     * Servicio para obtener una paginación de los foros de un usuario por su id
     *
     * @param int $userId
     * @param int $paginaActual
     * @return \Illuminate\Pagination\LengthAwarePaginator|null
     */
    public function obtenerPaginacionPorUserId(int $userId, int $paginaActual): \Illuminate\Pagination\LengthAwarePaginator|null
    {
        $paginacionForos = null;
        try {
            $forosDB = $this->foroRepository->obtenerColeccionPaginadaPorUserId(
                $userId,
                $paginaActual,
                $this->numeroRegistrosPorPagina
            );
            $paginacionForos = new \Illuminate\Pagination\LengthAwarePaginator(
                items: $forosDB['data'],
                total: $forosDB['total'],
                perPage: $this->numeroRegistrosPorPagina,
                currentPage: \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage(),
                options: [
                    'path' => \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPath()
                ]
            );
        } catch (\Throwable $th) {
            Log::error($th);
        }
        return $paginacionForos;
    }
    /**
     * Servicio que obtiene un foro por su id y el id del usuario del foro
     *
     * @param int $id
     * @param int $userId
     * @return Foro
     */
    public function obtenerPorId(int $id, int $userId): Foro
    {
        $foro = new Foro();
        try {
            $foro = $this->foroRepository->obtenerPorForoIdUsuarioId($id, $userId);
        } catch (\Throwable $th) {
            Log::error($th);
        }
        return $foro;
    }
    /**
     * Servicio para crear un foro desde el controlador
     *
     * @param array $atributos
     * @param int $userId
     * @return \App\Helpers\ResponseHelper
     */
    public function crear(array $atributos, int $userId): ResponseHelper
    {
        $response = new ResponseHelper();
        try {
            // Validaciones
            $materia = $this->materiaRepository->obtenerPorId($atributos['materia_id']);

            if($materia === null) {
                $response->success = false;
                $response->message = __('messages.not_found', ['name' => 'la Materia']);
                $response->statusCode = 404;
                return $response;
            }

            $foro = new Foro();
            $foro->fill($atributos);
            $foro->estatus = EstatusForoEnum::ACTIVO;
            $foro->user_id = $userId;
            $foro->materia_id = $materia->getKey();

            if (array_key_exists('imagen', $atributos)) { // Verificamos si hay la imagen
                $user = $this->userService->obtenerUsuarioPorId($userId);
                $path = 'public/foros/' .
                    mb_strtolower($user->apellido_paterno) .
                    '_' .
                    mb_strtolower(str_replace(' ', '_', $user->nombre), 'UTF-8');
                $path = strtr($path, [
                    'á' => 'a',
                    'é' => 'e',
                    'í' => 'i',
                    'ó' => 'o',
                    'ú' => 'u'
                ]);
                $pathImagenCreacion = $atributos['imagen']->store($path);
                $foro->path_imagen = $pathImagenCreacion;
                $foro->url_imagen = asset(Storage::url($pathImagenCreacion));
            }

            if ($this->foroRepository->guardar($foro)) {
                $response->success = true;
                $response->message = __('foros/foros_messages.successful_create');
                return $response;
            }

            $response->success = false;
            $response->message = __('foros/foros_messages.error_create');
            $response->statusCode = 400;
        } catch (\Throwable $th) {
            Log::error($th);
            $response->success = false;
            $response->message = __('foros/foros_messages.error_create');
            $response->statusCode = 400;
        }
        return $response;
    }
    /**
     * Servicio que actuliza un foro por su id y el id del usuario al que pertenece el foro
     *
     * @param array $atributos
     * @param mixed $foroId
     * @param mixed $userId
     * @return ResponseHelper
     */
    public function editar(array $atributos, $foroId, $userId): ResponseHelper
    {
        $response = new ResponseHelper();
        try {
            $foro = $this->foroRepository->obtenerPorForoIdUsuarioId($foroId, $userId);

            if ($foro->getKey() === null) {
                $response->success = false;
                $response->statusCode = 404;
                $response->message = __('foros/foros_messages.error_not_found');
                return $response;
            }

            if ($foro->estatus === EstatusForoEnum::BANEADO) {
                $response->success = false;
                $response->message = __('foros/foros_messages.error_banned');
                $response->statusCode = 400;
                return $response;
            }

            $foro->fill($atributos);

            if (array_key_exists('imagen', $atributos)) { // Verificamos si hay la imagen
                $user = $this->userService->obtenerUsuarioPorId($userId);
                $path = 'public/foros/' .
                    mb_strtolower($user->apellido_paterno) .
                    '_' .
                    mb_strtolower(str_replace(' ', '_', $user->nombre), 'UTF-8');
                $path = strtr($path, [
                    'á' => 'a',
                    'é' => 'e',
                    'í' => 'i',
                    'ó' => 'o',
                    'ú' => 'u'
                ]);
                // Eliminamos la imagen que tenia antes
                Storage::delete($foro->path_imagen);
                // Guardamos la imagen nueva y se le asigna la ruta de la nueva imagen
                $pathImagenCreacion = $atributos['imagen']->store($path);
                $foro->path_imagen = $pathImagenCreacion;
                $foro->url_imagen = asset(Storage::url($pathImagenCreacion));
            }

            if ($this->foroRepository->guardar($foro)) {
                $response->success = true;
                $response->message = __('foros/foros_messages.successful_update');
                return $response;
            }

            $response->success = false;
            $response->message = __('foros/foros_messages.error_update');
            $response->statusCode = 400;
        } catch (\Throwable $th) {
            Log::error($th);
            $response->success = false;
            $response->message = __('foros/foros_messages.error_update');
            $response->statusCode = 500;
        }
        return $response;
    }

    /**
     * Repositorio para borrar un foro por su id y el id del usuario del foro
     *
     * @param int $id
     * @param int $userId
     * @return ResponseHelper
     */
    public function eliminar(int $id, int $userId): ResponseHelper
    {
        $response = new ResponseHelper();
        try {
            $foro = $this->foroRepository->obtenerPorForoIdUsuarioId($id, $userId);

            if ($foro->getKey() === null) {
                $response->success = false;
                $response->message = __('foros/foros_messages.error_not_found');
                $response->statusCode = 404;
                return $response;
            }

            $pathImagen = $foro->path_imagen; // Se guarda antes de eliminarla de la DB
            if ($this->foroRepository->eliminar($foro)) {
                if (Storage::exists($pathImagen)) {
                    Storage::delete($pathImagen);
                }
                $response->success = true;
                $response->message = __('foros/foros_messages.successful_delete');
                return $response;
            }

            $response->success = false;
            $response->message = __('foros/foros_messages.error_delete');
            $response->statusCode = 400;
        } catch (\Throwable $th) {
            Log::error($th);
            $response->success = false;
            $response->message = __('foros/foros_messages.error_delete');
            $response->statusCode = 500;
        }
        return $response;
    }

    /**
     * Servicio para reporta un foro
     *
     * @param int $foroId
     * @param int $userReportanteId
     * @param string $motivo
     * @return ResponseHelper
     */
    public function reportar(int $foroId, int $userReportanteId, string $motivo = ""): ResponseHelper
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

            if ($foro->estatus === EstatusForoEnum::BANEADO) {
                $response->success = false;
                $response->message = __('foros/foros_messages.error_not_found');
                $response->statusCode = 404;
                return $response;
            }

            $reporte = new Reporte();
            $reporte->motivo = $motivo;
            $reporte->user_id = $userReportanteId;
            $reporte->foro_id = $foro->getKey();

            if ($this->reporteRepository->guardar($reporte)) {
                $response->success = true;
                $response->message = __('foros/foros_messages.successful_report');
                return $response;
            }

            $response->success = false;
            $response->message = __('foros/foros_messages.error_report');
            $response->statusCode = 400;
        } catch (\Throwable $th) {
            Log::error($th);
            $response->success = false;
            $response->message = __('foros/foros_messages.error_report');
            $response->statusCode = 500;
        }
        return $response;
    }
    /**
     * Servicio para dar like(de cualquier tipo) a un foro.
     * Se recibe el ID del foro al cual se dio like, el ID del usuario que dio like, y el tipo de like que se dio
     *
     * @param int $foroid
     * @param int $userLikedId
     * @param \App\Enums\Foros\TipoLikeEnum $tipoLike
     * @return ResponseHelper
     */
    public function darLike(int $foroId, int $userLikedId, TipoLikeEnum $tipoLike): ResponseHelper
    {
        $response = new ResponseHelper();
        try {
            $foro = $this->foroRepository->obtenerPorId($foroId);

            if($foro->getKey() === null) {
                $response->success = false;
                $response->message = __('foros/foros_messages.error_not_found');
                $response->statusCode = 404;
                return $response;
            }

            if($foro->estatus === EstatusForoEnum::BANEADO) {
                $response->success = false;
                $response->message = __('foros/foros_messages.error_banned');
                $response->statusCode = 400;
            }

            $likeUsuario = $this->likeRepository->obtenerPorUsuarioIdForoId($userLikedId, $foroId);

            if($likeUsuario->getKey() === null) {
                $likeUsuario = new Like();
                $likeUsuario->tipo = $tipoLike;
                $likeUsuario->user_id = $userLikedId;
                $likeUsuario->foro_id = $foroId;
            }

            $likeUsuario->tipo = $tipoLike;

            if($this->likeRepository->guardar($likeUsuario)) {
                $response->success = true;
                $response->message = __('foros/foros_messages.operation_like_successful');
                return $response;
            }

            $response->success = false;
            $response->message = __('foros/foros_messages.error_operation_like');
            $response->statusCode = 400;
        } catch (\Throwable $th) {
            Log::error($th);
            $response->success = false;
            $response->message = __('foros/foros_messages.error_operation_like');
            $response->statusCode = 500;
        }
        return $response;
    }
}
