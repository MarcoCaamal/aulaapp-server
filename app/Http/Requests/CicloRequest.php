<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CicloRequest extends FormRequest
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
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date',
            'is_activo' => 'boolean'
        ];
    }
}
