<?php

namespace App\Http\Controllers;

use App\Models\TemaForo;
use Illuminate\Http\Request;

class TemaForoController extends Controller {
    public function index(Request $request) {
        $query = TemaForo::with('usuario:id,nombre,foto_perfil'); // Cargamos solo lo necesario del usuario

        if ($request->has('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        // Usamos latest() para que los nuevos salgan arriba
        return $this->successResponse($query->latest()->get(), 'Temas del foro recuperados');
    }

    public function store(Request $request) {
        $request->validate([
            'titulo'    => 'required|string|max:255',
            'tipo'      => 'required|string|max:50',
            'contenido' => 'required|string',
        ]);

        $tema = TemaForo::create([
            'usuario_id' => auth()->id(),
            'titulo'     => $request->titulo,
            'tipo'       => $request->tipo,
            'contenido'  => $request->contenido,
        ]);

        return $this->successResponse($tema, 'Tema creado correctamente', 201);
    }

    public function show($id) {
        // Cargamos el usuario del tema y también sus respuestas con sus respectivos autores
        $tema = TemaForo::with(['usuario', 'respuestas.usuario'])->find($id);

        if (!$tema) {
            return $this->errorResponse('El tema no existe', 404);
        }

        return $this->successResponse($tema, 'Detalle del tema');
    }

    public function destroy($id) {
        $tema = TemaForo::find($id);

        if (!$tema) {
            return $this->errorResponse('Tema no encontrado', 404);
        }

        // Usamos el método que creamos en el modelo
        if (!$tema->puedeSerGestionadoPor(auth()->user())) {
            return $this->errorResponse('No tienes permiso para eliminar este tema', 403);
        }

        $tema->delete();

        return $this->successResponse(null, 'Tema eliminado con éxito');
    }
}
