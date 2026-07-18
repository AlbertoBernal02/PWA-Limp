<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fichajes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operario_id')->constrained('users')->cascadeOnDelete();
            $table->enum('tipo', ['entrada', 'inicio_descanso', 'fin_descanso', 'salida']);
            $table->dateTime('fichado_en');
            $table->boolean('creado_por_admin')->default(false);
            $table->timestamps();

            $table->index(['operario_id', 'fichado_en']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fichajes');
    }
};
