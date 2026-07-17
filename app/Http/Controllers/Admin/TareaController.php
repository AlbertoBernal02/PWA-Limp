<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\Tarea;
use App\Models\User;
use Illuminate\Http\Request;

class TareaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filtros = $request->only(['q', 'fecha', 'operario_id', 'estado']);

        $tareas = Tarea::with(['cliente', 'operario'])
            ->when($filtros['q'] ?? null, fn ($query, $q) => $query->where(function ($query) use ($q) {
                $query->whereHas('cliente', fn ($query) => $query->where('nombre', 'like', "%{$q}%"))
                    ->orWhereHas('operario', fn ($query) => $query->where('name', 'like', "%{$q}%"))
                    ->orWhere('descripcion', 'like', "%{$q}%");
            }))
            ->when($filtros['fecha'] ?? null, fn ($query, $fecha) => $query->whereDate('fecha', $fecha))
            ->when($filtros['operario_id'] ?? null, fn ($query, $id) => $query->where('operario_id', $id))
            ->when($filtros['estado'] ?? null, fn ($query, $estado) => $query->where('estado', $estado))
            ->orderByDesc('fecha')
            ->orderBy('hora_estimada')
            ->paginate(15)
            ->withQueryString();

        $operarios = User::where('role', 'operario')->orderBy('name')->get();

        return view('admin.tareas.index', compact('tareas', 'operarios', 'filtros'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        [$clientes, $operarios] = $this->opciones();

        return view('admin.tareas.create', compact('clientes', 'operarios'));
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

        $creadas = 0;

        foreach ($data['operario_ids'] as $operarioId) {
            foreach ($fechas as $fecha) {
                Tarea::create([
                    'cliente_id' => $data['cliente_id'],
                    'operario_id' => $operarioId,
                    'fecha' => $fecha,
                    'hora_estimada' => $data['hora_estimada'] ?? null,
                    'descripcion' => $data['descripcion'] ?? null,
                ]);
                $creadas++;
            }
        }

        $mensaje = $creadas === 1 ? 'Tarea creada correctamente.' : "{$creadas} tareas creadas correctamente.";

        return redirect()->route('admin.tareas.index')->with('status', $mensaje);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tarea $tarea)
    {
        [$clientes, $operarios] = $this->opciones();

        return view('admin.tareas.edit', compact('tarea', 'clientes', 'operarios'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tarea $tarea)
    {
        $data = $this->validarDatos($request);

        $tarea->update($data);

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
