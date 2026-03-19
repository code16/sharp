<?php

use Code16\Sharp\Form\Fields\SharpFormUploadField;
use Code16\Sharp\Tests\Fixtures\Entities\PersonEntity;
use Code16\Sharp\Tests\Unit\Form\Fakes\FakeSharpForm;
use Code16\Sharp\Utils\Fields\FieldsContainer;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    sharp()->config()->declareEntity(PersonEntity::class);
    Storage::fake('local');
    login();
});

it('uploads a file from field with validation', function () {
    $field = SharpFormUploadField::make('file')
        ->setMaxFileSize(1)
        ->setImageOnly();

    fakeFormFor('person', new class($field) extends FakeSharpForm
    {
        public function __construct(private SharpFormUploadField $field) {}
        public function buildFormFields(FieldsContainer $formFields): void
        {
            $formFields->addField($this->field);
        }
    });

    $this
        ->postJson(
            route('code16.sharp.api.form.upload', [
                'entityKey' => 'person',
                'uploadFieldKey' => 'file',
            ]), [
                'file' => UploadedFile::fake()->image('image.jpg', 600, 600),
            ])
        ->assertOk()
        ->assertJson(['name' => 'image.jpg']);

    $this
        ->postJson(route('code16.sharp.api.form.upload', [
            'entityKey' => 'person',
            'uploadFieldKey' => 'file',
        ]), [
            'file' => UploadedFile::fake()->create('file.pdf'),
        ])
        ->assertStatus(422)
        ->assertJsonValidationErrors('file');
});
