<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('herramienta_tarea', function (Blueprint $table) {
            $table->unsignedInteger('cantidad_usada')->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('herramienta_tarea', function (Blueprint $table) {
            $table->dropColumn('cantidad_usada');
        });
    }
};
