<?php

use Code16\Sharp\Form\Eloquent\WithSharpFormEloquentUpdater;
use Code16\Sharp\Form\Fields\Editor\Uploads\SharpFormEditorUpload;
use Code16\Sharp\Form\Fields\SharpFormEditorField;
use Code16\Sharp\Tests\Fixtures\Entities\PersonEntity;
use Code16\Sharp\Tests\Fixtures\Person;
use Code16\Sharp\Tests\Http\Form\Fixtures\FormEditorUploadsTestEmbed;
use Code16\Sharp\Tests\Unit\Form\Fakes\FakeSharpForm;
use Code16\Sharp\Utils\Fields\FieldsContainer;
use Illuminate\Foundation\Testing\Concerns\InteractsWithViews;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(InteractsWithViews::class);

beforeEach(function () {
    $this->withoutExceptionHandling();
    sharp()->config()->addEntity('person', PersonEntity::class);
    Storage::fake('local');
    login();
});

it('can post a newly uploaded file in editor, create case', function () {
    fakeFormFor('person', $form = new class extends FakeSharpForm
    {
        use WithSharpFormEloquentUpdater;

        public function buildFormFields(FieldsContainer $formFields): void
        {
            $formFields
                ->addField(
                    SharpFormEditorField::make('bio')
                        ->allowUploads(
                            SharpFormEditorUpload::make()
                                ->setStorageBasePath('test/{id}')
                                ->setStorageDisk('local')
                        )
                );
        }

        public function update($id, array $data)
        {
            return $this->save(new Person(), $data)->id;
        }
    });

    $uploadedFileData = $this
        ->postJson(route('code16.sharp.api.form.upload'), ['file' => UploadedFile::fake()->create('file.pdf')])
        ->json();

    $editorXSharpFileData = $this
        ->postJson(route('code16.sharp.api.form.editor.upload.form.update', ['person']), [
            'data' => [
                'file' => $uploadedFileData,
            ],
        ])
        ->json();

    $uploadedImageData = $this
        ->postJson(route('code16.sharp.api.form.upload'), ['file' => UploadedFile::fake()->image('image.jpg', 200, 200)])
        ->json();

    $editorXSharpImageData = $this
        ->postJson(route('code16.sharp.api.form.editor.upload.form.update', ['person']), [
            'data' => [
                'file' => $uploadedImageData,
            ],
        ])
        ->json();

    $this->post('/sharp/s-list/person/s-form/person', [
        'bio' => [
            'text' => <<<'HTML'
                <x-sharp-file data-key="0"></x-sharp-file>
                <x-sharp-image data-key="1"></x-sharp-image>
                HTML,
            'uploads' => [
                $editorXSharpFileData,
                $editorXSharpImageData,
            ],
        ],
    ]);

    expect(Person::first()->bio)->toEqual(
        sprintf(<<<'HTML'
            <x-sharp-file file="%s"></x-sharp-file>
            <x-sharp-image file="%s"></x-sharp-image>
            HTML,
            e(json_encode([
                'file_name' => 'test/1/file.pdf',
                'size' => 0,
                'mime_type' => 'application/pdf',
                'disk' => 'local',
            ])),
            e(json_encode([
                'file_name' => 'test/1/image.jpg',
                'size' => 1367,
                'mime_type' => 'image/jpeg',
                'disk' => 'local',
            ]))
        )
    );

    Storage::disk('local')->assertExists('test/1/file.pdf');
    Storage::disk('local')->assertExists('test/1/image.jpg');
});

