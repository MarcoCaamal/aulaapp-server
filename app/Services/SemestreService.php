<?php
namespace App\Services;

use App\Models\Semestre;
use App\Helpers\ResponseHelper;
use Illuminate\Support\Facades\Log;
use App\Repositories\SemestreRepository;
use App\Services\Interfaces\SemestreServiceInterface;



class SemestreService implements SemestreServiceInterface
{
    private $semestreRepository;
    public function __construct(SemestreRepository $semestreRepository)
    {
        $this->semestreRepository = $semestreRepository;
    }

    /**
     * Servicio que obtiene una colección de semestres
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function obtenerColeccion(): \Illuminate\Database\Eloquent\Collection
    {
        $semestres = new \Illuminate\Database\Eloquent\Collection();
        try {
            $semestres = $this->semestreRepository->obtenerColeccion();
        } catch (\Throwable $th) {
            Log::error($th);
        }
        return $semestres;
    }

    /**
     * Servicio que obtiene un semetre por su id
     *
     * @param int $id
     * @return Semestre
     */
    public function obtenerPorId(int $id): Semestre
    {
        $semestre = new Semestre();
        try {
            $semestre = $this->semestreRepository->obtenerPorId($id);
        } catch (\Throwable $th) {
            Log::error($th);
        }
        return $semestre;
    }

    /**
     * Servicio que crea un nuevo semestre
     *
     * @param array $attributes
     * @return ResponseHelper
     */
    public function crear(array $attributes): ResponseHelper
    {
        $response = new ResponseHelper();
        try {
            $semestre = new Semestre();

            $semestre->fill($attributes);

            if ($this->semestreRepository->guardar($semestre)) {
                $response->message = __('messages.successful_creation', ['name' => 'el Semestre']);
                $response->success = true;
                return $response;
            }
        } catch (\Throwable $th) {
            Log::error($th);
            $response->message = __('messages.failed_creation', ['name' => 'el Semestre']);
            $response->success = false;
            $response->statusCode = 400;
        }
        return $response;
    }

    /**
     * Servicio que actualiza un semestre
     * @param array $attributes
     * @param int $id
     * @return ResponseHelper
     */
    public function actualizar(array $attributes, int $id): ResponseHelper
    {
        $response = new ResponseHelper();
        try {
            $semestre = $this->semestreRepository->obtenerPorId($id);

            if (!$semestre) {
                $response->message = __('messages.not_found', ['name' => 'el Semestre']);
                $response->success = false;
                $response->statusCode = 404;
                return $response;
            }

            $semestre->fill($attributes);

            if ($this->semestreRepository->guardar($semestre)) {
                $response->message = __('messages.successful_update', ['name' => 'el Semestre']);
                $response->success = true;
                return $response;
            }
        } catch (\Throwable $th) {
            Log::error($th);
            $response->message = __('messages.failed_update', ['name' => 'el Semestre']);
            $response->success = false;
            $response->statusCode = 500;
        }
        return $response;
    }

    /**
     * Servicio que elimina un semestre
     *
     * @param int $id
     * @return ResponseHelper
     */
    public function eliminar(int $id): ResponseHelper
    {
        $response = new ResponseHelper();
        try {
            $semestre = $this->semestreRepository->obtenerPorId($id);

            if (!$semestre) {
                $response->message = __('messages.not_found', ['name' => 'el Semestre']);
                $response->success = false;
                $response->statusCode = 404;
                return $response;
            }

            if ($this->semestreRepository->eliminar($semestre)) {
                $response->message = __('messages.successful_deletion', ['name' => 'el Semestre']);
                $response->success = true;
                return $response;
            }
        } catch (\Throwable $th) {
            Log::error($th);
            $response->message = __('messages.failed_deletion', ['name' => 'el Semestre']);
            $response->success = false;
            $response->statusCode = 500;
        }
        return $response;
    }

    /**
     * @param int $id
     * @return bool
     */
    public function existe(int $id): bool
    {
        $exists = false;
        try {
            $exists = $this->semestreRepository->existe($id);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
        }
        return $exists;
    }
}
?>
