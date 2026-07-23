<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Herramienta;
use App\Models\Trabajo;
use App\Support\ExportaTabla;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TrabajoController extends Controller
{
    use ExportaTabla;

    /**
     * Listado de tipos de trabajo (limpieza de oficinas, síndrome de Diógenes...).
     */
    public function index()
    {
        $trabajos = Trabajo::withCount('herramientas', 'tareas')->orderBy('nombre')->get();

        return view('admin.trabajos.index', compact('trabajos'));
    }

    /**
     * Exporta el listado de tipos de trabajo a CSV o PDF.
     */
    public function exportar(Request $request)
    {
        $data = $request->validate(['formato' => ['required', Rule::in(['pdf', 'csv'])]]);

        $trabajos = Trabajo::withCount('herramientas', 'tareas')->orderBy('nombre')->get();

        $filas = $trabajos->map(fn (Trabajo $t) => [$t->nombre, $t->herramientas_count, $t->tareas_count])->all();

        return $this->exportarTabla($data['formato'], 'Trabajos', ['Nombre', 'Nº herramientas', 'Nº tareas'], $filas, 'trabajos');
    }

    public function create()
    {
        $herramientas = Herramienta::orderBy('nombre')->get();

        return view('admin.trabajos.create', compact('herramientas'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:255', 'unique:trabajos,nombre'],
            'herramientas' => ['nullable', 'array'],
            'herramientas.*' => ['exists:herramientas,id'],
        ]);

        $trabajo = Trabajo::create(['nombre' => $data['nombre']]);
        $trabajo->herramientas()->sync($data['herramientas'] ?? []);

        return redirect()->route('admin.trabajos.index')->with('status', 'Trabajo creado correctamente.');
    }

    public function edit(Trabajo $trabajo)
    {
        $herramientas = Herramienta::orderBy('nombre')->get();
        $trabajo->load('herramientas');

        return view('admin.trabajos.edit', compact('trabajo', 'herramientas'));
    }

    public function update(Request $request, Trabajo $trabajo)
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:255', Rule::unique('trabajos', 'nombre')->ignore($trabajo->id)],
            'herramientas' => ['nullable', 'array'],
            'herramientas.*' => ['exists:herramientas,id'],
        ]);

        $trabajo->update(['nombre' => $data['nombre']]);
        $trabajo->herramientas()->sync($data['herramientas'] ?? []);

        return redirect()->route('admin.trabajos.index')->with('status', 'Trabajo actualizado correctamente.');
    }

    public function destroy(Trabajo $trabajo)
    {
        $trabajo->delete();

        return redirect()->route('admin.trabajos.index')->with('status', 'Trabajo eliminado.');
    }
}
