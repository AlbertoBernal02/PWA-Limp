<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\Tarea;
use App\Models\User;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $hoy = now()->toDateString();

        $tareasHoy = Tarea::with(['cliente', 'operario'])
            ->whereDate('fecha', $hoy)
            ->orderBy('hora_estimada')
            ->get();

        $pendientesHoy = $tareasHoy->where('estado', 'pendiente')->count();
        $hechasHoy = $tareasHoy->where('estado', 'hecha')->count();

        $porOperarioHoy = $tareasHoy->groupBy('operario_id')->map(function ($tareas) {
            return [
                'operario' => $tareas->first()->operario,
                'total' => $tareas->count(),
                'hechas' => $tareas->where('estado', 'hecha')->count(),
            ];
        })->sortByDesc('total');

        $semana = collect(range(0, 6))->map(function (int $dias) {
            $fecha = now()->addDays($dias);

            return [
                'fecha' => $fecha,
                'total' => Tarea::whereDate('fecha', $fecha->toDateString())->count(),
            ];
        });

        $maxSemana = max(1, $semana->max('total'));

        return view('admin.dashboard', [
            'totalOperarios' => User::where('role', 'operario')->count(),
            'totalClientes' => Cliente::count(),
            'tareasHoy' => $tareasHoy,
            'pendientesHoy' => $pendientesHoy,
            'hechasHoy' => $hechasHoy,
            'porOperarioHoy' => $porOperarioHoy,
            'semana' => $semana,
            'maxSemana' => $maxSemana,
        ]);
    }
}
