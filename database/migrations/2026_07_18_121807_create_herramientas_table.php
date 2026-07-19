<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('herramientas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->unsignedInteger('cantidad')->default(0);
            $table->timestamps();
        });

        Schema::create('herramienta_tarea', function (Blueprint $table) {
            $table->foreignId('herramienta_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tarea_id')->constrained()->cascadeOnDelete();
            $table->primary(['herramienta_id', 'tarea_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('herramienta_tarea');
        Schema::dropIfExists('herramientas');
    }
};
