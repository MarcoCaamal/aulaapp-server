<?php

namespace Database\Seeders;

use App\Enums\EstatusAsesoriaEnum;
use App\Enums\EstatusAsistenciaEnum;
use App\Models\Asesoria;
use App\Models\Asistencia;
use App\Models\Ciclo;
use App\Models\Grupo;
use App\Models\Horario;
use App\Models\Materia;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

use function PHPUnit\Framework\isTrue;

/**
 * Summary of DataFakeSeeder
 */
class DataFakeSeeder extends Seeder
{
    /**
     * Array de grupos para la siembra
     *
     * @var array
     */
    private array $grupos = [
        ['nombre' => '1AMPR', 'semestre_id' => 1],
        ['nombre' => '2AMPR', 'semestre_id' => 2],
        ['nombre' => '3AMPR', 'semestre_id' => 3],
        ['nombre' => '4AMPR', 'semestre_id' => 4],
        ['nombre' => '5AMPR', 'semestre_id' => 5],
        ['nombre' => '6AMPR', 'semestre_id' => 6],
        ['nombre' => '1AMRH', 'semestre_id' => 1],
        ['nombre' => '2AMRH', 'semestre_id' => 2],
        ['nombre' => '3AMRH', 'semestre_id' => 3],
        ['nombre' => '4AMRH', 'semestre_id' => 4],
        ['nombre' => '5AMRH', 'semestre_id' => 5],
        ['nombre' => '6AMRH', 'semestre_id' => 6]
    ];
    /**
     * Array de materias para la siembra
     *
     * @var array
     */
    private array $materias = [
        ['nombre' => 'ALGEBRA', 'semestre_id' => 1],
        ['nombre' => 'INGLÉS I', 'semestre_id' => 1],
        ['nombre' => 'QUÍMICA I', 'semestre_id' => 1],
        ['nombre' => 'LEOYE', 'semestre_id' => 1],
        ['nombre' => 'TECNOLOGÍAS DE LA INFORMACIÓN Y LA COMUNICACIÓN', 'semestre_id' => 1],
        ['nombre' => 'LÓGICA', 'semestre_id' => 1],
        ['nombre' => 'GEOMETRÍA Y TRIGONOMETRÍA', 'semestre_id' => 2],
        ['nombre' => 'INGLÉS II', 'semestre_id' => 2],
        ['nombre' => 'QUÍMICA II', 'semestre_id' => 2],
        ['nombre' => 'LEOYE II', 'semestre_id' => 2],
        ['nombre' => 'GEOMETRÍA ANALÍTICA', 'semestre_id' => 3],
        ['nombre' => 'INGLÉS III', 'semestre_id' => 3],
        ['nombre' => 'BIOLOGÍA', 'semestre_id' => 3],
        ['nombre' => 'ÉTICA', 'semestre_id' => 3],
        ['nombre' => 'CÁLCULO DIFERENCIAL', 'semestre_id' => 4],
        ['nombre' => 'INGLÉS IV', 'semestre_id' => 4],
        ['nombre' => 'FÍSICA I', 'semestre_id' => 4],
        ['nombre' => 'ECOLOGÍA', 'semestre_id' => 4],
        ['nombre' => 'CÁLCULO INTEGRAL', 'semestre_id' => 5],
        ['nombre' => 'FÍSICA II', 'semestre_id' => 5],
        ['nombre' => 'CIENCIA, TECNOLOGÍA, SOCIEDAD Y VALORES', 'semestre_id' => 5],
        ['nombre' => 'INGLÉS V', 'semestre_id' => 5],
        ['nombre' => 'TEMAS DE MATEMATICAS', 'semestre_id' => 6],
        ['nombre' => 'TEMAS DE FILOSOFIA', 'semestre_id' => 6],
        ['nombre' => 'PROBABILIDAD Y ESTADISTICA', 'semestre_id' => 6],
        ['nombre' => 'TEMA DE FÍSICA', 'semestre_id' => 6]
    ];
    /**
     * Array de profesores para siembra
     *
     * @var array
     */
    private array $profesores = [
        ['nombre' => 'JORGE JESÚS', 'apellido_paterno' => 'CASTRO', 'apellido_materno' => 'MORALES', 'curp' => 'CAMJ770826HCLSRR68', 'email' => 'jorge.castro@asesorat.com',],
        ['nombre' => 'MARTÍN', 'apellido_paterno' => 'GUZMAN', 'apellido_materno' => 'ORTIZ', 'curp' => 'GUOM770826HJCZRR63', 'email' => 'martin.guzman@asesorat.com'],
        ['nombre' => 'MARÍA DE JESÚS', 'apellido_paterno' => 'SOTO', 'apellido_materno' => 'TORRES', 'curp' => 'SOTM770826MJCTRR96', 'email' => 'maria.soto.tores@asesorat.com'],
        ['nombre' => 'MARÍA DE GUADALUPE', 'apellido_paterno' => 'ORTEGA', 'apellido_materno' => 'MARTINEZ', 'curp' => 'OEMM770826MTCRRR25', 'email' => 'maria.ortega@asesorat.com'],
        ['nombre' => 'MARCO ANTONIO', 'apellido_paterno' => 'GUZMAN', 'apellido_materno' => 'GUZMAN', 'curp' => 'GUGM770826HOCZZR26', 'email' => 'marco.guzman.guzman@asesorat.com'],
        ['nombre' => 'RUBEN', 'apellido_paterno' => 'HERNANDEZ', 'apellido_materno' => 'GUTIERREZ', 'curp' => 'HEGR770826HYNRTB45', 'email' => 'ruben.hernandez@asesorat.com'],
        ['nombre' => 'ROSA MARIA', 'apellido_paterno' => 'DELGADO', 'apellido_materno' => 'PEREZ', 'curp' => 'DEPR770826MPLLRS42', 'email' => 'rosa.delgado.perez@asesorat.com'],
        ['nombre' => 'CARMEN', 'apellido_paterno' => 'FLORES', 'apellido_materno' => 'HERNANDEZ', 'curp' => 'FOHC770826MPLLRR89', 'email' => 'carmen.flores@asesorat.com'],
        ['nombre' => 'FERNANDO', 'apellido_paterno' => 'RAMIREZ', 'apellido_materno' => 'MEDINA', 'curp' => 'RAMF770826HCCMDR38', 'email' => 'fernando.ramirez@asesorat.com'],
        ['nombre' => 'MIGUEL ANGEL', 'apellido_paterno' => 'SANTIAGO', 'apellido_materno' => 'SOTO', 'curp' => 'SASM770826HCLNTG17', 'email' => 'miguel.angel.santiago@asesorat.com'],
    ];
    /**
     * Array de alumnos para la siembra
     *
     * @var array
     */
    private array $alumnos = [
        ['nombre' => 'RAMÓN', 'apellido_paterno' => 'SANTIAGO', 'apellido_materno' => 'RODRIGUEZ', 'curp' => 'SARR770826HMSNDM26', 'email' => 'ramon.rodriguez@asesorat.com'],
        ['nombre' => 'JAIME', 'apellido_paterno' => 'AGUILAR', 'apellido_materno' => 'HERNANDEZ', 'curp' => 'AUHJ770826HSLGRM34', 'email' => 'jaime.aguilar@asesorat.com'],
        ['nombre' => 'MARÍA LUISA', 'apellido_paterno' => 'FLORES', 'apellido_materno' => 'PEREZ', 'curp' => 'FOPM770826MCLLRR97', 'email' => 'maria.perez@asesorat.com'],
        ['nombre' => 'MARTHA', 'apellido_paterno' => 'MORALES', 'apellido_materno' => 'RAMOS', 'curp' => 'MORM770826MCCRMR58', 'email' => 'matha.morales@asesorat.com'],
        ['nombre' => 'FERNANDO', 'apellido_paterno' => 'SANTIAGO', 'apellido_materno' => 'TORRES', 'curp' => 'SATF770826HCSNRR24', 'email' => 'fernando.santiago@asesorat.com'],
        ['nombre' => 'DANIEL', 'apellido_paterno' => 'AGUILAR', 'apellido_materno' => 'ORTIZ', 'curp' => 'AUOD770826HSLGRN74', 'email' => 'daniel.aguilar@asesorat.com'],
    ];
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Se le crean las contraseñas a todos los usuarios
        $password = Hash::make('password');
        foreach($this->profesores as &$profesor) {
            $profesor['password'] = $password;
        }
        foreach($this->alumnos as &$alumno) {
            $alumno['password'] = $password;
        }

