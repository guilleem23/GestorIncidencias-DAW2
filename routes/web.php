<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
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
    Route::get('/admin/dashboard', function () {
        return view('admin.admin_dashboard_principal');
    })->name('admin.dashboard');

    Route::get('/admin/incidencias', function () {
        return view('admin.admin_dashboard_incidencias');
    })->name('admin.incidencias');

    Route::get('/admin/usuarios', [AdminController::class, 'index']);

    // CRUD Categorías
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
    Route::get('/admin/sedes', [SedeController::class, 'index'])->name('admin.sedes.index');
    Route::post('/admin/sedes', [SedeController::class, 'store'])->name('admin.sedes.store');
    Route::get('/admin/sedes/{id}/edit', [SedeController::class, 'edit'])->name('admin.sedes.edit');
    Route::put('/admin/sedes/{id}', [SedeController::class, 'update'])->name('admin.sedes.update');
    Route::delete('/admin/sedes/{id}', [SedeController::class, 'destroy'])->name('admin.sedes.destroy');
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
