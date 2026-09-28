<?php

use Code16\Sharp\Tests\Fixtures\Entities\PersonEntity;
use Code16\Sharp\Utils\Entities\SharpEntityManager;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\URL;

beforeEach(function () {
    sharp()->config()->declareEntity(PersonEntity::class);
    login();
    Storage::fake('public');
});

it('returns thumbnail', function () {
    UploadedFile::fake()->image('test.jpg', 600, 600)
        ->storeAs('data/Posts/1', 'image.jpg', ['disk' => 'local']);

    $this->postJson(
        URL::temporarySignedRoute('code16.sharp.api.form.upload.thumbnail.show', now()->addMinutes(120), [
            'entityKey' => 'person',
            'instanceId' => '1',
            'path' => 'data/Posts/1/image.jpg',
            'disk' => 'local',
            'width' => 1000,
            'height' => 1000,
        ])
    )
        ->assertOk()
        ->assertJson([
            'thumbnail' => sprintf(
                '/storage/thumbnails/data/Posts/1/1000-1000_q-90/image.jpg?%s',
                Storage::disk('public')->lastModified('thumbnails/data/Posts/1/1000-1000_q-90/image.jpg')
            ),
        ]);
});

it('returns a 401 for an invalid signature', function () {
    UploadedFile::fake()->image('test.jpg', 600, 600)
        ->storeAs('data/Posts/1', 'image.jpg', ['disk' => 'local']);

    $this->postJson(route('code16.sharp.api.form.upload.thumbnail.show', [
        'entityKey' => 'person',
        'instanceId' => '1',
        'path' => 'data/Posts/1/image.jpg',
        'disk' => 'local',
        'width' => 1000,
        'height' => 1000,
    ]))
        ->assertStatus(401);
});

it('does not allow to get a thumbnail without authorization', function () {
    app(SharpEntityManager::class)
        ->entityFor('person')
        ->setProhibitedActions(['view']);

    UploadedFile::fake()->image('test.jpg', 600, 600)
        ->storeAs('data/Posts/1', 'image.jpg', ['disk' => 'local']);

    $this->postJson(
        URL::temporarySignedRoute('code16.sharp.api.form.upload.thumbnail.show', now()->addMinutes(120), [
            'entityKey' => 'person',
            'instanceId' => '1',
            'path' => 'data/Posts/1/image.jpg',
            'disk' => 'local',
            'width' => 1000,
            'height' => 1000,
        ])
    )
        ->assertForbidden();
});
