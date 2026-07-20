<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['nombre'])]
class Trabajo extends Model
{
    /**
     * Herramientas típicas de este tipo de trabajo (para sugerirlas al crear una tarea).
     */
    public function herramientas(): BelongsToMany
    {
        return $this->belongsToMany(Herramienta::class);
    }

    public function tareas(): HasMany
    {
        return $this->hasMany(Tarea::class);
    }

    /**
     * Huecos del almacén marcados como "de este trabajo".
     */
    public function huecos(): BelongsToMany
    {
        return $this->belongsToMany(Hueco::class);
    }
}
