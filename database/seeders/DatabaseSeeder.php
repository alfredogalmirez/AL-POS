<?php

namespace Database\Seeders;

use App\Models\Product;
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
        // Product::factory(50)->create();

        User::updateOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Admin User',
                'username' => 'admin',
                'password' => bcrypt('password'),
                'role' => 'admin',
            ]
        );

        User::create([
            'name' => 'Cashier User',
            'username' => 'cashier',
            'password' => bcrypt('password'),
            'role' => 'cashier',
        ]);
    }
}
