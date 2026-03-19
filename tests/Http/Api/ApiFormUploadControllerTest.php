<?php

use Code16\Sharp\Dashboard\Commands\DashboardCommand;
use Code16\Sharp\EntityList\Commands\EntityCommand;
use Code16\Sharp\EntityList\Commands\InstanceCommand;
use Code16\Sharp\Form\Fields\Editor\Uploads\SharpFormEditorUpload;
use Code16\Sharp\Form\Fields\Embeds\SharpFormEditorEmbed;
use Code16\Sharp\Form\Fields\SharpFormEditorField;
use Code16\Sharp\Form\Fields\SharpFormListField;
use Code16\Sharp\Form\Fields\SharpFormUploadField;
use Code16\Sharp\Tests\Fixtures\Entities\DashboardEntity;
use Code16\Sharp\Tests\Fixtures\Entities\PersonEntity;
use Code16\Sharp\Tests\Fixtures\Sharp\PersonForm;
use Code16\Sharp\Tests\Fixtures\Sharp\PersonList;
use Code16\Sharp\Tests\Fixtures\Sharp\PersonShow;
use Code16\Sharp\Tests\Fixtures\Sharp\TestDashboard;
use Code16\Sharp\Utils\Fields\FieldsContainer;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('local');
    login();
});

function registerPersonEntityWithUploadField(?SharpFormUploadField $field = null): void
{
    sharp()->config()->declareEntity(PersonEntity::class);

    $field ??= SharpFormUploadField::make('file')
        ->setStorageDisk('local')
        ->setStorageBasePath('tmp');

    $form = new class($field) extends PersonForm
    {
        public function __construct(private SharpFormUploadField $field) {}

        public function buildFormFields(FieldsContainer $formFields): void
        {
            $formFields->addField($this->field);
        }
    };

    fakeFormFor('person', $form);
}

it('allows to upload a file', function () {
    registerPersonEntityWithUploadField();

    $this
        ->postJson(route('code16.sharp.api.form.upload', [
            'entityKey' => 'person',
            'uploadFieldKey' => 'file',
        ]), [
            'file' => UploadedFile::fake()->image('image.jpg', 600, 600),
        ])
        ->assertOk()
        ->assertJson(['name' => 'image.jpg']);
});

it('allows to upload a file on a custom defined disk', function () {
    sharp()->config()->configureUploads(uploadDisk: 'uploads');
    Storage::fake('uploads');
    registerPersonEntityWithUploadField();

    $this
        ->postJson(route('code16.sharp.api.form.upload', [
            'entityKey' => 'person',
            'uploadFieldKey' => 'file',
        ]), [
            'file' => UploadedFile::fake()->image('image.jpg', 600, 600),
        ])
        ->assertOk();

    Storage::disk('uploads')->assertExists('/tmp/image.jpg');
});

