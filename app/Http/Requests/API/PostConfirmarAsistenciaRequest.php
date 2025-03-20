<?php

namespace App\Http\Requests\API;

use App\Models\PersonalAccessToken;
use Illuminate\Foundation\Http\FormRequest;

class PostConfirmarAsistenciaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $token = $this->bearerToken();

        if(!$token) {
            return false;
        }

        $user = PersonalAccessToken::findToken($token)->tokenable;
        
        return $user->hasRole('Alumno');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'fecha' => ['date','nullable'],
            'horario_id' => ['required', 'numeric']
        ];
    }
}
