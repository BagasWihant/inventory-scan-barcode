<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Pallet;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pallet>
 */
class PalletFactory extends Factory
{
    protected $model = Pallet::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'pallet_barcode' => $this->faker->ean8(),
            'line' => 'CNC',
            'pallet_serial' => $this->faker->ean8() . ' CNC',
            'trucking_id' => $this->faker->numerify('#######'),
            'scanned_by' => $this->faker->randomElement(User::all())['id']
        ];
    }
}
