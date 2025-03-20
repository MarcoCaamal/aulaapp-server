<?php

namespace App\Http\Requests;

use App\Services\Interfaces\GrupoServiceInterface;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateGrupoRequest extends FormRequest
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
    public function rules(GrupoServiceInterface $grupoService)
    {
        $grupo = $grupoService->obtenerPorId($this->id);

        if(!isset($grupo)) {
            abort(404);
        }
        return [
            'nombre' => ['required', Rule::unique('grupos')->ignoreModel($grupo)],
            'id_semestre' => ['required', 'numeric']
        ];
    }
}
