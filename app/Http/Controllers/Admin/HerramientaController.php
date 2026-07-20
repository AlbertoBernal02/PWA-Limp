<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Herramienta;
use App\Models\Hueco;
use App\Models\Trabajo;
use Illuminate\Http\Request;

class HerramientaController extends Controller
{
    /**
     * Listado de herramientas y su cantidad en almacén. Se puede filtrar por
     * trabajo para ver de un vistazo qué hay para un tipo de servicio.
     */
    public function index(Request $request)
    {
        $trabajoId = $request->query('trabajo_id');

        $herramientas = Herramienta::with('huecos', 'trabajos')
            ->when($trabajoId, fn ($query) => $query->whereRelation('trabajos', 'trabajos.id', $trabajoId))
            ->orderBy('nombre')
            ->get();

        $trabajos = Trabajo::orderBy('nombre')->get();

        return view('admin.herramientas.index', compact('herramientas', 'trabajos', 'trabajoId'));
    }

    public function create()
    {
        $huecos = Hueco::orderBy('fila')->orderBy('columna')->get();
        $trabajos = Trabajo::orderBy('nombre')->get();

        return view('admin.herramientas.create', compact('huecos', 'trabajos'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'cantidad' => ['required', 'integer', 'min:0'],
            'huecos' => ['nullable', 'array'],
            'huecos.*' => ['exists:huecos,id'],
            'trabajos' => ['nullable', 'array'],
            'trabajos.*' => ['exists:trabajos,id'],
        ]);

        $herramienta = Herramienta::create([
            'nombre' => $data['nombre'],
            'cantidad' => $data['cantidad'],
        ]);

        $herramienta->huecos()->sync($data['huecos'] ?? []);
        $herramienta->trabajos()->sync($data['trabajos'] ?? []);

        return redirect()->route('admin.herramientas.index')->with('status', 'Herramienta creada correctamente.');
    }

    public function edit(Herramienta $herramienta)
    {
        $huecos = Hueco::orderBy('fila')->orderBy('columna')->get();
        $trabajos = Trabajo::orderBy('nombre')->get();
        $herramienta->load('huecos', 'trabajos');

        return view('admin.herramientas.edit', compact('herramienta', 'huecos', 'trabajos'));
    }

    public function update(Request $request, Herramienta $herramienta)
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'cantidad' => ['required', 'integer', 'min:0'],
            'huecos' => ['nullable', 'array'],
            'huecos.*' => ['exists:huecos,id'],
            'trabajos' => ['nullable', 'array'],
            'trabajos.*' => ['exists:trabajos,id'],
        ]);

        $herramienta->update([
            'nombre' => $data['nombre'],
            'cantidad' => $data['cantidad'],
        ]);

        $herramienta->huecos()->sync($data['huecos'] ?? []);
        $herramienta->trabajos()->sync($data['trabajos'] ?? []);

        return redirect()->route('admin.herramientas.index')->with('status', 'Herramienta actualizada correctamente.');
    }

    public function destroy(Herramienta $herramienta)
    {
        $herramienta->delete();

        return redirect()->route('admin.herramientas.index')->with('status', 'Herramienta eliminada.');
    }
}
