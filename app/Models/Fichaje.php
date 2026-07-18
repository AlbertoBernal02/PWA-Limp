<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['operario_id', 'tipo', 'fichado_en', 'creado_por_admin'])]
class Fichaje extends Model
{
    protected function casts(): array
    {
        return [
            'fichado_en' => 'datetime',
            'creado_por_admin' => 'boolean',
        ];
    }

    public function operario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'operario_id');
    }
}
