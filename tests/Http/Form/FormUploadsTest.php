<?php

use Code16\Sharp\Form\Fields\SharpFormUploadField;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    config()->set('sharp.uploads.tmp_dir', 'tmp');
    Storage::fake('local');
});

it('upload a file from field with validation', function () {
    $field = SharpFormUploadField::make('file')
        ->setMaxFileSize(1)
        ->setImageOnly();
    
    $this->postJson(route('code16.sharp.api.form.upload'), [
        'file' => UploadedFile::fake()->image('image.jpg', 600, 600),
        'validation_rule' => $field->toArray()['validationRule'],
    ])
        ->assertOk()
        ->assertJson(['name' => 'image.jpg']);
    
    $this->postJson(route('code16.sharp.api.form.upload'), [
        'file' => UploadedFile::fake()->create('file.pdf'),
        'validation_rule' => $field->toArray()['validationRule'],
    ])
        ->assertStatus(422)
        ->assertJsonValidationErrors('file');
});
