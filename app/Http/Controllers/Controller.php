<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\JsonResponse;

abstract class Controller extends BaseController {
    use AuthorizesRequests;
    use ValidatesRequests;

    // OK
    protected function successResponse($data, string $message = 'Operación exitosa', int $code = 200): JsonResponse {
        return response()->json([
            'ok'      => true,
            'message' => $message,
            'data'    => $data,
        ], $code);
    }

    /**
     * Error
     */
    protected function errorResponse(string $message, int $code = 400, $errors = []): JsonResponse {
        return response()->json([
            'ok'      => false,
            'message' => $message,
            'errors'  => $errors,
        ], $code);
    }
}
