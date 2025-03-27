<?php

use App\Http\Controllers\API\Operaciones\Foros\APIForoController;
use App\Http\Controllers\API\Operaciones\Foros\APIRespuestaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\Auth\APICuentaController;
use App\Http\Controllers\API\Operaciones\APIAsesoriaController;
use App\Http\Controllers\API\Operaciones\Desafios\APIDesafioController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Cuentas
Route::controller(APICuentaController::class)->group(function () {
    Route::post('/cuentas/login', 'login')->name('api.cuentas.login');

});

Route::get('/prueba', function(Request $request) {
    return response()->json($request);
});

Route::middleware(['auth:sanctum'])->group(function () {

    // RUTAS AUTORIZADAS PARA ALUMNOS Y PROFESORES
    Route::middleware('ability:profesor,alumno')->group(function () {
        // API PROFESORES
        // Route::controller(APIProfesorController::class)->group(function() {
        //     Route::get('/profesores', 'get')->name('api.profesores.getAll');
        //     Route::get('/profesores/{id}', 'getById')->name('api.profesores.getById');
        // });

        Route::controller(APIAsesoriaController::class)->group(function() {
            Route::get('/asesorias/{id}');
        });

        // API DE FOROS
        Route::controller(APIForoController::class)->group(function () {
            Route::get('/foros', 'index');
            Route::get('/users/{userId}/foros', 'indexUser');
            Route::get('/users/{userId}/foros/{foroId}', 'show');
            Route::post('/users/{userId}/foros', 'store');
            Route::post('/foros/{foroId}/like', 'darLike');
            Route::post('/foros/{foroId}', 'reportar');
            Route::put('/users/{userId}/foros/{foroId}', 'update');
            Route::delete('/users/{userId}/foros/{foroId}', 'delete');
        });

        // API DE RESPUESTAS
        Route::controller(APIRespuestaController::class)->group(function () {
            Route::get('/foros/{foroId}/respuestas', 'index')->name('respuestas.index');
            Route::post('/foros/{foroId}/respuestas', 'store')->name('respuestas.store');
            Route::put('/foros/{foroId}/respuestas/{respuestaId}', 'update')->name('respuestas.update');
            Route::delete('/foros/{foroId}/respuestas/{respuestaId}', 'delete')->name('respuestas.delete');
        });

        // API DESAFIOS
        Route::controller(APIDesafioController::class)->group(function () {
            Route::get('/desafios', 'index');
        });
    });


    // RUTAS AUTORIZADAS PARA PROFESORES
    Route::middleware('abilities:profesor')->group(function() {
        // API DE ASESORIAS
        Route::controller(APIAsesoriaController::class)->group(function() {
            Route::get('/profesores/{profesorId}/asesorias/finalizadas', 'getAllFinalizadasProfesor');
            Route::get('/profesores/{profesorId}/asesorias-confirmadas', 'getAllConfirmadasProfesor');
            Route::get('/profesores/{profesorId}/asesorias-confirmadas/{asesoriaId}/lista-alumnos', 'getListaAlumnosAsesoria');
            Route::put('/profesores/{profesorId}/asesorias-confirmadas/{asesoriaId}/cancelar', 'putCancelarAsesoriaProfesor');
        });

        // API DE DESAFIOS
        Route::controller(APIDesafioController::class)->group(function () {
            Route::get('/profesores/{profesorId}/desafios', 'indexProfesor');
            Route::get('/profesores/{profesorId}/desafios/{desafioId}', 'show');
            Route::post('/profesores/{profesorId}/desafios', 'store');
            Route::put('/profesores/{profesorId}/desafios/{desafioId}', 'update');
            Route::delete('/profesores/{profesorId}/desafios/{desafioId}', 'delete');

        });
    });

    // RUTAS AUTORIZADAS PARA ALUMNOS
    Route::middleware('abilities:alumno')->group(function() {
        // API ASESORIAS
        Route::controller(APIAsesoriaController::class)->group(function() {
            Route::get('/alumnos/{idAlumno}/asesorias-confirmadas', 'getConfirmadasAlumno')->name('api.asesorias.confirmadasAlumno');
            Route::get('/asesorias/horarios-disponibles', 'getAllDisponibles');
            Route::get('/asesorias/horarios-disponibles/cantidad-por-turnos', 'getCountPorTurnos');
            Route::post('/alumnos/{alumnoId}/horarios-disponibles/{horarioId}/agendar', 'postAgendarAlumno');
            Route::put('/alumnos/{alumnoId}/asesorias-confirmadas/{asesoriaId}/cancelar', 'putCancelarAsesoriaAlumno');
        });
    });
});
