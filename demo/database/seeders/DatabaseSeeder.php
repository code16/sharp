<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Media;
use App\Models\Post;
use App\Models\PostBlock;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->deleteStorageFiles();

        $admin = User::factory(['email' => 'admin@example.org', 'role' => 'admin'])
            ->create();
        $editor1 = User::factory(['email' => 'editor@example.org'])
            ->create();
        $editor2 = User::factory(['email' => 'editor2@example.org'])
            ->create();

        collect([$admin, $editor1, $editor2])
            ->shuffle()
            ->each(function (User $user, int $k) {
                Media::factory([
                    'model_id' => $user->id,
                    'model_type' => User::class,
                    'model_key' => 'avatar',
                ])
                    ->withFile(
                        database_path(sprintf('seeders/files/users/%s.jpg', $k + 1)),
                        sprintf('users/%s', $user->id),
                    )
                    ->create();
            });

        $categories = Category::factory()
            ->count(16)
            ->create();

        $coverImages = glob(database_path('seeders/files/posts/*.*'));
        $posts = Post::factory()
            ->state(new Sequence(
                ['author_id' => $admin->id],
                ['author_id' => $editor1->id],
                ['author_id' => $editor2->id],
            ))
            ->count(25)
            ->create()
            ->each(function (Post $post) use ($categories, $coverImages) {
                Media::factory([
                    'model_id' => $post->id,
                    'model_type' => Post::class,
                    'model_key' => 'cover',
                ])
                    ->withFile(
                        Arr::random($coverImages),
                        sprintf('posts/%s', $post->id),
                    )
                    ->create();

                $post->categories()->attach($categories->shuffle()->take(rand(1, 3))->pluck('id'));

                if (rand(0, 1)) {
                    PostBlock::factory()->text()->create(['post_id' => $post->id]);
                }
                if (rand(0, 1)) {
                    PostBlock::factory()->video()->create(['post_id' => $post->id]);
                }
                if (rand(0, 1)) {
                    $visualsPostBlock = PostBlock::factory()->visuals()->create(['post_id' => $post->id]);
                    for ($k = 0; $k < rand(2, 5); $k++) {
                        Media::factory([
                            'model_id' => $visualsPostBlock->id,
                            'model_type' => PostBlock::class,
                            'model_key' => 'files',
                        ])
                            ->withFile(
                                Arr::random($coverImages),
                                sprintf('posts/%s/blocks/%s', $post->id, $visualsPostBlock->id),
                            )
                            ->create();
                    }
                }
                if (! rand(0, 3)) {
                    $post->update(['state' => 'draft']);
                }
            });

        $posts->shuffle()
            ->take(15)
            ->each(function (Post $post) use ($posts) {
                $post->setTranslation(
                    'content',
                    'en',
                    sprintf(
                        '<x-related-post post="%s"></x-related-post>%s',
                        $posts->filter(fn ($filteredPost) => ! $filteredPost->is($post))
                            ->shuffle()
                            ->first()
                            ->id,
                        $post->getTranslation('content', 'en')
                    )
                );
                $post->save();
            });
    }

    private function deleteStorageFiles(): void
    {
        Storage::disk('local')->deleteDirectory('data');
        Storage::disk('local')->deleteDirectory('public/thumbnails');
    }
}
