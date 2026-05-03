<?php

namespace App\Http\Controllers;

use App\Models\Lista;
use App\Services\ListaService; // 🛠️ Importamos el nuevo servicio
use Illuminate\Http\Request;

class ListaController extends Controller {
    protected $listaService;

    // Para llamar al servicio de listas
    public function __construct(ListaService $listaService) {
        $this->listaService = $listaService;
    }


    public function index() {
        $data = $this->listaService->obtenerListas(auth()->user());
        return $this->successResponse($data, 'Listas recuperadas correctamente');
    }

    public function store(Request $request) {
        $request->validate([
            'nombre' => 'required|string|max:50'
        ]);

        // Para que el admin pueda crear listas a su nombre
        $usuarioId = auth()->user()->esAdmin()
            ? ($request->usuario_id ?? auth()->id())
            : auth()->id();

        $lista = $this->listaService->crearNuevaLista([
                    'usuario_id' => $usuarioId,
                    'nombre'     => $request->nombre
                ]);

        return $this->successResponse($lista, 'Lista creada con éxito', 201);
    }

    public function destroy($id) {
        $resultado = $this->listaService->eliminarLogic($id, auth()->user());

        if (!$resultado['success']) {
            return $this->errorResponse($resultado['message'], $resultado['code']);
        }

        return $this->successResponse(null, 'Lista enviada a la papelera');
    }

    public function forceDelete($id) {
        $exito = $this->listaService->eliminarDefinitivamente($id);

        if (!$exito) {
            return $this->errorResponse('Lista no encontrada', 404);
        }

        return $this->successResponse(null, 'Lista eliminada definitivamente');
    }

    public function restore($id) {
        $lista = $this->listaService->restaurar($id);

        if (!$lista) {
            return $this->errorResponse('Lista no encontrada o no pudo ser restaurada', 404);
        }

        return $this->successResponse($lista, 'Lista restaurada correctamente');
    }
}
