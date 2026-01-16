<?php

use Code16\Sharp\EntityList\Commands\EntityCommand;
use Code16\Sharp\EntityList\Commands\InstanceCommand;
use Code16\Sharp\EntityList\Commands\Wizards\EntityWizardCommand;
use Code16\Sharp\Form\Fields\SharpFormEditorField;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Show\Fields\SharpShowDashboardField;
use Code16\Sharp\Show\Fields\SharpShowEntityListField;
use Code16\Sharp\Tests\Fixtures\Entities\DashboardEntity;
use Code16\Sharp\Tests\Fixtures\Entities\PersonEntity;
use Code16\Sharp\Tests\Fixtures\Entities\SinglePersonEntity;
use Code16\Sharp\Tests\Fixtures\Sharp\PersonForm;
use Code16\Sharp\Tests\Fixtures\Sharp\PersonList;
use Code16\Sharp\Tests\Fixtures\Sharp\PersonShow;
use Code16\Sharp\Tests\Fixtures\Sharp\SinglePersonShow;
use Code16\Sharp\Tests\Fixtures\Sharp\TestDashboard;
use Code16\Sharp\Utils\Fields\FieldsContainer;
use Code16\Sharp\Utils\Testing\SharpAssertions;
use Illuminate\Http\UploadedFile;

pest()->use(SharpAssertions::class);

beforeEach(function () {
    login();
    sharp()->config()->declareEntity(PersonEntity::class);
    sharp()->config()->declareEntity(SinglePersonEntity::class);
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
    fakeListFor(PersonEntity::class, new class() extends PersonList
    {
        protected function getFilters(): ?array
        {
            return [
                new class() extends \Code16\Sharp\Filters\CheckFilter
                {
                    public function buildFilterConfig(): void
                    {
                        $this->configureKey('is_valid');
                    }
                },
            ];
        }

        protected function getEntityCommands(): ?array
        {
            return [
                'cmd-form' => new class() extends EntityCommand
                {
                    public function label(): ?string
                    {
                        return 'entity';
                    }

                    public function buildFormFields(FieldsContainer $formFields): void
                    {
                        $formFields
                            ->addField(SharpFormTextField::make('action'))
                            ->addField(SharpFormTextField::make('field_with_initial_value'));
                    }

                    protected function initialData(): array
                    {
                        return ['field_with_initial_value' => 'test'];
                    }

                    public function execute(array $data = []): array
                    {
                        expect($data)->toMatchArray(['field_with_initial_value' => 'test']);

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
                'cmd' => new class() extends EntityCommand
                {
                    public function label(): ?string
                    {
                        return 'entity';
                    }

                    public function execute(array $data = []): array
                    {
                        // expect($this->queryParams->filterFor('is_valid'))->toBeTrue();

                        return $this->info('ok');
                    }
                },
            ];
        }
    });

    $this->withoutExceptionHandling();

    $this->sharpList(PersonEntity::class)
        ->withFilter('is_valid', true)
        ->entityCommand('cmd')->post()
        ->assertReturnsInfo('ok');

    $this->sharpList(PersonEntity::class)
        ->entityCommand('cmd-form')->getForm()->post(['action' => 'info'])
        ->assertReturnsInfo('ok');

    $this->sharpList(PersonEntity::class)
        ->entityCommand('cmd-form')->getForm()->post(['action' => 'link'])
        ->assertReturnsLink('https://example.org');

    $this->sharpList(PersonEntity::class)
        ->entityCommand('cmd-form')->getForm()->post(['action' => 'view'])
        ->assertReturnsView('fixtures::test', [
            'text' => 'text',
        ]);

    $this->sharpList(PersonEntity::class)
        ->entityCommand('cmd-form')->getForm()->post(['action' => 'reload'])
        ->assertReturnsReload();

    $this->sharpList(PersonEntity::class)
        ->entityCommand('cmd-form')->getForm()->post(['action' => 'download'])
        ->assertReturnsDownload('account.pdf');

    $this->sharpList(PersonEntity::class)
        ->entityCommand('cmd-form')->getForm()->post(['action' => 'refresh'])
        ->assertReturnsRefresh([1, 2]);
});

it('call & assert an entity list entity wizard command', function () {
    fakeListFor(PersonEntity::class, new class() extends PersonList
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

                    protected function initialDataForFirstStep(): array
                    {
                        return ['field_with_initial_value' => 'test'];
                    }

                    public function buildFormFieldsForFirstStep(FieldsContainer $formFields): void
                    {
                        $formFields
                            ->addField(SharpFormTextField::make('name'))
                            ->addField(SharpFormTextField::make('field_with_initial_value'));
                    }

                    protected function executeFirstStep(array $data): array
                    {
                        expect($data)->toEqual(['name' => 'John', 'field_with_initial_value' => 'test']);

                        $this->validate($data, ['name' => 'required']);

                        return $this->toStep('second-step');
                    }

                    public function initialDataForStepSecondStep(): array
                    {
                        return ['field_with_initial_value' => 'test'];
                    }

                    public function buildFormFieldsForStepSecondStep(FieldsContainer $formFields): void
                    {
                        $formFields
                            ->addField(SharpFormTextField::make('age'))
                            ->addField(SharpFormTextField::make('field_with_initial_value'));
                    }

                    protected function executeStepSecondStep(array $data): array
                    {
                        expect($data)->toEqual(['field_with_initial_value' => 'test', 'age' => 30]);

                        return $this->reload();
                    }
                },
            ];
        }
    });

    $this->withoutExceptionHandling();

    $this->sharpList(PersonEntity::class)
        ->entityCommand('wizard')
        ->getForm()->post(['name' => 'John'])
        ->assertReturnsStep('second-step')
        ->getNextStepForm()->post(['age' => 30])
        ->assertReturnsReload();
});