it('can post a newly uploaded file in editor, update case', function () {
    Person::create(['bio' => '']);

    fakeFormFor('person', $form = new class extends FakeSharpForm
    {
        use WithSharpFormEloquentUpdater;

        public function buildFormFields(FieldsContainer $formFields): void
        {
            $formFields
                ->addField(
                    SharpFormEditorField::make('bio')
                        ->allowUploads(
                            SharpFormEditorUpload::make()
                                ->setStorageBasePath('test/{id}')
                                ->setStorageDisk('local')
                        )
                );
        }

        public function update($id, array $data)
        {
            return $this->save(Person::findOrFail($id), $data)->id;
        }
    });

    $uploadedFileData = $this
        ->postJson(route('code16.sharp.api.form.upload'), ['file' => UploadedFile::fake()->create('file.pdf')])
        ->json();

    $editorXSharpFileData = $this
        ->postJson(route('code16.sharp.api.form.editor.upload.form.update', ['person', 1]), [
            'data' => [
                'file' => $uploadedFileData,
            ],
            'fields' => $form->fields()['bio']['uploads']['fields'],
        ])
        ->json();

    $uploadedImageData = $this
        ->postJson(route('code16.sharp.api.form.upload'), ['file' => UploadedFile::fake()->image('image.jpg', 200, 200)])
        ->json();

    $editorXSharpImageData = $this
        ->postJson(route('code16.sharp.api.form.editor.upload.form.update', ['person', 1]), [
            'data' => [
                'file' => $uploadedImageData,
            ],
            'fields' => $form->fields()['bio']['uploads']['fields'],
        ])
        ->json();

    $this->post('/sharp/s-list/person/s-form/person/1', [
        'bio' => [
            'text' => <<<'HTML'
                <x-sharp-file data-key="0"></x-sharp-file>
                <x-sharp-image data-key="1"></x-sharp-image>
                HTML,
            'uploads' => [
                $editorXSharpFileData,
                $editorXSharpImageData,
            ],
        ],
    ]);

    expect(Person::first()->bio)->toEqual(
        sprintf(<<<'HTML'
            <x-sharp-file file="%s"></x-sharp-file>
            <x-sharp-image file="%s"></x-sharp-image>
            HTML,
            e(json_encode([
                'file_name' => 'test/1/file.pdf',
                'size' => 0,
                'mime_type' => 'application/pdf',
                'disk' => 'local',
            ])),
            e(json_encode([
                'file_name' => 'test/1/image.jpg',
                'size' => 1367,
                'mime_type' => 'image/jpeg',
                'disk' => 'local',
            ]))
        )
    );

    Storage::disk('local')->assertExists('test/1/file.pdf');
    Storage::disk('local')->assertExists('test/1/image.jpg');
});

it('can post an embed with upload, create case', function () {
    fakeFormFor('person', new class extends FakeSharpForm
    {
        use WithSharpFormEloquentUpdater;

        public function buildFormFields(FieldsContainer $formFields): void
        {
            $formFields
                ->addField(
                    SharpFormEditorField::make('bio')
                        ->allowEmbeds([
                            FormEditorUploadsTestEmbed::class,
                        ])
                );
        }

        public function update($id, array $data)
        {
            return $this->save(new Person(), $data)->id;
        }
    });

    $uploadedFile = UploadedFile::fake()->create('file.pdf');

    $uploadedFileData = $this
        ->postJson(route('code16.sharp.api.form.upload'), ['file' => $uploadedFile])
        ->json();

    $editorXEmbedData = $this
        ->postJson(route('code16.sharp.api.embed.instance.form.update', [
            (new FormEditorUploadsTestEmbed)->key(),
            'person',
            1,
        ]), [
            'file' => $uploadedFileData,
        ])
        ->json();

    $this->post('/sharp/s-list/person/s-form/person', [
        'bio' => [
            'text' => sprintf(
                '<x-embed data-key="0"></x-embed>',
            ),
            'embeds' => [
                (new FormEditorUploadsTestEmbed)->key() => [
                    [
                        'file' => $editorXEmbedData['file'],
                    ],
                ],
            ],
        ],
    ]);

    expect(Person::first()->bio)->toEqual(
        sprintf(
            '<x-embed file="%s"></x-embed>',
            e(json_encode([
                'file_name' => 'test/1/file.pdf',
                'size' => 0,
                'mime_type' => 'application/pdf',
                'disk' => 'local',
            ]))
        )
    );

    Storage::disk('local')->assertExists('test/1/file.pdf');
});
