<?php

use Code16\Sharp\Form\Eloquent\WithSharpFormEloquentUpdater;
use Code16\Sharp\Form\Fields\Editor\Uploads\SharpFormEditorUpload;
use Code16\Sharp\Form\Fields\SharpFormEditorField;
use Code16\Sharp\Tests\Fixtures\Entities\PersonEntity;
use Code16\Sharp\Tests\Fixtures\Person;
use Code16\Sharp\Tests\Http\Form\Fixtures\FormEditorUploadsTestEmbed;
use Code16\Sharp\Tests\Unit\Form\Fakes\FakeSharpForm;
use Code16\Sharp\Utils\Fields\FieldsContainer;
use Illuminate\Http\UploadedFile;

beforeEach(function () {
    config()->set('sharp.uploads.tmp_dir', 'tmp');
    config()->set(
        'sharp.entities.person',
        PersonEntity::class,
    );
    Storage::fake('local');
    login();
    $this->withoutExceptionHandling();
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
                        ->allowUploads(function (SharpFormEditorUpload $upload) {
                            $upload->setStorageBasePath('test/{id}')
                                ->setStorageDisk('local');
                        })
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

    $editorXSharpFileData = $this
        ->postJson(route('code16.sharp.api.form.editor.upload.form.update'), [
            'data' => [
                'file' => $uploadedFileData,
            ],
            'fields' => $form->fields()['bio']['uploads']['fields'],
        ])
        ->json();

    $this->post('/sharp/s-list/person/s-form/person', [
        'bio' => [
            'text' => sprintf(
                '<x-sharp-file file="%s"></x-sharp-file>',
                e(json_encode($editorXSharpFileData['file']))
            ),
            'files' => [
                $editorXSharpFileData['file'],
            ],
        ],
    ]);

    expect(Person::first()->bio)->toEqual(
        sprintf(
            '<x-sharp-file file="%s"></x-sharp-file>',
            e(json_encode([
                ...$editorXSharpFileData['file'],
                'path' => 'test/1/file.pdf',
            ]))
        )
    );

    Storage::disk('local')->assertExists('test/1/file.pdf');
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
                        ->allowUploads(function (SharpFormEditorUpload $upload) {
                            $upload->setStorageBasePath('test/{id}')
                                ->setStorageDisk('local');
                        })
                );
        }

        public function update($id, array $data)
        {
            return $this->save(Person::findOrFail($id), $data)->id;
        }
    });

    $uploadedFile = UploadedFile::fake()->create('file.pdf');

    $uploadedFileData = $this
        ->postJson(route('code16.sharp.api.form.upload'), ['file' => $uploadedFile])
        ->json();

    $editorXSharpFileData = $this
        ->postJson(route('code16.sharp.api.form.editor.upload.form.update'), [
            'data' => [
                'file' => $uploadedFileData,
            ],
            'fields' => $form->fields()['bio']['uploads']['fields'],
        ])
        ->json();

    $this->post('/sharp/s-list/person/s-form/person/1', [
        'bio' => [
            'text' => sprintf(
                '<x-sharp-file file="%s"></x-sharp-file>',
                e(json_encode($editorXSharpFileData['file']))
            ),
            'files' => [
                $editorXSharpFileData['file'],
            ],
        ],
    ]);

    expect(Person::first()->bio)->toEqual(
        sprintf(
            '<x-sharp-file file="%s"></x-sharp-file>',
            e(json_encode([
                ...$editorXSharpFileData['file'],
                'path' => 'test/1/file.pdf',
            ]))
        )
    );

    Storage::disk('local')->assertExists('test/1/file.pdf');
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
                '<x-embed file="%s"></x-embed>',
                e(json_encode($editorXEmbedData['file']))
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
                ...$editorXEmbedData['file'],
                'path' => 'test/1/file.pdf',
            ]))
        )
    );

    Storage::disk('local')->assertExists('test/1/file.pdf');
});
