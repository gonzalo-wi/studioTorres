<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            [
                'title' => 'Corte de Cabello',
                'description' => 'Corte de cabello masculino profesional con técnicas modernas. Incluye lavado y secado.',
                'duration_minutes' => 30,
                'price' => 3500.00,
                'active' => true,
            ],
            [
                'title' => 'Corte + Barba',
                'description' => 'Servicio completo que incluye corte de cabello y perfilado/arreglo de barba. Experiencia completa de barbería.',
                'duration_minutes' => 60,
                'price' => 5500.00,
                'active' => true,
            ],
        ];

        foreach ($services as $service) {
            Service::create($service);
            $this->command->info('✓ Service created: ' . $service['title']);
        }
    }
}
