<?php

use App\Http\Controllers\AuthController;

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Ruta protegida para el dashboard
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware('auth');

Route::get('/', function () {
    return view('auth.login');
});
// Solo los administradores pueden entrar aquí
Route::middleware(['auth', 'role:administrador'])->group(function () {
    Route::get('/admin/usuarios', [AdminController::class, 'index']);
});

// Solo los clientes pueden entrar aquí
Route::middleware(['auth', 'role:client'])->group(function () {
    Route::get('/mis-incidencias', [IncidenciaController::class, 'index']);
});
