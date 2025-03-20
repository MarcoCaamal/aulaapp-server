<?php

namespace App\Http\Requests;

use App\Services\Interfaces\UserServiceInterface;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProfesorRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(UserServiceInterface $userService)
    {
        $profesor = $userService->obtenerProfesorPorId($this->id);
        if($profesor->getKey() === null) {
            abort(404);
        }
        return [
            'nombre' => ['required', 'max:255', 'string'],
            'apellido_paterno' => ['required', 'max:255', 'string'],
            'apellido_materno' => ['required', 'max:255', 'string'],
            'curp' => [
                'required',
                'string',
                'max:18',
                Rule::unique('users')->ignore($profesor->id)
            ],
            'email' => ['required', 'email', Rule::unique('users')->ignore($profesor->id)],
            'materias' => 'required|array'
        ];
    }
}
