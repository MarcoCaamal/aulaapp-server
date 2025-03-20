<?php

namespace App\Http\Requests\API\Foros;

use App\Services\Interfaces\Foros\ForoServiceInterface;
use App\Services\Interfaces\UserServiceInterface;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;

class UpdateForoRequest extends FormRequest {
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(UserServiceInterface $userService, ForoServiceInterface $foroService)
    {
        $authUser = $userService->getAuthenticatedUserByBearerToken();
        $foro = $foroService->obtenerPorId($this->foroId, $this->userId);
        return $authUser->getKey() == $this->userId && $foro->getKey() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'titulo' => ['required', 'string', 'max:300'],
            'contenido' => ['required', 'string'],
            'imagen' => [
                'nullable',
                File::image()
                    ->max(5 * 1024)
            ],
        ];
    }
}