it('adds a increment to the file name if needed', function () {
    registerPersonEntityWithUploadField();

    $this
        ->postJson(route('code16.sharp.api.form.upload', [
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
            'file' => UploadedFile::fake()->image('image.jpg', 600, 600),
        ])
        ->assertOk()
        ->assertJson(['name' => 'image-1.jpg']);
});

it('copies the file to the wanted directory', function () {
    registerPersonEntityWithUploadField();

    $this
        ->postJson(route('code16.sharp.api.form.upload', [
            'entityKey' => 'person',
            'uploadFieldKey' => 'file',
        ]), [
            'file' => UploadedFile::fake()->image('image.jpg', 600, 600),
        ]);

    $this->assertTrue(Storage::disk('local')->exists('/tmp/image.jpg'));
});

it('throws a validation exception on missing file even without explicit rule', function () {
    registerPersonEntityWithUploadField();

    $this
        ->postJson(route('code16.sharp.api.form.upload', [
            'entityKey' => 'person',
            'uploadFieldKey' => 'file',
        ]), [
            'file' => null,
        ])
        ->assertStatus(422)
        ->assertJsonValidationErrorFor('file');

    $this
        ->postJson(route('code16.sharp.api.form.upload', [
            'entityKey' => 'person',
            'uploadFieldKey' => 'file',
        ]), [
            'file' => 'not a file',
        ])
        ->assertStatus(422)
        ->assertJsonValidationErrorFor('file');
});

it('validates on explicit rules', function () {
    registerPersonEntityWithUploadField(
        SharpFormUploadField::make('file')
            ->setStorageDisk('local')
            ->setStorageBasePath('tmp')
            ->setImageOnly(),
    );

    $this
        ->postJson(route('code16.sharp.api.form.upload', [
            'entityKey' => 'person',
            'uploadFieldKey' => 'file',
        ]), [
            'file' => UploadedFile::fake()->create('file.xls'),
        ])
        ->assertStatus(422)
        ->assertJsonValidationErrorFor('file');

    registerPersonEntityWithUploadField(
        SharpFormUploadField::make('file')
            ->setStorageDisk('local')
            ->setStorageBasePath('tmp')
            ->setMaxFileSize(2),
    );

    $this
        ->postJson(route('code16.sharp.api.form.upload', [
            'entityKey' => 'person',
            'uploadFieldKey' => 'file',
        ]), [
            'file' => UploadedFile::fake()->create('file.xls', 1024 * 3),
        ])
        ->assertStatus(422)
        ->assertJsonValidationErrorFor('file');
});

class UploadTestEmbed extends SharpFormEditorEmbed
{
    public function buildFormFields(FieldsContainer $formFields): void
    {
        $formFields->addField(
            SharpFormUploadField::make('file')
                ->setStorageDisk('local')
                ->setStorageBasePath('tmp')
        );
    }

    public function updateContent(array $data = []): array
    {
        return $data;
    }
}

it('allows to upload a file in an embed', function () {
    $this
        ->postJson(
            route('code16.sharp.api.form.upload', [
                'entityKey' => 'person',
                'uploadFieldKey' => 'file',
                'embed_key' => (new UploadTestEmbed())->key(),
            ]),
            ['file' => UploadedFile::fake()->image('image.jpg')]
        )
        ->assertOk()
        ->assertJson(['name' => 'image.jpg']);
});

it('allows to upload a file in an entity list entity command', function () {
    $command = new class() extends EntityCommand
    {
        public function label(): string
        {
            return 'label';
        }

        public function buildFormFields(FieldsContainer $formFields): void
        {
            $formFields->addField(
                SharpFormUploadField::make('file')
                    ->setStorageDisk('local')
                    ->setStorageBasePath('tmp')
            );
        }

        public function execute(array $data = []): array
        {
            return $this->reload();
        }
    };

    $list = new class($command) extends PersonList
    {
        public function __construct(private EntityCommand $command) {}

        public function getInstanceCommands(): ?array
        {
            return [$this->command];
        }

        public function getEntityCommands(): ?array
        {
            return ['entity-command' => $this->command];
        }
    };

    sharp()->config()->declareEntity(PersonEntity::class);
    fakeListFor('person', $list);

    $this
        ->postJson(
            route('code16.sharp.api.form.upload', [
                'entityKey' => 'person',
                'uploadFieldKey' => 'file',
                'entity_list_command_key' => 'entity-command',
            ]),
            ['file' => UploadedFile::fake()->image('image.jpg')]
        )
        ->assertOk()
        ->assertJson(['name' => 'image.jpg']);
});

it('allows to upload a file in an entity list instance command', function () {
    $command = new class() extends InstanceCommand
    {
        public function label(): string
        {
            return 'label';
        }

        public function buildFormFields(FieldsContainer $formFields): void
        {
            $formFields->addField(
                SharpFormUploadField::make('file')
                    ->setStorageDisk('local')
                    ->setStorageBasePath('tmp')
            );
        }

        public function execute(mixed $instanceId, array $data = []): array
        {
            return $this->reload();
        }
    };

    $list = new class($command) extends PersonList
    {
        public function __construct(private InstanceCommand $command) {}

        public function getInstanceCommands(): ?array
        {
            return ['instance-command' => $this->command];
        }
    };

    sharp()->config()->declareEntity(PersonEntity::class);
    fakeListFor('person', $list);

    $this
        ->postJson(
            route('code16.sharp.api.form.upload', [
                'entityKey' => 'person',
                'uploadFieldKey' => 'file',
                'entity_list_command_key' => 'instance-command',
                'instance_id' => 1,
            ]),
            ['file' => UploadedFile::fake()->image('image.jpg')]
        )
        ->assertOk()
        ->assertJson(['name' => 'image.jpg']);
});

it('allows to upload a file in a show instance command', function () {
    $command = new class() extends InstanceCommand
    {
        public function label(): string
        {
            return 'label';
        }

        public function buildFormFields(FieldsContainer $formFields): void
        {
            $formFields->addField(
                SharpFormUploadField::make('file')
                    ->setStorageDisk('local')
                    ->setStorageBasePath('tmp')
            );
        }

        public function execute(mixed $instanceId, array $data = []): array
        {
            return $this->reload();
        }
    };

    $show = new class($command) extends PersonShow
    {
        public function __construct(private InstanceCommand $command) {}

        public function getInstanceCommands(): ?array
        {
            return ['show-command' => $this->command];
        }
    };

    sharp()->config()->declareEntity(PersonEntity::class);
    fakeShowFor('person', $show);

    $this
        ->postJson(
            route('code16.sharp.api.form.upload', [
                'entityKey' => 'person',
                'uploadFieldKey' => 'file',
                'show_command_key' => 'show-command',
                'instance_id' => 1,
            ]),
            ['file' => UploadedFile::fake()->image('image.jpg')]
        )
        ->assertOk()
        ->assertJson(['name' => 'image.jpg']);
});

it('allows to upload a file within a dashboard command form', function () {
    sharp()->config()->declareEntity(DashboardEntity::class);

    $command = new class() extends DashboardCommand
    {
        public function label(): string
        {
            return 'label';
        }

        public function buildFormFields(FieldsContainer $formFields): void
        {
            $formFields->addField(
                SharpFormUploadField::make('file')
                    ->setStorageDisk('local')
                    ->setStorageBasePath('tmp')
            );
        }

        public function execute(array $data = []): array
        {
            return $this->reload();
        }
    };

    fakeDashboardFor('dashboard', new class($command) extends TestDashboard
    {
        public function __construct(private DashboardCommand $command) {}

        public function getDashboardCommands(): ?array
        {
            return [
                'my_command' => $this->command,
            ];
        }
    });

    $this
        ->postJson(
            route('code16.sharp.api.form.upload', [
                'entityKey' => 'dashboard',
                'uploadFieldKey' => 'file',
                'dashboard_command_key' => 'my_command',
            ]),
            ['file' => UploadedFile::fake()->image('image.jpg')]
        )
        ->assertOk()
        ->assertJson(['name' => 'image.jpg']);
});

it('allows to upload a file in a list field', function () {
    sharp()->config()->declareEntity(PersonEntity::class);

    $form = new class() extends PersonForm
    {
        public function buildFormFields(FieldsContainer $formFields): void
        {
            $formFields->addField(
                SharpFormListField::make('list')
                    ->addItemField(
                        SharpFormUploadField::make('file')
                            ->setStorageDisk('local')
                            ->setStorageBasePath('tmp')
                    )
            );
        }
    };

    fakeFormFor('person', $form);

    $this
        ->postJson(
            route('code16.sharp.api.form.upload', [
                'entityKey' => 'person',
                'uploadFieldKey' => 'list.file',
            ]),
            ['file' => UploadedFile::fake()->image('image.jpg')]
        )
        ->assertOk()
        ->assertJson(['name' => 'image.jpg']);
});

it('allows to upload a file in an editor field', function () {
    sharp()->config()->declareEntity(PersonEntity::class);

    $form = new class() extends PersonForm
    {
        public function buildFormFields(FieldsContainer $formFields): void
        {
            $formFields->addField(
                SharpFormEditorField::make('editor')
                    ->allowUploads(
                        SharpFormEditorUpload::make()
                            ->setStorageDisk('local')
                            ->setStorageBasePath('tmp')
                    )
            );
        }
    };

    fakeFormFor('person', $form);

    $this
        ->postJson(
            route('code16.sharp.api.form.upload', [
                'entityKey' => 'person',
                'uploadFieldKey' => 'editor',
            ]),
            ['file' => UploadedFile::fake()->image('image.jpg')]
        )
        ->assertOk()
        ->assertJson(['name' => 'image.jpg']);
});
