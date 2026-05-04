<?php

namespace App\Services;

use App\Models\Lista;
use Illuminate\Support\Facades\DB;

class ListaService {
    /**
     * Obtiene las listas según el rol del usuario
     */
    public function obtenerListas($user) {
        return Lista::where('usuario_id', $user->id)
            ->withCount('libros')
            ->with(['libros'])
            ->get();
    }

    /**
     * Nueva lista
     */
    public function crearNuevaLista(array $data) {
        return Lista::create([
            'usuario_id' => $data['usuario_id'],
            'nombre'     => $data['nombre']
        ]);
    }

    /**
     * Añade un libro a una lista específica
     */
    public function agregarLibroALista($listaId, $libroId, $user) {
        // Buscamos la lista asegurándonos de que pertenezca al usuario
        $lista = Lista::where('id', $listaId)
                    ->where('usuario_id', $user->id)
                    ->first();

        if (!$lista) {
            return ['success' => false, 'message' => 'Lista no encontrada', 'code' => 404];
        }

        // syncWithoutDetaching añade el ID al pivot si no existe, sin borrar los demás
        $lista->libros()->syncWithoutDetaching([$libroId]);

        return ['success' => true];
    }

    public function quitarLibroDeLista($listaId, $libroId, $usuario) {
        $lista = Lista::find($listaId);

        if (!$lista) {
            return ['success' => false, 'message' => 'Lista no encontrada', 'code' => 404];
        }

        // Seguridad: Verificar dueño
        if ($lista->usuario_id !== $usuario->id) {
            return ['success' => false, 'message' => 'No tienes permiso para modificar esta lista', 'code' => 403];
        }

        // Usamos detach para eliminar la relación en la tabla pivote (libro_lista)
        $lista->libros()->detach($libroId);

        return ['success' => true];
    }

    public function actualizarLista($id, array $datos, $usuario) {
        $lista = Lista::find($id);

        if (!$lista) {
            return ['success' => false, 'message' => 'Lista no encontrada', 'code' => 404];
        }

        if ($lista->usuario_id !== $usuario->id && !$usuario->esAdmin()) {
            return ['success' => false, 'message' => 'No tienes permiso para editar esta lista', 'code' => 403];
        }

        if ($lista->es_default && isset($datos['nombre'])) {
            return ['success' => false, 'message' => 'No puedes cambiar el nombre de una lista del sistema', 'code' => 422];
        }

        $lista->update($datos);

        return [
            'success' => true,
            'data' => $lista
        ];
    }

    /**
     * Crea las listas iniciales para un nuevo usuario
     */
    public function crearListasNuevoUsuario($usuarioId) {
        $listas = [
            ['nombre' => 'Favoritos', 'es_default' => true],
            ['nombre' => 'Leer más tarde', 'es_default' => true],
        ];

        foreach ($listas as $data) {
            Lista::create([
                'usuario_id' => $usuarioId,
                'nombre'     => $data['nombre'],
                'es_default' => true,
            ]);
        }
    }

    /**
     * Eliminado lógico
     */
    public function eliminarLogic($id, $user) {
        $lista = $user->listas()->find($id);

        if (!$lista) {
            return ['success' => false, 'message' => 'Lista no encontrada', 'code' => 404];
        }

        if ($lista->es_default && !auth()->user()->esAdmin()) {
            return ['success' => false, 'message' => 'No puedes eliminar una lista del sistema', 'code' => 403];
        }

        $lista->delete();
        return ['success' => true];
    }

    /**
     * Restaurar una lista
     */
    public function restaurar($id) {
        $lista = Lista::withTrashed()->find($id);

        if ($lista) {
            $lista->restore();
            return $lista;
        }

        return null;
    }

    /**
     * Eliminar
     */
    public function eliminarDefinitivamente($id) {
        $lista = Lista::withTrashed()->find($id);

        if ($lista) {
            $lista->forceDelete();
            return true;
        }

        return false;
    }
}
