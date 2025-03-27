<?php
namespace App\Repositories;

use App\Enums\EstatusAsesoriaEnum;
use App\Enums\EstatusAsistenciaEnum;
use App\Models\User;
use App\Enums\TurnoEnum;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\DTOs\ParamsProfesorApiDTO;

class UserRepository
{
    private $model;
    private $numeroDeRegistros = 50;

    public function __construct()
    {
        $this->model = new User();
    }
    /*
    |-----------------------|
    | SECCIÓN DE PROFESORES |
    |-----------------------|
    */
    /**
     * Repositorio que obtiene una colección de usuarios de tipo Profesor
     * @return \Illuminate\Database\Eloquent\Collection<\App\Models\User>
     */
    public function obtenerColeccionProfesores(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->model::role('Profesor')->get();
    }

    /**
     * Repositorio que obtiene un usuario de tipo profesor con sus materias por su id
     * @param int $idProfesor
     * @return \Illuminate\Database\Eloquent\Model<User>
     */
    public function obtenerProfesorPorProfesorIdConMaterias(int $idProfesor): \Illuminate\Database\Eloquent\Model
    {
        return $this->model::role('Profesor')->with('materias')->find($idProfesor);
    }

    /**
     * Repositorio que obtiene un usuario de tipo profesor por su id
     * @param int $idProfesor
     * @return User
     */
    public function obtenerProfesorPorId(int $id): User|null
    {
        return $this->model->with(['materias', 'horarios'])
            ->role('Profesor')->find($id);
    }

    /**
     * Repositorio que obtiene un profesor por su Id y por un SemestreId
     *
     * @param integer $id
     * @param integer $semestreId
     * @return User
     */
    public function obtenerProfesorPorProfesorIdSemestreId(int $id, int $semestreId): User|null
    {
        return $this->model
            ->with([
                'materias' => function ($query) use ($semestreId) {
                    $query->where('semestre_id', $semestreId);
                },
                'horarios' => function ($query) use ($semestreId) {
                    $query->whereHas('materia', function ($query) use ($semestreId) {
                        $query->where('semestre_id', $semestreId);
                    });
                }
            ])
            ->whereHas('materias', function($query) use($semestreId) {
                $query->where('semestre_id', $semestreId);
            })
            ->where('id', $id)->first();
    }

    /**
     * Repositorio que obtiene un usuario de tipo profesor por su id
     * @param int $idProfesor
     * @return User
     */
    public function obtenerProfesorPorProfesorIdConRelaciones(int $id, array $relations): User|\Illuminate\Database\Eloquent\Model
    {
        return $this->model->role('Profesor')->with($relations)->find($id);
    }

