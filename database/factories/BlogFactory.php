<?php

namespace Database\Factories;

use App\Models\Genre;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Blog>
 */
class BlogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id'=>User::factory(),
            'title'=>fake()->sentence(),
            'blogcoverphoto' => fake()->sentence(),
            'slug'=>fake()->slug(),
            'summary'=>fake()->paragraph(),
            'rating' => fake()->numberBetween(0,5)
           
   
        
            
        ];
    }
}
