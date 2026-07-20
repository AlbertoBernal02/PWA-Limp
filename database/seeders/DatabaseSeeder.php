<?php

namespace Database\Seeders;

use App\Models\Cliente;
use App\Models\Herramienta;
use App\Models\Hueco;
use App\Models\Tarea;
use App\Models\Trabajo;
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
            ['nombre' => 'Guantes (Por pares)', 'cantidad' => 30],
            ['nombre' => 'Fregona', 'cantidad' => 8],
            ['nombre' => 'Mascarilla FFP2', 'cantidad' => 40],
            ['nombre' => 'Kit limpiacristales telescópico', 'cantidad' => 3],
        ])->mapWithKeys(fn ($datos) => [$datos['nombre'] => Herramienta::create($datos)]);

        // Cuadrícula de ejemplo del almacén (A1/A2/B1/B2), con un par de herramientas
        // repartidas en más de un hueco a la vez.
        $a1 = Hueco::create(['fila' => 1, 'columna' => 1]);
        $a2 = Hueco::create(['fila' => 1, 'columna' => 2]);
        $b1 = Hueco::create(['fila' => 2, 'columna' => 1]);
        Hueco::create(['fila' => 2, 'columna' => 2]);

        $herramientas['Detergente multiusos']->huecos()->attach([$a1->id, $b1->id]);
        $herramientas['Bayetas']->huecos()->attach($a1->id);
        $herramientas['Guantes (Por pares)']->huecos()->attach($a2->id);

        $trabajos = collect([
            'Limpieza de oficinas' => ['Detergente multiusos', 'Bayetas', 'Guantes (Por pares)'],
            'Síndrome de Diógenes' => ['Mascarilla FFP2', 'Guantes (Por pares)', 'Bayetas'],
            'Limpieza de placas solares' => ['Kit limpiacristales telescópico', 'Detergente multiusos'],
        ])->map(function ($nombresHerramientas, $nombreTrabajo) use ($herramientas) {
            $trabajo = Trabajo::create(['nombre' => $nombreTrabajo]);
            $trabajo->herramientas()->attach(collect($nombresHerramientas)->map(fn ($n) => $herramientas[$n]->id));

            return $trabajo;
        });

        foreach ($operarios as $operario) {
            // 3 tareas de hoy por operario: suficiente para probar "Optimizar ruta".
            $tareasDeHoy = Tarea::factory(3)->create([
                'operario_id' => $operario->id,
                'cliente_id' => fn () => $clientes->random()->id,
                'trabajo_id' => fn () => $trabajos->random()->id,
            ]);

            foreach ($tareasDeHoy as $tarea) {
                $tarea->herramientas()->attach($herramientas->random(2)->pluck('id'));
            }
        }
    }
}