    /**
     * Repositorio que obtiene una colección de profesores filtrados y paginados para la API
     *
     * @param ParamsProfesorApiDTO $filtros
     * @param array $relations
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function obtenerColeccionPaginadaProfesoresConFiltros(ParamsProfesorApiDTO $filtros): \Illuminate\Pagination\LengthAwarePaginator
    {
        $sql = $this->model->query();
        $sql->role('profesor');
        $relations = [];

        if (isset($filtros->turno)) {
            $horaDeCorte = '14:00:00';
            // Se filtran por el turno Matutino
            if ($filtros->turno == TurnoEnum::Matutino) {
                $relations['materias'] = function ($query) use ($horaDeCorte, $filtros) {
                    $query->whereHas('horarios', function ($query) use ($horaDeCorte) {
                        $query->where('hora_fin', '<=', $horaDeCorte);
                    })->where('semestre_id', $filtros->idSemestreAlumno);
                };
                $relations['horarios'] = function ($query) use ($horaDeCorte, $filtros) {
                    $query->where('hora_fin', '<=', $horaDeCorte)->whereHas('materia', function ($query) use ($filtros) {
                        $query->where('semestre_id', $filtros->idSemestreAlumno);
                    });
                };
            }
            // Se filtra por el turno vespertino
            if ($filtros->turno == TurnoEnum::Vespertino) {
                $relations['materias'] = function ($query) use ($horaDeCorte, $filtros) {
                    $query->whereHas('horarios', function ($query) use ($horaDeCorte) {
                        $query->where('hora_fin', '>=', $horaDeCorte);
                    })->where('semestre_id', $filtros->idSemestreAlumno);
                };
                $relations['horarios'] = function ($query) use ($horaDeCorte, $filtros) {
                    $query->where('hora_fin', '>=', $horaDeCorte)->whereHas('materia', function ($query) use ($filtros) {
                        $query->where('semestre_id', $filtros->idSemestreAlumno);
                    });
                };
            }
        } else { // Si no se uso el filtro por turnos simplemente se filtra por el semestre del alumno
            $relations['materias'] = function ($query) use ($filtros) {
                $query->where('semestre_id', $filtros->idSemestreAlumno);
            };
            $relations['horarios'] = function ($query) use ($filtros) {
                $query->whereHas('materia', function ($query) use ($filtros) {
                    $query->where('semestre_id', $filtros->idSemestreAlumno);
                });
            };
        }
        $sql->with($relations);

        // Filtro por profesores que tiene materias que coincidan con el semestre del alumno
        $sql->whereHas('materias', function ($query) use ($filtros) {
            $query->where('semestre_id', $filtros->idSemestreAlumno);
        });

        // Filtro por nombre
        if (!empty($filtros->nombre) || !is_null($filtros->nombre)) {

            $nombre = str_replace(' ', ' ', $filtros->nombre);
            $sql->where(DB::raw('CONCAT(nombre, apellido_paterno, apellido_materno)'), 'like', "%{$nombre}%");
        }

        // Filtro por email
        if (!empty($filtros->email) || !is_null($filtros->email)) {
            $sql->where('email', 'like', "%$filtros->email%");
        }

        // Filtro por curp
        if (!empty($filtros->curp) || !is_null($filtros->curp)) {
            $sql->where('curp', 'like', "%$filtros->curp%");
        }

        // Este filtro es los profesores que tengan horarios Matutinos(menores a 2pm) y Vespertinos(mayores a 2pm)
        if (isset($filtros->turno)) {
            if ($filtros->turno == TurnoEnum::Matutino) {
                $sql->whereHas('horarios', function ($query) {
                    $query->where('hora_fin', '<=', '14:00:00');
                });
            }

            if ($filtros->turno == TurnoEnum::Vespertino) {
                $sql->whereHas('horarios', function ($query) {
                    $query->where('hora_inicio', '>=', '14:00:00');
                });
            }
        }

        return $sql->paginate($this->numeroDeRegistros);
    }

    /**
     * Repositorio que crea un usuaio de tipo Profesor
     * @param User $profesor
     * @param array $materiasIds
     * @return bool
     */
    public function crearProfesor(User $profesor, array $materiasIds): bool
    {
        $result = false;
        DB::transaction(function () use ($profesor, $materiasIds, &$result) {
            $profesor->save();
            $profesor->assignRole('Profesor');
            $profesor->materias()->attach($materiasIds);
            $result = true;
            return;
        });
        return $result;
    }

    /**
     * Repositorio que actualiza un profesor
     *
     * @param User $profesor
     * @param array $materiasIds
     * @return bool
     */
    public function actualizarProfesor(User $profesor, array $materiasIds): bool
    {
        $result = false;
        DB::transaction(function () use ($profesor, $materiasIds, &$result) {
            if (!$profesor->hasRole('Profesor')) {
                return;
            }
            $profesor->save();
            $profesor->materias()->sync($materiasIds);
            $result = true;
            return;
        });
        return $result;
    }
    /*
    |---------------------------------|
    | FIN DE LA SECCIÓN DE PROFESORES |
    |---------------------------------|
    */

    /*
    |--------------------|
    | SECCIÓN DE ALUMNOS |
    |--------------------|
    */

    /**
     * Repositorio que obtiene un colección de usuarios de tipo Alumno
     * @return \Illuminate\Database\Eloquent\Collection<\App\Models\User>
     */
    public function obtenerColeccionAlumnos(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->model->role('Alumno')->get();
    }
    /**
     * Repositorio que obtiene el total de alumnos paginados que van a asistir a una asesoria
     *
     * @param integer $asesoriaId
     * @param integer $paginaActual
     * @param integer $numeroRegistrosPorPagina
     *
     * @return array{total: int, data: \Illuminate\Support\Collection<User>}
     */
    public function obtenerColeccionAlumnosPorAsesoriaId(int $asesoriaId,
        int $paginaActual = 1,
        int $numeroRegistrosPorPagina = 15): array
    {
        $sql = $this->model->query();
        $resultado = [];
        $sql->role('Alumno')->whereHas('asistencias', function($query) use($asesoriaId) {
                $query->where('asesoria_id', $asesoriaId)
                    ->where('estatus', EstatusAsistenciaEnum::PENDIENTE);
            });

        $sql->select('users.*');

        $resultado['total'] = $sql->count();
        $resultado['data'] = $sql
            ->skip($paginaActual == 1 ? 0 : $paginaActual * $numeroRegistrosPorPagina)
            ->take($numeroRegistrosPorPagina)
            ->get();

        return $resultado;
    }

