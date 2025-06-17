<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Ramsey\Uuid\Type\Integer;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StarPackage>
 */
class StarPackageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'package_name' => fake()->name(),
            'star_amount' => fake()->numberBetween(1,100),
            'extra_star_amount' => fake()->numberBetween(1,100),
            'total_star_amount' => fake()->numberBetween(1,100),
            'star_created_by' => fake()->name(),
            'star_updated_by'=> fake()->name()
        
        ];
    }
}
