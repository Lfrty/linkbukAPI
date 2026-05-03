<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Http\Resources\UsuarioResource;

class AuthController extends Controller {
    // Crear Usuario
    public function registrar(Request $request) {
        try {
            $request->validate([
                'nombre'   => 'required|string|max:25',
                'email' => 'required|email|unique:usuarios,email',
                'password' => 'required|min:6',
            ], [
                'nombre.required'   => 'El nombre es obligatorio',
                'email.required' => 'El email es obligatorio',
                'email.email' => 'El formato del email no es válido',
                'email.unique' => 'Este email ya está registrado',
                'password.required' => 'La contraseña es obligatoria',
                'password.min' => 'La contraseña debe tener al menos 6 caracteres',
            ]);

            // Crear usuario
            $usuario = Usuario::create(['nombre'   => $request->nombre,
                'email'    => $request->email,
                'password' => $request->password, // Se cifra en el modelo
                'rol_id' => 3, // Role de Usuario por defecto
            ]);

            return $this->successResponse(null, 'Usuario creado correctamente', 201);

        } catch (ValidationException $e) {
            // Usamos el centralizador de errores
            return $this->errorResponse('Errores de validación', 422, $e->errors());
        }
    }

    // Login usuario
    public function login(Request $request) {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (!Auth::attempt($credentials)) {
            return $this->errorResponse('Credenciales incorrectas', 401);
        }

        $user = $request->user();

        // Elimina tokens anteriores
        $user->tokens()->delete();

        $token = $user->createToken('api-token')->plainTextToken;

        return $this->successResponse([
            'user'  => new UsuarioResource($user),
            'token' => $token,
        ], 'Usuario ' + $usuario->email + ' logueado');
    }

    public function edit(Request $request) {
        $user = $request->user();

        $validated = $request->validate([
            'nombre'    => 'sometimes|string|max:25',
            'email'     => 'sometimes|email|unique:usuarios,email,' . $user->id,
            'biografia' => 'nullable|string|max:500',
            'ubicacion' => 'nullable|string|max:100',
            'permitir_desconocidos' => 'sometimes|boolean',
        ]);

        $user->update($validated);

        // Devolvemos el Resource. Él se encarga de quitar lo que sobra.
        return $this->successResponse(
            new UsuarioResource($user),
            'Perfil actualizado'
        );
    }

    // Cerrar sesión
    public function logout(Request $request) {
        $request->user()->tokens()?->delete();

        return $this->successResponse(null, 'Logout correcto');
    }
}
