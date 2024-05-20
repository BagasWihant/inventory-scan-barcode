<?php

namespace Database\Factories;

use App\Models\Pallet;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $random = ['04860518','22170286','56399264','47304253','34838662','76899737','982742','203948','029375','12987'];
        return [
            'pallet_barcode' => $this->faker->randomElement(Pallet::all())['pallet_barcode'],
            'material_no' => $this->faker->randomElement($random),
            'qty' => $this->faker->numberBetween(0, 1092),
        ];
    }
}