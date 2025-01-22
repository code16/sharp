<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use App\Sharp\Posts\Commands\PreviewPostCommand;
use Code16\Sharp\Utils\Testing\SharpAssertions;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class PostSharpFormTest extends TestCase
{
    use LazilyRefreshDatabase;
    use SharpAssertions;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    /** @test */
    public function we_can_edit_a_post()
    {
        $this->loginAsSharpUser(User::factory()->create(['role' => 'admin']));
        $post = Post::factory()->create();

        $this
            ->withSharpBreadcrumb(
                fn ($builder) => $builder->appendEntityList('posts')
            )
            ->getSharpForm('posts', $post->id)
            ->assertOk();

        $this
            ->withSharpBreadcrumb(
                fn ($builder) => $builder->appendEntityList('posts')
            )
            ->getSharpForm('posts')
            ->assertOk();
    }

    /** @test */
    public function we_can_update_a_post()
    {
        $this->loginAsSharpUser(User::factory()->create(['role' => 'admin']));
        $post = Post::factory()->create(['content' => ['fr' => 'old', 'en' => 'old']]);

        $this
            ->updateSharpForm(
                'posts',
                $post->id,
                array_merge(
                    $post->toArray(),
                    [
                        'content' => [
                            'text' => [
                                'fr' => 'new',
                                'en' => 'new',
                            ],
                        ],
                    ],
                ),
            )
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'content' => json_encode(['fr' => 'new', 'en' => 'new']),
        ]);
    }

    /** @test */
    public function we_can_not_update_a_post_with_invalid_data()
    {
        $this->loginAsSharpUser(User::factory()->create(['role' => 'admin']));
        $post = Post::factory()->create();

        $this
            ->updateSharpForm(
                'posts',
                $post->id,
                array_merge(
                    $post->toArray(),
                    [
                        'title' => [
                            'fr' => 'updated',
                            'en' => null,
                        ],
                    ],
                ),
            )
            ->assertSessionHasErrors(['title.en']);
    }

    /** @test */
    public function we_can_store_a_new_post()
    {
        $this->loginAsSharpUser(User::factory()->create(['role' => 'admin']));

        $this
            ->storeSharpForm(
                'posts',
                [
                    'title' => [
                        'fr' => 'titre',
                        'en' => 'title',
                    ],
                    'published_at' => now()->setTime(10, 30)->format('Y-m-d H:i:s'),
                    'content' => [
                        'text' => [
                            'fr' => 'nouveau',
                            'en' => 'new',
                        ],
                    ],
                ],
            )
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('posts', [
            'title' => json_encode(['en' => 'title', 'fr' => 'titre']),
            'published_at' => now()->setTime(10, 30)->format('Y-m-d H:i:s'),
            'content' => json_encode(['en' => 'new', 'fr' => 'nouveau']),
        ]);
    }

    /** @test */
    public function we_can_delete_a_post()
    {
        $this->loginAsSharpUser(User::factory()->create(['role' => 'admin']));
        $post1 = Post::factory()->create();
        $post2 = Post::factory()->create();

        $this
            ->deleteFromSharpShow('posts', $post1->id)
            ->assertRedirect();

        $this->assertDatabaseMissing('posts', ['id' => $post1->id]);
        $this->assertDatabaseHas('posts', ['id' => $post2->id]);

        $this
            ->deleteFromSharpList('posts', $post2->id)
            ->assertOk();

        $this->assertDatabaseMissing('posts', ['id' => $post2->id]);
    }

    /** @test */
    public function as_an_editor_we_are_not_authorize_to_update_a_post_of_another_editor()
    {
        $this->loginAsSharpUser(User::factory()->create(['role' => 'editor']));

        $publishedPost = Post::factory()
            ->for(User::factory(), 'author')
            ->create();

        $this
            ->withSharpBreadcrumb(
                fn ($builder) => $builder->appendEntityList('posts')
            )
            ->getSharpShow('posts', $publishedPost->id)
            ->assertOk();

        $this
            ->withSharpBreadcrumb(
                fn ($builder) => $builder
                    ->appendEntityList('posts')
                    ->appendShowPage('posts', $publishedPost->id),
            )
            ->getSharpForm('posts', $publishedPost->id)
            ->assertForbidden();
    }

    /** @test */
    public function as_an_editor_we_are_not_authorize_to_view_an_unpublished_post_of_another_editor()
    {
        $this->loginAsSharpUser(User::factory()->create(['role' => 'editor']));

        $publishedPost = Post::factory()
            ->for(User::factory(), 'author')
            ->create([
                'state' => 'draft',
            ]);

        $this
            ->withSharpBreadcrumb(
                fn ($builder) => $builder->appendEntityList('posts')
            )
            ->getSharpShow('posts', $publishedPost->id)
            ->assertForbidden();
    }

    /** @test */
    public function we_can_preview_a_post_through_command()
    {
        $this->loginAsSharpUser(User::factory()->create(['role' => 'admin']));
        $post = Post::factory()->create();

        $this
            ->callSharpInstanceCommandFromList(
                'posts',
                $post->id,
                PreviewPostCommand::class,
            )
            ->assertOk();
    }
}
