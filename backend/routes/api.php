<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RolController;
use App\Http\Controllers\Api\UsuarioController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');

// Ruta nombrada para que las peticiones no autenticadas no fallen al redirigir.
Route::get('/login', fn () => response()->json(['message' => 'No autenticado.'], 401))->name('login');

Route::middleware('auth:sanctum')->group(function (): void {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::middleware(['auth:sanctum', 'role:ADMINISTRADOR'])->group(function (): void {
    Route::get('/roles', [RolController::class, 'index']);
    Route::get('/usuarios', [UsuarioController::class, 'index']);
    Route::post('/usuarios', [UsuarioController::class, 'store']);
    Route::put('/usuarios/{usuario}', [UsuarioController::class, 'update']);
    Route::patch('/usuarios/{usuario}/estado', [UsuarioController::class, 'estado']);
    Route::delete('/usuarios/{usuario}', [UsuarioController::class, 'destroy']);
});
