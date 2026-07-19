<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class OptimizadorRuta
{
    public function __construct(private readonly Geocodificador $geocodificador)
    {
    }

    /**
     * Reordena las tareas de $tareas por cercanía (empezando por la primera de
     * la lista) y guarda el resultado en "orden_ruta". Las tareas cuyo cliente
     * no se pueda geocodificar quedan al final, en su orden original.
     * Devuelve cuántas tareas se pudieron ordenar por ruta de verdad.
     */
    public function optimizar(Collection $tareas): int
    {
        $conCoordenadas = [];
        $sinCoordenadas = [];

        foreach ($tareas as $tarea) {
            $coordenadas = $this->geocodificador->coordenadasDe($tarea->cliente);

            if ($coordenadas) {
                $conCoordenadas[] = ['tarea' => $tarea, ...$coordenadas];
            } else {
                $sinCoordenadas[] = $tarea;
            }
        }

        if (count($conCoordenadas) < 2) {
            return 0;
        }

        $orden = $this->vecinoMasCercano($this->matrizDeDistancias($conCoordenadas));

        $posicion = 1;
        foreach ($orden as $indice) {
            $conCoordenadas[$indice]['tarea']->update(['orden_ruta' => $posicion++]);
        }

        foreach ($sinCoordenadas as $tarea) {
            $tarea->update(['orden_ruta' => $posicion++]);
        }

        return count($conCoordenadas);
    }

    /**
     * Matriz de distancias (metros) entre cada par de puntos. Con clave de
     * OpenRouteService configurada intenta la distancia real por carretera;
     * si no hay clave o la petición falla, usa línea recta (Haversine) —
     * la ordenación nunca depende por completo de un servicio externo.
     */
    private function matrizDeDistancias(array $puntos): array
    {
        $apiKey = config('services.openrouteservice.key');

        if ($apiKey) {
            $matriz = $this->matrizOpenRouteService($puntos, $apiKey);

            if ($matriz) {
                return $matriz;
            }
        }

        return $this->matrizHaversine($puntos);
    }

    private function matrizOpenRouteService(array $puntos, string $apiKey): ?array
    {
        try {
            $respuesta = Http::timeout(8)
                ->withHeaders(['Authorization' => $apiKey])
                ->post('https://api.openrouteservice.org/v2/matrix/driving-car', [
                    'locations' => array_map(fn ($p) => [$p['lng'], $p['lat']], $puntos),
                    'metrics' => ['distance'],
                ]);

            if (! $respuesta->successful()) {
                Log::warning('OpenRouteService matrix respondió '.$respuesta->status().', se usa línea recta.');

                return null;
            }

            return $respuesta->json('distances');
        } catch (Throwable $e) {
            Log::warning('OpenRouteService matrix no disponible ('.$e->getMessage().'), se usa línea recta.');

            return null;
        }
    }

    private function matrizHaversine(array $puntos): array
    {
        $n = count($puntos);
        $matriz = [];

        for ($i = 0; $i < $n; $i++) {
            $matriz[$i] = [];
            for ($j = 0; $j < $n; $j++) {
                $matriz[$i][$j] = $i === $j ? 0 : $this->haversine($puntos[$i], $puntos[$j]);
            }
        }

        return $matriz;
    }

    private function haversine(array $a, array $b): float
    {
        $radioTierra = 6371000;
        $dLat = deg2rad($b['lat'] - $a['lat']);
        $dLng = deg2rad($b['lng'] - $a['lng']);

        $h = sin($dLat / 2) ** 2 + cos(deg2rad($a['lat'])) * cos(deg2rad($b['lat'])) * sin($dLng / 2) ** 2;

        return $radioTierra * 2 * atan2(sqrt($h), sqrt(1 - $h));
    }

    /**
     * Heurística del vecino más cercano: empieza en el punto 0 (la primera
     * tarea de la lista original) y salta siempre al más cercano sin visitar.
     * Suficiente para el tamaño de ruta de un operario (unas pocas paradas al
     * día); devuelve los índices en el orden de visita.
     */
    private function vecinoMasCercano(array $matriz): array
    {
        $n = count($matriz);
        $visitados = [0 => true];
        $orden = [0];
        $actual = 0;

        while (count($orden) < $n) {
            $masCercano = null;
            $menorDistancia = null;

            for ($i = 0; $i < $n; $i++) {
                if (isset($visitados[$i])) {
                    continue;
                }

                if ($menorDistancia === null || $matriz[$actual][$i] < $menorDistancia) {
                    $menorDistancia = $matriz[$actual][$i];
                    $masCercano = $i;
                }
            }

            $visitados[$masCercano] = true;
            $orden[] = $masCercano;
            $actual = $masCercano;
        }

        return $orden;
    }
}
