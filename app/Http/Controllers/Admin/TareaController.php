<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\Herramienta;
use App\Models\Tarea;
use App\Models\Trabajo;
use App\Models\User;
use Illuminate\Http\Request;

class TareaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filtros = $request->only(['q', 'fecha', 'operario_id', 'estado', 'trabajo_id']);

        $query = Tarea::with(['cliente', 'operario', 'trabajo'])
            ->when($filtros['q'] ?? null, fn ($query, $q) => $query->where(function ($query) use ($q) {
                $query->whereHas('cliente', fn ($query) => $query->where('nombre', 'like', "%{$q}%"))
                    ->orWhereHas('operario', fn ($query) => $query->where('name', 'like', "%{$q}%"))
                    ->orWhere('descripcion', 'like', "%{$q}%");
            }))
            ->when($filtros['fecha'] ?? null, fn ($query, $fecha) => $query->whereDate('fecha', $fecha))
            ->when($filtros['operario_id'] ?? null, fn ($query, $id) => $query->where('operario_id', $id))
            ->when($filtros['estado'] ?? null, fn ($query, $estado) => $query->where('estado', $estado))
            ->when($filtros['trabajo_id'] ?? null, fn ($query, $id) => $query->where('trabajo_id', $id));

        $duracionMedia = Tarea::duracionMediaFormateada($query);

        $tareas = $query->orderByDesc('fecha')
            ->orderBy('hora_estimada')
            ->paginate(15)
            ->withQueryString();

        $operarios = User::where('role', 'operario')->orderBy('name')->get();
        $trabajos = Trabajo::orderBy('nombre')->get();

        return view('admin.tareas.index', compact('tareas', 'operarios', 'trabajos', 'filtros', 'duracionMedia'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        [$clientes, $operarios] = $this->opciones();
        $herramientas = Herramienta::orderBy('nombre')->get();
        $trabajos = Trabajo::with('herramientas')->orderBy('nombre')->get();

        return view('admin.tareas.create', compact('clientes', 'operarios', 'herramientas', 'trabajos'));
    }

    /**
     * Store a newly created resource in storage. Soporta crear varias tareas
     * a la vez: para varios operarios y/o repitiendo semanalmente hasta una fecha.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'cliente_id' => ['required', 'exists:clientes,id'],
            'operario_ids' => ['required', 'array', 'min:1'],
            'operario_ids.*' => ['exists:users,id'],
            'fecha' => ['required', 'date'],
            'repetir_hasta' => ['nullable', 'date', 'after_or_equal:fecha'],
            'hora_estimada' => ['nullable', 'date_format:H:i'],
            'descripcion' => ['nullable', 'string'],
            'trabajo_id' => ['nullable', 'exists:trabajos,id'],
            'herramienta_ids' => ['nullable', 'array'],
            'herramienta_ids.*' => ['exists:herramientas,id'],
        ]);

        $fechas = [$data['fecha']];

        if (! empty($data['repetir_hasta'])) {
            $cursor = \Illuminate\Support\Carbon::parse($data['fecha'])->addWeek();
            $fin = \Illuminate\Support\Carbon::parse($data['repetir_hasta']);

            // Tope de seguridad: máximo 2 años de repetición semanal.
            for ($i = 0; $i < 104 && $cursor->lte($fin); $i++) {
                $fechas[] = $cursor->toDateString();
                $cursor->addWeek();
            }
        }

        $operariosPorId = User::whereIn('id', $data['operario_ids'])->get()->keyBy('id');

        $creadas = 0;
        $omitidas = 0;

        foreach ($data['operario_ids'] as $operarioId) {
            $operario = $operariosPorId[$operarioId];

            foreach ($fechas as $fecha) {
                if ($operario->estaAusente(\Illuminate\Support\Carbon::parse($fecha))) {
                    $omitidas++;

                    continue;
                }

                $tarea = Tarea::create([
                    'cliente_id' => $data['cliente_id'],
                    'operario_id' => $operarioId,
                    'fecha' => $fecha,
                    'hora_estimada' => $data['hora_estimada'] ?? null,
                    'descripcion' => $data['descripcion'] ?? null,
                    'trabajo_id' => $data['trabajo_id'] ?? null,
                ]);

                if (! empty($data['herramienta_ids'])) {
                    $tarea->herramientas()->sync($data['herramienta_ids']);
                }

                $creadas++;
            }
        }

        $mensaje = $creadas === 1 ? 'Tarea creada correctamente.' : "{$creadas} tareas creadas correctamente.";

        if ($omitidas > 0) {
            $mensaje .= " {$omitidas} se ".($omitidas === 1 ? 'omitió' : 'omitieron').' por ausencia/vacaciones del operario en esas fechas.';
        }

        return redirect()->route('admin.tareas.index')->with('status', $mensaje);
    }

    /**
     * Formulario simple para duplicar una tarea suelta a otro día/cliente/operario,
     * sin pasar por el formulario completo de repetición semanal.
     */
    public function duplicarForm(Tarea $tarea)
    {
        [$clientes, $operarios] = $this->opciones();
        $herramientas = Herramienta::orderBy('nombre')->get();
        $trabajos = Trabajo::with('herramientas')->orderBy('nombre')->get();
        $herramientaIdsSeleccionadas = $tarea->herramientas()->pluck('herramientas.id')->all();

        return view('admin.tareas.duplicar', compact('tarea', 'clientes', 'operarios', 'herramientas', 'trabajos', 'herramientaIdsSeleccionadas'));
    }

    /**
     * Crea la copia de la tarea con los datos (posiblemente cambiados) del formulario de duplicar.
     */
    public function duplicar(Request $request, Tarea $tarea)
    {
        $data = $this->validarDatos($request);
        $herramientaIds = $data['herramienta_ids'] ?? [];
        unset($data['herramienta_ids']);

        $operario = User::find($data['operario_id']);

        if ($operario->estaAusente(\Illuminate\Support\Carbon::parse($data['fecha']))) {
            return back()->withInput()->withErrors([
                'fecha' => 'Ese operario tiene una ausencia/vacaciones registrada ese día.',
            ]);
        }

        $nueva = Tarea::create($data);
        $nueva->herramientas()->sync($herramientaIds);

        return redirect()->route('admin.tareas.index')->with('status', 'Tarea duplicada correctamente.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tarea $tarea)
    {
        [$clientes, $operarios] = $this->opciones();
        $herramientas = Herramienta::orderBy('nombre')->get();
        $trabajos = Trabajo::with('herramientas')->orderBy('nombre')->get();
        $herramientaIdsSeleccionadas = $tarea->herramientas()->pluck('herramientas.id')->all();

        return view('admin.tareas.edit', compact('tarea', 'clientes', 'operarios', 'herramientas', 'trabajos', 'herramientaIdsSeleccionadas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tarea $tarea)
    {
        $data = $this->validarDatos($request);
        $herramientaIds = $data['herramienta_ids'] ?? [];
        unset($data['herramienta_ids']);

        $tarea->update($data);
        $tarea->herramientas()->sync($herramientaIds);

        return redirect()->route('admin.tareas.index')->with('status', 'Tarea actualizada correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tarea $tarea)
    {
        $tarea->delete();

        return redirect()->route('admin.tareas.index')->with('status', 'Tarea eliminada.');
    }

    private function validarDatos(Request $request): array
    {
        return $request->validate([
            'cliente_id' => ['required', 'exists:clientes,id'],
            'operario_id' => ['required', 'exists:users,id'],
            'fecha' => ['required', 'date'],
            'hora_estimada' => ['nullable', 'date_format:H:i'],
            'descripcion' => ['nullable', 'string'],
            'trabajo_id' => ['nullable', 'exists:trabajos,id'],
            'herramienta_ids' => ['nullable', 'array'],
            'herramienta_ids.*' => ['exists:herramientas,id'],
        ]);
    }

    private function opciones(): array
    {
        return [
            Cliente::orderBy('nombre')->get(),
            User::where('role', 'operario')->orderBy('name')->get(),
        ];
    }
}
