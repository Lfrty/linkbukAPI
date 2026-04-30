<?php

namespace App\Http\Controllers;

use App\Models\TemaForo;
use Illuminate\Http\Request;

class TemaForoController extends Controller {
    // Listas Posts
    public function index(Request $request) {
        $query = TemaForo::with('usuario');

        if ($request->has('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        return response()->json([
            'ok' => true,
            'data' => $query->latest()->get(),
        ]);
    }

    // Crear post
    public function store(Request $request) {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'tipo' => 'required|string|max:50',
            'contenido' => 'required|string',
        ]);

        $tema = TemaForo::create([
            'usuario_id' => auth()->id(),
            'titulo' => $request->titulo,
            'tipo' => $request->tipo,
            'contenido' => $request->contenido,
        ]);

        return response()->json([
            'ok' => true,
            'data' => $tema,
        ], 201);
    }

    // Ver un post
    public function show($id) {
        $tema = TemaForo::with('usuario')->find($id);

        if (!$tema) {
            return response()->json([
                'ok' => false,
                'error' => 'Tema no encontrado',
            ], 404);
        }

        return response()->json([
            'ok' => true,
            'data' => $tema,
        ]);
    }

    // BORRAR (autor o admin)
    public function destroy($id) {
        $tema = TemaForo::find($id);

        if (!$tema) {
            return response()->json([
                'ok' => false,
                'error' => 'Tema no encontrado',
            ], 404);
        }

        $user = auth()->user();

        if ($tema->usuario_id !== $user->id && !auth()->user()-> esAdmin()) {
            return response()->json([
                'ok' => false,
                'error' => 'No autorizado',
            ], 403);
        }

        $tema->delete();

        return response()->json([
            'ok' => true,
            'message' => 'Tema eliminado',
        ]);
    }
}
