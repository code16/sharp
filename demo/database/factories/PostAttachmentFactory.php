<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PostAttachmentFactory extends Factory
{
    public function definition()
    {
        return [
            'title' => $this->faker->sentence,
            'is_link' => $this->faker->boolean,
            'link_url' => $this->faker->url,
        ];
    }
}
