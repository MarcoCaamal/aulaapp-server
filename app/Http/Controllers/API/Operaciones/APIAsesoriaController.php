<?php

namespace App\Http\Controllers\API\Operaciones;

use App\Enums\EstatusAsesoriaEnum;
use App\Enums\EstatusAsistenciaEnum;
use App\Http\Controllers\Controller;
use App\Models\Asesoria;
use App\Models\Asistencia;
use App\Services\Interfaces\UserServiceInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class APIAsesoriaController extends Controller
{
    public function __construct(
        private UserServiceInterface $_userService
    )
    {
        $this->_userService = $_userService;
    }
    public function index()
    {
        $user = $this->_userService->getAuthenticatedUserByBearerToken();
        if(!$user) {
            abort(401);
        }
        $userRoles = $user->roles()->get();

        if($userRoles->some('name', '=', 'Alumno')) {
            return Asesoria::with([
                'horario' => ['profesor', 'materia'],
                'asistencias' => function ($query) use ($user) {
                    $query->where('alumno_id', $user->id);
                }])
            ->where('fecha', '>=', Carbon::now()->toDateString())
            ->whereHas('asistencias', function(Builder $query) use($user) {
                $query->where('alumno_id', '=', $user->id);
            })
            ->paginate();
        }

        if($userRoles->some('name', '=', 'Profesor')) {
            return Asesoria::with(['horario' => ['profesor', 'materia']])
            ->where('estado', '=', EstatusAsesoriaEnum::PENDIENTE)
            ->where('fecha', '>=', Carbon::now()->toDateString())
            ->whereHas('horario', function(Builder $query) use($user) {
                $query->where('profesor_id', '=', $user->id);
            })
            ->paginate();
        }

        return Asesoria::with(['horario' => ['profesor', 'materia']])->paginate();
    }
    public function show(int $id)
    {
        $user = $this->_userService->getAuthenticatedUserByBearerToken();
        if(!$user) {
            abort(401);
        }
        $userRoles = $user->roles()->get();

        if($userRoles->some('name', '=', 'Alumno')) {
            return Asesoria::with(['horario' => ['profesor', 'materia']])
            ->where('fecha', '>=', Carbon::now()->toDateString())
            ->whereHas('asistencias', function(Builder $query) use($user) {
                $query->where('alumno_id', '=', $user->id);
            })
            ->where('id', '=', $id)
            ->limit(1)
            ->paginate();
        }

        if($userRoles->some('name', '=', 'Profesor')) {
            return Asesoria::with(['horario' => ['profesor', 'materia']])
            ->where('fecha', '>=', Carbon::now()->toDateString())
            ->whereHas('horario', function(Builder $query) use($user) {
                $query->where('profesor_id', '=', $user->id);
            })
            ->where('id', '=', $id)
            ->paginate();
        }

        return Asesoria::with(['horario' => ['profesor', 'materia']])
            ->where('id', '=', $id)
            ->paginate();;
    }
    public function store(Request $request) {
        
    }
    public function qr(int $id, Request $request)
    {
        $user = $this->_userService->getAuthenticatedUserByBearerToken();
        if (!$user) {
            abort(401);
        }

        $userRoles = $user->roles()->get();

        // Verificar si el usuario tiene el rol de Profesor
        if (!$userRoles->some('name', '=', 'Profesor')) {
            abort(401);
        }

        // Validar si la asesoría existe
        $asesoria = Asesoria::findOrFail($id);

        // Obtener el tiempo de expiración en minutos desde la solicitud o establecer un valor por defecto
        $expiracion = $request->input('expiracion', 60); // El valor puede venir en la solicitud, sino 60 por defecto
        $fechaGeneracion = now()->toDateTimeString();

        // Crear el texto a encriptar
        $texto = "{$id}|{$fechaGeneracion}|{$expiracion}";

        // Encriptar la información
        $textoEncriptado = Crypt::encryptString($texto);

        // Generar el código QR con el texto encriptado
        $qrCode = QrCode::format('png')->size(300)->generate($textoEncriptado);

        // Guardar el QR en un archivo en el servidor
        $qrPath = "qrs/asesoria_$asesoria->id.png";
        Storage::put($qrPath, $qrCode);

        // Obtener la URL completa del servidor para el archivo generado
        $qrUrl = Storage::url("qrs/asesoria_$asesoria->id.png");

        // Devolver la URL completa del QR generado
        return response()->json(['qr_url' => $qrUrl]);
    }

    public function qrUpdate(int $id, Request $request)
    {
        $request->validate([
            'asistencia_id' => ['required', 'numeric'],
            'qrCode' => ['required']
        ]);
        $response = [
            'success' => false,
            'statusCode' => 500,
            'message' => 'algo salio mal',
        ];
        $user = $this->_userService->getAuthenticatedUserByBearerToken();
        if (!$user) {
            abort(401);
        }

        // Verificar que el rol del usuario es "Alumno"
        if (!$user->roles()->where('name', 'Alumno')->exists()) {
            $response['message'] = 'Unauthorized';
            $response['statusCode'] = 401;
            return response()->json($response, 401);
        }

        // Verificar si la asesoría existe
        $asesoria = Asesoria::findOrFail($id);

        // Verificar si el alumno ya ha confirmado su asistencia
        $asistenciaExistente = Asistencia::findOrFail($request->input('asistencia_id'))
            ->firstOrFail();
        if ($asistenciaExistente->estatus == EstatusAsistenciaEnum::ASISTENCIA) {
            $response['message'] = 'Asistencia ya confirmada';
            $response['statusCode'] = 400;
            return response()->json($response, 400);
        }

        // Desencriptar el código QR
        try {
            $textoEncriptado = $request->input('qrCode');  // El QR encriptado enviado en el body de la solicitud
            $textoDescifrado = Crypt::decryptString($textoEncriptado);
            list($asesoriaId, $fechaGeneracion, $expiracion) = explode('|', $textoDescifrado);

            // Verificar que el id de la asesoría del QR coincida con el id recibido
            if ($asesoriaId != $id) {
                $response['message'] = 'El QR no corresponde con la asesoría';
                return response()->json($response, 400);
            }

            // Verificar que la fecha no haya expirado
            $fechaExpiracion = Carbon::parse($fechaGeneracion)->addMinutes($expiracion);
            if ($fechaExpiracion->isPast()) {
                $response['message'] = 'El QR ha expirado';
                $response['statusCode'] = 400;
                return response()->json($response, 400);
            }

            $asistenciaExistente->estatus = EstatusAsistenciaEnum::ASISTENCIA;
            $asistenciaExistente->save();

            return response()->json(['message' => 'Asistencia confirmada correctamente'], 200);
        } catch (\Exception $e) {
            return response()->json($response, 400);
        }
    }
}