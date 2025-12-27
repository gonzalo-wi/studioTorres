<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Barber;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class BarberUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Obtener barberos existentes
        $hernan = Barber::where('name', 'Hernán García')->first();
        $carlos = Barber::where('name', 'Carlos Mendez')->first();

        if ($hernan) {
            // Crear usuario para Hernán
            $hernanUser = User::create([
                'name' => 'Hernán García',
                'email' => 'hernan@studiotorres.com',
                'password' => Hash::make('password123'),
                'role' => 'BARBER',
            ]);

            // Asociar usuario con barbero
            $hernan->user_id = $hernanUser->id;
            $hernan->save();

            $this->command->info("✓ Usuario creado para Hernán García (email: hernan@studiotorres.com, password: password123)");
        }

        if ($carlos) {
            // Crear usuario para Carlos
            $carlosUser = User::create([
                'name' => 'Carlos Mendez',
                'email' => 'carlos@studiotorres.com',
                'password' => Hash::make('password123'),
                'role' => 'BARBER',
            ]);

            // Asociar usuario con barbero
            $carlos->user_id = $carlosUser->id;
            $carlos->save();

            $this->command->info("✓ Usuario creado para Carlos Mendez (email: carlos@studiotorres.com, password: password123)");
        }
    }
}
