<?php

use App\Http\Controllers\Admin\AgendaController;
use App\Http\Controllers\Admin\AlmacenController;
use App\Http\Controllers\Admin\AusenciaController;
use App\Http\Controllers\Admin\ClienteController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FichajeController as AdminFichajeController;
use App\Http\Controllers\Admin\HerramientaController;
use App\Http\Controllers\Admin\OperarioController;
use App\Http\Controllers\Admin\TareaController as AdminTareaController;
use App\Http\Controllers\Admin\TrabajoController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\DocumentacionController;
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
    Route::get('documentacion/{seccion?}', [DocumentacionController::class, 'index'])->name('documentacion.index');
    Route::post('documentacion/chat', [ChatController::class, 'responder'])
        ->middleware('throttle:chat-ia')
        ->name('documentacion.chat');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('operarios', OperarioController::class)->except('show');
    Route::get('operarios/exportar', [OperarioController::class, 'exportar'])->name('operarios.exportar');
    Route::patch('operarios/{operario}/toggle-activo', [OperarioController::class, 'toggleActivo'])->name('operarios.toggle-activo');
    Route::post('operarios/{operario}/ausencias', [AusenciaController::class, 'store'])->name('operarios.ausencias.store');
    Route::delete('ausencias/{ausencia}', [AusenciaController::class, 'destroy'])->name('operarios.ausencias.destroy');
    // La ruta de exportar debe ir ANTES del resource: clientes/{cliente} (show) también
    // matchea "clientes/exportar" si se registra primero, y "exportar" se interpretaría como un id.
    Route::get('clientes/exportar', [ClienteController::class, 'exportar'])->name('clientes.exportar');
    Route::resource('clientes', ClienteController::class);
    Route::get('clientes/{cliente}/informe', [ClienteController::class, 'informe'])->name('clientes.informe');
    Route::resource('tareas', AdminTareaController::class)->except('show');
    Route::get('tareas/exportar', [AdminTareaController::class, 'exportar'])->name('tareas.exportar');
    Route::get('tareas/{tarea}/duplicar', [AdminTareaController::class, 'duplicarForm'])->name('tareas.duplicar');
    Route::post('tareas/{tarea}/duplicar', [AdminTareaController::class, 'duplicar'])->name('tareas.duplicar.store');
    Route::get('agenda', [AgendaController::class, 'index'])->name('agenda.index');
    Route::post('agenda/notas', [AgendaController::class, 'storeNota'])->name('agenda.notas.store');
    Route::delete('agenda/notas/{nota}', [AgendaController::class, 'destroyNota'])->name('agenda.notas.destroy');
    Route::get('fichajes', [AdminFichajeController::class, 'index'])->name('fichajes.index');
    // Igual que con clientes: exportar debe ir antes de fichajes/{operario} (show).
    Route::get('fichajes/exportar', [AdminFichajeController::class, 'exportar'])->name('fichajes.exportar');
    Route::get('fichajes/{operario}', [AdminFichajeController::class, 'show'])->name('fichajes.show');
    Route::get('fichajes/{operario}/informe', [AdminFichajeController::class, 'informeMensual'])->name('fichajes.informe');
    Route::post('fichajes/{operario}', [AdminFichajeController::class, 'store'])->name('fichajes.store');
    Route::delete('fichajes/registro/{fichaje}', [AdminFichajeController::class, 'destroy'])->name('fichajes.destroy');
    Route::resource('herramientas', HerramientaController::class)->except('show');
    Route::get('herramientas/exportar', [HerramientaController::class, 'exportar'])->name('herramientas.exportar');
    Route::resource('trabajos', TrabajoController::class)->except('show');
    Route::get('trabajos/exportar', [TrabajoController::class, 'exportar'])->name('trabajos.exportar');
    Route::get('almacen', [AlmacenController::class, 'index'])->name('almacen.index');
    Route::post('almacen/filas', [AlmacenController::class, 'agregarFila'])->name('almacen.filas.store');
    Route::delete('almacen/filas/{fila}', [AlmacenController::class, 'eliminarFila'])->name('almacen.filas.destroy');
    Route::post('almacen/columnas', [AlmacenController::class, 'agregarColumna'])->name('almacen.columnas.store');
    Route::delete('almacen/columnas/{columna}', [AlmacenController::class, 'eliminarColumna'])->name('almacen.columnas.destroy');
    Route::post('almacen/huecos', [AlmacenController::class, 'crear'])->name('almacen.huecos.store');
    Route::post('almacen/huecos/{hueco}/asignar', [AlmacenController::class, 'asignar'])->name('almacen.huecos.asignar');
    Route::delete('almacen/huecos/{hueco}', [AlmacenController::class, 'destroy'])->name('almacen.huecos.destroy');
    Route::post('almacen/huecos/{hueco}/herramientas', [AlmacenController::class, 'agregarHerramienta'])->name('almacen.huecos.herramientas.store');
    Route::delete('almacen/huecos/{hueco}/herramientas/{herramienta}', [AlmacenController::class, 'quitarHerramienta'])->name('almacen.huecos.herramientas.destroy');
    Route::post('almacen/huecos/{hueco}/trabajos', [AlmacenController::class, 'agregarTrabajo'])->name('almacen.huecos.trabajos.store');
    Route::delete('almacen/huecos/{hueco}/trabajos/{trabajo}', [AlmacenController::class, 'quitarTrabajo'])->name('almacen.huecos.trabajos.destroy');
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
