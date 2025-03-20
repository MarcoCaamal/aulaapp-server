<?php
namespace App\Services;

use App\Models\Ciclo;
use App\Helpers\ResponseHelper;
use App\Services\Interfaces\CicloServiceInterface;
use Illuminate\Support\Facades\Log;
use App\Repositories\CicloRepository;

class CicloService implements CicloServiceInterface
{
    private $cicloRepository;
    public function __construct(CicloRepository $cicloRepository)
    {
        $this->cicloRepository = $cicloRepository;
    }

    /**
     * Servicio que obtiene un colección de ciclos
     *
     * @return \Illuminate\Database\Eloquent\Collection<Ciclo>
     */
    public function obtenerColeccion(): \Illuminate\Database\Eloquent\Collection
    {
        $ciclos = new \Illuminate\Database\Eloquent\Collection();
        try {
            $ciclos = $this->cicloRepository->obtenerColeccion();
        } catch (\Throwable $th) {
            Log::error($th);
        }
        return $ciclos;
    }

    /**
     * Servicio que obtiene un ciclo por su id
     *
     * @param int $id
     * @return Ciclo
     */
    public function obtenerPorId(int $id): Ciclo
    {
        $ciclo = new Ciclo();
        try {
            $ciclo = $this->cicloRepository->obtenerPorId($id);
        } catch (\Throwable $th) {
            Log::error($th);
        }
        return $ciclo;
    }

    /**
     * Servicio que obtiene el ciclo que esta activo actualmente
     *
     * @return Ciclo|null
     */
    public function obtenerCicloActivo(): Ciclo|null
    {
        $ciclo = new Ciclo();
        try {
            $ciclo = $this->cicloRepository->obtenerCicloActivo();
            return $ciclo;
        } catch (\Throwable $th) {
            Log::error($th);
        }
        return $ciclo;
    }

    /**
     * Servicio que registra un nuevo ciclo
     *
     * @param array $attributes
     * @return ResponseHelper
     */
    public function crear(array $attributes): ResponseHelper
    {
        $ciclo = new Ciclo();
        try {
            $response = new ResponseHelper();

            $ciclo->fill($attributes);

            $ciclo->is_activo = false;

            if ($this->cicloRepository->guardar($ciclo)) {
                $response->message = __('messages.successful_creation', ['name' => 'el Ciclo']);
                $response->success = true;
                return $response;
            }
        } catch (\Throwable $th) {
            Log::error($th);
            $response->message = __('messages.failed_creation', ['name' => 'el Ciclo']);
            $response->success = false;
            $response->statusCode = 500;
        }
        return $response;
    }

    /**
     * Servicio que actualiza un ciclo
     * @param array $attributes
     * @param int $id
     * @return ResponseHelper
     */
    public function actualizar(array $attributes, int $id): ResponseHelper
    {
        $response = new ResponseHelper();
        try {
            $ciclo = $this->cicloRepository->obtenerPorId($id);

            if ($ciclo->getKey() == null) {
                $response->message = __('messages.not_found', ['name' => 'el Ciclo']);
                $response->success = false;
                $response->statusCode = 404;
                return $response;
            }

            $ciclo->fill($attributes);

            if ($this->cicloRepository->guardar($ciclo)) {
                $response->message = __('messages.successful_update', ['name' => 'el Ciclo']);
                $response->success = true;
                return $response;
            }
        } catch (\Throwable $th) {
            Log::error($th);
            $response->message = __('messages.failed_update', ['name' => 'el Ciclo']);
            $response->success = false;
            $response->statusCode = 500;
        }
        return $response;
    }

    /**
     * Servicio que elimina un ciclo
     *
     * @param int $id
     * @return ResponseHelper
     */
    public function eliminar(int $id): ResponseHelper
    {
        $response = new ResponseHelper();
        try {
            $ciclo = $this->cicloRepository->obtenerPorId($id);

            if (!$ciclo) {
                $response->message = __('messages.not_found', ['name' => 'el Ciclo']);
                $response->success = false;
                $response->statusCode = 404;
                return $response;
            }

            if ($ciclo->is_activo) {
                $response->message = __('messages.ciclos.not_active_ciclo');
                $response->success = false;
                $response->statusCode = 400;
                return $response;
            }

            if ($this->cicloRepository->eliminar($ciclo)) {
                // $response->message = __('messages.successful_deletion', ['name' => 'el Ciclo']);
                $response->message = __('messages.successful_deletion', ['name' => 'el Ciclo']);
                $response->success = true;
                return $response;
            }
        } catch (\Throwable $th) {
            Log::error($th);
            $response->message = __('messages.failed_deletion', ['name' => 'el Ciclo']);
            $response->success = false;
            $response->statusCode = 500;
        }
        return $response;
    }
    /**
     * @param int $idCiclo
     * @return ResponseHelper
     */
    public function activar(int $idCiclo): ResponseHelper
    {
        $response = new ResponseHelper();
        try {
            $cicloActive = $this->cicloRepository->obtenerCicloActivo();

            if($cicloActive) {
                $response->success = false;
                $response->message = __('messages.ciclos.error_already_exists_active_ciclo');
                $response->statusCode = 404;
                return $response;
            }

            $ciclo = $this->cicloRepository->obtenerPorId($idCiclo);

            $ciclo->is_activo = true;

            if($this->cicloRepository->guardar($ciclo))
            {
                $response->success = true;
                $response->message = __('messages.ciclos.activate');
                return $response;
            }
        } catch (\Throwable $th) {
            $response->success = false;
            $response->message = __('messages.ciclos.error_activate');
            $response->statusCode = 500;
            Log::error($th);
        }
        return $response;
    }

    /**
     *
     * @param int $idCiclo
     * @return ResponseHelper
     */
    public function desactivar(int $idCiclo): ResponseHelper
    {
        $response = new ResponseHelper();
        try {
            $ciclo = $this->cicloRepository->obtenerPorId($idCiclo);

            $ciclo->is_activo = false;

            if($this->cicloRepository->guardar($ciclo))
            {
                $response->success = true;
                $response->message = __('messages.ciclos.desactivate');
                return $response;
            }
        } catch (\Throwable $th) {
            $response->success = false;
            $response->message = __('messages.ciclos.error_desactivate');
            $response->statusCode = 500;
            Log::error($th);
        }
        return $response;
    }
}
