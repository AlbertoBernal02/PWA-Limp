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
    public function index()
    {
        $clientes = Cliente::orderBy('nombre')->paginate(15);

        return view('admin.clientes.index', compact('clientes'));
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
