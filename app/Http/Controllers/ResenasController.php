<?php

namespace App\Http\Controllers;

use App\Services\ResenaService;
use Illuminate\Http\Request;

class ResenasController extends Controller {
    protected $resenaService;

    public function __construct(ResenaService $resenaService) {
        $this->resenaService = $resenaService;
    }

    /**
     * Obtiene listado de reseñas y el libro correspondiente
     */
    public function index() {
        $resenas = $this->resenaService->obtenerResenasUsuario(auth()->user());

        return $this->successResponse(
            $resenas,
            'Reseñas recuperadas correctamente'
        );
    }

    /**
     * Guarda una nueva reseña en la base de datos.
     */
    public function store(Request $request) {
        $request->validate([
            'libro_id'    => 'required|exists:libros,id',
            'puntuacion'  => 'required|integer|min:1|max:10',
            'comentario'  => 'nullable|string|max:250',
        ]);

        $resena = $this->resenaService->crearResena([
            'libro_id'   => $request->libro_id,
            'usuario_id' => auth()->id(),
            'puntuacion' => $request->puntuacion,
            'comentario' => $request->comentario,
        ]);

        return $this->successResponse($resena, 'Reseña publicada con éxito', 201);
    }

    /**
     * Eliminar reseña propia.
     */
    public function destroy($id) {
        $resultado = $this->resenaService->eliminarResena($id, auth()->user());

        if (!$resultado['success']) {
            return $this->errorResponse($resultado['message'], $resultado['code']);
        }

        return $this->successResponse(null, 'Reseña eliminada');
    }
}
