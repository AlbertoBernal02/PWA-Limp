<?php

namespace App\Services;

use App\Models\Herramienta;
use App\Models\Tarea;
use App\Models\User;

class ContextoAgregadoIa
{
    /**
     * Resumen fijo que se manda siempre al chat de IA junto a la pregunta,
     * para que pueda responder cosas agregadas sin tener que pedir una tool.
     */
    public function para(User $usuario): string
    {
        $inventario = Herramienta::orderBy('nombre')->get()
            ->map(fn (Herramienta $h) => "- {$h->nombre}: {$h->cantidad} unidades")
            ->implode("\n");

        $pendientesHoy = Tarea::where('estado', 'pendiente')->whereDate('fecha', now())->count();

        $lineas = [
            "Inventario de herramientas en almacén:\n{$inventario}",
            "Tareas pendientes de hoy (todas, todos los operarios): {$pendientesHoy}",
        ];

        if (! $usuario->esAdmin()) {
            $misPendientesHoy = $usuario->tareas()->where('estado', 'pendiente')->whereDate('fecha', now())->count();
            $lineas[] = "Tus tareas pendientes de hoy: {$misPendientesHoy}";
        }

        return implode("\n\n", $lineas);
    }
}
