<?php

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    config()->set('sharp.uploads.tmp_dir', 'tmp');
    Storage::fake('local');
});

it('allows to upload a file', function () {
    $this
        ->postJson(route('code16.sharp.api.form.upload'), [
            'file' => UploadedFile::fake()->image('image.jpg', 600, 600),
        ])
        ->assertOk()
        ->assertJson(['name' => 'image.jpg']);
});

it('allows to upload a file on a custom defined disk', function () {
    config()->set('sharp.uploads.tmp_disk', 'uploads');
    Storage::fake('uploads');

    $this
        ->postJson(route('code16.sharp.api.form.upload'), [
            'file' => UploadedFile::fake()->image('image.jpg', 600, 600),
        ])
        ->assertOk();

    Storage::disk('uploads')->assertExists('/tmp/image.jpg');
});

it('adds a increment to the file name if needed', function () {
    $this
        ->postJson(route('code16.sharp.api.form.upload'), [
            'file' => UploadedFile::fake()->image('image.jpg', 600, 600),
        ])
        ->assertOk()
        ->assertJson(['name' => 'image.jpg']);

    $this
        ->postJson(route('code16.sharp.api.form.upload'), [
            'file' => UploadedFile::fake()->image('image.jpg', 600, 600),
        ])
        ->assertOk()
        ->assertJson(['name' => 'image-1.jpg']);
});

it('copies the file to the wanted directory', function () {
    $this
        ->postJson(route('code16.sharp.api.form.upload'), [
            'file' => UploadedFile::fake()->image('image.jpg', 600, 600),
        ]);

    $this->assertTrue(Storage::disk('local')->exists('/tmp/image.jpg'));
});

it('throws a validation exception on missing file even without explicit rule', function () {
    $this
        ->postJson(route('code16.sharp.api.form.upload'), [
            'file' => null,
        ])
        ->assertStatus(422)
        ->assertJsonValidationErrorFor('file');

    $this
        ->postJson(route('code16.sharp.api.form.upload'), [
            'file' => 'not a file',
        ])
        ->assertStatus(422)
        ->assertJsonValidationErrorFor('file');
});

it('validates on explicit rules', function () {
    $this
        ->postJson(route('code16.sharp.api.form.upload'), [
            'file' => UploadedFile::fake()->create('file.xls'),
            'validation_rule' => ['image'],
        ])
        ->assertStatus(422)
        ->assertJsonValidationErrorFor('file');

    $this
        ->postJson(route('code16.sharp.api.form.upload'), [
            'file' => UploadedFile::fake()->create('file.xls', 1024 * 3),
            'validation_rule' => ['max:2048'],
        ])
        ->assertStatus(422)
        ->assertJsonValidationErrorFor('file');
});

it('does not permit to send insecure validation rules', function () {
    $this
        ->postJson(route('code16.sharp.api.form.upload'), [
            'file' => UploadedFile::fake()->create('file.xls'),
            'id' => 1,
            'validation_rule' => ['file', 'exists:users,id'],
        ])
        ->assertStatus(422)
        ->assertJsonValidationErrorFor('validation_rule.1');
});