    /**
     * Repositorio que obtiene un usuario de tipo Alumno por su id
     * @param int $id
     * @return User|\Illuminate\Database\Eloquent\Model
     */
    public function obtenerAlumnoPorId(int $id): User|\Illuminate\Database\Eloquent\Model|null
    {
        return $this->model::role('Alumno')
            ->with('grupos')
            ->find($id);
    }
    /**
     * Repositorio que crea un usuario de tipo Alumno
     *
     * @param User $alumno
     * @param int $grupoId
     * @return bool
     */
    public function crearAlumno(User $alumno, int $grupoId, int $cicloId): bool
    {
        $result = false;
        DB::transaction(function () use ($alumno, $grupoId, $cicloId, &$result) {
            $alumno->save();
            $alumno->assignRole('Alumno');
            $alumno->grupos()->attach($grupoId, ['is_activo' => true, 'ciclo_id' => $cicloId]);
            $result = true;
        });
        return $result;
    }
    /**
     * Reposiotorio que actualiza un alumno
     *
     * @param User $alumno
     * @param integer $grupoId
     * @param integer $cicloId
     * @return boolean
     */
    public function actualizarAlumno(User $alumno, int $grupoId, int $cicloId): bool
    {
        $result = false;
        DB::transaction(function () use ($alumno, $grupoId, $cicloId, &$result) {
            if (!$alumno->hasRole('Alumno')) {
                return;
            }
            $alumno->save();
            $alumno->load('grupos');
            $grupoAnterior = $alumno->grupos->where('pivot.is_activo', true)->first();
            if ($grupoAnterior->id != $grupoId) {
                $alumno->grupos()->attach($grupoId, ['is_active' => true, 'ciclo_id' => $cicloId]);
                $alumno->grupos()->updateExistingPivot($grupoAnterior->id, [
                    'is_active' => false
                ]);
            }
            $result = true;
        });
        return $result;
    }

    /*
    |------------------------------|
    | FIN DE LA SECCIÓN DE ALUMNOS |
    |------------------------------|
    */

    /*
    |--------------------------------|
    | SECCIÓN DE USUARIOS EN GENERAL |
    |--------------------------------|
    */

    /**
     * Repositorio que obtiene un usuario por su Id
     * @param int $id
     * @return User
     */
    public function obtenerUsuarioPorId(int $id): User
    {
        return $this->model->find($id);
    }

    /**
     * Repositorio que obtiene un usuario por su email
     *
     * @param string $email
     * @return \Illuminate\Database\Eloquent\Model|User
     */
    public function obtenerUsuarioPorEmail(string $email): \Illuminate\Database\Eloquent\Model|User
    {
        return $this->model->with('roles')->where('email', $email)->first();
    }

    /**
     * Repositorio que obtiene un usuario por su Id
     * @param int $id
     * @return User
     */
    public function obtenerUsuarioPorNombreCompleto(string $fullname): User|null
    {
        return $this->model->whereRaw("concat(nombre, ' ', apellido_paterno, ' ', apellido_materno) = ?", [$fullname])->first();
    }

    /**
     * Repositorio que guarda o actualiza un usuario en la base de datos
     * @param \Illuminate\Database\Eloquent\Model|User $user
     * @return bool
     */
    public function guardar(\Illuminate\Database\Eloquent\Model|User $user): bool
    {
        return $user->save();
    }
    /**
     * Repositorio que elimina un usuario de la base de datos
     * @param User $user
     * @return bool
     */
    public function eliminar(User $user): bool
    {
        return $user->delete();
    }

    /**
     * Repositorio que verifica si existe un usuario con un nombre igual
     *
     * @param string $fullName
     * @return bool
     */
    public function existeUsuarioPorNombre(string $fullName): bool
    {
        return $this->model->whereRaw("concat(nombre, ' ', apellido_paterno, ' ', apellido_materno) = ?", [$fullName])->exists();
    }

    public function verificarAsesoriaYaConfirmada(int $asesoriaId, int $alumnoId): bool
    {
        return $this->model
            ->whereHas('asistencias', function ($query) use ($asesoriaId, $alumnoId) {
                $query->where('asesoria_id', $asesoriaId)
                    ->where('alumno_id', $alumnoId)
                    ->where('asistencia', false)
                    ->whereHas(
                        'asesorias',
                        function ($query) use ($asesoriaId) {
                                $query->where('id', $asesoriaId)
                                    ->where('estado', EstatusAsesoriaEnum::PENDIENTE);
                            }
                    );
            })
            ->exist();
    }
}
?>
