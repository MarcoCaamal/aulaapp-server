<?php

namespace App\Http\Requests\API;

use App\Enums\TurnoEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class GetAllAsesoriasDisponiblesRequest extends FormRequest
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
            'horaInicio' => ['nullable', 'date_format:H:i:s'],
            'horaFin' => ['nullable', 'date_format:H:i:s', 'after:horaInicio'],
            'fecha' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:'.date('Y-m-d', strtotime('today midnight'))],
            'nombreProfesor' => ['nullable', 'string'],
            'turno' => ['nullable', new Enum(TurnoEnum::class)]
        ];
    }
}
