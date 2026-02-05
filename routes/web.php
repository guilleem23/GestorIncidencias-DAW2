<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\IncidenciaController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Ruta protegida para el dashboard
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware('auth');

Route::get('/', function () {
    return view('welcome');
    // CAMBIAR LUEGO POR LOGIN 
});
// Solo los administradores pueden entrar aquí
Route::middleware(['auth', 'role:administrador'])->group(function () {
    Route::get('/admin/usuarios', [AdminController::class, 'index']);
});

// Solo los clientes pueden entrar aquí
Route::middleware(['auth', 'role:client'])->group(function () {
    Route::get('/mis-incidencias', [IncidenciaController::class, 'index']);
});

// RUTAS DE PRUEBA, BORRAR DESPUÉS
Route::get('/admin/usuarios', function() { return "Panel de Administrador"; })->middleware(['auth', 'role:administrador']);
Route::get('/client/mis-incidencias', function() { return "Mis Incidencias como Cliente"; })->middleware(['auth', 'role:client']);
Route::get('/tecnic/tasques', function() { return "Tareas del Técnico"; })->middleware(['auth', 'role:tecnic']);

//Solo los gestores pueden entrar aquí
Route::middleware(['auth', 'role:gestor'])->group(function () {
    Route::get('/gestor/incidencies', [IncidenciaController::class, 'indexGestor'])->name('gestor.index');
    Route::post('/gestor/assignar/{id}', [IncidenciaController::class, 'assignarTecnic'])->name('gestor.assignar');
});

//Cambiar el estado de la incidencia
Route::post('tecnic/incidencia/{id}/estat', [IncidenciaController::class, 'updateEstat'])->name('tecnic.updateEstat')->middleware(['auth', 'role:tecnic']);