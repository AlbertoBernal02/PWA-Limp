<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NotaAgenda;
use App\Models\Tarea;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AgendaController extends Controller
{
    /**
     * Calendario mensual: tareas y notas sueltas agrupadas por día.
     */
    public function index(Request $request)
    {
        try {
            $mes = Carbon::createFromFormat('Y-m', $request->query('mes', now()->format('Y-m')))->startOfMonth();
        } catch (\Throwable) {
            $mes = now()->startOfMonth();
        }

        // Semanas completas (lunes a domingo), incluyendo días de relleno del
        // mes anterior/siguiente para no dejar filas a medias.
        $inicioGrid = $mes->copy()->startOfWeek(Carbon::MONDAY);
        $finGrid = $mes->copy()->endOfMonth()->endOfWeek(Carbon::MONDAY);

        $dias = collect();
        for ($d = $inicioGrid->copy(); $d->lte($finGrid); $d->addDay()) {
            $dias->push(['fecha' => $d->copy(), 'esDelMes' => $d->month === $mes->month]);
        }
        $semanas = $dias->chunk(7);

        $tareasPorDia = Tarea::with(['cliente:id,nombre', 'operario:id,name'])
            ->whereBetween('fecha', [$inicioGrid->toDateString(), $finGrid->toDateString()])
            ->get(['id', 'fecha', 'hora_estimada', 'estado', 'cliente_id', 'operario_id'])
            ->groupBy(fn (Tarea $t) => $t->fecha->toDateString());

        $notasPorDia = NotaAgenda::with('operario:id,name')
            ->whereBetween('fecha', [$inicioGrid->toDateString(), $finGrid->toDateString()])
            ->get()
            ->groupBy(fn (NotaAgenda $n) => $n->fecha->toDateString());

        $operarios = User::where('role', 'operario')->orderBy('name')->get(['id', 'name']);

        return view('admin.agenda.index', compact('mes', 'semanas', 'tareasPorDia', 'notasPorDia', 'operarios'));
    }

    /**
     * Crea una nota/recordatorio suelto para un día, sin cliente ni operario obligatorio.
     */
    public function storeNota(Request $request)
    {
        $data = $request->validate([
            'fecha' => ['required', 'date_format:Y-m-d'],
            'titulo' => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string', 'max:2000'],
            'operario_id' => ['nullable', 'exists:users,id'],
            'mes' => ['nullable', 'string'],
        ]);

        $mes = $data['mes'] ?? null;
        unset($data['mes']);

        NotaAgenda::create($data);

        return redirect()->route('admin.agenda.index', $mes ? ['mes' => $mes] : [])->with('status', 'Nota añadida correctamente.');
    }

    /**
     * Elimina una nota/recordatorio suelto.
     */
    public function destroyNota(Request $request, NotaAgenda $nota)
    {
        $mes = $request->query('mes');
        $nota->delete();

        return redirect()->route('admin.agenda.index', $mes ? ['mes' => $mes] : [])->with('status', 'Nota eliminada.');
    }
}
