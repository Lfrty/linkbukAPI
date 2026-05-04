<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Captura como un 401 para cuando no hay Auth.
        $middleware->redirectGuestsTo(fn () => null);

        $middleware->alias([
            'checkRol' => \App\Http\Middleware\CheckRol::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {

        $exceptions->render(function (Throwable $e, Request $request) {

            // Solo actuamos si la petición pide JSON (API)
            if ($request->is('api/*') || $request->expectsJson()) {

                if ($e instanceof \Illuminate\Auth\AuthenticationException) {
                    return response()->json([
                        'ok' => false,
                        'code' => 401, // <-- Código explícito
                        'error' => 'No estás autenticado o el token ha expirado',
                    ], 401);
                }

                if ($e instanceof ValidationException) {
                    return response()->json([
                        'ok' => false,
                        'message' => 'Errores de validación',
                        'errors' => $e->errors(),
                    ], 422);
                }

                // 2. Determinar el status code
                $status = $e instanceof HttpExceptionInterface ? $e->getStatusCode() : 500;

                // 3. Respuesta estructurada
                return response()->json([
                    'ok' => false,
                    'error' => match ($status) {
                        404 => 'Recurso no encontrado',
                        403 => 'No tienes permisos',
                        401 => 'No autenticado',
                        405 => 'Método no permitido',
                        default => config('app.debug')
                                   ? $e->getMessage() // En desarrollo: el error real
                                   : 'Error interno del servidor',
                    },
                    // Añadimos el archivo y línea solo en debug para arreglar ese 500 rápido
                    'debug' => config('app.debug') ? [
                        'class' => get_class($e),
                        'file' => $e->getFile(),
                        'line' => $e->getLine(),
                    ] : null,
                ], $status);
            }
        });
    })
    ->create();
