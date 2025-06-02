<?php

namespace Database\Factories;

use Database\Seeders\WithTextFixtures;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostAttachmentFactory extends Factory
{
    use WithTextFixtures;

    public function definition()
    {
        return [
            'title' => $this->faker->randomElement(static::$attachmentTitles),
            'is_link' => $this->faker->boolean,
            'link_url' => $this->faker->url,
        ];
    }
}
