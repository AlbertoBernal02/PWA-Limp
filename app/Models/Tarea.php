<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['cliente_id', 'operario_id', 'fecha', 'hora_estimada', 'descripcion', 'estado', 'completada_at', 'foto_evidencia_path', 'nota'])]
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

    public function marcarHecha(?string $fotoEvidenciaPath = null, ?string $nota = null): void
    {
        $data = [
            'estado' => 'hecha',
            'completada_at' => now(),
        ];

        if ($fotoEvidenciaPath) {
            $data['foto_evidencia_path'] = $fotoEvidenciaPath;
        }

        if ($nota) {
            $data['nota'] = $nota;
        }

        $this->update($data);
    }

    public function fotoEvidenciaUrl(): ?string
    {
        return $this->foto_evidencia_path ? asset('storage/'.$this->foto_evidencia_path) : null;
    }
}
