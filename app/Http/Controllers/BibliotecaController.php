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
        $biblioteca = $user->biblioteca()->firstOrCreate(['usuario_id' => $user->id]);

        $estado = $request->estado_lectura;
        $fechaManual = $request->input('fecha_finalizacion');
        $fechaFinal = ($estado === 'leido') ? ($fechaManual ?? now()) : null;

        // Preparamos los datos de la tabla pivote
        $datosPivote = [
            'estado_lectura' => $estado,
            'fecha_finalizacion' => $fechaFinal,
            'updated_at' => now(),
        ];

        if ($biblioteca->libros()->where('libro_id', $request->libro_id)->exists()) {
            // Si ya existe, actualizo estado
            $biblioteca->libros()->updateExistingPivot($request->libro_id, $datosPivote);
            $mensaje = 'Estado del libro actualizado';
        } else {
            // Si no existe, lo añado
            $datosPivote['created_at'] = now();
            $biblioteca->libros()->attach($request->libro_id, $datosPivote);
            $mensaje = 'Libro añadido a tu biblioteca';
        }

        return $this->successResponse(null, $mensaje);
    }

    // Actualizar estado
    public function updateEstado(Request $request, $libroId) {
        $biblioteca = $request->auth()->user()->biblioteca;

        $biblioteca->libros()->updateExistingPivot($libroId, [
            'estado_lectura' => $request->estado_lectura,
            'fecha_finalizacion' =>
                $request->estado_lectura === 'leido' ? now() : null,
        ]);

        return $this->successResponse(null, 'Estado actualizado correctamente');
    }

    // Eliminar libro
    public function deleteLibro(Request $request) {
        $request->validate([
        'id' => 'required|exists:libros,id'
    ]);

        $user = $request->user();

        if (!$user->biblioteca) {
            return $this->errorResponse('Biblioteca no encontrada', 404);
        }

        $request->user()->biblioteca->libros()->detach($request->id);

        return $this->successResponse(null, 'Libro eliminado de tu biblioteca');
    }
}
