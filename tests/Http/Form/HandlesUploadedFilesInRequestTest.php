<?php

use Code16\Sharp\Form\Fields\Editor\Uploads\SharpFormEditorUpload;
use Code16\Sharp\Form\Fields\SharpFormEditorField;
use Code16\Sharp\Form\Fields\SharpFormListField;
use Code16\Sharp\Form\Fields\SharpFormUploadField;
use Code16\Sharp\Http\Jobs\HandleTransformedFileJob;
use Code16\Sharp\Http\Jobs\HandleUploadedFileJob;
use Code16\Sharp\Tests\Fixtures\Entities\PersonEntity;
use Code16\Sharp\Tests\Fixtures\Sharp\PersonForm;
use Code16\Sharp\Utils\Fields\FieldsContainer;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->withoutExceptionHandling();
    sharp()->config()->addEntity('person', PersonEntity::class);
    login();
    Storage::fake('local');
    Queue::fake();
});

it('dispatches HandlePostedFilesJob on update and on create if needed', function () {
    fakeFormFor('person', new class() extends PersonForm
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

    Queue::assertPushed(function (HandleUploadedFileJob $job) {
        return $job->filePath == 'data/test/image.jpg'
            && $job->uploadedFileName == '/image.jpg';
    });

    UploadedFile::fake()
        ->image('image-2.jpg')
        ->storeAs('/tmp', 'image-2.jpg', ['disk' => 'local']);

    $this
        ->post('/sharp/s-list/person/s-form/person', [
            'text' => '<x-sharp-image data-key="0"></x-sharp-image>',
            'file' => [
                'name' => '/image-2.jpg',
                'uploaded' => true,
            ],
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect();

    Queue::assertPushed(function (HandleUploadedFileJob $job) {
        return $job->filePath == 'data/test/image-2.jpg'
            && $job->uploadedFileName == '/image-2.jpg';
    });
});

it('dispatches HandlePostedFilesJob for editors on update and on create if needed', function () {
    fakeFormFor('person', new class() extends PersonForm
    {
        public function buildFormFields(FieldsContainer $formFields): void
        {
            $formFields->addField(
                SharpFormEditorField::make('bio')
                    ->allowUploads(
                        SharpFormEditorUpload::make()
                            ->setStorageDisk('local')
                            ->setStorageBasePath('data/test')
                    )
            );
        }
    });

    UploadedFile::fake()
        ->image('image.jpg')
        ->storeAs('/tmp', 'image.jpg', ['disk' => 'local']);

    $this
        ->post('/sharp/s-list/person/s-form/person/2', [
            'bio' => [
                'text' => '<x-sharp-image data-key="0"></x-sharp-image>',
                'uploads' => [
                    [
                        'file' => [
                            'name' => 'image.jpg',
                            'path' => 'data/test/image.jpg',
                            'uploaded' => true,
                        ],
                    ],
                ],
            ],
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect();

    Queue::assertPushed(function (HandleUploadedFileJob $job) {
        return $job->filePath == 'data/test/image.jpg'
            && $job->uploadedFileName == 'image.jpg';
    });

    UploadedFile::fake()
        ->image('image-2.jpg')
        ->storeAs('/tmp', 'image-2.jpg', ['disk' => 'local']);

    $this
        ->post('/sharp/s-list/person/s-form/person', [
            'bio' => [
                'text' => '<x-sharp-image data-key="0"></x-sharp-image>',
                'uploads' => [
                    [
                        'file' => [
                            'name' => 'image-2.jpg',
                            'path' => 'data/test/image-2.jpg',
                            'uploaded' => true,
                        ],
                    ],
                ],
            ],
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect();

    Queue::assertPushed(function (HandleUploadedFileJob $job) {
        return $job->filePath == 'data/test/image-2.jpg'
            && $job->uploadedFileName == 'image-2.jpg';
    });
});

it('dispatches HandlePostedFilesJob for lists on update and on create if needed', function () {
    fakeFormFor('person', new class() extends PersonForm
    {
        public function buildFormFields(FieldsContainer $formFields): void
        {
            $formFields->addField(
                SharpFormListField::make('pictures')
                    ->addItemField(
                        SharpFormUploadField::make('file')
                            ->setStorageDisk('local')
                            ->setStorageBasePath('data/test')
                    )
            );
        }
    });

    UploadedFile::fake()
        ->image('image.jpg')
        ->storeAs('/tmp', 'image.jpg', ['disk' => 'local']);

    $this
        ->post('/sharp/s-list/person/s-form/person/2', [
            'pictures' => [
                [
                    'id' => 1,
                    'file' => [
                        'name' => 'image.jpg',
                        'path' => 'data/test/image.jpg',
                        'uploaded' => true,
                    ],
                ],
            ],
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect();

    Queue::assertPushed(function (HandleUploadedFileJob $job) {
        return $job->filePath == 'data/test/image.jpg'
            && $job->uploadedFileName == 'image.jpg';
    });

    UploadedFile::fake()
        ->image('image-2.jpg')
        ->storeAs('/tmp', 'image-2.jpg', ['disk' => 'local']);

    $this
        ->post('/sharp/s-list/person/s-form/person', [
            'pictures' => [
                [
                    'id' => 1,
                    'file' => [
                        'name' => 'image-2.jpg',
                        'path' => 'data/test/image-2.jpg',
                        'uploaded' => true,
                    ],
                ],
            ],
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect();

    Queue::assertPushed(function (HandleUploadedFileJob $job) {
        return $job->filePath == 'data/test/image-2.jpg'
            && $job->uploadedFileName == 'image-2.jpg';
    });
});

it('does not dispatch HandlePostedFilesJob if not needed', function () {
    fakeFormFor('person', new class() extends PersonForm
    {
        public function buildFormFields(FieldsContainer $formFields): void
        {
            $formFields
                ->addField(
                    SharpFormEditorField::make('bio')
                        ->allowUploads(
                            SharpFormEditorUpload::make()
                                ->setStorageDisk('local')
                                ->setStorageBasePath('data/test')
                        )
                )
                ->addField(
                    SharpFormUploadField::make('file')
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
                'path' => 'data/test/doc.pdf',
                'disk' => 'local',
            ],
            'bio' => [
                'text' => '<x-sharp-file data-key="0"></x-sharp-file>',
                'uploads' => [
                    [
                        'file' => [
                            'name' => 'doc-2.pdf',
                            'path' => 'data/test/doc-2.pdf',
                            'disk' => 'local',
                        ],
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
                'path' => 'data/test/doc.pdf',
                'disk' => 'local',
            ],
            'bio' => [
                'files' => [
                    [
                        'name' => 'doc-2.pdf',
                        'path' => 'data/test/doc-2.pdf',
                        'disk' => 'local',
                    ],
                ],
            ],
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect();

    Queue::assertNotPushed(HandleUploadedFileJob::class);
});

it('does not dispatch HandlePostedFilesJob when temporary', function () {
    fakeFormFor('person', new class() extends PersonForm
    {
        public function buildFormFields(FieldsContainer $formFields): void
        {
            $formFields
                ->addField(
                    SharpFormUploadField::make('file')
                        ->setStorageTemporary()
                );
        }
    });

    UploadedFile::fake()
        ->image('image.jpg')
        ->storeAs('/tmp', 'image.jpg', ['disk' => 'local']);

    $this
        ->post('/sharp/s-list/person/s-form/person/2', [
            'name' => 'Stephen Hawking',
            'file' => [
                'name' => 'image.jpg',
                'uploaded' => true,
            ],
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect();

    $this
        ->post('/sharp/s-list/person/s-form/person', [
            'name' => 'Marie Curie',
            'file' => [
                'name' => 'image.jpg',
                'uploaded' => true,
            ],
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect();

    Queue::assertNotPushed(HandleUploadedFileJob::class);
});

it('handles isTransformOriginal to transform the image on a newly uploaded file', function ($transformKeepOriginal) {
    fakeFormFor('person', new class($transformKeepOriginal) extends PersonForm
    {
        public function __construct(private bool $transformKeepOriginal) {}

        public function buildFormFields(FieldsContainer $formFields): void
        {
            $formFields
                ->addField(
                    SharpFormUploadField::make('file')
                        ->setStorageDisk('local')
                        ->setStorageBasePath('data/test')
                        ->setImageTransformable(transformKeepOriginal: $this->transformKeepOriginal)
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

    if ($transformKeepOriginal) {
        Queue::assertPushed(function (HandleUploadedFileJob $job) {
            return $job->filePath == 'data/test/image.jpg'
                && $job->disk == 'local'
                && $job->instanceId == 12
                && $job->uploadedFileName == '/image.jpg'
                && $job->transformFilters == null;
        });
    } else {
        Queue::assertPushed(function (HandleUploadedFileJob $job) {
            return $job->filePath == 'data/test/image.jpg'
                && $job->disk == 'local'
                && $job->instanceId == 12
                && $job->uploadedFileName == '/image.jpg'
                && $job->transformFilters == ['rotate' => ['angle' => 90]];
        });
    }
})->with([
    'transformKeepOriginal' => true,
    'not transformKeepOriginal' => false,
]);

it('handles isTransformOriginal to transform the image on an existing file', function ($transformKeepOriginal) {
    fakeFormFor('person', new class($transformKeepOriginal) extends PersonForm
    {
        public function __construct(private bool $transformKeepOriginal) {}

        public function buildFormFields(FieldsContainer $formFields): void
        {
            $formFields
                ->addField(
                    SharpFormUploadField::make('file')
                        ->setStorageDisk('local')
                        ->setStorageBasePath('data/test')
                        ->setImageTransformable(transformKeepOriginal: $this->transformKeepOriginal)
                );
        }
    });

    UploadedFile::fake()
        ->image('image.jpg')
        ->storeAs('/data/test', 'image.jpg', ['disk' => 'local']);

    $this
        ->post('/sharp/s-list/person/s-form/person/1', [
            'file' => [
                'path' => 'data/test/image.jpg',
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

    Queue::assertNotPushed(HandleUploadedFileJob::class);

    if ($transformKeepOriginal) {
        Queue::assertNotPushed(HandleTransformedFileJob::class);
    } else {
        Queue::assertPushed(function (HandleTransformedFileJob $job) {
            return $job->filePath == 'data/test/image.jpg'
                && $job->disk == 'local'
                && $job->transformFilters == ['rotate' => ['angle' => 90]];
        });
    }
})->with([
    'transformKeepOriginal' => true,
    'not transformKeepOriginal' => false,
]);

it('pushes jobs on right queue / connections', function () {
    fakeFormFor('person', new class() extends PersonForm
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

    Queue::assertPushed(function (HandleUploadedFileJob $job) {
        return $job->queue == 'default'
            && $job->connection == 'sync';
    });

    sharp()->config()->configureUploads(
        fileHandingQueue: 'uploads',
        fileHandlingQueueConnection: 'redis'
    );

    $this
        ->post('/sharp/s-list/person/s-form/person/2', [
            'file' => [
                'name' => '/image.jpg',
                'uploaded' => true,
            ],
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect();

    Queue::assertPushed(function (HandleUploadedFileJob $job) {
        return $job->queue == 'uploads'
            && $job->connection == 'redis';
    });
});
