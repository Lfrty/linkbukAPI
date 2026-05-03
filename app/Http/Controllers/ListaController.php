<?php

namespace App\Http\Controllers;

use App\Models\Lista;
use Illuminate\Http\Request;

class ListaController extends Controller {
    public function index() {
        $user = auth()->user();

        // Simplificamos la lógica de roles
        if ($user->esAdmin() || $user->esSupervisor()) {
            $data = Lista::all();
        } else {
            $data = Lista::where('usuario_id', $user->id)->get();
        }

        return $this->successResponse($data, 'Listas recuperadas correctamente');
    }

    public function store(Request $request) {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'es_default' => 'boolean',
        ]);

        $lista = Lista::create([
            'usuario_id' => auth()->id(),
            'nombre'     => $request->nombre,
            'es_default' => $request->es_default ?? false,
        ]);

        return $this->successResponse($lista, 'Lista creada con éxito', 201);
    }

    public function destroy($id) {
        $lista = Lista::find($id);

        if (!$lista) {
            return $this->errorResponse('Lista no encontrada', 404);
        }

        if ($lista->es_default) {
            return $this->errorResponse('No puedes eliminar una lista del sistema', 403);
        }

        $lista->delete(); // Soft delete

        return $this->successResponse(null, 'Lista enviada a la papelera');
    }

    public function forceDelete($id) {
        $lista = Lista::withTrashed()->find($id);

        if (!$lista) {
            return $this->errorResponse('Lista no encontrada', 404);
        }

        $lista->forceDelete();

        return $this->successResponse(null, 'Lista eliminada definitivamente');
    }

    public function restore($id) {
        $lista = Lista::withTrashed()->find($id);

        if (!$lista) {
            return $this->errorResponse('Lista no encontrada', 404);
        }

        $lista->restore();

        return $this->successResponse($lista, 'Lista restaurada correctamente');
    }

    /**
     * Crea listas por defecto
     */
    public function crearListasSistema($usuario) {
        $listas = ['Favoritos', 'Leer más tarde'];

        foreach ($listas as $nombre) {
            Lista::create([
                'usuario_id' => $usuario->id,
                'nombre'     => $nombre,
                'es_default' => true,
            ]);
        }
    }
}
