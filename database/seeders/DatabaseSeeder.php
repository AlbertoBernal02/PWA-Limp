<?php

namespace Database\Seeders;

use App\Models\Cliente;
use App\Models\Herramienta;
use App\Models\Tarea;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::factory()->admin()->create([
            'name' => 'Admin',
            'email' => 'admin@pwa-limp.test',
        ]);

        $operarios = User::factory(3)->create();

        $clientes = Cliente::factory(5)->create();

        $herramientas = collect([
            ['nombre' => 'Detergente multiusos', 'cantidad' => 20],
            ['nombre' => 'Bayetas', 'cantidad' => 50],
            ['nombre' => 'Guantes', 'cantidad' => 30],
            ['nombre' => 'Fregona', 'cantidad' => 8],
        ])->map(fn ($datos) => Herramienta::create($datos));

        foreach ($operarios as $operario) {
            // 3 tareas de hoy por operario: suficiente para probar "Optimizar ruta".
            $tareasDeHoy = Tarea::factory(3)->create([
                'operario_id' => $operario->id,
                'cliente_id' => fn () => $clientes->random()->id,
            ]);

            foreach ($tareasDeHoy as $tarea) {
                $tarea->herramientas()->attach($herramientas->random(2)->pluck('id'));
            }
        }
    }
}
