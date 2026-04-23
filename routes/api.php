<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/test', function () {
    return response()->json(['ok' => true]);
});

Route::post('/registrar', [AuthController::class, 'registrar']);