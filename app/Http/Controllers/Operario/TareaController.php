<?php

namespace App\Http\Controllers\Operario;

use App\Http\Controllers\Controller;
use App\Models\Tarea;
use App\Services\OptimizadorRuta;
use App\Support\Jornada;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class TareaController extends Controller
{
    /**
     * Listado de tareas del día para el operario autenticado.
     */
    public function index(Request $request)
    {
        $tareas = $request->user()
            ->tareas()
            ->with(['cliente', 'herramientas'])
            ->whereDate('fecha', now()->toDateString())
            ->orderByRaw('orden_ruta is null, orden_ruta')
            ->orderBy('hora_estimada')
            ->get();

        $estaTrabajando = Jornada::estaTrabajando($this->ultimoTipoFichaje($request->user()));

        return view('operario.tareas.index', compact('tareas', 'estaTrabajando'));
    }

    /**
     * Reordena por cercanía las tareas pendientes de hoy del operario
     * autenticado (solo tiene sentido con varias tareas seguidas en el día).
     */
    public function optimizarRuta(Request $request, OptimizadorRuta $optimizador)
    {
        $tareas = $request->user()
            ->tareas()
            ->with('cliente')
            ->whereDate('fecha', now()->toDateString())
            ->where('estado', 'pendiente')
            ->orderBy('hora_estimada')
            ->get();

        if ($tareas->count() < 2) {
            return redirect()->route('operario.tareas.index')->with('status', 'Necesitas al menos 2 tareas pendientes hoy para poder ordenar por cercanía.');
        }

        $ordenadas = $optimizador->optimizar($tareas);

        if ($ordenadas === 0) {
            return redirect()->route('operario.tareas.index')->with('status', 'No se pudo calcular la ruta: faltan direcciones que se puedan localizar en el mapa.');
        }

        return redirect()->route('operario.tareas.index')->with('status', 'Ruta optimizada: '.$ordenadas.' de '.$tareas->count().' tareas ordenadas por cercanía.');
    }

    /**
     * Historial de tareas del operario autenticado: días anteriores completos
     * más las tareas ya hechas de hoy.
     */
    public function historial(Request $request)
    {
        $tareas = $request->user()
            ->tareas()
            ->with(['cliente', 'herramientas'])
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
     * Marca el inicio real del trabajo en una tarea (para medir la duración real).
     */
    public function empezar(Request $request, Tarea $tarea)
    {
        abort_unless($tarea->operario_id === $request->user()->id, 403);

        $data = $request->validate([
            'lat' => ['nullable', 'numeric', 'between:-90,90'],
            'lng' => ['nullable', 'numeric', 'between:-180,180'],
        ]);

        $tarea->empezar(lat: $data['lat'] ?? null, lng: $data['lng'] ?? null);

        return redirect()->route('operario.tareas.index')->with('status', 'Tarea iniciada.');
    }

    /**
     * Marca una tarea del operario autenticado como hecha.
     */
    public function marcarHecha(Request $request, Tarea $tarea)
    {
        abort_unless($tarea->operario_id === $request->user()->id, 403);

        if (! Jornada::estaTrabajando($this->ultimoTipoFichaje($request->user()))) {
            return redirect()->route('operario.tareas.index')
                ->with('error', 'Tienes que fichar la entrada antes de poder marcar una tarea como hecha.');
        }

        $data = $request->validate([
            'foto_evidencia' => ['nullable', 'image', 'max:4096'],
            'nota' => ['nullable', 'string', 'max:1000'],
            'ocurrido_en' => ['nullable', 'date'],
            'cantidades' => ['nullable', 'array'],
            'cantidades.*' => ['nullable', 'integer', 'min:0'],
            'lat' => ['nullable', 'numeric', 'between:-90,90'],
            'lng' => ['nullable', 'numeric', 'between:-180,180'],
        ]);

        $fotoPath = $request->hasFile('foto_evidencia')
            ? $request->file('foto_evidencia')->store('evidencias', 'public')
            : null;

        $completadaEn = ! empty($data['ocurrido_en']) ? Carbon::parse($data['ocurrido_en']) : null;

        $tarea->marcarHecha($fotoPath, $data['nota'] ?? null, $completadaEn, $data['lat'] ?? null, $data['lng'] ?? null);
        $tarea->registrarMaterialGastado($data['cantidades'] ?? []);

        return redirect()->route('operario.tareas.index')->with('status', 'Tarea marcada como hecha.');
    }

    /**
     * Tipo del último fichaje del operario (sin importar el día — si dejó la
     * jornada abierta de un día anterior, sigue contando como "trabajando"
     * hasta que la cierre o la corrija el admin).
     */
    private function ultimoTipoFichaje($operario): ?string
    {
        return $operario->fichajes()->orderByDesc('fichado_en')->value('tipo');
    }
}
