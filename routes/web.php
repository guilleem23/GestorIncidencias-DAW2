<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminIncidenciaController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\IncidenciaController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SedeController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\TecnicController;

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Ruta protegida para el dashboard
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware('auth');

Route::get('/', function () {
    return redirect()->route('login');
});

// Solo los administradores pueden entrar aquí
Route::middleware(['auth', 'role:administrador'])->group(function () {
    Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/admin/resum', [AdminDashboardController::class, 'resum'])->name('admin.resum');
    Route::get('/admin/resum/sedes', [AdminDashboardController::class, 'resumSedes'])->name('admin.resum.sedes');
    Route::get('/admin/resum/{id}', [AdminDashboardController::class, 'resumData'])->name('admin.resum.data');

    Route::get('/admin/incidencias', [AdminIncidenciaController::class, 'index'])->name('admin.incidencias');
    Route::get('/admin/incidencias/{id}', [AdminIncidenciaController::class, 'show'])->name('admin.incidencias.show');
    Route::get('/admin/incidencias/{id}/edit', [AdminIncidenciaController::class, 'edit'])->name('admin.incidencias.edit');
    Route::put('/admin/incidencias/{id}', [AdminIncidenciaController::class, 'update'])->name('admin.incidencias.update');
    Route::delete('/admin/incidencias/{id}', [AdminIncidenciaController::class, 'destroy'])->name('admin.incidencias.destroy');
    Route::post('/admin/incidencias/{id}/assign', [AdminIncidenciaController::class, 'assignTecnic'])->name('admin.incidencias.assign');
    Route::post('/admin/incidencias/{id}/comentarios', [AdminIncidenciaController::class, 'storeComentario'])->name('admin.incidencias.comentarios.store');
    Route::delete('/admin/comentarios/{id}', [AdminIncidenciaController::class, 'destroyComentario'])->name('admin.comentarios.destroy');
    Route::get('/admin/comentarios/{id}/edit', [AdminIncidenciaController::class, 'editComentario'])->name('admin.comentarios.edit');
    Route::put('/admin/comentarios/{id}', [AdminIncidenciaController::class, 'updateComentario'])->name('admin.comentarios.update');

    // Gestión de usuarios
    Route::get('/admin/usuarios/check-email', [UserController::class, 'checkEmail']);
    Route::get('/admin/usuarios/check-username', [UserController::class, 'checkUsername']);
    Route::get('/admin/usuarios/check-gestor', [UserController::class, 'checkSedeGestor']);
    Route::get('/admin/usuarios', [UserController::class, 'index'])->name('admin.usuarios.index');
    Route::get('/admin/usuarios/create', [UserController::class, 'create'])->name('admin.usuarios.create');
    Route::get('/admin/usuarios/{id}', [UserController::class, 'show'])->name('admin.usuarios.show');
    Route::post('/admin/usuarios', [UserController::class, 'store'])->name('admin.usuarios.store');
    Route::get('/admin/usuarios/{id}/edit', [UserController::class, 'edit'])->name('admin.usuarios.edit');
    Route::put('/admin/usuarios/{id}', [UserController::class, 'update'])->name('admin.usuarios.update');
    Route::delete('/admin/usuarios/{id}', [UserController::class, 'destroy'])->name('admin.usuarios.destroy');

    // CRUD Categorías
    Route::get('/admin/categorias/check-nom', [CategoriaController::class, 'checkNom']);
    Route::get('/admin/subcategorias/check-nom', [CategoriaController::class, 'checkNomSubcategoria']);
    Route::get('/admin/categorias', [CategoriaController::class, 'index'])->name('admin.categorias.index');
    Route::post('/admin/categorias', [CategoriaController::class, 'store'])->name('admin.categorias.store');
    Route::get('/admin/categorias/{id}/edit', [CategoriaController::class, 'edit'])->name('admin.categorias.edit');
    Route::put('/admin/categorias/{id}', [CategoriaController::class, 'update'])->name('admin.categorias.update');
    Route::delete('/admin/categorias/{id}', [CategoriaController::class, 'destroy'])->name('admin.categorias.destroy');

    // CRUD Subcategorías
    Route::post('/admin/subcategorias', [CategoriaController::class, 'storeSubcategoria'])->name('admin.subcategorias.store');
    Route::get('/admin/subcategorias/{id}/edit', [CategoriaController::class, 'editSubcategoria'])->name('admin.subcategorias.edit');
    Route::put('/admin/subcategorias/{id}', [CategoriaController::class, 'updateSubcategoria'])->name('admin.subcategorias.update');
    Route::delete('/admin/subcategorias/{id}', [CategoriaController::class, 'destroySubcategoria'])->name('admin.subcategorias.destroy');

    // CRUD Sedes
    Route::get('/admin/sedes/check-nombre', [SedeController::class, 'checkNombre']);
    Route::get('/admin/sedes', [SedeController::class, 'index'])->name('admin.sedes.index');
    Route::post('/admin/sedes', [SedeController::class, 'store'])->name('admin.sedes.store');
    Route::get('/admin/sedes/{id}/edit', [SedeController::class, 'edit'])->name('admin.sedes.edit');
    Route::put('/admin/sedes/{id}', [SedeController::class, 'update'])->name('admin.sedes.update');
    Route::delete('/admin/sedes/{id}', [SedeController::class, 'destroy'])->name('admin.sedes.destroy');
});

