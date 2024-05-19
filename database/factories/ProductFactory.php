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
        return [
            'pallet_barcode' => $this->faker->randomElement(Pallet::all())['pallet_barcode'],
            'product_barcode' => $this->faker->ean13(),
            'product_name' => $this->faker->word,
            'stock' => $this->faker->numberBetween(0, 138),
        ];
    }
}