it('call & assert a entity list instance command', function () {
    fakeListFor(PersonEntity::class, new class() extends PersonList
    {
        protected function getInstanceCommands(): ?array
        {
            return [
                'cmd-form' => new class() extends InstanceCommand
                {
                    public function label(): ?string
                    {
                        return 'entity';
                    }

                    public function buildFormFields(FieldsContainer $formFields): void
                    {
                        $formFields->addField(SharpFormTextField::make('action'));
                    }

                    public function execute($instanceId, array $data = []): array
                    {
                        return match ($data['action']) {
                            'info' => $this->info('instance '.$instanceId),
                        };
                    }
                },
                'cmd' => new class() extends InstanceCommand
                {
                    public function label(): ?string
                    {
                        return 'entity';
                    }

                    public function execute($instanceId, array $data = []): array
                    {
                        return $this->info('instance '.$instanceId);
                    }
                },
            ];
        }
    });

    $this->sharpList(PersonEntity::class)
        ->instanceCommand('cmd', 1)->post()
        ->assertReturnsInfo('instance 1');

    $this->sharpList(PersonEntity::class)
        ->instanceCommand('cmd-form', 1)->getForm()->post(['action' => 'info'])
        ->assertReturnsInfo('instance 1');
});

it('call & assert a show instance command', function () {
    fakeShowFor(PersonEntity::class, new class() extends PersonShow
    {
        public function getInstanceCommands(): ?array
        {
            return [
                'cmd-form' => new class() extends InstanceCommand
                {
                    public function label(): ?string
                    {
                        return 'entity';
                    }

                    public function buildFormFields(FieldsContainer $formFields): void
                    {
                        $formFields->addField(SharpFormTextField::make('action'));
                    }

                    public function execute($instanceId, array $data = []): array
                    {
                        return match ($data['action']) {
                            'info' => $this->info('instance '.$instanceId),
                        };
                    }
                },
                'cmd' => new class() extends InstanceCommand
                {
                    public function label(): ?string
                    {
                        return 'entity';
                    }

                    public function execute($instanceId, array $data = []): array
                    {
                        return $this->info('instance '.$instanceId);
                    }
                },
            ];
        }
    });

    $this->sharpShow(PersonEntity::class, 1)
        ->instanceCommand('cmd')->post()
        ->assertReturnsInfo('instance 1');

    $this->sharpShow(PersonEntity::class, 1)
        ->instanceCommand('cmd-form')->getForm()->post(['action' => 'info'])
        ->assertReturnsInfo('instance 1');
});

