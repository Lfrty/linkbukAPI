<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\AuthService;
use Illuminate\Auth\Access\AuthorizationException;
use App\Http\Resources\UsuarioResource;

class LoginController extends Controller {
    protected AuthService $authService;

    public function __construct(AuthService $authService) {
        $this -> authService = $authService;
    }

    // Login usuario
    public function login(Request $request) {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);
        try {
            $result = $this->authService -> login($credentials);
        } catch (AuthorizationException $e) {
            return $this->errorResponse($e->getMessage(), 401);
        }

        return $this->successResponse(
            [
                'user'  => new UsuarioResource($result -> user),
                'token' => $result -> token
            ],
            $result -> mensaje,
            201
        );
    }


    // Cerrar sesión
    public function logout(Request $request) {
        $request->user()->tokens()?->delete();

        return $this->successResponse(null, 'Logout correcto');
    }
}
