<?php
use App\Models\Desafios\PreguntaDesafio;

class PreguntaDesafioRepository {
    private $model;

    public function __construct()
    {
        $this->model = new PreguntaDesafio();
    }

    /**
     * Repositorio que retorna una PreguntaDesafio
     * @param int $id
     * @return App\Models\Desafios\PreguntaDesafio
     */
    public function obtenerPorId(int $id): PreguntaDesafio
    {
        return $this->model->find($id);
    }

    /**
     * Repositorio que retorna una colección de PreguntaDesafio
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function obtenerColeccion(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->model->all();
    }

    /**
     * Repositorio que crea o actualiza una PreguntaDesafio
     * @param App\Models\Desafios\PreguntaDesafio $preguntaDesafio
     * @return bool
     */
    public function guardar(PreguntaDesafio $preguntaDesafio): bool
    {
        return $preguntaDesafio->save();
    }

    /**
     * Repositorio que elimina una PreguntaDesafio
     * @param App\Models\Desafios\PreguntaDesafio $preguntaDesafio
     * @return bool
     */
    public function eliminar(PreguntaDesafio $preguntaDesafio): bool
    {
        return $preguntaDesafio->delete();
    }
}
