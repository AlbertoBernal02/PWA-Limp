<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ausencia;
use App\Models\User;
use Illuminate\Http\Request;

class AusenciaController extends Controller
{
    /**
     * Registra un periodo de ausencia/vacaciones para un operario.
     */
    public function store(Request $request, User $operario)
    {
        abort_unless($operario->role === 'operario', 404);

        $data = $request->validate([
            'fecha_inicio' => ['required', 'date'],
            'fecha_fin' => ['required', 'date', 'after_or_equal:fecha_inicio'],
            'motivo' => ['nullable', 'string', 'max:255'],
        ]);

        $operario->ausencias()->create($data);

        return redirect()->route('admin.operarios.edit', $operario)->with('status', 'Ausencia registrada correctamente.');
    }

    /**
     * Elimina un periodo de ausencia.
     */
    public function destroy(Ausencia $ausencia)
    {
        $operario = $ausencia->operario;
        $ausencia->delete();

        return redirect()->route('admin.operarios.edit', $operario)->with('status', 'Ausencia eliminada.');
    }
}
