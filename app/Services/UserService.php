<?php
namespace App\Services;

use App\Models\User;
use App\Models\DTOs\UserDTO;
use Illuminate\Http\Request;
use App\Helpers\ResponseHelper;
use App\Models\PersonalAccessToken;
use Illuminate\Support\Facades\Log;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use App\Models\DTOs\ParamsProfesorApiDTO;
use App\Services\Interfaces\CicloServiceInterface;
use App\Services\Interfaces\UserServiceInterface;
use App\Services\Interfaces\GrupoServiceInterface;
use App\Services\Interfaces\MateriaServiceInterface;



class UserService implements UserServiceInterface
{
	private UserRepository $userRepository;
	private MateriaServiceInterface $materiaService;
	private GrupoServiceInterface $grupoService;
	private CicloServiceInterface $cicloService;
	private Request $request;

	public function __construct(
		UserRepository $userRepository,
		MateriaServiceInterface $materiaService,
		GrupoServiceInterface $grupoService,
		CicloServiceInterface $cicloService,
		Request $request,
	)
	{
		$this->userRepository = $userRepository;
		$this->materiaService = $materiaService;
		$this->grupoService = $grupoService;
		$this->cicloService = $cicloService;
		$this->request = $request;
	}

	/*
	|--------------------|
	| SECCIÓN DE ALUMNOS |
	|--------------------|
	*/

	/**
	 * Servicio que obtiene una colección de usuarios de tipo Alumno
	 *
	 * @return \Illuminate\Database\Eloquent\Collection<\App\Models\User>
	 */
	public function obtenerColeccionAlumnos(): \Illuminate\Database\Eloquent\Collection
	{
		$alumnos = new \Illuminate\Database\Eloquent\Collection();
		try {
			$alumnos = $this->userRepository->obtenerColeccionAlumnos();
		} catch (\Throwable $th) {
			Log::error($th);
		}
		return $alumnos;
	}
	/**
	 * Servicio que obtiene un usuario de tipo Alumno por su id
	 *
	 * @param int $id
	 * @return \App\Models\User
	 */
	public function obtenerAlumnoPorId(int $id): User|null
	{
		$alumno = new User();
		try {
			$alumno = $this->userRepository->obtenerAlumnoPorId($id);
		} catch (\Throwable $th) {
			Log::error($th);
		}
		return $alumno;
	}
	/**
	 * Servicio para crear un alumno desde el controlador
	 *
	 * @param array $attributes
	 * @return ResponseHelper
	 */
	public function crearAlumno(array $attributes): ResponseHelper
	{
		$response = new ResponseHelper();
		try {
			$alumno = new User();
			$alumno->fill($attributes);
			$alumno->password = Hash::make('password'); // De momento
			$alumno->curp = strtoupper($alumno->curp);
			if (!$this->grupoService->existe($attributes['grupo_id'])) {
				$response->message = __('messages.not_found', ['name' => 'el Grupo']);
				$response->success = false;
				$response->statusCode = 404;
				return $response;
			}

			$cicloActivo = $this->cicloService->obtenerCicloActivo();

			if ($cicloActivo === null) {
				$response->message = __('messages.ciclos.not_active_ciclo');
				$response->success = false;
				return $response;
			}

			$alumnoFullName = $alumno->getFullName();

			if ($this->userRepository->existeUsuarioPorNombre($alumnoFullName)) {
				$response->message = __('messages.users.alumno_already_exists', ['name' => $alumno->getFullName()]);
				$response->success = false;
				$response->statusCode = 400;
				return $response;
			}

			if ($this->userRepository->crearAlumno($alumno, $attributes['grupo_id'], $cicloActivo->id)) {
				event(new Registered($alumno));
				$response->message = __('messages.successful_creation', ['name' => 'el Alumno']);
				$response->success = true;
				return $response;
			}
		} catch (\Throwable $th) {
			Log::error($th);
			$response->message = __('messages.failed_creation', ['name' => 'el Alumno']);
			$response->success = false;
			$response->statusCode = 500;
		}
		return $response;
	}

