<?php

namespace App\Http\Requests;

use App\Services\Interfaces\UserServiceInterface;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateAlumnoRequest extends FormRequest
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
        $alumno = $userService->obtenerAlumnoPorId($this->id);

        if($alumno->getKey() === null) {
            abort(404);
        }
        return [
            'nombre' => ['required', 'string', 'max:255'],
            'apellido_paterno' => ['required', 'string', 'max:255'],
            'apellido_materno' => ['required', 'string', 'max:255'],
            'curp' => ['required', 'string', 'size:18', Rule::unique('users', 'curp')->ignore($alumno->id)],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($alumno->id)],
            'grupo_id' => ['required', 'numeric']
        ];
    }
}
