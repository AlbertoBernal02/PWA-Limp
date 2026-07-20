<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['nombre', 'cantidad'])]
class Herramienta extends Model
{
    public function tareas(): BelongsToMany
    {
        return $this->belongsToMany(Tarea::class);
    }

    /**
     * Huecos donde está guardada esta herramienta. Puede estar repartida
     * en varios a la vez (ej. medio stock en A1 y el resto en B3).
     */
    public function huecos(): BelongsToMany
    {
        return $this->belongsToMany(Hueco::class);
    }

    /**
     * Trabajos para los que se usa típicamente esta herramienta.
     */
    public function trabajos(): BelongsToMany
    {
        return $this->belongsToMany(Trabajo::class);
    }
}
