<?php

namespace App\Http\Controllers;

use App\Models\RespuestaForo;
use Illuminate\Http\Request;

class RespuestaForoController extends Controller {
    public function crear(Request $request) {
        $request->validate([
            'tema_id'   => 'required|integer|exists:temas_foro,id',
            'contenido' => 'required|string',
        ]);

        $respuesta = RespuestaForo::create([
            'tema_id'    => $request->tema_id,
            'usuario_id' => auth()->id(),
            'contenido'  => $request->contenido,
        ]);

        return $this->successResponse($respuesta, 'Respuesta publicada con éxito', 201);
    }

    public function borrar($id) {
        $respuesta = RespuestaForo::find($id);

        if (!$respuesta) {
            return $this->errorResponse('Respuesta no encontrada', 404);
        }

        // Le pasamos el usuario autenticado al método del modelo
        if (!$respuesta->tieneAcceso(auth()->user())) {
            return $this->errorResponse('No puede borrar esta respuesta', 403);
        }

        $respuesta->delete();

        return $this->successResponse(null, 'Respuesta eliminada');
    }
}
