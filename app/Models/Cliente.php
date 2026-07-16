<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['nombre', 'direccion', 'telefono', 'notas', 'foto_path'])]
class Cliente extends Model
{
    use HasFactory;

    public function tareas(): HasMany
    {
        return $this->hasMany(Tarea::class);
    }

    public function fotoUrl(): ?string
    {
        return $this->foto_path ? asset('storage/'.$this->foto_path) : null;
    }
}
