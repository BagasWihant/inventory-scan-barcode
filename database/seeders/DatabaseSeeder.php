<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Pallet;
use App\Models\Product;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::table('menu_options')->insert(
            [
                'code' => '1',
                'name' => 'DISABLE ALL MENU, PREPARE STOCK TAKING',
                'status' => '1',
            ]
        );
    }
}
