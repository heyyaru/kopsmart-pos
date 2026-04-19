<?php

namespace Database\Seeders;

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
        \App\Models\Product::create(['name' => 'Kopi Susu', 'price' => 15000, 'stock' => 100, 'barcode' => '111']);
        \App\Models\Product::create(['name' => 'Roti Bakar', 'price' => 12000, 'stock' => 50, 'barcode' => '222']);
        \App\Models\Product::create(['name' => 'Teh Manis', 'price' => 5000, 'stock' => 200, 'barcode' => '333']);
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
        // Buat User Admin
        User::create([
            'name' => 'Juragan Toko',
            'email' => 'admin@toko.com',
            'password' => bcrypt('password'), // Password-nya 'password'
        ]);
    }
}