        $this->crearCiclos();
        $this->crearGrupos();
        $this->crearMaterias();
        $this->crearProfesores();
        $this->crearAlumnos();
        $this->crearAsesorias();
        $this->crearAsistenciasAsesorias();
    }

    private function crearCiclos()
    {
        /** CICLOS **/
        $ciclo = Ciclo::create([
            'nombre' => 'CICLO ' . Carbon::now()->year,
            'fecha_inicio' => Carbon::now()->toDateString(),
            'fecha_fin' => Carbon::now()->addYear()->toDateString(),
            'is_activo' => true
        ]);
    }

    private function crearGrupos()
    {
        Grupo::upsert($this->grupos, ['nombre']);
    }
    private function crearMaterias()
    {
        Materia::upsert($this->materias, ['nombre', 'semestre_id']);
    }
    private function crearAlumnos()
    {
        // Se crean los usuarios en la DB
        User::upsert($this->alumnos, ['curp', 'email']);
        // Se obtienen los emails de los alumnos en un array
        $emails = array_column($this->alumnos, 'email');
        // Se obtiene los alumnos creados de la DB
        $alumnosDB = User::whereIn('email', $emails)->get();

        foreach($alumnosDB as $alumnoDB) { // Recorremos la colección de alumnos para asignarles el rol de alumno
            $alumnoDB->assignRole('Alumno');
            // Se busca y se le asigna un grupo random al alumno
            $grupoRandomId = Grupo::inRandomOrder()->value('id');
            $alumnoDB->grupos()->attach($grupoRandomId, ['ciclo_id' => 1, 'is_activo' => true]);
        }
    }

    private function crearProfesores()
    {
        // Array que tiene los ids de los profesores con los id de sus materias;
        $profesoresConMateriasIds = [];
        // Se crean los profesores
        User::upsert($this->profesores, ['curp', 'email']);
        // Se obtienen los emails de los profesores en un array
        $emails = array_column($this->profesores, 'email');
        // Se obtiene los profesores creados de la DB
        $profesoresDB = User::whereIn('email', $emails)->get();
        foreach($profesoresDB as $profesorDB) {
            $profesorDB->assignRole('Profesor');

            // Se le asignan materias random al profesor
            $materiasIdsRandomDB = Materia::inRandomOrder()->take(3)->pluck('id')->toArray();
            $profesorDB->materias()->attach($materiasIdsRandomDB);

            // Añadimos al array el profesor con su materia
            $profesoresConMateriasIds[] = ['profesor_id' => $profesorDB->id, 'materiasIds' => $materiasIdsRandomDB];
        }

        $this->crearHorariosProfesores($profesoresConMateriasIds);
    }

    private function crearHorariosProfesores(array $profesoresConMateriasIds)
    {
        $horarios = [];
        $lugares = ['Biblioteca', 'Taller de Mate', 'Aula 3', 'Aula 5'];
        $fecha_inicial = now()->setTime(8,0,0);
        if($fecha_inicial->dayOfWeek !== Carbon::MONDAY) {
            $fecha_inicial->next(Carbon::MONDAY);
        }

        foreach($profesoresConMateriasIds as $profesorConMateriasIds) {
            foreach($profesorConMateriasIds['materiasIds'] as $materiaId) {
                if($fecha_inicial->hour == 20) {
                    $fecha_inicial->addHours(12);
                }

                if($fecha_inicial->dayOfWeek === Carbon::SUNDAY || $fecha_inicial->dayOfWeek === Carbon::SATURDAY) {
                    $fecha_inicial->next(Carbon::MONDAY);
                }

                $horario = [];
                $horario['lugar'] = $lugares[array_rand($lugares)];
                $horario['dia_semana'] = $fecha_inicial->dayOfWeek - 1;
                $horario['hora_inicio'] = $fecha_inicial->toTimeString();
                $fecha_inicial->addHour();
                $horario['hora_fin'] = $fecha_inicial->toTimeString();
                $horario['materia_id'] = $materiaId;
                $horario['profesor_id'] = $profesorConMateriasIds['profesor_id'];

                $horarios[] = $horario;
            }
        }

        Horario::upsert($horarios, ['id']);
    }

    private function crearAsesorias()
    {
        $asesorias = [];
        $fechaActual = now();
        $profesoresDB = User::role('profesor')
            ->with(['horarios', 'materias:id'])
            ->get(['id']);
        $fechaActualMenosUnMes = now()->subMonth();

        $primerCiclo = isTrue();

        foreach($profesoresDB as $profesorDB) {
            if($profesorDB instanceof User) {
                foreach($profesorDB->horarios as $horario) {
                    if($primerCiclo) {
                        $fechaActual->next($horario->dia_semana->value + 1);
                        $primerCiclo = false;
                    }
                    if($fechaActual->dayOfWeek !== ($horario->dia_semana->value + 1)) {
                        $fechaActual->next($horario->dia_semana->value + 1);
                    }
                    if($fechaActualMenosUnMes->dayOfWeek !== ($horario->dia_semana->value + 1)) {
                        $fechaActualMenosUnMes->next($horario->dia_semana->value + 1);
                    }
                    $asesoriaPendiente = [
                        'estado' => EstatusAsesoriaEnum::PENDIENTE,
                        'fecha' => $fechaActual->toDateString(),
                        'materia_asesor_id' => $profesorDB->materias->where('id', $horario->materia_id)->first()->pivot->id,
                        'horario_id' => $horario->id,
                    ];

                    $asesoriaFinalizada = [
                        'estado' => EstatusAsesoriaEnum::FINALIZADA,
                        'fecha' => $fechaActualMenosUnMes->toDateString(),
                        'materia_asesor_id' => $profesorDB->materias->random()->pivot->id,
                        'horario_id' => $horario->id,
                    ];

                    $asesorias[] = $asesoriaPendiente;
                    $asesorias[] = $asesoriaFinalizada;
                }
            }
        }

        Asesoria::upsert($asesorias, ['id']);
    }

    private function crearAsistenciasAsesorias()
    {
        $asistencias = [];
        $asesoriasIds = Asesoria::where('estado', EstatusAsesoriaEnum::PENDIENTE)->join('materias_asesores', 'asesorias.materia_asesor_id', '=', 'materias_asesores.id')->join('materias', 'materias_asesores.materia_id', '=', 'materias.id')->select('asesorias.id', 'materias.semestre_id')->get();
        $alumnosIds = User::role('Alumno')->join('grupos_alumnos', 'users.id', '=', 'grupos_alumnos.alumno_id')->join('grupos', 'grupos_alumnos.grupo_id', '=', 'grupos.id')->where('grupos_alumnos.is_activo', true)->select('users.id', 'grupos.semestre_id')->get();
        foreach($asesoriasIds as $asesoriaId) {
            $alumnosIdsFiltradosPorSemestre = $alumnosIds->where('semestre_id', '>=',$asesoriaId->semestre_id)->all();
            if(empty($alumnosIdsFiltradosPorSemestre)) {
                continue;
            }
            foreach($alumnosIdsFiltradosPorSemestre as $alumnoId) {
                $asistencia = [
                    'asesoria_id' => $asesoriaId->id,
                    'alumno_id' => $alumnoId->id,
                    'estatus' => EstatusAsistenciaEnum::PENDIENTE,
                ];
                $asistencias[] = $asistencia;
            }
        }
        Asistencia::upsert($asistencias, ['id']);
    }
}
