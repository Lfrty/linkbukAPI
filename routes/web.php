<?php

use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test', function () {
    return response()->json([
        'status' => 'success',
        'message' => 'Hello World',
        'data' => [
            'framework' => 'Laravel',
            'version' => app()->version(),
        ]
    ], 200);
});
