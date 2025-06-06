<?php

namespace App\Http\Requests\API;

use App\Enums\TurnoEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class GetAsesoriasRequest extends FormRequest
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
            'turno' => [new Enum(TurnoEnum::class), 'nullable'],
            'param' => ['nullable', 'string']
        ];
    }
}
