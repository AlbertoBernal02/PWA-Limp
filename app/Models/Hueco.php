<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['fila', 'columna'])]
class Hueco extends Model
{
    public function herramientas(): BelongsToMany
    {
        return $this->belongsToMany(Herramienta::class);
    }

    /**
     * Trabajos guardados en este hueco (ej. "todo lo de Diógenes va en A1"),
     * independiente de qué herramientas sueltas tenga marcadas.
     */
    public function trabajos(): BelongsToMany
    {
        return $this->belongsToMany(Trabajo::class);
    }

    /**
     * Código visible del hueco, ej. fila 1 / columna 2 -> "A2".
     */
    public function codigo(): string
    {
        return chr(64 + $this->fila).$this->columna;
    }
}
