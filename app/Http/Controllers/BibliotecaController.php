<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BibliotecaController extends Controller {
    // Get biblioetca completa
    public function index(Request $request) {
        $biblioteca = $request->user()->biblioteca()->with('libros')->first();

        return response()->json($biblioteca);
    }

    // Actualizar estado
    public function updateEstado(Request $request, $libroId) {
        $biblioteca = $request->user()->biblioteca;

        $biblioteca->libros()->updateExistingPivot($libroId, [
            'estado_lectura' => $request->estado_lectura,
            'fecha_finalizacion' =>
                $request->estado_lectura === 'leido' ? now() : null,
        ]);

        return response()->json(['message' => 'Estado actualizado']);
    }

    // Eliminar libro
    public function removeLibro(Request $request, $libroId) {
        $biblioteca = $request->user()->biblioteca;

        $biblioteca->libros()->detach($libroId);

        return response()->json(['message' => 'Libro eliminado']);
    }
}
