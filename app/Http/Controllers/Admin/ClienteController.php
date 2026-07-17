<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ClienteController extends Controller
{
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

        return view('admin.clientes.show', compact('cliente', 'tareas', 'total', 'hechas', 'cumplimiento'));
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
