<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Herramienta;
use App\Models\Hueco;
use Illuminate\Http\Request;

class AlmacenController extends Controller
{
    /**
     * Mapa del almacén: cuadrícula de huecos con las herramientas guardadas en cada uno.
     */
    public function index()
    {
        $huecos = Hueco::with('herramientas')->get()->keyBy(fn ($hueco) => $hueco->fila.'-'.$hueco->columna);
        $filas = Hueco::max('fila') ?? 0;
        $columnas = Hueco::max('columna') ?? 0;
        $herramientas = Herramienta::orderBy('nombre')->get();

        return view('admin.almacen.index', compact('huecos', 'filas', 'columnas', 'herramientas'));
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
     * Marca qué herramientas están guardadas en este hueco. Las que se desmarcan
     * quedan sin ubicación; no toca las que están asignadas a otros huecos.
     */
    public function asignar(Request $request, Hueco $hueco)
    {
        $ids = $request->input('herramientas', []);

        Herramienta::where('hueco_id', $hueco->id)->whereNotIn('id', $ids)->update(['hueco_id' => null]);
        Herramienta::whereIn('id', $ids)->update(['hueco_id' => $hueco->id]);

        return redirect()->route('admin.almacen.index')->with('status', 'Hueco '.$hueco->codigo().' actualizado.');
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
     * simplemente quedan sin ubicación (hueco_id a null por la clave foránea).
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
