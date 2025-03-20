<?php

namespace App\Http\Requests;

use App\Services\Interfaces\MateriaServiceInterface;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateMateriaRequest extends FormRequest
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
    public function rules(MateriaServiceInterface $materiaService)
    {
        $materia = $materiaService->obtenerPorId($this->id);

        if($materia->getKey() === null) {
            abort(404);
        }
        return [
            'nombre' => ['required', 'max:255', Rule::unique('materias')->ignore($materia->id)],
            'semestre_id' => 'required|numeric'
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'semestre_id.required' => __('validation.required', ['attribute' => 'Semestre']),
        ];
    }
}
