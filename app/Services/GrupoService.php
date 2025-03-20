<?php
namespace App\Services;

use App\Models\Grupo;
use App\Helpers\ResponseHelper;
use Illuminate\Support\Facades\Log;
use App\Repositories\UserRepository;
use App\Repositories\GrupoRepository;
use App\Services\Interfaces\GrupoServiceInterface;
use App\Services\Interfaces\SemestreServiceInterface;


class GrupoService implements GrupoServiceInterface
{
    private GrupoRepository $grupoRepository;
    private SemestreServiceInterface $semestreService;
    private UserRepository $userRepository;

    public function __construct(GrupoRepository $grupoRepository, SemestreServiceInterface $semestreService,
        UserRepository $userRepository) {
        $this->grupoRepository = $grupoRepository;
        $this->semestreService = $semestreService;
        $this->userRepository = $userRepository;
    }

    /**
     * Servicio que obtiene un colección de grupos
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function obtenerColeccion(): \Illuminate\Database\Eloquent\Collection
    {
        $grupos = new \Illuminate\Database\Eloquent\Collection();
        try {
            $grupos = $this->grupoRepository->obtenerColeccion();
        } catch (\Throwable $th) {
            Log::error($th);
        }
        return $grupos;
    }

    /**
     * Servicio que obtiene un grupo por su id
     *
     * @param int $id
     * @return Grupo
     */
    public function obtenerPorId(int $id): Grupo
    {
        $model = new Grupo();
        try {
            $model = $this->grupoRepository->obtenerPorId($id);
        } catch (\Throwable $th) {
            Log::error($th);
        }
        return $model;
    }

    public function obtenerGrupoActualAlumnoPorIdAlumno(int $idAlumno): Grupo|null
    {
        $grupo = new Grupo();
        try {
            $alumno = $this->userRepository->obtenerAlumnoPorId($idAlumno);
            $grupo = $alumno->grupos
                ->sortByDesc(function($grupo) {
                    return $grupo->pivot->created_at;
                })->first();
        } catch (\Throwable $th) {
            Log::error($th);
        }
        return $grupo;
    }

    /**
     * Servicio que guarda un grupo en la DB
     *
     * @param array $attributes
     * @return ResponseHelper
     */
    public function crear(array $attributes): ResponseHelper
    {
        $response = new ResponseHelper();
        try {
            $grupo = new Grupo();
            $grupo->nombre = $attributes['nombre'];
            $grupo->semestre_id = $attributes['id_semestre'];

            $existsSemestre = $this->semestreService->existe($grupo->semestre_id);

            if(!$existsSemestre) {
                $response->success = false;
                $response->message = __('messages.not_found', ['name' => 'el Semestre']);
                $response->statusCode = 404;
                return $response;
            }

            if($this->grupoRepository->guardar($grupo)) {
                $response->success = true;
                $response->message = __('messages.successful_creation', ['name' => 'el Grupo']);
                return $response;
            }

        } catch (\Throwable $th) {
            $response->success = false;
            $response->message = __('messages.failed_creation', ['name' => 'el Grupo']);
            $response->statusCode = 500;
            Log::error($th);
        }
        return $response;
    }

    /**
     * Servicio que actualiza un grupo en la DB
     *
     * @param array $attributes
     * @param int $id
     * @return ResponseHelper
     */
    public function actualizar(array $attributes, int $id): ResponseHelper
    {
        $response = new ResponseHelper();
        try {
            $grupo = $this->grupoRepository->obtenerPorId($id);

            if($grupo->getKey() === null) {
                $response->success = false;
                $response->message = __('messages.not_found', ['name' => 'el Grupo']);
                $response->statusCode = 404;
                return $response;
            }

            if(!$this->semestreService->existe($attributes['id_semestre'])) {
                $response->success = false;
                $response->message = __('messages.not_found', ['name' => 'el Semestre']);
                $response->statusCode = 404;
                return $response;
            }

            $grupo->nombre = $attributes['nombre'];
            $grupo->semestre_id = $attributes['id_semestre'];

            if($this->grupoRepository->guardar($grupo)) {
                $response->success = true;
                $response->message = __('messages.successful_update', ['name' => 'el Grupo']);
                return $response;
            }
        } catch (\Throwable $th) {
            $response->success = false;
            $response->message = __('messages.failed_update', ['name' => 'el Grupo']);
            $response->statusCode = 500;
            Log::error($th);
        }
        return $response;
    }

    /**
     * Servicio que elimina un grupo en la DB
     *
     * @param int $id
     * @return ResponseHelper
     */
    public function eliminar(int $id): ResponseHelper
    {
        $response = new ResponseHelper();
        try {
            $grupo = $this->grupoRepository->obtenerPorId($id);

            if(!isset($grupo)) {
                $response->success = false;
                $response->message = __('messages.not_found', ['name' => 'el Grupo']);
                $response->statusCode = 404;
                return $response;
            }

            if($this->grupoRepository->eliminar($grupo)) {
                $response->success = true;
                $response->message = __('messages.successful_deletion', ['name' => 'el Grupo']);
                return $response;
            }
        } catch (\Throwable $th) {
            $response->success = false;
            $response->message = __('messages.failed_deletion', ['name' => 'el Grupo']);
            $response->statusCode = 500;
            Log::error($th);
        }
        return $response;
    }

    public function existe(int $id): bool
    {
        $exists = false;

        try {
            $exists = $this->grupoRepository->existe($id);
        } catch (\Throwable $th) {
            Log::error($th);
        }
        return $exists;
    }
}
