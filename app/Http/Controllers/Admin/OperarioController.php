<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class OperarioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $busqueda = $request->query('q');

        $operarios = User::where('role', 'operario')
            ->when($busqueda, fn ($query, $q) => $query->where(function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('telefono', 'like', "%{$q}%");
            }))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.operarios.index', compact('operarios', 'busqueda'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.operarios.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:8'],
            'avatar' => ['nullable', 'image', 'max:2048'],
        ]);

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'telefono' => $data['telefono'] ?? null,
            'password' => Hash::make($data['password']),
            'role' => 'operario',
            'avatar_path' => $request->hasFile('avatar') ? $request->file('avatar')->store('avatars', 'public') : null,
        ]);

        return redirect()->route('admin.operarios.index')->with('status', 'Operario creado correctamente.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $operario)
    {
        return view('admin.operarios.edit', compact('operario'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $operario)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($operario->id)],
            'telefono' => ['nullable', 'string', 'max:20'],
            'password' => ['nullable', 'string', 'min:8'],
            'avatar' => ['nullable', 'image', 'max:2048'],
        ]);

        $operario->name = $data['name'];
        $operario->email = $data['email'];
        $operario->telefono = $data['telefono'] ?? null;

        if (! empty($data['password'])) {
            $operario->password = Hash::make($data['password']);
        }

        if ($request->hasFile('avatar')) {
            if ($operario->avatar_path) {
                Storage::disk('public')->delete($operario->avatar_path);
            }

            $operario->avatar_path = $request->file('avatar')->store('avatars', 'public');
        }

        $operario->save();

        return redirect()->route('admin.operarios.index')->with('status', 'Operario actualizado correctamente.');
    }

    /**
     * Activa o desactiva al operario, sin borrar su historial de tareas.
     */
    public function toggleActivo(User $operario)
    {
        $operario->update(['activo' => ! $operario->activo]);

        $mensaje = $operario->activo ? 'Operario activado.' : 'Operario desactivado.';

        return redirect()->route('admin.operarios.index')->with('status', $mensaje);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $operario)
    {
        if ($operario->avatar_path) {
            Storage::disk('public')->delete($operario->avatar_path);
        }

        $operario->delete();

        return redirect()->route('admin.operarios.index')->with('status', 'Operario eliminado.');
    }
}
