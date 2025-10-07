<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Property>
 */
class PropertyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'price' => fake()->numberBetween(500000000, 5000000000),
            'area' => fake()->numberBetween(50, 200),
            'address' => fake()->address(),
            'description' => fake()->paragraph(),
        ];
    }
}
