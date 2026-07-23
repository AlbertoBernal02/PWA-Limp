<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\Tarea;
use App\Support\ExportaTabla;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ClienteController extends Controller
{
    use ExportaTabla;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $busqueda = $request->query('q');

        $clientes = Cliente::when($busqueda, fn ($query, $q) => $query->where(function ($query) use ($q) {
                $query->where('nombre', 'like', "%{$q}%")
                    ->orWhere('direccion', 'like', "%{$q}%")
                    ->orWhere('telefono', 'like', "%{$q}%");
            }))
            ->orderBy('nombre')
            ->paginate(15)
            ->withQueryString();

        return view('admin.clientes.index', compact('clientes', 'busqueda'));
    }

    /**
     * Exporta el listado de clientes (respetando la búsqueda actual) a CSV o PDF.
     */
    public function exportar(Request $request)
    {
        $data = $request->validate(['formato' => ['required', Rule::in(['pdf', 'csv'])]]);
        $busqueda = $request->query('q');

        $clientes = Cliente::when($busqueda, fn ($query, $q) => $query->where(function ($query) use ($q) {
                $query->where('nombre', 'like', "%{$q}%")
                    ->orWhere('direccion', 'like', "%{$q}%")
                    ->orWhere('telefono', 'like', "%{$q}%");
            }))
            ->orderBy('nombre')
            ->get();

        $filas = $clientes->map(fn (Cliente $c) => [$c->nombre, $c->direccion, $c->telefono ?: ''])->all();

        return $this->exportarTabla($data['formato'], 'Clientes', ['Nombre', 'Dirección', 'Teléfono'], $filas, 'clientes');
    }

    /**
     * Ficha de cliente: datos e historial de tareas.
     */
    public function show(Cliente $cliente)
    {
        $tareas = $cliente->tareas()
            ->with('operario')
            ->orderByDesc('fecha')
            ->orderByDesc('hora_estimada')
            ->paginate(10);

        $total = $cliente->tareas()->count();
        $hechas = $cliente->tareas()->where('estado', 'hecha')->count();
        $cumplimiento = $total > 0 ? round(($hechas / $total) * 100) : 0;
        $duracionMedia = Tarea::duracionMediaFormateada($cliente->tareas());

        return view('admin.clientes.show', compact('cliente', 'tareas', 'total', 'hechas', 'cumplimiento', 'duracionMedia'));
    }

    /**
     * Informe mensual de tareas de un cliente, en PDF o CSV (para facturación).
     */
    public function informe(Request $request, Cliente $cliente)
    {
        $data = $request->validate([
            'mes' => ['required', 'date_format:Y-m'],
            'formato' => ['required', Rule::in(['pdf', 'csv'])],
        ]);

        $inicio = Carbon::createFromFormat('Y-m', $data['mes'])->startOfMonth();
        $fin = $inicio->copy()->endOfMonth();

        $tareas = $cliente->tareas()
            ->with('operario')
            ->whereBetween('fecha', [$inicio->toDateString(), $fin->toDateString()])
            ->orderBy('fecha')
            ->orderBy('hora_estimada')
            ->get();

        $totales = [
            'total' => $tareas->count(),
            'hechas' => $tareas->where('estado', 'hecha')->count(),
        ];

        $nombreArchivo = Str::slug($cliente->nombre).'-'.$data['mes'];

        if ($data['formato'] === 'csv') {
            return response()->streamDownload(function () use ($tareas) {
                $salida = fopen('php://output', 'w');
                fputcsv($salida, ['Fecha', 'Hora', 'Operario', 'Estado', 'Duración', 'Nota']);

                foreach ($tareas as $tarea) {
                    fputcsv($salida, [
                        $tarea->fecha->format('d/m/Y'),
                        $tarea->hora_estimada ? Carbon::parse($tarea->hora_estimada)->format('H:i') : '',
                        $tarea->operario->name,
                        $tarea->estado === 'hecha' ? 'Hecha' : 'Pendiente',
                        $tarea->duracionFormateada() ?? '',
                        $tarea->nota ?? '',
                    ]);
                }

                fclose($salida);
            }, "{$nombreArchivo}.csv", ['Content-Type' => 'text/csv']);
        }

        return Pdf::loadView('admin.informes.cliente', compact('cliente', 'tareas', 'totales', 'inicio'))
            ->download("{$nombreArchivo}.pdf");
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.clientes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'direccion' => ['required', 'string', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:50'],
            'notas' => ['nullable', 'string'],
            'foto' => ['nullable', 'image', 'max:4096'],
            'lat' => ['nullable', 'numeric', 'between:-90,90'],
            'lng' => ['nullable', 'numeric', 'between:-180,180'],
        ]);

        unset($data['foto']);

        if ($request->hasFile('foto')) {
            $data['foto_path'] = $request->file('foto')->store('clientes', 'public');
        }

        Cliente::create($data);

        return redirect()->route('admin.clientes.index')->with('status', 'Cliente creado correctamente.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Cliente $cliente)
    {
        return view('admin.clientes.edit', compact('cliente'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Cliente $cliente)
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'direccion' => ['required', 'string', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:50'],
            'notas' => ['nullable', 'string'],
            'foto' => ['nullable', 'image', 'max:4096'],
            'lat' => ['nullable', 'numeric', 'between:-90,90'],
            'lng' => ['nullable', 'numeric', 'between:-180,180'],
        ]);

        unset($data['foto']);

        if ($request->hasFile('foto')) {
            if ($cliente->foto_path) {
                Storage::disk('public')->delete($cliente->foto_path);
            }

            $data['foto_path'] = $request->file('foto')->store('clientes', 'public');
        }

        $cliente->update($data);

        return redirect()->route('admin.clientes.index')->with('status', 'Cliente actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cliente $cliente)
    {
        if ($cliente->foto_path) {
            Storage::disk('public')->delete($cliente->foto_path);
        }

        $cliente->delete();

        return redirect()->route('admin.clientes.index')->with('status', 'Cliente eliminado.');
    }
}
