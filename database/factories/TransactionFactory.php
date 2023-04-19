<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Seller;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $seller = Seller::has('products')->get()->random()->first();
        $buyer = User::all()->except($seller->id)->random()->first();
        return [
            'quantity' => fake()->numberBetween(1,3),
            'buyer_id' => $buyer->id,
            'product_id' => $seller->products->random()->id,
        ];
    }
}
