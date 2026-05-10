<?php

namespace App\Services;

use App\Models\Lista;
use App\Models\Usuario;

class ListaService {
    /**
     * Obtiene las listas según el rol del usuario
     */
    public function obtenerListas(Usuario $user) {
        return Lista::query()->where('usuario_id', $user->id)
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
    public function agregarLibroALista(int $listaId, int $libroId, Usuario $user) {
        // Buscamos la lista asegurándonos de que pertenezca al usuario
        $lista = Lista::query()->where('id', $listaId)
                    ->where('usuario_id', $user->id)
                    ->first();

        if (!$lista) {
            return ['success' => false, 'message' => 'Lista no encontrada', 'code' => 404];
        }

        // syncWithoutDetaching añade el ID al pivot si no existe, sin borrar los demás
        $lista->libros()->syncWithoutDetaching([$libroId]);

        return ['success' => true];
    }

    public function quitarLibroDeLista(int $listaId, int $libroId, Usuario $usuario) {
        $lista = Lista::query()->find($listaId);

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

    public function actualizarLista(int $id, array $datos, Usuario $usuario) {
        $lista = Lista::query()->find($id);

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
    public function crearListasNuevoUsuario(int $usuarioId) {
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
    public function eliminarLogic(int $id, Usuario $user) {
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
    public function restaurar(int $id) {
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
    public function eliminarDefinitivamente(int $id) {
        $lista = Lista::withTrashed()->find($id);

        if ($lista) {
            $lista->forceDelete();
            return true;
        }

        return false;
    }
}