	/**
	 * Servicio para actualizar un alumno desde el controlador
	 *
	 * @param array $attributes
	 * @param int $id
	 * @return ResponseHelper
	 */
	public function actualizarAlumno(array $attributes, int $id): ResponseHelper
	{
		$response = new ResponseHelper();
		try {
			$alumno = $this->userRepository->obtenerAlumnoPorId($id);
			$alumno->fill($attributes);
			$alumno->curp = strtoupper($alumno->curp);
			$cicloActivo = $this->cicloService->obtenerCicloActivo();
			$grupoActualAlumno = $alumno->grupos->where('pivot.is_activo', true)->first();

			if (!$alumno) {
				$response->message = __('messages.not_found', ['name' => 'el Alumno']);
				$response->success = false;
				$response->statusCode = 404;
				return $response;
			}

			$duplicateUser = $this->userRepository->obtenerUsuarioPorNombreCompleto($alumno->getFullName());
			$isDuplicateUser = $duplicateUser !== null ? $duplicateUser->getKey() !== $alumno->getKey() : false;

			if ($isDuplicateUser) {
				$response->message = __('messages.users.already_exists', ['user' => 'Alumno', 'name' => $alumno->getFullName()]);
				$response->success = false;
				$response->statusCode = 400;
				return $response;
			}

			if (!$this->grupoService->existe($attributes['grupo_id'])) {
				$response->message = __('messages.not_found', ['name' => 'el Grupo']);
				$response->success = false;
				$response->statusCode = 404;
				return $response;
			}

			if (!isset($cicloActivo)) {
				$response->message = __('messages.ciclos.not_active_ciclo');
				$response->success = false;
				return $response;
			}

			$grupoAlreadyUsed = $alumno->grupos->contains($attributes['grupo_id']);

			if ($grupoAlreadyUsed && $grupoActualAlumno->id != $attributes['grupo_id']) {
				$response->message = __('messages.grupo_already_used');
				$response->success = false;
				$response->statusCode = 400;
				return $response;
			}

			if ($this->userRepository->actualizarAlumno($alumno, $attributes['grupo_id'], $cicloActivo->id)) {
				$response->message = __('messages.successful_update', ['name' => 'el Alumno']);
				$response->success = true;
				return $response;
			}

		} catch (\Throwable $th) {
			Log::error($th);
			$response->message = __('messages.failed_update', ['name' => 'el Alumno']);
			$response->success = false;
			$response->statusCode = 500;
		}

		return $response;
	}

	/*
	|--------------------------------|
	| FINAL DE LA SECCIÓN DE ALUMNOS |
	|--------------------------------|
	*/

	/*
	|-----------------------|
	| SECCIÓN DE PROFESORES |
	|-----------------------|
	*/

