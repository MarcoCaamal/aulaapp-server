<?php
use App\Helpers\ResponseHelper;
use App\Models\Desafios\Desafio;
use App\Repositories\Desafios\DesafioRepository;
use App\Repositories\MateriaRepository;
use App\Repositories\UserRepository;
use App\Services\Interfaces\Desafios\DesafioServiceInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class DesafioService implements DesafioServiceInterface {
    private DesafioRepository $desafioRepository;
    private UserRepository $userRepository;
    private MateriaRepository $materiaRepository;

    private int $numRegistrosPorPagina = 15;

    public function __construct(
        DesafioRepository $desafioRepository,
        UserRepository $userRepository,
        MateriaRepository $materiaRepository
    )
    {
        $this->desafioRepository = $desafioRepository;
        $this->userRepository = $userRepository;
        $this->materiaRepository = $materiaRepository;
    }

    /**
     * Servicio que obtiene una paginación de desafios
     *
     * @param int $page
     * @return Illuminate\Pagination\LengthAwarePaginator
     */
    public function obtenerPaginacionPorProfesorId(int $profesorId, int $paginaActual): Illuminate\Pagination\LengthAwarePaginator|null
    {
        $paginacion = null;
        try {
            $desafiosDB = $this->desafioRepository->obtenerColeccionPaginadaPorProfesorId(
                $profesorId,
                $paginaActual,
                $this->numRegistrosPorPagina
            );
            $paginacion = new \Illuminate\Pagination\LengthAwarePaginator(
                items: $desafiosDB['data'],
                total: $desafiosDB['total'],
                perPage: $this->numRegistrosPorPagina,
                currentPage: $paginaActual,
                options: [
                    'path' => \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPath()
                ]
            );
        } catch (\Throwable $th) {
            Log::error($th);
        }
        return $paginacion;
    }

    /**
     * Servicio que obtiene un desafio por su ID
     *
     * @param int $id
     * @return App\Models\Desafios\Desafio
     */
    public function obtenerPorId(int $id): App\Models\Desafios\Desafio
    {
        $desafio = new Desafio();
        try {
            $desafio = $this->desafioRepository->obtenerPorId($id);
        } catch (\Throwable $th) {
            Log::error($th);
        }
        return $desafio;
    }
    /**
     * Servicio que crea un desafio
     *
     * @param array $atributos
     * @param int $materiaId
     * @param int $profesorId
     * @return App\Helpers\ResponseHelper
     */
    public function crear(array $atributos, int $materiaId, int $profesorId): App\Helpers\ResponseHelper
    {
        $response = new ResponseHelper();
        try {
            $profesorTieneMateria = $this->materiaRepository->verificarMateriaProfesor($profesorId, $materiaId);

            if(!$profesorTieneMateria) {
                $response->success = false;
                $response->message = __('desafios/desafios_messages.error_profesor_materia_mismatch');
                $response->statusCode = 400;
                return $response;
            }

            $fechaInicio = Carbon::parse($atributos['fecha_inicio']);

            if($fechaInicio->lessThan(Carbon::now())) {
                $response->success = false;
                $response->message = __('desafios/desafios_messages.error_profesor_materia_mismatch');
                $response->statusCode = 400;
                return $response;
            }

            $desafio = new Desafio();
            $desafio->fill($atributos);
            $desafio->materia_id = $materiaId;
            $desafio->profesor_id = $profesorId;

            if($this->desafioRepository->guardar($desafio)) {
                $response->success = true;
                $response->message = __('desafios/desafios_messages.successful_create');
                return $response;
            }

            $response->success = false;
            $response->message = __('desafios/desafios_messages.error_create');
            $response->statusCode = 400;
        } catch (\Throwable $th) {
            Log::error($th);
            $response->success = false;
            $response->message = __('desafios/desafios_messages.error_create');
            $response->statusCode = 500;
        }
        return $response;
    }
	/**
	 * Servicio para editar un desafio
	 *
	 * @param array $atributos
	 * @param int $profesorId
	 * @param int $desafioId
	 * @return ResponseHelper
	 */
	public function editar(array $atributos, int $profesorId, int $desafioId): ResponseHelper
    {
        $response = new ResponseHelper();
        try {
            $desafio = $this->desafioRepository->buscarPorDesafioIdProfesorId($desafioId, $profesorId);

            if($desafio->getKey() === null) {
                $response->success = false;
                $response->message = __('desafios/desafios_messages.error_not_found');
                $response->statusCode = 404;
                return $response;
            }

            $fechaInicioDesafio = Carbon::parse($desafio->fecha_inicio);

            if($fechaInicioDesafio->lessThanOrEqualTo(Carbon::now())) {
                $response->success = false;
                $response->message = __('desafios/desafios_messages.error_desafio_finished');
                $response->statusCode = 400;
                return $response;
            }

            $desafio->fill($atributos);

            if($this->desafioRepository->guardar($desafio)) {
                $response->success = false;
                $response->message = __('desafios/desafios_messages.successful_update');
                return $response;
            }

            $response->success = false;
            $response->message = __('desafios/desafios_messages.error_update');
            $response->statusCode = 400;
        } catch (\Throwable $th) {
            Log::error($th);
            $response->success = false;
            $response->message = __('desafios/desafios_messages.error_update');
            $response->statusCode = 500;
        }
        return $response;
	}
	/**
	 * Repositorio para eliminar un desafio
	 *
	 * @param int $desafioId
	 * @param int $profesorId
	 * @return ResponseHelper
	 */
	public function eliminar(int $desafioId, int $profesorId): ResponseHelper
    {
        $response = new ResponseHelper();
        try {
            $desafio = $this->desafioRepository->buscarPorDesafioIdProfesorId($desafioId, $profesorId);

            if($desafio->getKey() === null) {
                $response->success = false;
                $response->message = __('desafios/desafios_messages.error_not_found');
                $response->statusCode = 404;
                return $response;
            }

            $fechaInicioDesafio = Carbon::parse($desafio->fecha_inicio);

            if($fechaInicioDesafio->lessThanOrEqualTo(Carbon::now())) {
                $response->success = false;
                $response->message = __('desafios/desafios_messages.error_desafio_finished');
                $response->statusCode = 400;
                return $response;
            }

            if($this->desafioRepository->eliminar($desafio)) {
                $response->success = false;
                $response->message = __('desafios/desafios_messages.successful_delete');
                return $response;
            }

            $response->success = false;
            $response->message = __('desafios/desafios_messages.error_delete');
            $response->statusCode = 400;
        } catch (\Throwable $th) {
            Log::error($th);
            $response->success = false;
            $response->message = __('desafios/desafios_messages.error_delete');
            $response->statusCode = 500;
        }
        return $response;
	}
}
