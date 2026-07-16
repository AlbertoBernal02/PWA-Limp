<?php

use App\Http\Controllers\Admin\ClienteController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OperarioController;
use App\Http\Controllers\Admin\TareaController as AdminTareaController;
use App\Http\Controllers\Operario\TareaController as OperarioTareaController;
use App\Http\Controllers\ProfileController;
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
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('operarios', OperarioController::class)->except('show');
    Route::resource('clientes', ClienteController::class)->except('show');
    Route::resource('tareas', AdminTareaController::class)->except('show');
});

Route::middleware(['auth', 'role:operario'])->prefix('operario')->name('operario.')->group(function () {
    Route::get('tareas', [OperarioTareaController::class, 'index'])->name('tareas.index');
    Route::patch('tareas/{tarea}/marcar-hecha', [OperarioTareaController::class, 'marcarHecha'])->name('tareas.marcar-hecha');
});

require __DIR__.'/auth.php';
