<?php

use Code16\Sharp\EntityList\Commands\EntityCommand;
use Code16\Sharp\EntityList\Commands\Wizards\EntityWizardCommand;
use Code16\Sharp\Form\Fields\SharpFormEditorField;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Show\Fields\SharpShowEntityListField;
use Code16\Sharp\Tests\Fixtures\Entities\PersonEntity;
use Code16\Sharp\Tests\Fixtures\Sharp\PersonForm;
use Code16\Sharp\Tests\Fixtures\Sharp\PersonList;
use Code16\Sharp\Tests\Fixtures\Sharp\PersonShow;
use Code16\Sharp\Utils\Fields\FieldsContainer;
use Code16\Sharp\Utils\Testing\SharpAssertions;
use Illuminate\Http\UploadedFile;

pest()->use(SharpAssertions::class);

beforeEach(function () {
    login();
    sharp()->config()->declareEntity(PersonEntity::class);
});

it('get & assert an entity list', function () {
    fakeListFor(PersonEntity::class, new class() extends PersonList
    {
        public function getListData(): array
        {
            return [
                ['id' => 1, 'name' => 'Marie Curie'],
            ];
        }
    });

    $this->sharpList(PersonEntity::class)
        ->get()
        ->assertOk()
        ->assertListCount(1)
        ->assertListContains(['name' => 'Marie Curie']);
});

it('call & assert an entity list entity command', function () {
    fakeListFor('person', new class() extends PersonList
    {
        protected function getEntityCommands(): ?array
        {
            return [
                'cmd' => new class() extends EntityCommand
                {
                    public function label(): ?string
                    {
                        return 'entity';
                    }

                    public function buildFormFields(FieldsContainer $formFields): void
                    {
                        $formFields->addField(SharpFormTextField::make('action'));
                    }

                    public function execute(array $data = []): array
                    {
                        if ($data['action'] === 'download') {
                            Storage::fake('files');
                            UploadedFile::fake()
                                ->create('account.pdf', 100, 'application/pdf')
                                ->storeAs('pdf', 'account.pdf', ['disk' => 'files']);
                        }

                        return match ($data['action']) {
                            'info' => $this->info('ok'),
                            'link' => $this->link('https://example.org'),
                            'view' => $this->view('fixtures::test', ['text' => 'text']),
                            'reload' => $this->reload(),
                            'download' => $this->download('pdf/account.pdf', 'account.pdf', 'files'),
                            'refresh' => $this->refresh([1, 2]),
                        };
                    }
                },
            ];
        }
    });

    $this->sharpList(PersonEntity::class)
        ->callEntityCommand('cmd', ['action' => 'info'])
        ->assertReturnsInfo('ok');

    $this->sharpList(PersonEntity::class)
        ->callEntityCommand('cmd', ['action' => 'link'])
        ->assertReturnsLink('https://example.org');

    $this->sharpList(PersonEntity::class)
        ->callEntityCommand('cmd', ['action' => 'view'])
        ->assertReturnsView('fixtures::test', [
            'text' => 'text',
        ]);

    $this->sharpList(PersonEntity::class)
        ->callEntityCommand('cmd', ['action' => 'reload'])
        ->assertReturnsReload();

    $this->sharpList(PersonEntity::class)
        ->callEntityCommand('cmd', ['action' => 'download'])
        ->assertReturnsDownload('account.pdf');

    $this->sharpList(PersonEntity::class)
        ->callEntityCommand('cmd', ['action' => 'refresh'])
        ->assertReturnsRefresh([1, 2]);
});

it('call & assert an entity list entity wiard command', function () {
    fakeListFor('person', new class() extends PersonList
    {
        protected function getEntityCommands(): ?array
        {
            return [
                'wizard' => new class() extends EntityWizardCommand
                {
                    public function label(): ?string
                    {
                        return 'my command';
                    }

                    public function buildFormFieldsForFirstStep(FieldsContainer $formFields): void
                    {
                        $formFields->addField(SharpFormTextField::make('name'));
                    }

                    protected function executeFirstStep(array $data): array
                    {
                        $this->validate($data, ['name' => 'required']);

                        return $this->toStep('second-step');
                    }

                    public function buildFormFieldsForStepSecondStep(FieldsContainer $formFields): void
                    {
                        $formFields->addField(SharpFormTextField::make('age'));
                    }

                    protected function executeStepSecondStep(array $data): array
                    {
                        expect($data)->toEqual(['age' => 30]);

                        return $this->reload();
                    }
                },
            ];
        }
    });

    $this->sharpList(PersonEntity::class)
        ->callEntityCommand('wizard', ['name' => 'John'])
        ->assertReturnsStep('second-step')
        ->callNextStep(['age' => 30])
        ->assertReturnsReload();
});

