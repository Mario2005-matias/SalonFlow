<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\V1\CategoryController;
use App\Http\Controllers\V1\ReserveController;
use App\Http\Controllers\V1\RoomController;
use Illuminate\Support\Facades\Route;

Route::middleware('throttle:6,1')->group(function () {
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/login', [AuthController::class, 'login'])->name('login');

    Route::get('/rooms', [RoomController::class, 'indexClient'])->name('room.index');
    Route::get('/rooms/{room}', [RoomController::class, 'show'])->name('room.show');

    Route::get('/reserves', [ReserveController::class, 'index'])->name('reserve.index');
    Route::post('/reserves', [ReserveController::class, 'store'])->name('reserve.create');
    Route::get('/reserves/{reserve}', [ReserveController::class, 'show'])->name('reserve.show');
    Route::put('/reserves/{reserve}/cancelation', [ReserveController::class, 'cancelation'])->name('reserve.update');
});

Route::middleware('auth:sanctum')->prefix('admin')->group(function () {
    Route::post('/logout-all', [AuthController::class, 'logoutAll'])->name('logout-all');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/perfil', [AuthController::class, 'me'])->name('me');

    Route::get('/rooms', [RoomController::class, 'index'])->name('room.index');
    Route::post('/room', [RoomController::class, 'store'])->name('room.create');
    Route::get('/rooms/{room}', [RoomController::class, 'show'])->name('room.show');
    Route::put('/rooms/{room}', [RoomController::class, 'update'])->name('room.update');
    Route::put('/rooms/{room}/disable', [RoomController::class, 'disable'])->name('room.disable');
    Route::put('/rooms/{room}/enable', [RoomController::class, 'enable'])->name('room.enable');

    Route::get('/reserves', [ReserveController::class, 'index'])->name('reserve.index');
    Route::get('/reserves/{reserve}', [ReserveController::class, 'show'])->name('reserve.show');
    Route::post('/reserves', [ReserveController::class, 'store'])->name('reserve.create');
    Route::put('/reserves/{reserve}/cancelation', [ReserveController::class, 'cancelation'])->name('reserve.update');

    Route::get('/categories', [CategoryController::class, 'index']);
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::put('/categories/{category}', [CategoryController::class, 'update']);
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);
});
