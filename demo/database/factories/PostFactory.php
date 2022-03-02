<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    public function definition()
    {
        return [
            'title' => [
                'fr' => $this->faker->sentence,
                'en' => $this->faker->sentence,
            ],
            'content' => [
                'fr' => collect($this->faker->paragraphs(rand(3, 5)))->map(fn($paragraph) => "<p>$paragraph</p>")->implode(''),
                'en' => collect($this->faker->paragraphs(rand(3, 5)))->map(fn($paragraph) => "<p>$paragraph</p>")->implode(''),
            ],
            'state' => 'online',
            'published_at' => now()->subMinutes($this->faker->numberBetween(-60*24*3, 60*24*50)),
        ];
    }
}
