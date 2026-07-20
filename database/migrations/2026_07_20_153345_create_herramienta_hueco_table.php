<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Herramienta <-> Hueco pasa de "una herramienta, un hueco" (columna
     * hueco_id) a muchos-a-muchos: la misma herramienta puede guardarse
     * repartida en varios huecos a la vez.
     */
    public function up(): void
    {
        Schema::create('herramienta_hueco', function (Blueprint $table) {
            $table->foreignId('herramienta_id')->constrained()->cascadeOnDelete();
            $table->foreignId('hueco_id')->constrained()->cascadeOnDelete();
            $table->primary(['herramienta_id', 'hueco_id']);
        });

        // Migrar las asignaciones que ya existían en herramientas.hueco_id.
        DB::table('herramientas')
            ->whereNotNull('hueco_id')
            ->get(['id', 'hueco_id'])
            ->each(function ($herramienta) {
                DB::table('herramienta_hueco')->insert([
                    'herramienta_id' => $herramienta->id,
                    'hueco_id' => $herramienta->hueco_id,
                ]);
            });

        Schema::table('herramientas', function (Blueprint $table) {
            $table->dropConstrainedForeignId('hueco_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('herramientas', function (Blueprint $table) {
            $table->foreignId('hueco_id')->nullable()->constrained('huecos')->nullOnDelete();
        });

        DB::table('herramienta_hueco')->get()->groupBy('herramienta_id')->each(function ($filas, $herramientaId) {
            DB::table('herramientas')->where('id', $herramientaId)->update(['hueco_id' => $filas->first()->hueco_id]);
        });

        Schema::dropIfExists('herramienta_hueco');
    }
};
