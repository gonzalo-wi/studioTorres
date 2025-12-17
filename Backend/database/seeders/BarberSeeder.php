<?php

namespace Database\Seeders;

use App\Models\Barber;
use App\Models\BarberSchedule;
use Illuminate\Database\Seeder;

class BarberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create barbers
        $barber1 = Barber::create([
            'name' => 'Hernán García',
            'phone' => '+54 11 1234 5678',
            'email' => 'hernan@barbershop.com',
            'active' => true,
            'earnings_type' => 'PORCENTAJE',
            'earnings_value' => 60,
        ]);

        $barber2 = Barber::create([
            'name' => 'Carlos Mendez',
            'phone' => '+54 11 8765 4321',
            'email' => 'carlos@barbershop.com',
            'active' => true,
            'earnings_type' => 'FIJO',
            'earnings_value' => 5000,
        ]);

        $this->command->info('✓ Barbers created: ' . $barber1->name . ', ' . $barber2->name);

        // Create weekly schedule for both barbers (Monday to Saturday, 10:00-19:00)
        $schedules = [
            ['weekday' => 1, 'start_time' => '10:00:00', 'end_time' => '19:00:00'], // Monday
            ['weekday' => 2, 'start_time' => '10:00:00', 'end_time' => '19:00:00'], // Tuesday
            ['weekday' => 3, 'start_time' => '10:00:00', 'end_time' => '19:00:00'], // Wednesday
            ['weekday' => 4, 'start_time' => '10:00:00', 'end_time' => '19:00:00'], // Thursday
            ['weekday' => 5, 'start_time' => '10:00:00', 'end_time' => '19:00:00'], // Friday
            ['weekday' => 6, 'start_time' => '10:00:00', 'end_time' => '19:00:00'], // Saturday
        ];

        foreach ([$barber1, $barber2] as $barber) {
            foreach ($schedules as $schedule) {
                BarberSchedule::create([
                    'barber_id' => $barber->id,
                    'weekday' => $schedule['weekday'],
                    'start_time' => $schedule['start_time'],
                    'end_time' => $schedule['end_time'],
                ]);
            }
        }

        $this->command->info('✓ Barber schedules created for both barbers (Mon-Sat 10:00-19:00)');
    }
}
