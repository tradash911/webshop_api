<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
                "name" => fake()->word(1,3),
                "price"=>fake()->numberBetween(1000,20000),
                "description"=>fake()->word(4,10),
                "quantity"=>fake()->numberBetween(1,30),
                'is_active'=>fake()->boolean(90),
                "weight" =>fake()->numberBetween(10,3000),
                
                "category_id" => Category::inRandomOrder()->first()->id,

        ];
    }
}
