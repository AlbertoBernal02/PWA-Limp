<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['fecha', 'titulo', 'descripcion', 'operario_id'])]
class NotaAgenda extends Model
{
    protected $table = 'notas_agenda';

    protected function casts(): array
    {
        return [
            'fecha' => 'date',
        ];
    }

    public function operario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'operario_id');
    }
}
