<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ListaController extends Controller
{
   public function index()
    {
        return response()->json([
            'ok' => true,
            'data' => Lista::all()
        ]);
    }

    // Nueva lista
    public function store(Request $request)
    {
        $request->validate([
            'usuario_id' => 'required|integer',
            'nombre' => 'required|string|max:255',
            'es_default' => 'boolean'
        ]);

        $lista = Lista::create([
            'usuario_id' => $request->usuario_id,
            'nombre' => $request->nombre,
            'es_default' => $request->es_default ?? false,
        ]);

        return response()->json([
            'ok' => true,
            'data' => $lista
        ], 201);
    }

    // DELETE lógico (usuario normal)
    public function destroy($id)
    {
        $lista = Lista::find($id);

        if (!$lista) {
            return response()->json([
                'ok' => false,
                'error' => 'Lista no encontrada'
            ], 404);
        }

        $lista->delete(); // Soft delete

        return response()->json([
            'ok' => true,
            'message' => 'Lista eliminada (lógica)'
        ]);
    }

    // DELETE (solo admin)
    public function forceDelete($id)
    {
        $lista = Lista::withTrashed()->find($id);

        if (!$lista) {
            return response()->json([
                'ok' => false,
                'error' => 'Lista no encontrada'
            ], 404);
        }

        $lista->forceDelete();

        return response()->json([
            'ok' => true,
            'message' => 'Lista eliminada definitivamente'
        ]);
    }

    // Recuperar lista (admin)
    public function restore($id)
    {
        $lista = Lista::withTrashed()->find($id);

        if (!$lista) {
            return response()->json([
                'ok' => false,
                'error' => 'Lista no encontrada'
            ], 404);
        }

        $lista->restore();

        return response()->json([
            'ok' => true,
            'data' => $lista
        ]);
    }
}