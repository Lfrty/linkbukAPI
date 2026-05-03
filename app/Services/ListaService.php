<?php

namespace App\Services;

use App\Models\Lista;
use Illuminate\Support\Facades\DB;

class ListaService {
    /**
     * Obtiene las listas según el rol del usuario
     */
    public function obtenerListas($user) {
        if ($user->esAdmin() || $user->esSupervisor()) {
            return Lista::all();
        }
        return Lista::where('usuario_id', $user->id)->get();
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
