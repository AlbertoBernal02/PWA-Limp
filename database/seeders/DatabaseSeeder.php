<?php

namespace Database\Seeders;

use App\Models\Cliente;
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

        foreach ($operarios as $operario) {
            Tarea::factory(3)->create([
                'operario_id' => $operario->id,
                'cliente_id' => fn () => $clientes->random()->id,
            ]);
        }
    }
}
