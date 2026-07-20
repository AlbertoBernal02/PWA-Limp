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
        Schema::create('hueco_trabajo', function (Blueprint $table) {
            $table->foreignId('hueco_id')->constrained()->cascadeOnDelete();
            $table->foreignId('trabajo_id')->constrained()->cascadeOnDelete();
            $table->primary(['hueco_id', 'trabajo_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hueco_trabajo');
    }
};
