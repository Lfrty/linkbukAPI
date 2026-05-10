<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateProfileRequest extends FormRequest {
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string>
     */
    public function rules(): array {
        $rules =  [
        'nombre'   => 'sometimes|required|string|max:25',
        'biografia' => 'nullable|string|max:300',
        'ubicacion' => 'nullable|string|max:100',
        'permitir_desconocidos' => 'sometimes|boolean',
        'foto_perfil'  => 'nullable|image|mimes:jpeg,png,jpg|max:1024'
    ];

        if (Auth::user()->esAdmin()) {
            $rules['password'] = 'sometimes|required|min:6';
            $rules['id_rol'] = 'sometimes|required|in:1,2,3';
            // Con la excepción del id puedo guardar repitiendo el email pues exceptúa al del propio usuario
            $rules['email'] = 'sometimes|required|email|unique:usuarios,email,' . $this->route('usuario')->id;
        }

        return $rules;
    }


    // Mensajes de error
    public function messages(): array {
        return [
            'nombre.required'   => 'El nombre es obligatorio',
            'nombre.max'        => 'El nombre solo admite hasta 25 caracteres',
            'email.required'    => 'El email es obligatorio',
            'email.email'       => 'El formato del email no es válido',
            'email.unique'      => 'Este email ya está registrado',
            'password.required' => 'La contraseña es obligatoria',
            'password.min'      => 'La contraseña debe tener al menos 6 caracteres',
            'biografia.max'     => 'La biografía excede los 300 caracteres',
            'ubicacion'         => 'La ubicación excede los 100 caracteres',
            'foto_perfil.max'   => 'El tamaño máximo de la imagen es de 1MB'
        ];
    }
}
