<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\AvisoTareasDelDia;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Throwable;

#[Signature('app:avisar-tareas-del-dia')]
#[Description('Avisa a cada operario activo (por email y/o push) de cuántas tareas tiene pendientes hoy')]
class AvisarTareasDelDia extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $operarios = User::where('role', 'operario')
            ->where('activo', true)
            ->get();

        $avisados = 0;

        foreach ($operarios as $operario) {
            $total = $operario->tareas()
                ->whereDate('fecha', today())
                ->where('estado', 'pendiente')
                ->count();

            if ($total === 0) {
                continue;
            }

            // Una suscripción push corrupta no debe impedir que el resto de
            // operarios reciba su aviso.
            try {
                $operario->notify(new AvisoTareasDelDia($total));
                $avisados++;
            } catch (Throwable $e) {
                $this->error("No se pudo avisar a {$operario->name}: {$e->getMessage()}");
            }
        }

        $this->info("Aviso enviado a {$avisados} operario(s) con tareas pendientes hoy.");
    }
}
