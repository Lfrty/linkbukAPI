<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
     ->withExceptions(function (Exceptions $exceptions) {

        $exceptions->render(function (Throwable $e, $request) {

            // Si es API (JSON)
            if ($request->expectsJson()) {

                $status = $e instanceof HttpExceptionInterface
                    ? $e->getStatusCode()
                    : 500;

                $mensajes = [
                    404 => 'Recurso no encontrado',
                    403 => 'No tienes permisos para acceder a esto',
                    500 => 'Error interno del servidor',
                ];

                return response()->json([
                    'ok' => false,
                    'error' => $mensajes[$status] ?? 'Error inesperado',
                ], $status);
            }

            // Web normal (HTML)
            $status = $e instanceof HttpExceptionInterface
                ? $e->getStatusCode()
                : 500;

            return response()->view('errors.custom', [
                'mensaje' => match ($status) {
                    404 => 'Lo que buscas no existe.',
                    403 => 'No tienes permiso para ver esto.',
                    500 => 'Algo explotó dentro del servidor.',
                    default => 'Error inesperado.',
                },
                'codigo' => $status
            ], $status);
        });

    })
    ->create();