	/**
	 * Servicio que obtiene una colección de usuarios de tipo Profesor
	 *
	 * @return \Illuminate\Database\Eloquent\Collection<\App\Models\User>
	 */
	public function obtenerColeccionProfesores(): \Illuminate\Database\Eloquent\Collection
	{
		$profesores = new \Illuminate\Database\Eloquent\Collection();
		try {
			$profesores = $this->userRepository->obtenerColeccionProfesores();
		} catch (\Throwable $th) {
			Log::error($th);
		}
		return $profesores;
	}
	/**
	 * Servicio que obtiene un usuario de tipo Profesor por su id con sus relaciones
	 *
	 * @param int $id
	 * @param array $relations
	 * @return \Illuminate\Database\Eloquent\Model<User>
	 */
	public function obtenerProfesorPorIdConRelaciones(int $id, array $relations): \Illuminate\Database\Eloquent\Model|User
	{
		$profesor = new User();
		try {
			$profesor = $this->userRepository->obtenerProfesorPorProfesorIdConRelaciones($id, $relations);
		} catch (\Throwable $th) {
			Log::error($th);
		}
		return $profesor;
	}
	public function obtenerColeccionPaginadaConFiltrosRelaciones(ParamsProfesorApiDTO $filtros): \Illuminate\Pagination\LengthAwarePaginator|null
	{
		$profesores = null;
		try {
            $alumno = $this->userRepository->obtenerAlumnoPorId($filtros->idAlumno);
            $grupoAlumnoActual = $alumno->grupos->sortByDesc('semestre_id')->first();
            $filtros->idSemestreAlumno = $grupoAlumnoActual->semestre_id;

			$profesores = $this->userRepository->obtenerColeccionPaginadaProfesoresConFiltros($filtros);
		} catch (\Throwable $th) {
			Log::error($th);
		}
		return $profesores;
	}
	public function obtenerProfesorPorProfesorIdSemestreId(int $idProfesor, int $semestreId): User|null
	{
		$profesor = new User();
		try {

			$profesor = $this->userRepository->obtenerProfesorPorProfesorIdSemestreId($idProfesor, $semestreId);
		} catch (\Throwable $th) {
			Log::error($th);
		}
		return $profesor;
	}
	/**
	 * Servicio que obtiene un usuario de tipo Profesor por su id
	 *
	 * @param int $id
	 * @return \Illuminate\Database\Eloquent\Model<User>
	 */
	public function obtenerProfesorPorId(int $id): \Illuminate\Database\Eloquent\Model
	{
		$profesor = new User();
		try {
			$profesor = $this->userRepository->obtenerProfesorPorId($id);
		} catch (\Throwable $th) {
			Log::error($th);
		}
		return $profesor;
	}
	/**
	 * Servicio para crear un profesor desde el controlador
	 *
	 * @param array $attributes
	 * @return ResponseHelper
	 */
	public function crearProfesor(array $attributes): ResponseHelper
	{
		$response = new ResponseHelper();
		try {
			$profesor = new User();

			$profesor->fill($attributes);
			$profesor->curp = strtoupper($profesor->curp);
			$profesor->password = Hash::make('password'); // De momento

			$existsMaterias = $this->materiaService->existeMateriasPorIds($attributes['materias']);

			if (!$existsMaterias) {
				$response->message = __('messages.not_found', ['name' => 'alguna de las materias ingresadas']);
				$response->success = false;
				$response->statusCode = 404;
				return $response;
			}

			if ($this->userRepository->existeUsuarioPorNombre($profesor->getFullName())) {
				$response->message = __('messages.users.already_exists', ['user' => 'Profesor', 'name' => $profesor->getFullName()]);
				$response->success = false;
				$response->statusCode = 400;
				return $response;
			}

			if ($this->userRepository->crearProfesor($profesor, $attributes['materias'])) {
				event(new Registered($profesor));
				$response->message = __('messages.successful_creation', ['name' => 'el Profesor']);
				$response->success = true;
				return $response;
			}
		} catch (\Throwable $th) {
			Log::error($th);
			$response->message = __('messages.failed_creation', ['name' => 'el Profesor']);
			$response->success = false;
			$response->statusCode = 500;
		}
		return $response;
	}

	/**
	 * Servicio para actualizar un profesor desde el controlador
	 *
	 * @param array $attributes
	 * @param int $id
	 * @return ResponseHelper
	 */
	public function actualizarProfesor(array $attributes, int $id): ResponseHelper
	{
		$response = new ResponseHelper();
		try {
			$profesor = $this->userRepository->obtenerProfesorPorId($id);

			if (!$profesor) {
				$response->message = __('messages.not_found', ['name' => 'el Profesor.']);
				$response->success = false;
				$response->statusCode = 404;
				return $response;
			}

			$profesor->fill($attributes);

			$exitsMaterias = $this->materiaService->existeMateriasPorIds($attributes['materias']);

			if (!$exitsMaterias) {
				$response->message = __('messages.not_found', ['name' => 'alguna de las materias ingresadas']);
				$response->success = false;
				$response->statusCode = 404;
				return $response;
			}

			$duplicateUser = $this->userRepository->obtenerUsuarioPorNombreCompleto($profesor->getFullName());
			$isDuplicateUser = $duplicateUser !== null ? $duplicateUser->getKey() !== $profesor->getKey() : false;

			if ($isDuplicateUser) {
				$response->message = __('messages.users.already_exists', ['user' => 'Profesor', 'name' => $profesor->getFullName()]);
				$response->success = false;
				$response->statusCode = 400;
				return $response;
			}

			$profesor->curp = strtoupper($profesor->curp);

			if ($this->userRepository->actualizarProfesor($profesor, $attributes['materias'])) {
				$response->message = __('messages.successful_update', ['name' => 'el Profesor']);
				$response->success = true;
				return $response;
			}
		} catch (\Throwable $th) {
			Log::error($th);
			$response->message = __('messages.failed_update', ['name' => 'el Profesor']);
			$response->success = false;
			$response->statusCode = 500;
		}
		return $response;
	}

