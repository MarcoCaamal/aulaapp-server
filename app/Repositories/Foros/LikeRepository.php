<?php
namespace App\Repositories\Foros;

use App\Enums\Foros\EstatusForoEnum;
use App\Models\Foros\Like;

class LikeRepository
{
    private Like $model;

    public function __construct() {
        $this->model = new Like();
    }
    /**
     * Repositorio que devuelve un like por su id
     * @param int $id
     * @return Like
     */
    public function obtenerPorId(int $id): Like
    {
        return $this->model->find($id) ?? new Like();
    }
    /**
     * Repositorio para obtener el like de un usuario, de un determinado foro. Si no hay devuelve un objeto vacio
     * @param int $userId
     * @param int $foroId
     * @return Like
     */
    public function obtenerPorUsuarioIdForoId(int $userId, int $foroId): Like
    {
        return $this->model
            ->select('likes.*')
            ->join('users', 'likes.user_id', '=', 'users.id')
            ->join('foros', 'likes.foro_id', '=', 'foros.id')
            ->where([
                ['users.id', $userId],
                ['foros.id', $foroId],
                ['foros.estatus', EstatusForoEnum::ACTIVO],
            ])
            ->first() ?? new Like();
    }
    /**
     * Repositorio para crear o guardar un like
     * @param Like $like
     * @return bool
     */
    public function guardar(Like $like): bool
    {
        return $like->save();
    }
    /**
     * Repositorio para eliminar un like
     * @param Like $like
     * @return bool
     */
    public function eliminar(Like $like): bool
    {
        return $like->delete();
    }
}
