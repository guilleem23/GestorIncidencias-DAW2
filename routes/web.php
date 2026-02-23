<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\IncidenciaController;
use App\Http\Controllers\UserController;

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
// Rutas del controlador de administrador
Route::middleware(['auth', 'role:administrador'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index']);
    // Gestión de usuarios
    Route::get('/admin/usuarios', [UserController::class, 'index'])->name('admin.usuarios.index');
    Route::get('/admin/usuarios/create', [UserController::class, 'create'])->name('admin.usuarios.create');
    Route::put('/admin/usuarios', [UserController::class, 'store'])->name('admin.usuarios.store');
    Route::get('/admin/usuarios/{id}/edit', [UserController::class, 'edit'])->name('admin.usuarios.edit');
    Route::put('/admin/usuarios/{id}', [UserController::class, 'update'])->name('admin.usuarios.update');
    Route::delete('/admin/usuarios/{id}', [UserController::class, 'destroy'])->name('admin.usuarios.destroy');
});

// Solo los clientes pueden entrar aquí
Route::middleware(['auth', 'role:client'])->group(function () {
    Route::get('/mis-incidencias', [IncidenciaController::class, 'index']);
});

//Solo los gestores pueden entrar aquí
Route::middleware(['auth', 'role:gestor'])->group(function () {
    Route::get('/gestor/incidencies', [IncidenciaController::class, 'indexGestor'])->name('gestor.index');
    Route::post('/gestor/assignar/{id}', [IncidenciaController::class, 'assignarTecnic'])->name('gestor.assignar');
});
