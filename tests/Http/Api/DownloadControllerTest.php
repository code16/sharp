<?php

use Code16\Sharp\Tests\Fixtures\Entities\PersonEntity;
use Code16\Sharp\Utils\Entities\SharpEntityManager;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('local');
    sharp()->config()->addEntity('person', PersonEntity::class);
    login();
});

it('allows to download a file from a form field', function () {
    $file = UploadedFile::fake()->image('test.jpg', 600, 600);
    $file->storeAs('/files', 'test.jpg', ['disk' => 'local']);

    $response = $this
        ->get(
            route('code16.sharp.download.show', [
                'entityKey' => 'person',
                'instanceId' => 1,
                'disk' => 'local',
                'path' => '/files/test.jpg',
            ]),
        )
        ->assertOk();

    expect($response->content())
        ->toEqual(Storage::disk('local')->get('files/test.jpg'));
});

it('allows to download a file from a show field', function () {
    $file = UploadedFile::fake()->image('test.jpg', 600, 600);
    $file->storeAs('/files', 'test.jpg', ['disk' => 'local']);

    $response = $this
        ->get(
            route('code16.sharp.download.show', [
                'entityKey' => 'person',
                'instanceId' => 1,
                'disk' => 'local',
                'path' => '/files/test.jpg',
            ]),
        )
        ->assertOk();

    expect($response->content())
        ->toEqual(Storage::disk('local')->get('files/test.jpg'));
});

it('returns a 404 for a missing file', function () {
    $this
        ->get(
            route('code16.sharp.download.show', [
                'entityKey' => 'person',
                'instanceId' => 1,
                'fileName' => 'test.jpg',
            ]),
        )
        ->assertNotFound();
});

it('does not allow to download a file without authorization', function () {
    app(SharpEntityManager::class)
        ->entityFor('person')
        ->setProhibitedActions(['view']);

    $this
        ->get(
            route('code16.sharp.download.show', [
                'entityKey' => 'person',
                'instanceId' => 1,
                'disk' => 'local',
                'path' => '/files/test.jpg',
            ]),
        )
        ->assertForbidden();
});
