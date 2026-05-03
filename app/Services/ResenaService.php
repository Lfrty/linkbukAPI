<?php

namespace App\Services;

use App\Models\Resena;
use App\Models\Libro;
use Illuminate\Support\Facades\DB;

class ResenaService {
    /**
     * Obtiene todas las reseñas del usuario autenticado 👤
     */
    public function obtenerResenasUsuario($user) {
        return $user->resenas()->with('libro')->get();
    }

    /**
     * Crea una reseña y asegura que el usuario no repita en el mismo libro.
     */
    public function crearResena(array $data) {
        // 🛡️ Comprobamos si ya existe una reseña de este usuario para este libro
        $existe = Resena::where('libro_id', $data['libro_id'])
                        ->where('usuario_id', $data['usuario_id'])
                        ->exists();

        if ($existe) {
            // Aquí podríamos lanzar una excepción o devolver un error
            return null;
        }

        return Resena::create([
            'libro_id'   => $data['libro_id'],
            'usuario_id' => $data['usuario_id'],
            'puntuacion' => $data['puntuacion'],
            'comentario' => $data['comentario'],
        ]);
    }

    /**
     * Elimina una reseña verificando que pertenezca al usuario.
     */
    public function eliminarResena($id, $user) {
        // Buscamos la reseña a través de las relaciones del usuario para mayor seguridad
        $resena = $user->resenas()->find($id);

        if (!$resena) {
            return [
                'success' => false,
                'message' => 'Reseña no encontrada o no tienes permiso',
                'code' => 404
            ];
        }

        $resena->delete(); // Usará Soft Delete si el modelo tiene el Trait
        return ['success' => true];
    }
}
