<?php
namespace App\Services\Interfaces\Desafios;

use App\Helpers\ResponseHelper;

interface DesafioServiceInterface {
    /**
     * Servicio que obtiene una paginación de desafios
     * @param int $page
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function obtenerPaginacionPorProfesorId(int $profesorId,int $paginaActual): \Illuminate\Pagination\LengthAwarePaginator|null;
    /**
     * Servicio que obtiene un desafio por su ID
     * @param int $id
     * @return \App\Models\Desafios\Desafio
     */
    public function obtenerPorId(int $id): \App\Models\Desafios\Desafio;
    /**
     * Servicio que crea un desafio
     * @param array $atributos
     * @param int $materiaId
     * @param int $profesorId
     * @return \App\Helpers\ResponseHelper
     */
    public function crear(array $atributos, int $materiaId, int $profesorId): ResponseHelper;
    /**
     * Servicio para editar un desafio
     * @param array $atributos
     * @param int $profesorId
     * @param int $desafioId
     * @return \App\Helpers\ResponseHelper
     */
    public function editar(array $atributos, int $profesorId, int $desafioId): ResponseHelper;
    /**
     * Repositorio para eliminar un desafio
     * @param int $desafioId
     * @param int $profesorId
     * @return \App\Helpers\ResponseHelper
     */
    public function eliminar(int $desafioId, int $profesorId): ResponseHelper;
}
