<?php

use Code16\Sharp\Form\Eloquent\WithSharpFormEloquentUpdater;
use Code16\Sharp\Form\Fields\Editor\Uploads\SharpFormEditorUpload;
use Code16\Sharp\Form\Fields\SharpFormEditorField;
use Code16\Sharp\Tests\Fixtures\Entities\PersonEntity;
use Code16\Sharp\Tests\Fixtures\Person;
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

it('can post a newly uploaded file in editor', function () {
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
            'fields' => $form->fields()['bio']['embeds']['upload']['fields'],
        ])
        ->json();

    $this->post('/sharp/s-list/person/s-form/person', [
        'bio' => [
            'text' => '<x-sharp-file file="'.e(json_encode($editorXSharpFileData['file'])).'"></x-sharp-file>',
            'files' => [
                $editorXSharpFileData['file'],
            ],
        ],
    ]);

    $expectedXSharpFileData = [...$editorXSharpFileData];
    $expectedXSharpFileData['file']['path'] = 'test/1/file.pdf';

    expect(Person::first()->bio)->toEqual(
        '<x-sharp-file file="'.e(json_encode($expectedXSharpFileData['file'])).'"></x-sharp-file>'
    );

    Storage::disk('local')->assertExists('test/1/file.pdf');
});
