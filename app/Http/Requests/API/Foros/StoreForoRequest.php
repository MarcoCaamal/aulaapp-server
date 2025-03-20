<?php

namespace App\Http\Requests\API\Foros;

use App\Enums\Foros\EstatusForoEnum;
use App\Services\Interfaces\UserServiceInterface;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Rules\File;

class StoreForoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(UserServiceInterface $userService)
    {
        $userAuth = $userService->getAuthenticatedUserByBearerToken();
        return $userAuth->getKey() == $this->route('userId');
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
            'materia_id' => ['required', 'numeric'],
        ];
    }
}
