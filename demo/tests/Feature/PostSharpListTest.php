<?php

use App\Enums\PostState;
use App\Models\Post;
use App\Models\User;
use App\Sharp\Entities\PostEntity;
use App\Sharp\Utils\Filters\AuthorFilter;
use App\Sharp\Utils\Filters\StateFilter;
use Illuminate\Testing\Fluent\AssertableJson;

it('can display the post list', function () {
    $this->loginAsSharpUser(User::factory()->create(['role' => 'admin']));
    $post1 = Post::factory()->create(['published_at' => now()->subDay()]);
    $post2 = Post::factory()->create(['published_at' => now()]);

    $this->sharpList(PostEntity::class)
        ->get()
        ->assertOk()
        ->assertListData(fn (AssertableJson $data) => $data
            ->count(2)
            ->where('0.id', $post2->id)
            ->where('1.id', $post1->id)
            ->etc()
        );
});

it('can filter the post list by state', function () {
    $this->loginAsSharpUser(User::factory()->create(['role' => 'admin']));
    Post::factory()->count(2)->create(['state' => 'online']);
    $draft = Post::factory()->create(['state' => 'draft']);

    $this->sharpList(PostEntity::class)
        ->withFilter(StateFilter::class, 'draft')
        ->get()
        ->assertOk()
        ->assertListData(fn (AssertableJson $data) => $data
            ->count(1)
            ->where('0.id', $draft->id)
            ->where('0.is_draft', true)
            ->etc()
        );
});

it('can filter the post list by author', function () {
    $this->loginAsSharpUser(User::factory()->create(['role' => 'admin']));
    $author = User::factory()->create();
    $post = Post::factory()->for($author, 'author')->create();
    Post::factory()->for(User::factory(), 'author')->create();

    $this->sharpList(PostEntity::class)
        ->withFilter(AuthorFilter::class, $author->id)
        ->get()
        ->assertOk()
        ->assertListData(fn (AssertableJson $data) => $data
            ->count(1)
            ->where('0.id', $post->id)
            ->etc()
        );
});

it('can update the state of a post', function () {
    $this->loginAsSharpUser(User::factory()->create(['role' => 'admin']));
    $post = Post::factory()->create(['state' => 'draft']);

    $this->sharpList(PostEntity::class)
        ->entityState($post->id, 'online')
        ->assertReturnsRefresh([$post->id])
        ->assertJson(['value' => 'online']);

    expect($post->fresh()->state)->toBe(PostState::ONLINE);
});

it('can not update the state of a post with an unknown state', function () {
    $this->loginAsSharpUser(User::factory()->create(['role' => 'admin']));
    $post = Post::factory()->create(['state' => 'draft']);

    $this->sharpList(PostEntity::class)
        ->entityState($post->id, 'unknown')
        ->assertStatus(422);

    expect($post->fresh()->state)->toBe(PostState::DRAFT);
});

it('as an editor can update the state of its own post but not of another editor’s post', function () {
    $editor = User::factory()->create(['role' => 'editor']);
    $this->loginAsSharpUser($editor);

    $ownPost = Post::factory()->for($editor, 'author')->create(['state' => 'draft']);
    $otherPost = Post::factory()->for(User::factory(), 'author')->create(['state' => 'draft']);

    $this->sharpList(PostEntity::class)
        ->entityState($ownPost->id, 'online')
        ->assertReturnsRefresh([$ownPost->id]);

    $this->sharpList(PostEntity::class)
        ->entityState($otherPost->id, 'online')
        ->assertForbidden();

    expect($ownPost->fresh()->state)->toBe(PostState::ONLINE)
        ->and($otherPost->fresh()->state)->toBe(PostState::DRAFT);
});
