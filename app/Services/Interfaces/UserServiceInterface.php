<?php
namespace App\Services\Interfaces;

use App\Models\User;
use App\Models\DTOs\UserDTO;
use App\Helpers\ResponseHelper;
use App\Models\DTOs\ParamsProfesorApiDTO;

interface UserServiceInterface
{
	// ALUMNOS
	public function obtenerColeccionAlumnos(): \Illuminate\Database\Eloquent\Collection;
	public function obtenerAlumnoPorId(int $id): \Illuminate\Database\Eloquent\Model|User|null;
	public function crearAlumno(array $attributes): ResponseHelper;
	public function actualizarAlumno(array $attributes, int $id): ResponseHelper;
	// PROFESORES
	public function obtenerColeccionProfesores(): \Illuminate\Database\Eloquent\Collection;
	public function obtenerProfesorPorId(int $id): \Illuminate\Database\Eloquent\Model|User;
	public function obtenerProfesorPorIdConRelaciones(int $id, array $relations): \Illuminate\Database\Eloquent\Model|User;
	public function obtenerColeccionPaginadaConFiltrosRelaciones(ParamsProfesorApiDTO $filtros): \Illuminate\Pagination\LengthAwarePaginator|null;
	public function obtenerProfesorPorProfesorIdSemestreId(int $idProfesor, int $idSemestre): User|null;
	public function actualizarProfesor(array $attributes, int $id): ResponseHelper;
	public function crearProfesor(array $attributes): ResponseHelper;
	// USERS
	public function obtenerUsuarioPorId(int $id): \Illuminate\Database\Eloquent\Model|User;
	public function obtenerUsuarioPorEmail(string $email): \Illuminate\Database\Eloquent\Model|User;
	public function getAuthenticatedUserByBearerToken(): ?User;
	public function eliminar(int $id): ResponseHelper;
	public function mapUserToUserDTO(User $user): UserDTO;
	public function verificarAsesoriaYaConfirmada(int $asesoriaId, int $alumnoId): bool;
}
