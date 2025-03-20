<?php

namespace App\Http\Requests\API;

use App\Enums\TurnoEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class GetProfesoresRequest extends FormRequest
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
            'nombre' => ['nullable', 'string'],
            'curp' => ['nullable', 'string'],
            'email' => ['nullable', 'email'],
            'turno' => ['nullable', new Enum(TurnoEnum::class)]
        ];
    }
}
