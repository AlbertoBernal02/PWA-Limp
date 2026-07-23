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
        Schema::table('tareas', function (Blueprint $table) {
            // El índice existente es compuesto (operario_id, fecha); la Agenda
            // consulta "todas las tareas de un mes, cualquier operario", que
            // necesita un índice propio sobre fecha para no acabar en full scan.
            $table->index('fecha');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tareas', function (Blueprint $table) {
            $table->dropIndex(['fecha']);
        });
    }
};
