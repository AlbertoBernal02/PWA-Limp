<?php

namespace App\Http\Controllers;

use App\Support\Documentacion;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DocumentacionController extends Controller
{
    public function index(Request $request, ?string $seccion = null): View
    {
        $usuario = $request->user();
        $indice = Documentacion::indice($usuario);
        $activa = $seccion && Documentacion::existe($seccion, $usuario) ? $seccion : array_key_first($indice);
        $html = $activa ? Documentacion::contenidoHtml($activa) : '';

        return view('documentacion.index', compact('indice', 'activa', 'html'));
    }
}
