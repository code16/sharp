<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Post;
use App\Models\PostBlock;
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

        $categories = Category::factory()
            ->count(8)
            ->create();

        Post::factory()
            ->state(new Sequence(
                ['author_id' => $admin->id],
                ['author_id' => $editors[0]->id],
                ['author_id' => $editors[1]->id],
            ))
            ->count(50)
            ->create()
            ->each(function (Post $post) use ($categories) {
                $post->categories()->attach($categories->shuffle()->take(rand(1, 3))->pluck('id'));

                if(rand(0, 1)) {
                    PostBlock::factory()->text()->create(['post_id' => $post->id]);
                }
                if(rand(0, 1)) {
                    PostBlock::factory()->video()->create(['post_id' => $post->id]);
                }
            });
    }
}
