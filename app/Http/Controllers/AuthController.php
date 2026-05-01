<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller {
    // Crear Usuario
    public function registrar(Request $request) {
        try {
            $request->validate([
                'nombre' => 'required|string|max:255',
                'email' => 'required|email|unique:usuarios,email',
                'password' => 'required|min:6',
            ], [
                'nombre.required' => 'El nombre es obligatorio',
                'email.required' => 'El email es obligatorio',
                'email.email' => 'El formato del email no es válido',
                'email.unique' => 'Este email ya está registrado',
                'password.required' => 'La contraseña es obligatoria',
                'password.min' => 'La contraseña debe tener al menos 6 caracteres',
            ]);

            // Crear usuario
            $usuario = Usuario::create([
                'nombre' => $request->nombre,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'rol_id' => 2, // Usuario por defecto
            ]);

            return response()->json([
                'message' => 'Usuario creado correctamente',
                'user' => $usuario,
            ], 201);

        } catch (ValidationException $e) {

            return response()->json([
                'status' => 'error',
                'message' => 'Errores de validación',
                'errors' => $e->errors(),
            ], 422);
        }
    }

    // Login usuario
    public function login(Request $request) {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Credenciales incorrectas',
            ], 401);
        }

        $user = $request->user();

        // Elimina tokens anteriores
        $user->tokens()->delete();

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'user' => [
                'id' => $user->id,
                'nombre' => $user->nombre,
                'email' => $user->email,
                'biografia' => $user->biografia,
                'ubicacion' => $user->ubicacion,
                'foto_perfil' => $user->foto_perfil,
                'permite_desconocidos' => $user->permite_desconocidos,
            ],
            'token' => $token,
        ]);
    }

    // Cerrar sesión
    public function logout(Request $request) {
        $request->user()->tokens()?->delete();

        return response()->json([
            'message' => 'Logout correcto',
        ]);
    }
}
