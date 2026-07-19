<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use NotificationChannels\WebPush\HasPushSubscriptions;

#[Fillable(['name', 'email', 'password', 'role', 'avatar_path', 'telefono', 'activo'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasPushSubscriptions;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function avatarUrl(): ?string
    {
        return $this->avatar_path ? asset('storage/'.$this->avatar_path) : null;
    }

    public function iniciales(): string
    {
        $partes = collect(explode(' ', trim($this->name)))->filter();

        return mb_strtoupper($partes->take(2)->map(fn ($p) => mb_substr($p, 0, 1))->implode('')) ?: '?';
    }

    /**
     * Tareas asignadas a este operario.
     */
    public function tareas(): HasMany
    {
        return $this->hasMany(Tarea::class, 'operario_id');
    }

    /**
     * Fichajes de jornada (entrada/descanso/salida) de este operario.
     */
    public function fichajes(): HasMany
    {
        return $this->hasMany(Fichaje::class, 'operario_id');
    }

    /**
     * Periodos de vacaciones/ausencia de este operario.
     */
    public function ausencias(): HasMany
    {
        return $this->hasMany(Ausencia::class, 'operario_id');
    }

    /**
     * Si este operario tiene una ausencia registrada que cubre esa fecha.
     */
    public function estaAusente(Carbon $fecha): bool
    {
        return $this->ausencias()
            ->whereDate('fecha_inicio', '<=', $fecha->toDateString())
            ->whereDate('fecha_fin', '>=', $fecha->toDateString())
            ->exists();
    }

    public function esAdmin(): bool
    {
        return $this->role === 'admin';
    }
}
