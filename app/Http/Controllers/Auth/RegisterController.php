<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Services\RegisterUserService;
use Illuminate\Validation\ValidationException;
use App\Models\Usuario;

class RegisterController extends Controller {
    // Crear Usuario
    public function registrar(RegisterRequest $request, RegisterUserService $service) {

        try {

            $data = $service->registrar(new Usuario($request->only('nombre', 'email', 'password')));

            // Devolvemos tanto el usuario como el token
            return $this->successResponse($data, 'Usuario registrado y logueado correctamente', 201);

        } catch (ValidationException $e) {
            // Usamos el centralizador de errores
            return $this->errorResponse('Errores de validación', 422, $e->errors());
        }
    }
}
