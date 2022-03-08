<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use App\Sharp\Posts\Commands\PreviewPostCommand;
use Code16\Sharp\Form\Fields\SharpFormDateField;
use Code16\Sharp\Utils\Testing\SharpAssertions;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class PostSharpFormTest extends TestCase
{
    use DatabaseMigrations, SharpAssertions;

    /** @test */
    public function we_can_get_a_valid_post_update_form()
    {
        $this->loginAsSharpUser(User::factory()->create(['role' => 'admin']));
        $post = Post::factory()->create();

        $this->getSharpForm('posts', $post->id)
            ->assertSharpFormHasFieldOfType('published_at', SharpFormDateField::class)
            ->assertSharpFormHasFields([
                'title', 'content', 'categories', 'cover',
            ]);
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

    /** @test */
    public function we_can_get_a_valid_post_create_form()
    {
        $this->loginAsSharpUser(User::factory()->create(['role' => 'admin']));

        $this->getSharpForm('posts')
            ->assertSharpFormHasFields([
                'title', 'content', 'categories', 'cover',
            ]);
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
            ->assertOk();

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'content' => json_encode(['fr' => 'new', 'en' => 'new']),
        ]);
    }

    /** @test */
    public function as_an_editor_we_are_not_authorize_to_update_a_spaceship_of_another_editor()
    {
        $this->loginAsSharpUser(User::factory()->create(['role' => 'editor']));

        $post = Post::factory()
            ->for(User::factory(), 'author')
            ->create();

        $this->getSharpForm('posts', $post->id)
            ->assertSharpHasNotAuthorization('update');
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->initSharpAssertions();
    }
}
