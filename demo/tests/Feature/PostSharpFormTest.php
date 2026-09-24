<?php

use App\Models\Post;
use App\Models\User;
use App\Sharp\Entities\PostEntity;
use App\Sharp\Posts\Commands\PreviewPostCommand;
use Code16\Sharp\Utils\Testing\SharpAssertions;

uses(SharpAssertions::class);

it('can edit a post', function () {
    $this->loginAsSharpUser(User::factory()->create(['role' => 'admin']));
    $post = Post::factory()->create();

    $this->sharpForm(PostEntity::class, $post->id)
        ->edit()
        ->assertOk();

    $this->sharpForm(PostEntity::class)
        ->create()
        ->assertOk();
});

it('can update a post', function () {
    $this->loginAsSharpUser(User::factory()->create(['role' => 'admin']));
    $post = Post::factory()->create(['content' => ['fr' => 'old', 'en' => 'old']]);

    $this->sharpForm(PostEntity::class, $post->id)
        ->update(
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
});

it('can not update a post with invalid data', function () {
    $this->loginAsSharpUser(User::factory()->create(['role' => 'admin']));
    $post = Post::factory()->create();

    $this->sharpForm(PostEntity::class, $post->id)
        ->update(
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
});

it('can store a new post', function () {
    $this->loginAsSharpUser(User::factory()->create(['role' => 'admin']));

    $this->sharpForm(PostEntity::class)
        ->store([
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
        ])
        ->assertSessionHasNoErrors();

    $this->assertDatabaseHas('posts', [
        'title' => json_encode(['en' => 'title', 'fr' => 'titre']),
        'published_at' => now()->setTime(10, 30)->format('Y-m-d H:i:s'),
        'content' => json_encode(['en' => 'new', 'fr' => 'nouveau']),
    ]);
});

it('can delete a post', function () {
    $this->loginAsSharpUser(User::factory()->create(['role' => 'admin']));
    $post1 = Post::factory()->create();
    $post2 = Post::factory()->create();

    $this->sharpShow(PostEntity::class, $post1->id)
        ->delete()
        ->assertRedirect();

    $this->assertDatabaseMissing('posts', ['id' => $post1->id]);
    $this->assertDatabaseHas('posts', ['id' => $post2->id]);

    $this->sharpList(PostEntity::class)
        ->delete($post2->id)
        ->assertOk();

    $this->assertDatabaseMissing('posts', ['id' => $post2->id]);
});

it('as an editor is not authorized to update a post of another editor', function () {
    $this->loginAsSharpUser(User::factory()->create(['role' => 'editor']));

    $publishedPost = Post::factory()
        ->for(User::factory(), 'author')
        ->create();

    $this->sharpShow(PostEntity::class, $publishedPost->id)
        ->get()
        ->assertOk();

    $this->sharpShow(PostEntity::class, $publishedPost->id)
        ->sharpForm(PostEntity::class, $publishedPost->id)
        ->edit()
        ->assertForbidden();
});

it('as an editor is not authorized to view an unpublished post of another editor', function () {
    $this->loginAsSharpUser(User::factory()->create(['role' => 'editor']));

    $publishedPost = Post::factory()
        ->for(User::factory(), 'author')
        ->create([
            'state' => 'draft',
        ]);

    $this->sharpShow(PostEntity::class, $publishedPost->id)
        ->get()
        ->assertForbidden();
});

it('can preview a post through command', function () {
    $this->loginAsSharpUser(User::factory()->create(['role' => 'admin']));
    $post = Post::factory()->create();

    $this->sharpList(PostEntity::class)
        ->instanceCommand(PreviewPostCommand::class, $post->id)
        ->post()
        ->assertReturnsView('pages.post');
});
