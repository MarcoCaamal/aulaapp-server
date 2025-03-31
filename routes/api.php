<?php

use App\Http\Controllers\API\Operaciones\Foros\APIForoController;
use App\Http\Controllers\API\Operaciones\Foros\APIRespuestaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\Auth\APICuentaController;
use App\Http\Controllers\API\Operaciones\APIAsesoriaController;
use App\Http\Controllers\API\Operaciones\APIAsistenciaController;
use App\Http\Controllers\API\Operaciones\APIHorarioController;
use App\Http\Controllers\API\Operaciones\Desafios\APIDesafioController;


// Cuentas
Route::controller(APICuentaController::class)->group(function () {
    Route::post('/cuentas/login', 'login')->name('api.cuentas.login');
    Route::get('/cuentas/usuario', 'usuario')->middleware('auth:sanctum');
});

Route::middleware(['auth:sanctum'])->group(function () {

    Route::controller(APIAsesoriaController::class)->group(function() {
        Route::get('/asesorias', 'index');
        Route::get('/asesorias/{id}', 'show');
        
        Route::post('/asesorias/{id}/qr', 'qr');
        Route::put('/asesorias/{id}/qr', 'qr');
    });

    Route::controller(APIHorarioController::class)->group(function() {
        Route::get('/horarios', 'index');
        Route::get('/horarios/{id}', 'show');
        Route::post('/horarios/{id}/asesoria', 'store');
    });
});
