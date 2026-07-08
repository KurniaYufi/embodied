<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        User::factory()->create([
            'name' => 'Admin Embodied',
            'email' => 'admin-emboied@embodied.com',
            'password' => Hash::make('embodied123'),
        ]);

        $this->call([
            CategorySeeder::class,
            SizeSeeder::class,
            ProductSeeder::class,
            OrderSeeder::class,
        ]);
    }
}