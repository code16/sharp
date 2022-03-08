<?php

namespace Database\Factories;

use Database\Seeders\WithTextFixtures;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    use WithTextFixtures;

    public function definition()
    {
        return [
            'name' => $this->faker->unique()->randomElement(static::$categoryNames),
        ];
    }
}
