<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tareas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->foreignId('operario_id')->constrained('users')->cascadeOnDelete();
            $table->date('fecha');
            $table->time('hora_estimada')->nullable();
            $table->text('descripcion')->nullable();
            $table->enum('estado', ['pendiente', 'hecha'])->default('pendiente');
            $table->timestamp('completada_at')->nullable();
            $table->timestamps();

            $table->index(['operario_id', 'fecha']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tareas');
    }
};
