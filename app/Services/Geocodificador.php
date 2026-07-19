<?php

namespace App\Services;

use App\Models\Cliente;
use Illuminate\Support\Facades\Http;
use Throwable;

class Geocodificador
{
    /**
     * Coordenadas del cliente. Si no las tenía guardadas, las obtiene
     * geocodificando su dirección (Photon/OSM, gratis y sin clave) y las
     * guarda para no volver a llamar a la API la próxima vez.
     */
    public function coordenadasDe(Cliente $cliente): ?array
    {
        if ($cliente->tieneCoordenadas()) {
            return ['lat' => (float) $cliente->lat, 'lng' => (float) $cliente->lng];
        }

        $coordenadas = $this->geocodificar($cliente->direccion);

        if ($coordenadas) {
            $cliente->update($coordenadas);
        }

        return $coordenadas;
    }

    /**
     * Convierte una dirección en texto a lat/lng vía Photon. Null si no
     * encuentra nada o la API no responde a tiempo.
     */
    public function geocodificar(string $direccion): ?array
    {
        try {
            $respuesta = Http::timeout(5)->get('https://photon.komoot.io/api/', [
                'q' => $direccion,
                'limit' => 1,
            ]);

            $coordenadas = $respuesta->json('features.0.geometry.coordinates');

            if (! $coordenadas) {
                return null;
            }

            // Photon devuelve GeoJSON: [lng, lat].
            return ['lng' => (float) $coordenadas[0], 'lat' => (float) $coordenadas[1]];
        } catch (Throwable $e) {
            return null;
        }
    }
}
