<?php

namespace App\Services;

use App\Models\Usuario;
use Illuminate\Support\Facades\DB;
use App\Services\ListaService;
use App\Http\Resources\UsuarioResource;

class RegisterUserService {
    protected ListaService $listaService;

    public function __construct(ListaService $listaService) {
        $this->listaService = $listaService;
    }


    public function registrar(Usuario $data) {
        return DB::transaction(function () use ($data) {
            // Crear usuario
            $usuario = Usuario::create([
                'nombre'   => $data->nombre,
                'email'    => $data->email,
                'password' => $data->password,
                'rol_id'   => 3,
            ]);

            // Crear listas por defecto
            $this ->listaService->crearListasNuevoUsuario($usuario->id);

            // Generar token
            $token = $usuario->createToken('api-token')->plainTextToken;

            return [
                'user'  => new UsuarioResource($usuario),
                'token' => $token
            ];
        });
    }
}
