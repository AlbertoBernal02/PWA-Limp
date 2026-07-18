<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Fichaje;
use App\Models\User;
use App\Support\Jornada;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class FichajeController extends Controller
{
    /**
     * Resumen de todos los operarios: estado de hoy y total de horas de la semana.
     */
    public function index()
    {
        $inicioSemana = now()->startOfWeek();
        $finSemana = now()->endOfWeek();

        $resumen = User::where('role', 'operario')
            ->orderBy('name')
            ->get()
            ->map(function (User $operario) use ($inicioSemana, $finSemana) {
                $fichajesHoy = $operario->fichajes()
                    ->whereDate('fichado_en', now()->toDateString())
                    ->orderBy('fichado_en')
                    ->get();

                $fichajesSemana = $operario->fichajes()
                    ->whereBetween('fichado_en', [$inicioSemana, $finSemana])
                    ->orderBy('fichado_en')
                    ->get();

                return [
                    'operario' => $operario,
                    'ultimoTipo' => $fichajesHoy->last()?->tipo,
                    'segundosHoy' => Jornada::segundosTrabajadosHasta($fichajesHoy),
                    'segundosSemana' => Jornada::segundosTrabajadosHasta($fichajesSemana),
                ];
            });

        return view('admin.fichajes.index', compact('resumen'));
    }

    /**
     * Detalle semanal de un operario, con los fichajes en bruto para poder corregirlos.
     */
    public function show(Request $request, User $operario)
    {
        abort_unless($operario->role === 'operario', 404);

        $semana = $request->query('semana')
            ? Carbon::parse($request->query('semana'))
            : now();

        $inicioSemana = $semana->copy()->startOfWeek();
        $finSemana = $semana->copy()->endOfWeek();

        $fichajes = $operario->fichajes()
            ->whereBetween('fichado_en', [$inicioSemana, $finSemana])
            ->orderBy('fichado_en')
            ->get();

        $porDia = $fichajes->groupBy(fn (Fichaje $f) => $f->fichado_en->toDateString());
        $segundosSemana = Jornada::segundosTrabajadosHasta($fichajes);
        $diasSemana = Jornada::horasPorDia($fichajes, $inicioSemana);

        return view('admin.fichajes.show', compact(
            'operario', 'porDia', 'segundosSemana', 'diasSemana', 'inicioSemana', 'finSemana'
        ));
    }

    /**
     * Informe mensual de horas trabajadas de un operario, en PDF o CSV (para nómina).
     */
    public function informeMensual(Request $request, User $operario)
    {
        abort_unless($operario->role === 'operario', 404);

        $data = $request->validate([
            'mes' => ['required', 'date_format:Y-m'],
            'formato' => ['required', Rule::in(['pdf', 'csv'])],
        ]);

        $inicio = Carbon::createFromFormat('Y-m', $data['mes'])->startOfMonth();
        $fin = $inicio->copy()->endOfMonth();

        $fichajes = $operario->fichajes()
            ->whereBetween('fichado_en', [$inicio, $fin])
            ->orderBy('fichado_en')
            ->get();

        $dias = Jornada::horasPorRango($fichajes, $inicio, $fin);
        $totalHoras = round(array_sum(array_column($dias, 'horas')), 2);

        $nombreArchivo = Str::slug($operario->name).'-'.$data['mes'];

        if ($data['formato'] === 'csv') {
            return response()->streamDownload(function () use ($dias) {
                $salida = fopen('php://output', 'w');
                fputcsv($salida, ['Fecha', 'Horas trabajadas']);

                foreach ($dias as $dia) {
                    fputcsv($salida, [$dia['fecha']->format('d/m/Y'), number_format($dia['horas'], 2)]);
                }

                fclose($salida);
            }, "{$nombreArchivo}.csv", ['Content-Type' => 'text/csv']);
        }

        return Pdf::loadView('admin.informes.operario', compact('operario', 'dias', 'inicio', 'totalHoras'))
            ->download("{$nombreArchivo}.pdf");
    }

    /**
     * Añade un fichaje manual (corrección de olvidos) para un operario.
     */
    public function store(Request $request, User $operario)
    {
        abort_unless($operario->role === 'operario', 404);

        $data = $request->validate([
            'tipo' => ['required', Rule::in(['entrada', 'inicio_descanso', 'fin_descanso', 'salida'])],
            'fichado_en' => ['required', 'date'],
        ]);

        $operario->fichajes()->create([
            'tipo' => $data['tipo'],
            'fichado_en' => $data['fichado_en'],
            'creado_por_admin' => true,
        ]);

        return back()->with('status', 'Fichaje añadido correctamente.');
    }

    /**
     * Elimina un fichaje incorrecto.
     */
    public function destroy(Fichaje $fichaje)
    {
        $operario = $fichaje->operario;
        $fichaje->delete();

        return redirect()->route('admin.fichajes.show', $operario)->with('status', 'Fichaje eliminado.');
    }
}
