<?php

namespace App\Http\Controllers\Operario;

use App\Http\Controllers\Controller;
use App\Support\Jornada;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FichajeController extends Controller
{
    /**
     * Estado de jornada del operario autenticado: fichajes de hoy,
     * siguiente(s) acción(es) válida(s), y totales de hoy/semana.
     */
    public function index(Request $request)
    {
        $operario = $request->user();

        $fichajesHoy = $operario->fichajes()
            ->whereDate('fichado_en', now()->toDateString())
            ->orderBy('fichado_en')
            ->get();

        $inicioSemana = now()->startOfWeek();
        $finSemana = now()->endOfWeek();

        $fichajesSemana = $operario->fichajes()
            ->whereBetween('fichado_en', [$inicioSemana, $finSemana])
            ->orderBy('fichado_en')
            ->get();

        $ultimoHoy = $fichajesHoy->last();
        $siguientes = Jornada::siguientesAcciones($ultimoHoy?->tipo);
        $segundosHoy = Jornada::segundosTrabajadosHasta($fichajesHoy);
        $segundosSemana = Jornada::segundosTrabajadosHasta($fichajesSemana);
        $enDescanso = $ultimoHoy?->tipo === 'inicio_descanso';
        $diasSemana = Jornada::horasPorDia($fichajesSemana, $inicioSemana);

        return view('operario.fichajes.index', compact(
            'fichajesHoy', 'siguientes', 'segundosHoy', 'segundosSemana', 'enDescanso', 'diasSemana'
        ));
    }

    /**
     * Registra el siguiente fichaje del operario autenticado (entrada,
     * inicio/fin de descanso o salida), validando que sea la acción esperada.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'tipo' => ['required', Rule::in(['entrada', 'inicio_descanso', 'fin_descanso', 'salida'])],
        ]);

        $operario = $request->user();
        $ultimo = $operario->fichajes()->orderByDesc('fichado_en')->first();

        abort_unless(
            in_array($data['tipo'], Jornada::siguientesAcciones($ultimo?->tipo), true),
            422,
            'Ese fichaje no corresponde al siguiente paso de tu jornada.'
        );

        $operario->fichajes()->create([
            'tipo' => $data['tipo'],
            'fichado_en' => now(),
        ]);

        return redirect()->route('operario.fichajes.index')
            ->with('status', Jornada::etiqueta($data['tipo']).' registrado.');
    }
}
