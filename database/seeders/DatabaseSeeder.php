<?php

namespace Database\Seeders;

use App\Models\Pallet;
use App\Models\Product;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        // User::factory()->create([
        //     'name' => 'bagas',
        //     'email' => 'bagas@mail.com',
        // ]);

        Pallet::factory(6)->create();
        Product::factory(120)->create();

    }
}
