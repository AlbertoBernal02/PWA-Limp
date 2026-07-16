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
        Schema::table('users', function (Blueprint $table) {
            $table->string('avatar_path')->nullable()->after('email');
        });

        Schema::table('clientes', function (Blueprint $table) {
            $table->string('foto_path')->nullable()->after('notas');
        });

        Schema::table('tareas', function (Blueprint $table) {
            $table->string('foto_evidencia_path')->nullable()->after('completada_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('avatar_path');
        });

        Schema::table('clientes', function (Blueprint $table) {
            $table->dropColumn('foto_path');
        });

        Schema::table('tareas', function (Blueprint $table) {
            $table->dropColumn('foto_evidencia_path');
        });
    }
};
