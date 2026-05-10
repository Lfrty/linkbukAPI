<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\AuthenticationException;

class AuthService {
    public function login(array $credentials) {
        if (!Auth::attempt($credentials)) {
            throw new AuthenticationException('Credenciales inválidas');
        }

        // Si lo autentico recoge los datos
        $user = Auth::user();

        // Elimina tokens anteriores
        $user->tokens()->delete();

        // Modificado en Sanctum el tiempo de caducidad a 1h
        $token = $user->createToken('api-token')->plainTextToken;

        return (object)([
            'user'  => $user,
            'token' => $token,
            'mensaje' => 'Usuario ' . $user->email . ' logueado']);
    }
}
