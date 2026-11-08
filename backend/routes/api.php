<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CatalogoController;
use App\Http\Controllers\Api\DanioController;
use App\Http\Controllers\Api\FilaController;
use App\Http\Controllers\Api\IngresoController;
use App\Http\Controllers\Api\InspeccionController;
use App\Http\Controllers\Api\PerfilController;
use App\Http\Controllers\Api\RolController;
use App\Http\Controllers\Api\UsuarioController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');

// Ruta nombrada para que las peticiones no autenticadas no fallen al redirigir.
Route::get('/login', fn () => response()->json(['message' => 'No autenticado.'], 401))->name('login');

Route::middleware('auth:sanctum')->group(function (): void {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/perfil', [PerfilController::class, 'show']);
    Route::put('/perfil', [PerfilController::class, 'update']);
    Route::put('/perfil/password', [PerfilController::class, 'password']);

    Route::get('/filas', [FilaController::class, 'index']);

    Route::get('/catalogos', [CatalogoController::class, 'index']);
    Route::get('/ingresos', [IngresoController::class, 'index']);
    Route::get('/ingresos/estimar', [IngresoController::class, 'estimar']);
    Route::get('/ingresos/{ingreso}/preview', [IngresoController::class, 'preview']);
    Route::get('/ingresos/{ingreso}/danios', [DanioController::class, 'index']);
    Route::get('/ingresos/{ingreso}', [IngresoController::class, 'show']);
});

Route::middleware(['auth:sanctum', 'role:ADMINISTRADOR,OPERADOR'])->group(function (): void {
    Route::post('/ingresos', [IngresoController::class, 'store']);
    Route::post('/ingresos/{ingreso}/cobrar', [IngresoController::class, 'cobrar']);
    Route::post('/ingresos/{ingreso}/inspeccion', [InspeccionController::class, 'store']);
    Route::post('/ingresos/{ingreso}/danios', [DanioController::class, 'store']);
    Route::put('/danios/{danio}', [DanioController::class, 'update']);
    Route::delete('/danios/{danio}', [DanioController::class, 'destroy']);
});

Route::middleware(['auth:sanctum', 'role:ADMINISTRADOR'])->group(function (): void {
    Route::get('/roles', [RolController::class, 'index']);
    Route::put('/filas/{fila}', [FilaController::class, 'update']);
    Route::get('/usuarios', [UsuarioController::class, 'index']);
    Route::post('/usuarios', [UsuarioController::class, 'store']);
    Route::put('/usuarios/{usuario}', [UsuarioController::class, 'update']);
    Route::patch('/usuarios/{usuario}/estado', [UsuarioController::class, 'estado']);
    Route::delete('/usuarios/{usuario}', [UsuarioController::class, 'destroy']);
});
