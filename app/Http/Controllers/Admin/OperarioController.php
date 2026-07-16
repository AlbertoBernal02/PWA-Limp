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
    public function index()
    {
        $operarios = User::where('role', 'operario')->orderBy('name')->paginate(15);

        return view('admin.operarios.index', compact('operarios'));
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
            'password' => ['required', 'string', 'min:8'],
            'avatar' => ['nullable', 'image', 'max:2048'],
        ]);

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
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
            'password' => ['nullable', 'string', 'min:8'],
            'avatar' => ['nullable', 'image', 'max:2048'],
        ]);

        $operario->name = $data['name'];
        $operario->email = $data['email'];

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