test('get & assert show', function () {
    fakeShowFor('person', new class() extends PersonShow
    {
        public function find($id): array
        {
            return ['name' => 'John Doe', 'age' => 31];
        }
    });

    $this->sharpShow(PersonEntity::class, 1)
        ->get()
        ->assertOk()
        ->assertShowData(['name' => 'John Doe']);
});

test('get & assert show EEL', function () {
    fakeShowFor('person', new class() extends PersonShow
    {
        public function buildShowFields(FieldsContainer $showFields): void
        {
            $showFields->addField(
                SharpShowEntityListField::make(PersonEntity::class)
            );
        }

        public function find($id): array
        {
            return ['name' => 'John Doe', 'age' => 31];
        }
    });

    fakeListFor(PersonEntity::class, new class() extends PersonList
    {
        public function getListData(): array
        {
            return [
                ['id' => 1, 'name' => 'Marie Curie'],
                ['id' => 2, 'name' => 'Albert Einstein'],
            ];
        }
    });

    $this->sharpShow(PersonEntity::class, 1)
        ->sharpListField(PersonEntity::class)
        ->get()
        ->assertOk()
        ->assertListCount(2)
        ->assertListContains(['name' => 'Marie Curie']);
});

test('nested form', function () {
    $this->sharpForm(PersonEntity::class)->create();
    expect(sharp()->context()->breadcrumb()->getCurrentPath())->toEqual('s-list/person/s-form/person');
    $this->sharpForm(PersonEntity::class, 1)->update([]);
    expect(sharp()->context()->breadcrumb()->getCurrentPath())->toEqual('s-list/person/s-show/person/1/s-form/person/1');
});

test('create & store form', function () {
    fakeFormFor('person', new class() extends PersonForm
    {
        public function buildFormFields(FieldsContainer $formFields): void
        {
            $formFields
                ->addField(SharpFormEditorField::make('name'))
                ->addField(SharpFormEditorField::make('job'));
        }

        public function find($id): array
        {
            return ['name' => 'John Wayne', 'job' => 'actor'];
        }

        public function update($id, array $data)
        {
            expect($data)->toEqual(['name' => 'John Doe', 'job' => null]);

            return 1;
        }
    });

    $this->sharpForm(PersonEntity::class)
        ->create()
        ->assertOk()
        ->store(['name' => 'John Doe'])
        ->assertValid()
        ->assertRedirect();
});

test('store form', function () {
    fakeFormFor('person', new class() extends PersonForm
    {
        public function buildFormFields(FieldsContainer $formFields): void
        {
            $formFields
                ->addField(SharpFormEditorField::make('name'))
                ->addField(SharpFormEditorField::make('job'));
        }

        public function find($id): array
        {
            return ['name' => 'John Wayne', 'job' => 'actor'];
        }

        public function update($id, array $data)
        {
            expect($data)->toEqual(['name' => 'John Doe', 'job' => null]);

            return 1;
        }
    });

    $this->sharpForm(PersonEntity::class)
        ->store(['name' => 'John Doe'])
        ->assertValid()
        ->assertRedirect();
});

test('edit & update form', function () {
    fakeFormFor('person', new class() extends PersonForm
    {
        public function buildFormFields(FieldsContainer $formFields): void
        {
            $formFields
                ->addField(SharpFormEditorField::make('name'))
                ->addField(SharpFormEditorField::make('job'));
        }

        public function find($id): array
        {
            return ['name' => 'John Wayne', 'job' => 'actor'];
        }

        public function update($id, array $data)
        {
            expect($data)->toEqual(['name' => 'John Doe', 'job' => 'actor']);

            return 1;
        }
    });

    $this->sharpForm(PersonEntity::class, 1)
        ->edit()
        ->assertOk()
        ->update(['name' => 'John Doe'])
        ->assertValid()
        ->assertRedirect();
});

test('update form', function () {
    fakeFormFor('person', new class() extends PersonForm
    {
        public function buildFormFields(FieldsContainer $formFields): void
        {
            $formFields
                ->addField(SharpFormEditorField::make('name'))
                ->addField(SharpFormEditorField::make('job'));
        }

        public function find($id): array
        {
            return ['name' => 'John Wayne', 'job' => 'actor'];
        }

        public function update($id, array $data)
        {
            expect($data)->toEqual(['name' => 'John Doe', 'job' => null]);

            return 1;
        }
    });

    $this->sharpForm(PersonEntity::class, 1)
        ->update(['name' => 'John Doe'])
        ->assertValid()
        ->assertRedirect();
});
