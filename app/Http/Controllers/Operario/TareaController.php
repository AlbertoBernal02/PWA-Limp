<?php

namespace App\Http\Controllers\Operario;

use App\Http\Controllers\Controller;
use App\Models\Tarea;
use Illuminate\Http\Request;

class TareaController extends Controller
{
    /**
     * Listado de tareas del día para el operario autenticado.
     */
    public function index(Request $request)
    {
        $tareas = $request->user()
            ->tareas()
            ->with('cliente')
            ->whereDate('fecha', now()->toDateString())
            ->orderBy('hora_estimada')
            ->get();

        return view('operario.tareas.index', compact('tareas'));
    }

    /**
     * Historial de tareas del operario autenticado: días anteriores completos
     * más las tareas ya hechas de hoy.
     */
    public function historial(Request $request)
    {
        $tareas = $request->user()
            ->tareas()
            ->with('cliente')
            ->where(function ($query) {
                $query->whereDate('fecha', '<', now()->toDateString())
                    ->orWhere('estado', 'hecha');
            })
            ->orderByDesc('fecha')
            ->orderByDesc('hora_estimada')
            ->paginate(10);

        return view('operario.tareas.historial', compact('tareas'));
    }

    /**
     * Marca una tarea del operario autenticado como hecha.
     */
    public function marcarHecha(Request $request, Tarea $tarea)
    {
        abort_unless($tarea->operario_id === $request->user()->id, 403);

        $data = $request->validate([
            'foto_evidencia' => ['nullable', 'image', 'max:4096'],
            'nota' => ['nullable', 'string', 'max:1000'],
        ]);

        $fotoPath = $request->hasFile('foto_evidencia')
            ? $request->file('foto_evidencia')->store('evidencias', 'public')
            : null;

        $tarea->marcarHecha($fotoPath, $data['nota'] ?? null);

        return redirect()->route('operario.tareas.index')->with('status', 'Tarea marcada como hecha.');
    }
}
