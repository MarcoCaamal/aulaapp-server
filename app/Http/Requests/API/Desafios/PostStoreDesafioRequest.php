<?php

namespace App\Http\Requests\API\Desafios;

use Illuminate\Foundation\Http\FormRequest;

class PostStoreDesafioRequest extends FormRequest
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
            'nombre_publico' => ['required', 'string', 'max:300'],
            'nombre_privado' => ['required', 'string', 'max:300'],
            'descripción' => ['required', 'string'],
            'fecha_inicio' => ['required', 'date', 'date_format:Y-m-d H:i'],
            'fecha_fin' => ['required', 'date', 'date_format:Y-m-d H:i', 'after:fecha_inicio'],
            'materia_id' => ['required', 'numeric']
        ];
    }
}
