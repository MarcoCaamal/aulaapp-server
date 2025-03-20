<?php
namespace App\Services;

use App\Models\Materia;
use App\Helpers\ResponseHelper;
use Illuminate\Support\Facades\Log;
use App\Repositories\MateriaRepository;
use App\Repositories\SemestreRepository;
use App\Services\Interfaces\MateriaServiceInterface;



class MateriaService implements MateriaServiceInterface
{
    private $materiaRepository;
    private $semestreRepository;
    public function __construct(MateriaRepository $materiaRepository, SemestreRepository $semestreRepository)
    {
        $this->materiaRepository = $materiaRepository;
        $this->semestreRepository = $semestreRepository;
    }

    /**
     * Servicio que obtiene una colección de materias
     *
     * @return \Illuminate\Database\Eloquent\Collection<Materia>
     */
    public function obtenerColeccion(): \Illuminate\Database\Eloquent\Collection
    {
        $materias = new \Illuminate\Database\Eloquent\Collection();
        try {
            $materias = $this->materiaRepository->obtenerColeccion();
        } catch (\Throwable $th) {
            Log::error($th);
        }
        return $materias;
    }

    /**
     * Servicio que obtiene una colección de materias con sus relaciones
     *
     * @return \Illuminate\Database\Eloquent\Collection<Materia>
     */
    public function obtenerColeccionConRelaciones(array $relations): \Illuminate\Database\Eloquent\Collection
    {
        $materias = new \Illuminate\Database\Eloquent\Collection();
        try {
            $materias = $this->materiaRepository->obtenerColeccionConRelaciones($relations);
        } catch (\Throwable $th) {
            Log::error($th);
        }
        return $materias;
    }

    public function obtenerColeccionPorProfesorId(int $idProfesor): \Illuminate\Database\Eloquent\Collection
    {
        $materias = new \Illuminate\Database\Eloquent\Collection();
        try {

        } catch (\Throwable $th) {
            Log::error($th);
        }
        return $materias;
    }

    /**
     * Servicio que obtiene una materia por su id
     *
     * @param int $id
     * @return Materia
     */
    public function obtenerPorId(int $id): Materia
    {
        $materia = new Materia();
        try {
            $materia = $this->materiaRepository->obtenerPorId($id);
        } catch (\Throwable $th) {
            Log::error($th);
        }
        return $materia;
    }

    /**
     * Servicio para crear una materia
     *
     * @param array $attributes
     * @return ResponseHelper
     */
    public function crear(array $attributes): ResponseHelper
    {
        $response = new ResponseHelper();
        try {
            $materia = new Materia();

            $materia->fill($attributes);

            $existeSemestre = $this->semestreRepository->existe($materia->semestre_id);

            if (!$existeSemestre) {
                $response->success = false;
                $response->statusCode = 404;
                return $response;
            }

            if ($this->materiaRepository->guardar($materia)) {
                $response->message = __('messages.successful_creation', ['name' => 'la Materia']);
                $response->success = true;
                return $response;
            }
        } catch (\Throwable $th) {
            Log::error($th);
            $response->message = __('messages.failed_creation', ['name' => 'la Materia']);
            $response->success = false;
            $response->statusCode = 500;
        }
        return $response;
    }

    /**
     * Servicio para actualizar una materia
     *
     * @param array $attributes
     * @param int $id
     * @return ResponseHelper
     */
    public function actualizar(array $attributes, int $id): ResponseHelper
    {
        $response = new ResponseHelper();
        try {
            $materia = $this->materiaRepository->obtenerPorId($id);

            if (!$materia) {
                $response->success = false;
                $response->statusCode = 404;
                return $response;
            }

            $materia->fill($attributes);
            $existeSemestre = $this->semestreRepository->existe($materia->semestre->id);

            if (!$existeSemestre) {
                $response->success = false;
                $response->statusCode = 404;
                return $response;
            }

            if ($this->materiaRepository->guardar($materia)) {
                $response->message = __('messages.successful_update', ['name' => 'la Materia']);
                $response->success = true;
                return $response;
            }
        } catch (\Throwable $th) {
            Log::error($th);
            $response->message = __('messages.failed_update', ['name' => 'la Materia']);
            $response->success = false;
            $response->statusCode = 500;
        }
        return $response;
    }

    /**
     * Servicio para eliminar una materia
     *
     * @param int $id
     * @return ResponseHelper
     */
    public function eliminar(int $id): ResponseHelper
    {
        $response = new ResponseHelper();
        try {
            $materia = $this->materiaRepository->obtenerPorId($id);

            if (!$materia) {
                $response->message = __('messages.not_found', ['name' => 'la Materia']);
                $response->success = false;
                $response->statusCode = 404;
                return $response;
            }

            if ($this->materiaRepository->eliminar($materia)) {
                $response->message = __('messages.successful_deletion', ['name' => 'la Materia']);
                $response->success = true;
                return $response;
            }
        } catch (\Throwable $th) {
            Log::error($th);
            $response->message = __('messages.failed_deletion', ['name' => 'la Materia']);
            $response->success = false;
            $response->statusCode = 500;
        }
        return $response;
    }

    /**
     * Servicio para verificar si exiten un conjunto de materias
     *
     * @param array $materiasIds
     * @return bool
     */
    public function existeMateriasPorIds(array $materiasIds): bool
    {
        $exists = true;
        try {
            $materias = $this->materiaRepository->obtenerColeccionMateriasByIds($materiasIds);

            foreach ($materiasIds as $materiaId) {
                if (!$materias->contains($materiaId)) {
                    $exists = false;
                    break;
                }
            }
        } catch (\Throwable $th) {
            Log::error($th);
            $exists = false;
        }
        return $exists;
    }
}
?>
