<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

#[Fillable([
    'cliente_id', 'operario_id', 'fecha', 'hora_estimada', 'orden_ruta', 'iniciada_at', 'descripcion', 'estado',
    'completada_at', 'foto_evidencia_path', 'nota', 'iniciada_lat', 'iniciada_lng', 'completada_lat', 'completada_lng',
])]
class Tarea extends Model
{
    use HasFactory;

    /** Margen de gracia antes de considerar "atrasada" una tarea pendiente de hoy. */
    const HORAS_MARGEN_ATRASO = 2;

    protected function casts(): array
    {
        return [
            'fecha' => 'date',
            'iniciada_at' => 'datetime',
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

    /**
     * Herramientas que el operario debe llevar a esta tarea.
     */
    public function herramientas(): BelongsToMany
    {
        return $this->belongsToMany(Herramienta::class)->withPivot('cantidad_usada');
    }

    /**
     * Guarda cuánto se gastó de cada herramienta que llevaba esta tarea
     * (solo las que de verdad estaban asignadas) y lo descuenta del almacén general.
     * $cantidadesPorHerramientaId viene del formulario: ['3' => '2', '5' => '', ...].
     */
    public function registrarMaterialGastado(array $cantidadesPorHerramientaId): void
    {
        foreach ($this->herramientas as $herramienta) {
            $usado = (int) ($cantidadesPorHerramientaId[$herramienta->id] ?? 0);

            if ($usado <= 0) {
                continue;
            }

            $this->herramientas()->updateExistingPivot($herramienta->id, ['cantidad_usada' => $usado]);
            $herramienta->update(['cantidad' => max(0, $herramienta->cantidad - $usado)]);
        }
    }

    /**
     * Marca el inicio real del trabajo. No pisa un inicio ya registrado
     * (evita que un doble clic o un reenvío alargue la tarea "hacia atrás").
     * $lat/$lng son la posición del operario en ese momento (si el navegador
     * la dio y el operario dio permiso); quedan como prueba de que estuvo ahí.
     */
    public function empezar(?Carbon $en = null, ?float $lat = null, ?float $lng = null): void
    {
        if ($this->iniciada_at) {
            return;
        }

        $this->update([
            'iniciada_at' => $en ?? now(),
            'iniciada_lat' => $lat,
            'iniciada_lng' => $lng,
        ]);
    }

    public function marcarHecha(?string $fotoEvidenciaPath = null, ?string $nota = null, ?Carbon $completadaEn = null, ?float $lat = null, ?float $lng = null): void
    {
        $data = [
            'estado' => 'hecha',
            'completada_at' => $completadaEn ?? now(),
            'completada_lat' => $lat,
            'completada_lng' => $lng,
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

    /**
     * Enlace a Google Maps con la posición registrada al empezar/marcar hecha,
     * o null si el operario no dio permiso de ubicación (o el navegador no la dio).
     */
    public function ubicacionInicioUrl(): ?string
    {
        return $this->iniciada_lat && $this->iniciada_lng
            ? "https://www.google.com/maps?q={$this->iniciada_lat},{$this->iniciada_lng}"
            : null;
    }

    public function ubicacionFinUrl(): ?string
    {
        return $this->completada_lat && $this->completada_lng
            ? "https://www.google.com/maps?q={$this->completada_lat},{$this->completada_lng}"
            : null;
    }

    /**
     * Tiempo real de limpieza (fin - inicio). Null si no se fichó el inicio
     * o la tarea todavía no está hecha.
     */
    public function duracionSegundos(): ?int
    {
        if (! $this->iniciada_at || ! $this->completada_at) {
            return null;
        }

        return abs($this->completada_at->diffInSeconds($this->iniciada_at));
    }

    public function duracionFormateada(): ?string
    {
        $segundos = $this->duracionSegundos();

        return $segundos === null ? null : self::formatoDuracion($segundos);
    }

    public static function formatoDuracion(int $segundos): string
    {
        $horas = intdiv($segundos, 3600);
        $minutos = intdiv($segundos % 3600, 60);

        return $horas > 0 ? "{$horas}h {$minutos}min" : "{$minutos} min";
    }

    /**
     * Duración media (en segundos) de las tareas hechas que resultan de $query,
     * sea la consulta global o ya filtrada por cliente/operario/fecha/etc.
     * Null si ninguna tarea de esa consulta tiene inicio y fin registrados.
     */
    public static function duracionMediaSegundos($query): ?int
    {
        $promedio = (clone $query)
            ->where('estado', 'hecha')
            ->whereNotNull('iniciada_at')
            ->whereNotNull('completada_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(SECOND, iniciada_at, completada_at)) as promedio')
            ->value('promedio');

        return $promedio !== null ? (int) round((float) $promedio) : null;
    }

    public static function duracionMediaFormateada($query): ?string
    {
        $segundos = self::duracionMediaSegundos($query);

        return $segundos === null ? null : self::formatoDuracion($segundos);
    }

    /**
     * Una tarea está "atrasada" si sigue pendiente y: es de un día anterior,
     * o es de hoy y ya ha pasado su hora prevista más el margen de gracia.
     * Las tareas de hoy sin hora prevista no se marcan (no hay referencia).
     */
    public function estaAtrasada(): bool
    {
        if ($this->estado !== 'pendiente') {
            return false;
        }

        if ($this->fecha->lt(now()->startOfDay())) {
            return true;
        }

        if (! $this->fecha->isToday() || ! $this->hora_estimada) {
            return false;
        }

        $limite = $this->fecha->copy()
            ->setTimeFromTimeString($this->hora_estimada)
            ->addHours(self::HORAS_MARGEN_ATRASO);

        return now()->gt($limite);
    }
}
