<?php

namespace Database\Seeders;

use App\Models\Coffee;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Admin User for Sanctum Authentication
        User::firstOrCreate(
            ['email' => 'admin@kopinusantara.id'],
            [
                'name'     => 'Akmal Manggala Putra (Roaster Admin)',
                'password' => Hash::make('password123'),
            ]
        );

        $this->call([
            CoffeeSeeder::class,
        ]);
    }
}
