<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\IncidenciaController;
use App\Http\Controllers\TecnicController;
use App\Http\Controllers\ClientController;
use Illuminate\Support\Facades\Route;

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
    Route::get('/admin/usuarios', [AdminController::class, 'index'])->name('admin.usuarios.index');
    Route::get('/admin/usuarios/listado', [AdminController::class, 'listado'])->name('admin.usuarios.listado');
    Route::get('/admin/usuarios/create', [AdminController::class, 'create'])->name('admin.usuarios.create');
    Route::post('/admin/usuarios', [AdminController::class, 'store'])->name('admin.usuarios.store');
    Route::post('/admin/usuarios/{id}/baja', [AdminController::class, 'darBaja'])->name('admin.usuarios.baja');
    Route::post('/admin/usuarios/{id}/alta', [AdminController::class, 'darAlta'])->name('admin.usuarios.alta');
    Route::get('/admin/categorias', [AdminController::class, 'categorias'])->name('admin.categorias.index');
});

// Solo los clientes pueden entrar aquí
Route::middleware(['auth', 'role:client'])->group(function () {
    Route::get('/client/mis-incidencias', [ClientController::class, 'index'])->name('client.index');
    Route::get('/client/crear', [ClientController::class, 'crear'])->name('client.crear');
    Route::post('/client/crear', [ClientController::class, 'store'])->name('client.store');
    Route::post('/client/tancar/{id}', [ClientController::class, 'tancarIncidencia'])->name('client.tancar');
});

//Solo los gestores pueden entrar aquí
Route::middleware(['auth', 'role:gestor'])->group(function () {
    Route::get('/gestor/incidencies', [IncidenciaController::class, 'indexGestor'])->name('gestor.index');
    Route::post('/gestor/assignar/{id}', [IncidenciaController::class, 'assignarTecnic'])->name('gestor.assignar');
});

// Solo los técnicos pueden entrar aquí
Route::middleware(['auth', 'role:tecnic'])->group(function () {
    Route::get('/tecnic/tasques', [TecnicController::class, 'index'])->name('tecnic.index');
    Route::post('/tecnic/iniciar/{id}', [TecnicController::class, 'iniciarTreball'])->name('tecnic.iniciar');
    Route::post('/tecnic/resoldre/{id}', [TecnicController::class, 'marcarResolta'])->name('tecnic.resoldre');
});
