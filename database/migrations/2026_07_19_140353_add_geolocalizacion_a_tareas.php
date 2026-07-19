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
            $table->decimal('iniciada_lat', 10, 7)->nullable()->after('iniciada_at');
            $table->decimal('iniciada_lng', 10, 7)->nullable()->after('iniciada_lat');
            $table->decimal('completada_lat', 10, 7)->nullable()->after('completada_at');
            $table->decimal('completada_lng', 10, 7)->nullable()->after('completada_lat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tareas', function (Blueprint $table) {
            $table->dropColumn(['iniciada_lat', 'iniciada_lng', 'completada_lat', 'completada_lng']);
        });
    }
};
