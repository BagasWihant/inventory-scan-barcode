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
        // User::factory(10)->create();
        User::factory()->create([
            'name' => 'bagas',
            'email' => 'bagas@mail.com',
        ]);
        
        DB::table('pallets')->insert([
            'pallet_no' => 'Y-01-00003',
            'line_c' => 'CNC',
            'pallet_serial' => 'asda',
            'trucking_id' => 987,
        ]);
        $csvFile = public_path('data.csv');

        if (!file_exists($csvFile) || !is_readable($csvFile)) {
            $this->command->error('CSV file not found or not readable');
            return;
        }

        if (($handle = fopen($csvFile, 'r')) !== false) {
            
            while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                DB::table('products')->insert([
                    'pallet_no' => $data[2],
                    'material_no' => $data[0],
                    'picking_qty' => $data[1],
                ]);
            }

            fclose($handle);
            $this->command->info('Data inserted successfully');
        } else {
            $this->command->error('Error reading CSV file');
        }
        // Pallet::factory(6)->create();
        // Product::factory(120)->create();

    }
}
