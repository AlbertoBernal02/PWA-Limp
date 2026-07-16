<?php

namespace Database\Factories;

use App\Models\Cliente;
use App\Models\Tarea;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Tarea>
 */
class TareaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'cliente_id' => Cliente::factory(),
            'operario_id' => User::factory(),
            'fecha' => now()->toDateString(),
            'hora_estimada' => fake()->time('H:i'),
            'descripcion' => fake()->sentence(),
            'estado' => 'pendiente',
            'completada_at' => null,
        ];
    }

    /**
     * Indicate that the task is done.
     */
    public function hecha(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => 'hecha',
            'completada_at' => now(),
        ]);
    }
}
