<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UsuarioResource extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array {
        return [
            'id'          => $this->id,
            'nombre'      => $this->nombre,
            'email'       => $this->email,
            'biografia'   => $this->biografia === '-' ? 'Sin biografía' : $this->biografia,
            'ubicacion'   => $this->ubicacion ?? 'No especificada',
            'foto_perfil' => $this->foto_perfil,
            'ajustes'     => [
                'permitir_desconocidos' => (bool)$this->permitir_desconocidos,
            ],
            'rol_id'      => $this->rol_id,
            'rol_nombre'  => $this->rol->nombre ?? 'Usuario',
        ];
    }
}
