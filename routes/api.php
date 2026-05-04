<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ListaController;
use App\Http\Controllers\LibroController;
use App\Http\Controllers\BibliotecaController;
use App\Http\Controllers\ResenasController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/test', function () {
    return response()->json(['ok' => true]);
});

// Opciones Usuario

// Registro
Route::post('/auth/registrar', [AuthController::class, 'registrar']);

// Login
Route::post('/auth/login', [AuthController::class, 'login']);

// Buscar libro
Route::get('/libros/search', [LibroController::class, 'search']);

// Get Work
Route::get('/libros/detalle/{workKey}', [LibroController::class, 'show']);

// Opciones logueado
Route::middleware('auth:sanctum')->group(function () {

    /**
     * Usuario
     */
    // Cerrar sesión
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    // Get Perfil
    Route::get('/user', fn (Request $request) => $request->user());

    // Editar perfil
    Route::put('/user', [AuthController::class, 'edit']);
    /**
     * Biblioteca usuario
     */

    Route::get('/biblioteca', [BibliotecaController::class, 'index']);

    Route::post('biblioteca/nuevo', [BibliotecaController::class, 'addLibro']);

    Route::post('biblioteca/estadoLibro/{libroId}', [BibliotecaController::class, 'updateEstado']);

    Route::delete('biblioteca', [BibliotecaController::class, 'deleteLibro']);



    /**
     * LISTAS
     */
    Route::get('/listas/all', [ListaController::class, 'index']);

    Route::post('/listas/new', [ListaController::class, 'store']);

    Route::delete('/listas/{id}', [ListaController::class, 'destroy']); // soft delete

    // Metadatos de la lista
    Route::put('listas/{id}', [ListaController::class, 'update']);

    // Lista respecto a libros
    Route::delete('listas/{id}/libros/{libro_id}', [ListaController::class, 'deleteLibro']);

    Route::post('/listas/{id}/addLibro', [ListaController::class, 'addLibro']);

    /**
     * Reseñas
     */
    Route::get('/resenas', [ResenasController::class, 'index']);

    Route::post('/resenas/new', [ResenasController::class, 'store']);

    Route::delete('/resenas/{id}', [ResenasController::class, 'destroy']); // soft delete

    // --- RUTAS DE ADMINISTRACIÓN Y SUPERVISIÓN ---

    // Solo Admin
    Route::middleware('checkRol:admin')->group(function () {
        Route::delete('/listas/{id}/force', [ListaController::class, 'forceDelete']);
    });

    Route::middleware('checkRol:admin')->group(function () {
        Route::post('/listas/{id}/restore', [ListaController::class, 'restore']);
    });

});
