<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProfesorRequest extends FormRequest
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
    public function rules()
    {
        return [
            'nombre' => 'required|max:255|string',
            'apellido_paterno' => 'required|max:255|string',
            'apellido_materno' => 'required|max:255|string',
            'curp' => 'required|string|max:18|unique:users,curp',
            'email' => 'required|email|max:255|unique:users,email',
            'materias' => 'required|array'
        ];
    }
}
