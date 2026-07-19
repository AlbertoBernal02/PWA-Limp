<?php

use App\Http\Controllers\Admin\AlmacenController;
use App\Http\Controllers\Admin\AusenciaController;
use App\Http\Controllers\Admin\ClienteController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FichajeController as AdminFichajeController;
use App\Http\Controllers\Admin\HerramientaController;
use App\Http\Controllers\Admin\OperarioController;
use App\Http\Controllers\Admin\TareaController as AdminTareaController;
use App\Http\Controllers\Operario\FichajeController as OperarioFichajeController;
use App\Http\Controllers\Operario\TareaController as OperarioTareaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PushSubscriptionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return auth()->user()->esAdmin()
        ? redirect()->route('admin.dashboard')
        : redirect()->route('operario.tareas.index');
})->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/push-subscriptions', [PushSubscriptionController::class, 'store'])->name('push-subscriptions.store');
    Route::delete('/push-subscriptions', [PushSubscriptionController::class, 'destroy'])->name('push-subscriptions.destroy');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('operarios', OperarioController::class)->except('show');
    Route::patch('operarios/{operario}/toggle-activo', [OperarioController::class, 'toggleActivo'])->name('operarios.toggle-activo');
    Route::post('operarios/{operario}/ausencias', [AusenciaController::class, 'store'])->name('operarios.ausencias.store');
    Route::delete('ausencias/{ausencia}', [AusenciaController::class, 'destroy'])->name('operarios.ausencias.destroy');
    Route::resource('clientes', ClienteController::class);
    Route::get('clientes/{cliente}/informe', [ClienteController::class, 'informe'])->name('clientes.informe');
    Route::resource('tareas', AdminTareaController::class)->except('show');
    Route::get('tareas/{tarea}/duplicar', [AdminTareaController::class, 'duplicarForm'])->name('tareas.duplicar');
    Route::post('tareas/{tarea}/duplicar', [AdminTareaController::class, 'duplicar'])->name('tareas.duplicar.store');
    Route::get('fichajes', [AdminFichajeController::class, 'index'])->name('fichajes.index');
    Route::get('fichajes/{operario}', [AdminFichajeController::class, 'show'])->name('fichajes.show');
    Route::get('fichajes/{operario}/informe', [AdminFichajeController::class, 'informeMensual'])->name('fichajes.informe');
    Route::post('fichajes/{operario}', [AdminFichajeController::class, 'store'])->name('fichajes.store');
    Route::delete('fichajes/registro/{fichaje}', [AdminFichajeController::class, 'destroy'])->name('fichajes.destroy');
    Route::resource('herramientas', HerramientaController::class)->except('show');
    Route::get('almacen', [AlmacenController::class, 'index'])->name('almacen.index');
    Route::post('almacen/filas', [AlmacenController::class, 'agregarFila'])->name('almacen.filas.store');
    Route::delete('almacen/filas/{fila}', [AlmacenController::class, 'eliminarFila'])->name('almacen.filas.destroy');
    Route::post('almacen/columnas', [AlmacenController::class, 'agregarColumna'])->name('almacen.columnas.store');
    Route::delete('almacen/columnas/{columna}', [AlmacenController::class, 'eliminarColumna'])->name('almacen.columnas.destroy');
    Route::post('almacen/huecos', [AlmacenController::class, 'crear'])->name('almacen.huecos.store');
    Route::post('almacen/huecos/{hueco}/asignar', [AlmacenController::class, 'asignar'])->name('almacen.huecos.asignar');
    Route::delete('almacen/huecos/{hueco}', [AlmacenController::class, 'destroy'])->name('almacen.huecos.destroy');
});

Route::middleware(['auth', 'role:operario'])->prefix('operario')->name('operario.')->group(function () {
    Route::get('tareas', [OperarioTareaController::class, 'index'])->name('tareas.index');
    Route::post('tareas/optimizar-ruta', [OperarioTareaController::class, 'optimizarRuta'])->name('tareas.optimizar-ruta');
    Route::get('tareas/historial', [OperarioTareaController::class, 'historial'])->name('tareas.historial');
    Route::patch('tareas/{tarea}/empezar', [OperarioTareaController::class, 'empezar'])->name('tareas.empezar');
    Route::patch('tareas/{tarea}/marcar-hecha', [OperarioTareaController::class, 'marcarHecha'])->name('tareas.marcar-hecha');
    Route::get('fichajes', [OperarioFichajeController::class, 'index'])->name('fichajes.index');
    Route::post('fichajes', [OperarioFichajeController::class, 'store'])->name('fichajes.store');
});

require __DIR__.'/auth.php';
