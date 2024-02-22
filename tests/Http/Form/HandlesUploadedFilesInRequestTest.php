<?php

use Code16\Sharp\Form\Fields\Editor\Uploads\SharpFormEditorUpload;
use Code16\Sharp\Tests\Fixtures\Entities\PersonEntity;
use Code16\Sharp\Form\Fields\SharpFormEditorField;
use Code16\Sharp\Form\Fields\SharpFormUploadField;
use Code16\Sharp\Http\Jobs\HandleTransformedFileJob;
use Code16\Sharp\Http\Jobs\HandleUploadedFileJob;
use Code16\Sharp\Tests\Fixtures\Sharp\PersonForm;
use Code16\Sharp\Utils\Fields\FieldsContainer;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    login();

    config()->set(
        'sharp.entities.person',
        PersonEntity::class,
    );

    Storage::fake('local');
    Bus::fake();
});

it('dispatches HandlePostedFilesJob on update and on create if needed', function () {
    $this->withoutExceptionHandling();

    fakeFormFor('person', new class extends PersonForm
    {
        public function buildFormFields(FieldsContainer $formFields): void
        {
            $formFields->addField(
                SharpFormUploadField::make('file')
                    ->setStorageDisk('local')
                    ->setStorageBasePath('data/test')
            );
        }
    });

    UploadedFile::fake()
        ->image('image.jpg')
        ->storeAs('/tmp', 'image.jpg', ['disk' => 'local']);

    $this
        ->post('/sharp/s-list/person/s-form/person/2', [
            'file' => [
                'name' => '/image.jpg',
                'uploaded' => true,
            ],
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect();

    Bus::assertDispatched(HandleUploadedFileJob::class, function ($job) {
        return $job->filePath == 'data/test/image.jpg'
            && $job->uploadedFileName == '/image.jpg';
    });

    UploadedFile::fake()
        ->image('image-2.jpg')
        ->storeAs('/tmp', 'image-2.jpg', ['disk' => 'local']);

    $this
        ->post('/sharp/s-list/person/s-form/person', [
            'file' => [
                'name' => '/image-2.jpg',
                'uploaded' => true,
            ],
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect();

    Bus::assertDispatched(HandleUploadedFileJob::class, function ($job) {
        return $job->filePath == 'data/test/image-2.jpg'
            && $job->uploadedFileName == '/image-2.jpg';
    });
});

it('dispatches HandlePostedFilesJob for editors on update and on create if needed', function () {
    $this->withoutExceptionHandling();

    fakeFormFor('person', new class extends PersonForm
    {
        public function buildFormFields(FieldsContainer $formFields): void
        {
            $formFields->addField(
                SharpFormEditorField::make('bio')
                    ->allowUploads(function (SharpFormEditorUpload $upload) {
                        $upload
                            ->setStorageDisk('local')
                            ->setStorageBasePath('data/test');
                    })
            );
        }
    });

    UploadedFile::fake()
        ->image('image.jpg')
        ->storeAs('/tmp', 'image.jpg', ['disk' => 'local']);

    $this
        ->post('/sharp/s-list/person/s-form/person/2', [
            'bio' => [
                // we don't care about editor content here
                'files' => [
                    [
                        'name' => 'image.jpg',
                        'path' => 'data/test/image.jpg',
                        'uploaded' => true,
                    ],
                ],
            ],
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect();

    Bus::assertDispatched(HandleUploadedFileJob::class, function ($job) {
        return $job->filePath == 'data/test/image.jpg'
            && $job->uploadedFileName == 'image.jpg';
    });

    UploadedFile::fake()
        ->image('image-2.jpg')
        ->storeAs('/tmp', 'image-2.jpg', ['disk' => 'local']);

    $this
        ->post('/sharp/s-list/person/s-form/person', [
            'bio' => [
                'files' => [
                    [
                        'name' => 'image-2.jpg',
                        'path' => 'data/test/image-2.jpg',
                        'uploaded' => true,
                    ],
                ],
            ],
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect();

    Bus::assertDispatched(HandleUploadedFileJob::class, function ($job) {
        return $job->filePath == 'data/test/image-2.jpg'
            && $job->uploadedFileName == 'image-2.jpg';
    });
});

it('does not dispatch HandlePostedFilesJob if not needed', function () {
    fakeFormFor('person', new class extends PersonForm
    {
        public function buildFormFields(FieldsContainer $formFields): void
        {
            $formFields->addField(
                SharpFormEditorField::make('bio')
                    ->allowUploads(function (SharpFormEditorUpload $upload) {
                        $upload
                            ->setStorageDisk('local')
                            ->setStorageBasePath('data/test');
                    })
            )->addField(SharpFormUploadField::make('file')
                ->setStorageDisk('local')
                ->setStorageBasePath('data/test')
            );
        }
    });

    $this
        ->post('/sharp/s-list/person/s-form/person/2', [
            'name' => 'Stephen Hawking',
            'file' => [
                'name' => 'doc.pdf',
                'file_name' => 'data/test/doc.pdf',
                'disk' => 'local',
            ],
            'bio' => [
                'files' => [
                    [
                        'name' => 'doc-2.pdf',
                        'file_name' => 'data/test/doc-2.pdf',
                        'disk' => 'local',
                    ],
                ],
            ],
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect();

    $this
        ->post('/sharp/s-list/person/s-form/person', [
            'name' => 'Marie Curie',
            'file' => [
                'name' => 'doc.pdf',
                'file_name' => 'data/test/doc.pdf',
                'disk' => 'local',
            ],
            'bio' => [
                'files' => [
                    [
                        'name' => 'doc-2.pdf',
                        'file_name' => 'data/test/doc-2.pdf',
                        'disk' => 'local',
                    ],
                ],
            ],
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect();

    Bus::assertNotDispatched(HandleUploadedFileJob::class);
});

it('handles isTransformOriginal to transform the image on a newly uploaded file', function ($transformKeepOriginal) {
    fakeFormFor('person', new class($transformKeepOriginal) extends PersonForm
    {
        public function __construct(private bool $transformKeepOriginal)
        {
        }

        public function buildFormFields(FieldsContainer $formFields): void
        {
            $formFields
                ->addField(
                    SharpFormUploadField::make('file')
                        ->setStorageDisk('local')
                        ->setStorageBasePath('data/test')
                        ->setTransformable(transformKeepOriginal: $this->transformKeepOriginal)
                );
        }

        public function update($id, array $data)
        {
            return $id;
        }
    });

    UploadedFile::fake()
        ->image('image.jpg')
        ->storeAs('/tmp', 'image.jpg', ['disk' => 'local']);

    $this
        ->post('/sharp/s-list/person/s-form/person/12', [
            'file' => [
                'name' => '/image.jpg',
                'uploaded' => true,
                'transformed' => true,
                'filters' => [
                    'rotate' => ['angle' => 90],
                ],
            ],
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect();

    Bus::assertDispatched(HandleUploadedFileJob::class, function ($job) use ($transformKeepOriginal) {
        return $job->filePath == 'data/test/image.jpg'
        && $job->disk == 'local'
        && $job->instanceId == 12
        && $job->uploadedFileName == '/image.jpg'
        && $job->transformFilters == $transformKeepOriginal
            ? null
            : ['rotate' => ['angle' => 90]];
    });
})->with([
    'transformKeepOriginal' => [true, false],
]);

it('handles isTransformOriginal to transform the image on an existing file', function ($transformKeepOriginal) {
    fakeFormFor('person', new class($transformKeepOriginal) extends PersonForm
    {
        public function __construct(private bool $transformKeepOriginal)
        {
        }

        public function buildFormFields(FieldsContainer $formFields): void
        {
            $formFields
                ->addField(
                    SharpFormUploadField::make('file')
                        ->setStorageDisk('local')
                        ->setStorageBasePath('data/test')
                        ->setTransformable(transformKeepOriginal: $this->transformKeepOriginal)
                );
        }
    });

    UploadedFile::fake()
        ->image('image.jpg')
        ->storeAs('/data/test', 'image.jpg', ['disk' => 'local']);

    $this->withoutExceptionHandling();

    $this
        ->post('/sharp/s-list/person/s-form/person/1', [
            'file' => [
                'path' => '/data/test/image.jpg',
                'size' => 12,
                'disk' => 'local',
                'uploaded' => false,
                'transformed' => true,
                'filters' => [
                    'rotate' => ['angle' => 90],
                ],
            ],
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect();

    Bus::assertNotDispatched(HandleUploadedFileJob::class);

    Bus::assertNotDispatched(HandleTransformedFileJob::class, function ($job) use ($transformKeepOriginal) {
        return $job->filePath == 'data/test/image.jpg'
        && $job->disk == 'local'
        && $job->transformFilters == $transformKeepOriginal
            ? null
            : ['rotate' => ['angle' => 90]];
    });
})->with([
    'transformKeepOriginal' => [true, false],
]);