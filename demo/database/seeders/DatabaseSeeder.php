<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $admin = User::factory(['email' => 'admin@example.org', 'role' => 'admin'])
            ->create();

        $editors = User::factory(['email' => 'editor@example.org'])
            ->state(new Sequence(
                ['email' => 'editor@example.org'],
                ['email' => 'editor2@example.org'],
            ))
            ->count(2)
            ->create();

        $posts = Post::factory()
            ->state(new Sequence(
                ['author_id' => $admin->id],
                ['author_id' => $editors[0]->id],
                ['author_id' => $editors[1]->id],
            ))
            ->count(50)
            ->create();

        Category::factory()->count(8)
            ->create()
            ->each(function (Category $category) use ($posts) {
                $category->posts()->attach($posts->shuffle()->take(rand(5, 12))->pluck('id'));
            });
    }
}
