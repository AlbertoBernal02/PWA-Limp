<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Herramienta;
use App\Models\Hueco;
use App\Models\Trabajo;
use Illuminate\Http\Request;

class AlmacenController extends Controller
{
    /**
     * Mapa del almacén: cuadrícula de huecos con las herramientas y trabajos guardados en cada uno.
     */
    public function index()
    {
        $huecos = Hueco::with('herramientas', 'trabajos')->get()->keyBy(fn ($hueco) => $hueco->fila.'-'.$hueco->columna);
        $filas = Hueco::max('fila') ?? 0;
        $columnas = Hueco::max('columna') ?? 0;
        $herramientas = Herramienta::orderBy('nombre')->get();
        $trabajos = Trabajo::with('herramientas')->orderBy('nombre')->get();

        return view('admin.almacen.index', compact('huecos', 'filas', 'columnas', 'herramientas', 'trabajos'));
    }

    /**
     * Añade una fila nueva de huecos (una celda por cada columna ya existente).
     */
    public function agregarFila()
    {
        $filas = Hueco::max('fila') ?? 0;
        $columnas = Hueco::max('columna') ?? 1;

        for ($columna = 1; $columna <= $columnas; $columna++) {
            Hueco::firstOrCreate(['fila' => $filas + 1, 'columna' => $columna]);
        }

        return redirect()->route('admin.almacen.index')->with('status', 'Fila añadida al almacén.');
    }

    /**
     * Añade una columna nueva de huecos (una celda por cada fila ya existente).
     */
    public function agregarColumna()
    {
        $filas = Hueco::max('fila') ?? 1;
        $columnas = Hueco::max('columna') ?? 0;

        for ($fila = 1; $fila <= $filas; $fila++) {
            Hueco::firstOrCreate(['fila' => $fila, 'columna' => $columnas + 1]);
        }

        return redirect()->route('admin.almacen.index')->with('status', 'Columna añadida al almacén.');
    }

    /**
     * Marca qué herramientas y trabajos están guardados en este hueco (checklists
     * del modal, igual en móvil y escritorio). Ambas relaciones son independientes
     * de las de otros huecos: esto solo cambia lo que toca a ESTE hueco.
     */
    public function asignar(Request $request, Hueco $hueco)
    {
        $hueco->herramientas()->sync($request->input('herramientas', []));
        $hueco->trabajos()->sync($request->input('trabajos', []));

        return redirect()->route('admin.almacen.index')->with('status', 'Hueco '.$hueco->codigo().' actualizado.');
    }

    /**
     * Añade una herramienta a un hueco (arrastrar y soltar en escritorio).
     * No quita la herramienta de otros huecos donde ya estuviera.
     */
    public function agregarHerramienta(Request $request, Hueco $hueco)
    {
        $data = $request->validate([
            'herramienta_id' => ['required', 'exists:herramientas,id'],
        ]);

        $hueco->herramientas()->syncWithoutDetaching($data['herramienta_id']);

        if ($request->wantsJson()) {
            return response()->noContent();
        }

        return redirect()->route('admin.almacen.index')->with('status', 'Herramienta añadida al hueco '.$hueco->codigo().'.');
    }

    /**
     * Quita una herramienta de un hueco concreto (botón "×" en la tarjeta).
     */
    public function quitarHerramienta(Request $request, Hueco $hueco, Herramienta $herramienta)
    {
        $hueco->herramientas()->detach($herramienta->id);

        if ($request->wantsJson()) {
            return response()->noContent();
        }

        return redirect()->route('admin.almacen.index')->with('status', $herramienta->nombre.' quitada del hueco '.$hueco->codigo().'.');
    }

    /**
     * Añade un trabajo a un hueco (arrastrar y soltar en escritorio).
     */
    public function agregarTrabajo(Request $request, Hueco $hueco)
    {
        $data = $request->validate([
            'trabajo_id' => ['required', 'exists:trabajos,id'],
        ]);

        $hueco->trabajos()->syncWithoutDetaching($data['trabajo_id']);

        if ($request->wantsJson()) {
            return response()->noContent();
        }

        return redirect()->route('admin.almacen.index')->with('status', 'Trabajo añadido al hueco '.$hueco->codigo().'.');
    }

    /**
     * Quita un trabajo de un hueco concreto.
     */
    public function quitarTrabajo(Request $request, Hueco $hueco, Trabajo $trabajo)
    {
        $hueco->trabajos()->detach($trabajo->id);

        if ($request->wantsJson()) {
            return response()->noContent();
        }

        return redirect()->route('admin.almacen.index')->with('status', $trabajo->nombre.' quitado del hueco '.$hueco->codigo().'.');
    }

    /**
     * Crea un único hueco suelto (para rellenar el vacío que deja un hueco eliminado
     * a media cuadrícula, sin tener que añadir toda una fila o columna nueva).
     */
    public function crear(Request $request)
    {
        $data = $request->validate([
            'fila' => ['required', 'integer', 'min:1', 'max:26'],
            'columna' => ['required', 'integer', 'min:1'],
        ]);

        $hueco = Hueco::firstOrCreate($data);

        return redirect()->route('admin.almacen.index')->with('status', 'Hueco '.$hueco->codigo().' creado.');
    }

    /**
     * Elimina un hueco. Las herramientas que tuviera guardadas no se borran,
     * solo se quita esa relación (cascade en la tabla pivote herramienta_hueco);
     * si estaban también en otro hueco, ahí se quedan.
     */
    public function destroy(Hueco $hueco)
    {
        $codigo = $hueco->codigo();
        $hueco->delete();

        return redirect()->route('admin.almacen.index')->with('status', "Hueco {$codigo} eliminado.");
    }

    /**
     * Elimina todos los huecos de una fila (balda) entera.
     */
    public function eliminarFila(int $fila)
    {
        Hueco::where('fila', $fila)->delete();

        return redirect()->route('admin.almacen.index')->with('status', 'Fila '.chr(64 + $fila).' eliminada.');
    }

    /**
     * Elimina todos los huecos de una columna entera.
     */
    public function eliminarColumna(int $columna)
    {
        Hueco::where('columna', $columna)->delete();

        return redirect()->route('admin.almacen.index')->with('status', 'Columna '.$columna.' eliminada.');
    }
}
