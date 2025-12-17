<?php

use Code16\Sharp\Tests\Fixtures\Entities\PersonEntity;
use Illuminate\Http\UploadedFile;

beforeEach(function () {
    sharp()->config()->declareEntity(PersonEntity::class);
    login();
});

it('returns thumbnail', function () {
    UploadedFile::fake()->image('test.jpg', 600, 600)
        ->storeAs('data/Posts/1', 'image.jpg', ['disk' => 'local']);

    $this->postJson(route('code16.sharp.api.form.upload.thumbnail.show', [
        'filterKey' => 'root',
        'entityKey' => 'person',
        'instanceId' => '1',
        'path' => 'data/Posts/1/image.jpg',
        'disk' => 'local',
        'width' => 1000,
        'height' => 1000,
    ]))
        ->assertOk()
        ->assertJson([
            'thumbnail' => sprintf(
                '/storage/thumbnails/data/Posts/1/1000-1000_q-90/image.jpg?%s',
                Storage::disk('public')->lastModified('thumbnails/data/Posts/1/1000-1000_q-90/image.jpg')
            ),
        ]);
});
