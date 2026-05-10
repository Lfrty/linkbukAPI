<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Usuario;
use App\Services\RegisterAdminService;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Resources\UsuarioResource;
use App\Http\Controllers\Controller;

class UserController extends Controller {
    protected RegisterAdminService $registerAdminService;

    public function __construct(RegisterAdminService $registerAdminService) {
        $this -> registerAdminService = $registerAdminService;
    }


    // Obtiene lista de usuarios
    public function obtenerLista() {
        $users = Usuario::all();
        return $this->successResponse($users, 'Usuarios obtenidos correctamente');
    }


    // Obtiene un usuario por id
    public function obtenerUsuario(int $id) {
        $user = Usuario::query()->find($id);
        if (!$user) {
            return $this->errorResponse('Usuario no encontrado', 404);
        }
        return $this->successResponse($user, 'Usuario obtenido correctamente');
    }


    // Registrar Usuario
    public function crearUsuario(Request $request) {
        try {

            $data = $this -> registerAdminService->registrar(new Usuario($request->only('nombre', 'email', 'password', 'idRol')));

            // Devolvemos tanto el usuario como el token
            return $this->successResponse($data, 'Usuario registrado y logueado correctamente', 201);

        } catch (ValidationException $e) {
            // Usamos el centralizador de errores
            return $this->errorResponse('Errores de validación', 422, $e->errors());
        }


    }

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


    // Eliminar usuario (puede recuperarse)
    public function borrar(int $id) {
        $user = Usuario::query()->find($id);
        if (!$user) {
            return $this->errorResponse('Usuario no encontrado', 404);
        }

        $user->delete($id);
        return $this->successResponse(null, 'Usuario eliminado correctamente');
    }


    // Eliminar definitvamente un usuario
    public function destruir(int $id) {
        $user = Usuario::query()->find($id);
        if (!$user) {
            return $this->errorResponse("Usuario no encontrado", 404);
        }
    }
}
