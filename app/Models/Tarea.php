<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['cliente_id', 'operario_id', 'fecha', 'hora_estimada', 'descripcion', 'estado', 'completada_at'])]
class Tarea extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'fecha' => 'date',
            'completada_at' => 'datetime',
        ];
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function operario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'operario_id');
    }

    public function marcarHecha(): void
    {
        $this->update([
            'estado' => 'hecha',
            'completada_at' => now(),
        ]);
    }
}
