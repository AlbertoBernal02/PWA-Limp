<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['fila', 'columna'])]
class Hueco extends Model
{
    public function herramientas(): HasMany
    {
        return $this->hasMany(Herramienta::class);
    }

    /**
     * Código visible del hueco, ej. fila 1 / columna 2 -> "A2".
     */
    public function codigo(): string
    {
        return chr(64 + $this->fila).$this->columna;
    }
}
