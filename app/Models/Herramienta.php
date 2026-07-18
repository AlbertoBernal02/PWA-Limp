<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['nombre', 'cantidad', 'hueco_id'])]
class Herramienta extends Model
{
    public function tareas(): BelongsToMany
    {
        return $this->belongsToMany(Tarea::class);
    }

    public function hueco(): BelongsTo
    {
        return $this->belongsTo(Hueco::class);
    }
}
