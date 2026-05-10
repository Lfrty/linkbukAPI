<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class RegisterRequest extends FormRequest {
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string>
     */
    public function rules(): array {
        $rules = [
            'nombre'   => 'required|string|max:25',
            'email' => 'required|email|unique:usuarios,email',
            'password' => 'required|min:6',
        ];

        // Cuando registra un admin
        if (Auth::user()?->esAdmin()) {
            $rules['biografia'] = 'nullable|string|max:300';
            $rules['ubicacion'] = 'nullable|string|max:100';
            $rules['permitir_desconocidos'] = 'sometimes|boolean';
            $rules['id_rol'] = 'sometimes|required|in:1,2,3';
            $rules['foto_perfil']  = 'nullable|image|mimes:jpeg,png,jpg|max:1024';
        }

        return $rules;

    }

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
