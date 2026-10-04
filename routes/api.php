<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\V1\CategoryController;
use App\Http\Controllers\V1\ReserveController;
use App\Http\Controllers\V1\RoomController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rotas públicas (visitantes)
|--------------------------------------------------------------------------
| Rate limit apertado: 6 pedidos por minuto por IP.
| Previne brute-force no login e spam no registo.
*/

Route::middleware('throttle:6,1')->group(function () {
    Route::post('/register', [AuthController::class, 'register'])->name('auth.register');
    Route::post('/login',    [AuthController::class, 'login'])->name('auth.login');

    // Catálogo — leitura pública, visitante pode ver salas disponíveis
    Route::get('/rooms',        [RoomController::class, 'indexClient'])->name('rooms.public.index');
    Route::get('/rooms/{room}', [RoomController::class, 'show'])->name('rooms.public.show');
});

/*
|--------------------------------------------------------------------------
| Rotas autenticadas (qualquer user com token)
|--------------------------------------------------------------------------
| Operações do próprio user — não faz sentido prefixar /admin
| porque um user comum também faz logout, vê o próprio perfil, etc.
*/
Route::middleware('auth:sanctum')->group(function () {
    // Auth / perfil
    Route::post('/logout',     [AuthController::class, 'logout'])->name('auth.logout');
    Route::post('/logout-all', [AuthController::class, 'logoutAll'])->name('auth.logout-all');
    Route::get('/me',          [AuthController::class, 'me'])->name('auth.me');

    // Reservas do próprio user
    Route::get('/reserves',                          [ReserveController::class, 'index'])->name('reserves.index');
    Route::post('/reserves',                         [ReserveController::class, 'store'])->name('reserves.store');
    Route::get('/reserves/{reserve}',                [ReserveController::class, 'show'])->name('reserves.show');
    Route::put('/reserves/{reserve}/cancelation',    [ReserveController::class, 'cancelation'])->name('reserves.cancel');
});

/*
    |--------------------------------------------------------------------------
| Rotas admin
|--------------------------------------------------------------------------
| Dupla proteção:
|   1. auth:sanctum  → precisa de token válido
|   2. admin         → precisa de role = 'admin'
| Prefixo /admin para deixar claro na URL que é área restrita.
*/
Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Gestão de salas
    Route::get('/rooms',                 [RoomController::class, 'index'])->name('rooms.index');
    Route::post('/rooms',                [RoomController::class, 'store'])->name('rooms.store');
    Route::get('/rooms/{room}',          [RoomController::class, 'show'])->name('rooms.show');
    Route::put('/rooms/{room}',          [RoomController::class, 'update'])->name('rooms.update');
    Route::put('/rooms/{room}/disable',  [RoomController::class, 'disable'])->name('rooms.disable');
    Route::put('/rooms/{room}/enable',   [RoomController::class, 'enable'])->name('rooms.enable');

    // Reservas — admin vê/geral todas
    Route::get('/reserves',                       [ReserveController::class, 'indexAdmin'])->name('reserves.index');
    Route::get('/reserves/{reserve}',             [ReserveController::class, 'show'])->name('reserves.show');
    Route::put('/reserves/{reserve}/cancelation', [ReserveController::class, 'cancelation'])->name('reserves.cancel');

    // Categorias
    Route::get('/categories',              [CategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories',             [CategoryController::class, 'store'])->name('categories.store');
    Route::put('/categories/{category}',   [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
});