test('get show', function () {
    fakeShowFor(PersonEntity::class, new class() extends PersonShow
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

    expect(sharp()->context()->breadcrumb()->getCurrentPath())->toEqual('s-list/person/s-show/person/1');
});

test('get single show', function () {

    fakeShowFor(SinglePersonEntity::class, new class() extends SinglePersonShow
    {
        public function findSingle(): array
        {
            return ['name' => 'John Doe', 'age' => 31];
        }
    });

    $this->sharpShow(SinglePersonEntity::class)
        ->get()
        ->assertOk()
        ->assertShowData(['name' => 'John Doe']);

    expect(sharp()->context()->breadcrumb()->getCurrentPath())->toEqual('s-show/single-person');
});

test('get show EEL', function () {
    fakeShowFor(PersonEntity::class, new class() extends PersonShow
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

test('get nested show', function () {
    fakeShowFor(PersonEntity::class, new class() extends PersonShow
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
        ->sharpShow(PersonEntity::class, 2)
        ->get()
        ->assertOk();

    expect(sharp()->context()->breadcrumb()->getCurrentPath())->toEqual('s-list/person/s-show/person/1/s-show/person/2');
});

test('create & store form', function () {
    fakeFormFor(PersonEntity::class, new class() extends PersonForm
    {
        public function buildFormFields(FieldsContainer $formFields): void
        {
            $formFields
                ->addField(SharpFormEditorField::make('name'))
                ->addField(SharpFormEditorField::make('job'));
        }

        public function create(): array
        {
            return ['name' => 'John Wayne', 'job' => 'actor'];
        }

        public function update($id, array $data)
        {
            expect($data)->toEqual(['name' => 'John Doe', 'job' => 'actor']);

            return 1;
        }
    });

    $this->sharpForm(PersonEntity::class)
        ->create()
        ->assertOk()
        ->store(['name' => 'John Doe'])
        ->assertValid()
        ->assertRedirect();

    expect(sharp()->context()->breadcrumb()->getCurrentPath())->toEqual('s-list/person/s-form/person');
});

test('store form', function () {
    fakeFormFor(PersonEntity::class, new class() extends PersonForm
    {
        public function buildFormFields(FieldsContainer $formFields): void
        {
            $formFields
                ->addField(SharpFormEditorField::make('name'))
                ->addField(SharpFormEditorField::make('job'));
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

    expect(sharp()->context()->breadcrumb()->getCurrentPath())->toEqual('s-list/person/s-form/person');
});

test('edit & update form', function () {
    fakeFormFor(PersonEntity::class, new class() extends PersonForm
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

    expect(sharp()->context()->breadcrumb()->getCurrentPath())->toEqual('s-list/person/s-show/person/1/s-form/person/1');
});

test('update form', function () {
    fakeFormFor(PersonEntity::class, new class() extends PersonForm
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

    expect(sharp()->context()->breadcrumb()->getCurrentPath())->toEqual('s-list/person/s-show/person/1/s-form/person/1');
});

test('update single form', function () {
    $this->sharpForm(SinglePersonEntity::class)
        ->update(['name' => 'John Doe'])
        ->assertValid()
        ->assertRedirect();

    expect(sharp()->context()->breadcrumb()->getCurrentPath())->toEqual('s-show/single-person/s-form/single-person');
});

test('get dashboard', function () {

    sharp()->config()->declareEntity(DashboardEntity::class);

    fakeDashboardFor(DashboardEntity::class, new class() extends TestDashboard
    {
        protected function buildWidgetsData(): void
        {
            $this->setPanelData('panel', ['name' => 'Marie Curie']);
        }
    });

    $this->sharpDashboard(DashboardEntity::class)
        ->get()
        ->assertOk();
});

test('get show dashboard field', function () {
    sharp()->config()->declareEntity(DashboardEntity::class);

    fakeShowFor(PersonEntity::class, new class() extends PersonShow
    {
        public function buildShowFields(FieldsContainer $showFields): void
        {
            $showFields->addField(
                SharpShowDashboardField::make(DashboardEntity::class)
            );
        }

        public function find($id): array
        {
            return ['name' => 'John Doe', 'age' => 31];
        }
    });

    fakeDashboardFor(DashboardEntity::class, new class() extends TestDashboard
    {
        protected function buildWidgetsData(): void
        {
            $this->setPanelData('panel', ['name' => 'Albert Einstein']);
        }
    });

    $this->sharpShow(PersonEntity::class, 1)
        ->sharpDashboardField(DashboardEntity::class)
        ->get()
        ->assertOk();
});
