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
// Rutas de prueba para verificar las redirecciones
Route::get('/admin/usuarios', function() { return "Panel de Administrador"; })->middleware(['auth', 'role:administrador']);
Route::get('/client/mis-incidencias', function() { return "Mis Incidencias como Cliente"; })->middleware(['auth', 'role:client']);
Route::get('/tecnic/tasques', function() { return "Tareas del Técnico"; })->middleware(['auth', 'role:tecnic']);
Route::get('/gestor/incidencies', function() { return "Panel del Gestor de Sede"; })->middleware(['auth', 'role:gestor']);
