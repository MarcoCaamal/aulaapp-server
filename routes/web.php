<?php

use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Operaciones\CicloController;
use App\Http\Controllers\Operaciones\GrupoController;
use App\Http\Controllers\Operaciones\HorarioController;
use App\Http\Controllers\Operaciones\MateriaController;
use App\Http\Controllers\Operaciones\Personas\AlumnoController;
use App\Http\Controllers\Operaciones\SemestreController;
use App\Http\Controllers\Operaciones\Personas\ProfesorController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Rutas para cualquier usuario
Route::middleware('guest')->group(function() {
    Route::controller(LoginController::class)->group(function() {
        Route::get('/', 'index')->name('login.index');
        Route::post('/login', 'authenticate')->name('login.authenticate');
    });
});

// Rutas de usuarios autenticados
Route::middleware('auth')->group(function () {
    Route::get('/home', HomeController::class)->name('home');
    Route::view('/user-not-available' ,'auth.user-not-available')->name('user.not.available');

    // Ruta para el deslogueo
    Route::controller(LoginController::class)->group(function() {
        Route::post('/logout', 'logout')->name('login.logout');
    });

    // Rutas de Adminstrador
    Route::middleware('role:Administrador')->group(function() {
        Route::controller(DashboardController::class)->group(function() {
            Route::get('/dashboard/admin', 'admin')->name('dashboard.admin');
        });

        Route::controller(CicloController::class)->group(function() {
            Route::get('/ciclos', 'index')->name('ciclos.index');
            Route::get('/ciclos/create', 'create')->name('ciclos.create');
            Route::post('ciclos/store', 'store')->name('ciclos.store');
            Route::post('ciclos/{id}/activate', 'activate')->name('ciclos.activate');
            Route::post('ciclos/{id}/desactivate', 'desactivate')->name('ciclos.desactivate');
            Route::get('/ciclos/edit/{id}', 'edit')->name('ciclos.edit');
            Route::put('/ciclos/update/{id}', 'update')->name('ciclos.update');
            Route::delete('/ciclos/delete/{id}', 'delete')->name('ciclos.delete');
        });

        Route::controller(SemestreController::class)->group(function() {
            Route::get('/semestres', 'index')->name('semestres.index');
            Route::get('/semestres/create', 'create')->name('semestres.create');
            Route::post('/semestres/store', 'store')->name('semestres.store');
            Route::get('/semestres/edit/{id}', 'edit')->name('semestres.edit');
            Route::put('/semestres/update/{id}', 'update')->name('semestres.update');
            Route::delete('/semestres/delete/{id}', 'delete')->name('semestres.delete');
        });

        Route::controller(MateriaController::class)->group(function() {
            Route::get('/materias', 'index')->name('materias.index');
            Route::get('/materias/create', 'create')->name('materias.create');
            Route::post('/materias/store', 'store')->name('materias.store');
            Route::get('/materias/edit/{id}', 'edit')->name('materias.edit');
            Route::put('/materias/update/{id}', 'update')->name('materias.update');
            Route::delete('/materias/delete/{id}', 'delete')->name('materias.delete');
        });

        Route::controller(GrupoController::class)->group(function() {
            Route::get('/grupos', 'index')->name('grupos.index');
            Route::get('/grupos/create', 'create')->name('grupos.create');
            Route::post('/grupos/store', 'store')->name('grupos.store');
            Route::get('/grupos/edit/{id}', 'edit')->name('grupos.edit');
            Route::put('/grupos/update/{id}', 'update')->name('grupos.update');
            Route::delete('/grupos/delete/{id}', 'delete')->name('grupos.delete');
        });

        Route::controller(ProfesorController::class)->group(function () {
            Route::get('/profesores', 'index')->name('profesores.index');
            Route::get('/profesores/create', 'create')->name('profesores.create');
            Route::post('/profesores/store', 'store')->name('profesores.store');
            Route::get('/profesores/carga-masiva', 'getCargaMasiva')->name('profesores.carga-masiva');
            Route::post('/profesores/carga-masiva/cargar', 'postCargaMasiva')->name('profesores.carga-masiva-cargar');
            Route::get('/profesores/edit/{id}', 'edit')->name('profesores.edit');
            Route::put('/profesores/update/{id}', 'update')->name('profesores.update');
            Route::delete('/profesores/delete/{id}', 'delete')->name('profesores.delete');
        });

        Route::controller(AlumnoController::class)->group(function() {
            Route::get('/alumnos', 'index')->name('alumnos.index');
            Route::get('/alumnos/create', 'create')->name('alumnos.create');
            Route::post('/alumnos/store', 'store')->name('alumnos.store');
            Route::get('/alumnos/edit/{id}', 'edit')->name('alumnos.edit');
            Route::put('/alumnos/update/{id}', 'update')->name('alumnos.update');
            Route::delete('/alumnos/delete/{id}', 'delete')->name('alumnos.delete');
        });

        Route::controller(HorarioController::class)->group(function() {
            Route::get('/profesores/{idProfesor}/horarios', 'index')->name('horarios.index');
            Route::get('/profesores/{idProfesor}/horarios/create', 'create')->name('horarios.create');
            Route::post('profesores/{idProfesor}/horarios/store', 'store')->name('horarios.store');
            Route::get('/profesores/{idProfesor}/horarios/edit/{idHorario}', 'edit')->name('horarios.edit');
            Route::put('/profesores/{idProfesor}/horarios/update/{idHorario}', 'update')->name('horarios.update');
            Route::delete('/profesores/{idProfesor}/horarios/delete/{idHorario}', 'delete')->name('horarios.delete');

            Route::get('/horarios/dias-disponibles', 'diasDisponibles')->name('horarios.diasDisponibles');
        });

    });

    // Rutas para emails
    Route::view('/email/verify', 'emails.auth.verify-email')->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', VerifyEmailController::class)
        ->middleware('signed')->name('verification.verify');
    Route::post('/email/verification-notification', EmailVerificationNotificationController::class)
        ->middleware(['auth', 'throttle:6,1'])->name('verification.send');
});
