<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Herramienta;
use App\Models\Hueco;
use Illuminate\Http\Request;

class HerramientaController extends Controller
{
    /**
     * Listado de herramientas y su cantidad en almacén.
     */
    public function index()
    {
        $herramientas = Herramienta::with('hueco')->orderBy('nombre')->get();

        return view('admin.herramientas.index', compact('herramientas'));
    }

    public function create()
    {
        $huecos = Hueco::orderBy('fila')->orderBy('columna')->get();

        return view('admin.herramientas.create', compact('huecos'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'cantidad' => ['required', 'integer', 'min:0'],
            'hueco_id' => ['nullable', 'exists:huecos,id'],
        ]);

        Herramienta::create($data);

        return redirect()->route('admin.herramientas.index')->with('status', 'Herramienta creada correctamente.');
    }

    public function edit(Herramienta $herramienta)
    {
        $huecos = Hueco::orderBy('fila')->orderBy('columna')->get();

        return view('admin.herramientas.edit', compact('herramienta', 'huecos'));
    }

    public function update(Request $request, Herramienta $herramienta)
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'cantidad' => ['required', 'integer', 'min:0'],
            'hueco_id' => ['nullable', 'exists:huecos,id'],
        ]);

        $herramienta->update($data);

        return redirect()->route('admin.herramientas.index')->with('status', 'Herramienta actualizada correctamente.');
    }

    public function destroy(Herramienta $herramienta)
    {
        $herramienta->delete();

        return redirect()->route('admin.herramientas.index')->with('status', 'Herramienta eliminada.');
    }
}
