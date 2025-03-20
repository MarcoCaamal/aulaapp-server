<?php

namespace App\Http\Requests;

use App\Enums\DiaSemanaEnum;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Foundation\Http\FormRequest;

class StoreHorarioRequest extends FormRequest
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
            'lugar' => ['required', 'string', 'max:255'],
            'materia_id' => ['required', 'numeric'],
            'hora_inicio' => ['required', 'date_format:H:i'],
            'hora_fin' => ['required', 'date_format:H:i'],
            'dia_semana' => ['required', new Enum(DiaSemanaEnum::class)]
        ];
    }
}
