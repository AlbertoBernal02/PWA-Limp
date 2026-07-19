<?php

namespace Database\Factories;

use App\Models\Cliente;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Cliente>
 */
class ClienteFactory extends Factory
{
    /**
     * Direcciones reales de Madrid (no inventadas por Faker) para que la
     * geocodificación y el "Optimizar ruta" funcionen de verdad con datos de ejemplo.
     */
    private const DIRECCIONES_REALES = [
        'Gran Vía, 28, Madrid',
        'Calle de Alcalá, 45, Madrid',
        'Paseo del Prado, 12, Madrid',
        'Calle Mayor, 5, Madrid',
        'Calle de Fuencarral, 80, Madrid',
        'Calle de Serrano, 60, Madrid',
        'Calle de Atocha, 30, Madrid',
        'Ronda de Toledo, 8, Madrid',
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nombre' => fake()->company(),
            'direccion' => fake()->unique()->randomElement(self::DIRECCIONES_REALES),
            'telefono' => fake()->phoneNumber(),
            'notas' => null,
        ];
    }
}
