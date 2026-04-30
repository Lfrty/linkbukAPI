<?php

namespace App\Http\Controllers;

use App\Models\RespuestaForo;
use Illuminate\Http\Request;

class RespuestaForoController extends Controller {
    public function crear(Request $request) {
        $request->validate([
            'tema_id' => 'required|integer|exists:temas_foro,id',
            'contenido' => 'required|string',
        ]);

        $respuesta = RespuestaForo::create([
            'tema_id' => $request->tema_id,
            'usuario_id' => auth()->id(),
            'contenido' => $request->contenido,
        ]);

        return response()->json([
            'ok' => true,
            'data' => $respuesta,
        ], 201);
    }

    public function borrar($id) {
        $respuesta = RespuestaForo::find($id);

        if (!$respuesta) {
            return response()->json([
                'ok' => false,
                'error' => 'Respuesta no encontrada',
            ], 404);
        }

        $user = auth()->user();

        if ($respuesta->usuario_id !== $user->id && auth()->user()->esAdmin()) {
            return response()->json([
                'ok' => false,
                'error' => 'No autorizado',
            ], 403);
        }

        $respuesta->delete();

        return response()->json([
            'ok' => true,
            'message' => 'Respuesta eliminada',
        ]);
    }
}