// Solo los clientes pueden entrar aquí
Route::middleware(['auth', 'role:client'])->group(function () {
    Route::get('/client/mis-incidencias', [ClientController::class, 'index'])->name('client.index');
    Route::post('/client/mis-incidencias', [ClientController::class, 'index'])->name('client.index.filter');
    Route::get('/client/incidencias/{id}', [ClientController::class, 'verIncidencia'])->name('client.incidencias.show');
    Route::get('/client/crear', [ClientController::class, 'crear'])->name('client.crear');
    Route::post('/client/crear', [ClientController::class, 'store'])->name('client.store');

    Route::post('/client/tancar/{id}', [ClientController::class, 'tancarIncidencia'])->name('client.tancar');
    Route::post('/client/incidencias/{id}/comentarios', [ClientController::class, 'storeComentario'])->name('client.incidencias.comentarios.store');
    Route::put('/client/comentarios/{id}', [ClientController::class, 'updateComentario'])->name('client.comentarios.update');
    Route::delete('/client/comentarios/{id}', [ClientController::class, 'destroyComentario'])->name('client.comentarios.destroy');
});

//Solo los gestores pueden entrar aquí
Route::middleware(['auth', 'role:gestor'])->group(function () {
    Route::get('/gestor/asignar_incidencias', [IncidenciaController::class, 'indexGestor'])->name('gestor.index');
    Route::get('/gestor/incidencias', [IncidenciaController::class, 'indexGestorTodas'])->name('gestor.incidencias');
    Route::get('/gestor/incidencias/{id}', [IncidenciaController::class, 'showGestor'])->name('gestor.incidencias.show');
    Route::get('/gestor/incidencias/{id}/edit', [IncidenciaController::class, 'editGestor'])->name('gestor.incidencias.edit');
    Route::put('/gestor/incidencias/{id}', [IncidenciaController::class, 'updateGestor'])->name('gestor.incidencias.update');
    Route::post('/gestor/incidencias/{id}/comentarios', [IncidenciaController::class, 'storeComentarioGestor'])->name('gestor.incidencias.comentarios.store');
    Route::delete('/gestor/incidencias/{id}', [IncidenciaController::class, 'destroyGestor'])->name('gestor.incidencias.destroy');
    Route::delete('/gestor/comentarios/{id}', [IncidenciaController::class, 'destroyComentarioGestor'])->name('gestor.comentarios.destroy');
    Route::get('/gestor/comentarios/{id}/edit', [IncidenciaController::class, 'editComentarioGestor'])->name('gestor.comentarios.edit');
    Route::put('/gestor/comentarios/{id}', [IncidenciaController::class, 'updateComentarioGestor'])->name('gestor.comentarios.update');
    Route::get('/gestor/usuarios', [UserController::class, 'indexGestor'])->name('gestor.usuarios');
    Route::get('/gestor/usuarios/{id}', [UserController::class, 'showGestor'])->name('gestor.usuarios.show');
    Route::post('/gestor/assignar/{id}', [IncidenciaController::class, 'assignarTecnic'])->name('gestor.assignar');
});

// Solo los técnicos pueden entrar aquí
Route::middleware(['auth', 'role:tecnic'])->group(function () {
    Route::get('/tecnic/tasques', [TecnicController::class, 'index'])->name('tecnic.index');
    Route::get('/tecnic/totes-tasques', [TecnicController::class, 'totesTasques'])->name('tecnic.totes');
    Route::get('/tecnic/incidencias/{id}', [TecnicController::class, 'show'])->name('tecnic.incidencias.show');
    Route::post('/tecnic/iniciar/{id}', [TecnicController::class, 'iniciarTreball'])->name('tecnic.iniciar');
    Route::post('/tecnic/resoldre/{id}', [TecnicController::class, 'marcarResolta'])->name('tecnic.resoldre');
    Route::post('/tecnic/incidencias/{id}/comentarios', [TecnicController::class, 'storeComentario'])->name('tecnic.incidencias.comentarios.store');
});
