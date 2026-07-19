<?php

namespace App\Support;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class Jornada
{
    /**
     * Acciones de fichaje válidas después de la última registrada.
     * El descanso es opcional: desde "dentro" se puede fichar salida directamente.
     *
     * @return array<int, string>
     */
    public static function siguientesAcciones(?string $ultimoTipo): array
    {
        return match ($ultimoTipo) {
            null, 'salida' => ['entrada'],
            'entrada', 'fin_descanso' => ['inicio_descanso', 'salida'],
            'inicio_descanso' => ['fin_descanso'],
        };
    }

    /**
     * Si, según el último fichaje registrado, el operario está ahora mismo
     * "dentro" de su jornada y trabajando (ha fichado entrada y no está de
     * descanso ni ha fichado ya la salida).
     */
    public static function estaTrabajando(?string $ultimoTipo): bool
    {
        return in_array($ultimoTipo, ['entrada', 'fin_descanso'], true);
    }

    public static function etiqueta(string $tipo): string
    {
        return match ($tipo) {
            'entrada' => 'Fichar entrada',
            'inicio_descanso' => 'Iniciar descanso',
            'fin_descanso' => 'Fin del descanso',
            'salida' => 'Fichar salida',
        };
    }

    /**
     * Segundos trabajados (sin contar descansos) a partir de una lista de
     * fichajes ordenada cronológicamente. Acepta objetos con "tipo" y "fichado_en".
     */
    public static function segundosTrabajados(iterable $fichajesOrdenados): int
    {
        $totalSegundos = 0;
        $inicioTramo = null;
        $enDescanso = false;

        foreach ($fichajesOrdenados as $fichaje) {
            if ($fichaje->tipo === 'entrada') {
                $inicioTramo = $fichaje->fichado_en;
                $enDescanso = false;

                continue;
            }

            if ($fichaje->tipo === 'inicio_descanso' && $inicioTramo && ! $enDescanso) {
                $totalSegundos += $inicioTramo->diffInSeconds($fichaje->fichado_en);
                $enDescanso = true;

                continue;
            }

            if ($fichaje->tipo === 'fin_descanso') {
                $inicioTramo = $fichaje->fichado_en;
                $enDescanso = false;

                continue;
            }

            if ($fichaje->tipo === 'salida' && $inicioTramo && ! $enDescanso) {
                $totalSegundos += $inicioTramo->diffInSeconds($fichaje->fichado_en);
                $inicioTramo = null;
            }
        }

        return $totalSegundos;
    }

    /**
     * Igual que segundosTrabajados(), pero si la jornada sigue abierta
     * (no hay salida todavía) cuenta hasta el momento actual — solo cuando
     * ese tramo abierto es de hoy. Un olvido de días anteriores no se
     * "cierra" contra la hora actual (inflaría las horas de forma absurda);
     * se queda sin contar hasta que se corrija el fichaje.
     */
    public static function segundosTrabajadosHasta(Collection $fichajes, ?Carbon $ahora = null): int
    {
        $ahora = $ahora ?? now();
        $ordenados = $fichajes->sortBy('fichado_en')->values();
        $ultimo = $ordenados->last();

        if ($ultimo && $ultimo->tipo !== 'salida' && $ultimo->fichado_en->isSameDay($ahora)) {
            $ordenados->push((object) ['tipo' => 'salida', 'fichado_en' => $ahora]);
        }

        return self::segundosTrabajados($ordenados);
    }

    public static function formatoHoras(int $segundos): string
    {
        $horas = intdiv($segundos, 3600);
        $minutos = intdiv($segundos % 3600, 60);

        return "{$horas}h {$minutos}m";
    }

    /**
     * Horas trabajadas de cada uno de los 7 días de una semana (lunes a domingo),
     * para dibujar un gráfico. $fichajesSemana puede traer fichajes de varios días mezclados.
     *
     * @return array<int, array{etiqueta: string, horas: float, esHoy: bool}>
     */
    public static function horasPorDia(Collection $fichajesSemana, Carbon $inicioSemana): array
    {
        $etiquetas = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'];
        $porFecha = $fichajesSemana->groupBy(fn ($f) => $f->fichado_en->toDateString());

        return collect(range(0, 6))->map(function (int $i) use ($inicioSemana, $etiquetas, $porFecha) {
            $fecha = $inicioSemana->copy()->addDays($i);
            $fichajesDia = collect($porFecha->get($fecha->toDateString(), []));

            return [
                'etiqueta' => $etiquetas[$i],
                'horas' => round(self::segundosTrabajadosHasta($fichajesDia) / 3600, 1),
                'esHoy' => $fecha->isToday(),
            ];
        })->all();
    }

    /**
     * Horas trabajadas cada día entre $inicio y $fin (inclusive), para informes
     * de cualquier duración (ej. un mes completo para nómina).
     *
     * @return array<int, array{fecha: Carbon, horas: float}>
     */
    public static function horasPorRango(Collection $fichajes, Carbon $inicio, Carbon $fin): array
    {
        $porFecha = $fichajes->groupBy(fn ($f) => $f->fichado_en->toDateString());
        $dias = [];
        $cursor = $inicio->copy()->startOfDay();
        $fin = $fin->copy()->startOfDay();

        while ($cursor->lte($fin)) {
            $fichajesDia = collect($porFecha->get($cursor->toDateString(), []));

            $dias[] = [
                'fecha' => $cursor->copy(),
                'horas' => round(self::segundosTrabajadosHasta($fichajesDia) / 3600, 2),
            ];

            $cursor->addDay();
        }

        return $dias;
    }
}
