<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'role_id' => fake()->name(),
            'name' => fake()->name(),
            'username' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' =>'hellowow',
            'userphoto'=>'./assets/img/profile.png'

        ];
    }
}
