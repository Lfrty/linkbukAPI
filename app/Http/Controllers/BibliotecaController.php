<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BibliotecaController extends Controller {
    // Get biblioetca completa
    public function index(Request $request) {
        $biblioteca = $request->user()->biblioteca()->with(['libros' => function ($q) {
            $q->select('libros.id', 'work_key', 'titulo', 'autor', 'portada')
            ->withPivot('estado_lectura', 'fecha_finalizacion');
            ;
        }])->first();

        // Si el usuario no tiene biblioteca envía un array vacío en data
        $data = $biblioteca ? $biblioteca->libros : [];

        return $this->successResponse($data, 'Biblioteca recuperada');
    }

    // Añadir libro a la biblioteca
    public function addLibro(Request $request) {
        $request->validate([
            'libro_id' => 'required|exists:libros,id',
            'estado_lectura' => 'nullable|in:pendiente,leyendo,leido',
            'fecha_finalizacion' => 'nullable|date',
        ]);

        $user = $request->user();

        // Uso firstOrCreate por si el usuario no tiene biblioteca aún
        $biblioteca = $user->biblioteca()->firstOrCreate(['user_id' => $user->id]);

        // Compruebo si ya lo tiene para no duplicar
        if ($biblioteca->libros()->where('libro_id', $request->libro_id)->exists()) {
            return $this->errorResponse('Este libro ya está la biblioteca', 409);
        }

        $estado = $request->estado_lectura;
        $fechaManual = $request->input('fecha_finalizacion');

        $fechaFinal = ($estado === 'leido')
            ? ($fechaManual ?? now())
            : null;

        // Asociamos el libro a la biblioteca del usuario
        $biblioteca->libros()->attach($request->libro_id, [
            'estado_lectura' => $estado,
            'fecha_finalizacion' => $fechaFinal,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return $this->successResponse(null, 'Libro añadido a tu biblioteca');
    }

    // Actualizar estado
    public function updateEstado(Request $request, $libroId) {
        $biblioteca = $request->user()->biblioteca;

        $biblioteca->libros()->updateExistingPivot($libroId, [
            'estado_lectura' => $request->estado_lectura,
            'fecha_finalizacion' =>
                $request->estado_lectura === 'leido' ? now() : null,
        ]);

        return $this->successResponse(null, 'Estado actualizado correctamente');
    }

    // Eliminar libro
    public function deleteLibro(Request $request, $libroId) {
        $biblioteca = $request->user()->biblioteca;

        $biblioteca->libros()->detach($libroId);

        return $this->successResponse(null, 'Libro eliminado de tu biblioteca');
    }
}
