<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Administrador',
            'email' => env('ADMIN_EMAIL', 'admin@studiotorres.com'),
            'password' => Hash::make(env('ADMIN_PASSWORD', 'Admin123!')),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        $this->command->info('✓ Admin user created: ' . env('ADMIN_EMAIL', 'admin@studiotorres.com'));
    }
}
