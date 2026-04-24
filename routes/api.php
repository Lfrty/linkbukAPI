<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/test', function () {
    return response()->json(['ok' => true]);
});

// Opciones Usuario

// Registro
Route::post('/registrar', [AuthController::class, 'registrar']);

// Login
Route::post('/login', [AuthController::class, 'login']);

// Opciones logueado
Route::middleware('auth:sanctum')->group(function () {

    // Cerrar sesión
    Route::post('/logout', [AuthController::class, 'logout']);

    // Perfil
    Route::get('/user', fn (Request $request) => $request->user());

    // Ver Listas
    Route::get('/listas', [ListaController::class, 'index']);

});

Route::post('/listas', [ListaController::class, 'store']);

Route::delete('/listas/{id}', [ListaController::class, 'destroy']); // soft delete


Route::delete('/listas/{id}/force', [ListaController::class, 'forceDelete']);
Route::post('/listas/{id}/restore', [ListaController::class, 'restore']);