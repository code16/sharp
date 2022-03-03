<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Media;
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

        Media::factory([
            'model_id' => $admin->id,
            'model_type' => User::class,
            'model_key' => 'avatar',
        ])
            ->withFile(storage_path('app/seed/users/01.jpeg'))
            ->create();

        $editor1 = User::factory(['email' => 'editor@example.org'])
            ->create();

        Media::factory([
            'model_id' => $editor1->id,
            'model_type' => User::class,
            'model_key' => 'avatar',
        ])
            ->withFile(storage_path('app/seed/users/02.jpeg'))
            ->create();

        $editor2 = User::factory(['email' => 'editor2@example.org'])
            ->create();

        Media::factory([
            'model_id' => $editor2->id,
            'model_type' => User::class,
            'model_key' => 'avatar',
        ])
            ->withFile(storage_path('app/seed/users/03.jpeg'))
            ->create();

        $categories = Category::factory()
            ->count(8)
            ->create();

        Post::factory()
            ->state(new Sequence(
                ['author_id' => $admin->id],
                ['author_id' => $editor1->id],
                ['author_id' => $editor2->id],
            ))
            ->count(50)
            ->create()
            ->each(function (Post $post) use ($categories) {
                $post->categories()->attach($categories->shuffle()->take(rand(1, 3))->pluck('id'));

                if (rand(0, 1)) {
                    PostBlock::factory()->text()->create(['post_id' => $post->id]);
                }
                if (rand(0, 1)) {
                    PostBlock::factory()->video()->create(['post_id' => $post->id]);
                }
            });
    }
}