	/*
	|-----------------------------------|
	| FINAL DE LA SECCIÓN DE PROFESORES |
	|-----------------------------------|
	*/

	/*
	|-----------------------------|
	| SECCIÓN USUARIOS EN GENERAL |
	|-----------------------------|
	*/

	/**
	 * Servicio que obtiene un usuario por su email
	 *
	 * @param string $email
	 * @return \Illuminate\Database\Eloquent\Model|User
	 */
	public function obtenerUsuarioPorEmail(string $email): \Illuminate\Database\Eloquent\Model|User
	{
		$user = new User();
		try {
			$user = $this->userRepository->obtenerUsuarioPorEmail($email);
		} catch (\Throwable $th) {
			Log::error($th);
		}
		return $user;
	}
	/**
	 * Servicio que obtiene un usuario por su id
	 *
	 * @param int $id
	 * @return User
	 */
	public function obtenerUsuarioPorId(int $id): User
	{
		$user = new User();
		try {
			$user = $this->userRepository->obtenerUsuarioPorId($id);
		} catch (\Throwable $th) {
			Log::error($th);
		}
		return $user;
	}

	/**
	 * Servicio para eliminar un usuario desde el controlador
	 *
	 * @param int $id
	 * @return ResponseHelper
	 */
	public function eliminar(int $id): ResponseHelper
	{
		$response = new ResponseHelper();
		try {
			$user = $this->userRepository->obtenerUsuarioPorId($id);

			if (!$user) {
				$response->message = __('messages.not_found', ['name' => 'el Alumno']);
				$response->success = false;
				$response->statusCode = 404;
				return $response;
			}

            // Antes de que se borre el usuario guardamos
            if($user->hasRole('Profesor')) {
                $response->message = __('messages.successful_deletion', ['name' => 'el Profesor']);
            }
            if($user->hasRole('Alumno')) {
                $response->message = __('messages.successful_deletion', ['name' => 'el Alumno']);
            }

			if ($this->userRepository->eliminar($user)) {
				$response->success = true;
				return $response;
			}

		} catch (\Throwable $th) {
			$response->message = __('messages.failed_deletion', ['name' => 'el Usuario']);
			$response->success = false;
			$response->statusCode = 500;
			Log::error($th);
		}
		return $response;
	}

	public function mapUserToUserDTO(User $user): UserDTO
	{
		$userDTO = new UserDTO();
		$userDTO->id = $user->id;
		$userDTO->nombre = $user->getFullName();
		$userDTO->curp = $user->curp;
		foreach ($user->roles as $role) {
			$userDTO->roles[] = $role->name;
		}
		return $userDTO;
	}
	/**
	 * @return User
	 */
	public function getAuthenticatedUserByBearerToken(): ?User {
		$user = null;
		try {
			$token = $this->request->bearerToken();
			$user = PersonalAccessToken::findToken($token)->tokenable;
		} catch (\Throwable $th) {
			Log::error($th);
		}
		return $user;
	}
	/**
	 * @return bool
	 */
	public function verificarAsesoriaYaConfirmada(int $asesoriaId, int $alumnoId): bool {
		$result = false;
		try {
			$result = $this->userRepository->verificarAsesoriaYaConfirmada($asesoriaId, $alumnoId);
		} catch (\Throwable $th) {
			Log::error($th);
		}
		return $result;
	}
}
?>
