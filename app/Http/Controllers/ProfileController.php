<?php

namespace App\Http\Controllers;

use App\Http\Resources\UsuarioResource;
use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller {
    // Valido los datos el Request personalizado
    public function editarPerfil(UpdateProfileRequest $request) {
        // Tomo el usuario de la petición
        $user = $request->user();

        try {
            // Obtengo los datos validados
            $data = $request->validated();

            $user->update($data);

            return $this->successResponse(
                new UsuarioResource($user),
                'Perfil actualizado'
            );
        } catch (ValidationException $e) {
            // Usamos el centralizador de errores
            return $this->errorResponse('Errores de validación', 422, $e->errors());
        }
    }
}
