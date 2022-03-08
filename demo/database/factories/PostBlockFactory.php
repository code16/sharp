<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PostBlockFactory extends Factory
{
    public function definition()
    {
        return [
            'type' => $this->faker->randomElement(['text', 'visuals', 'video']),
        ];
    }

    public function visuals(): self
    {
        return $this->state(fn () => [
            'type' => 'visuals',
        ]);
    }

    public function text(): self
    {
        return $this->state(fn () => [
            'type' => 'text',
            'content' => $this->faker->paragraph,
        ]);
    }

    public function video(): self
    {
        return $this->state(fn () => [
            'type' => 'video',
            'content' => '<iframe width="560" height="315" src="https://www.youtube.com/embed/R1h4Vl6oTyA?controls=0" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>',
        ]);
    }
}
