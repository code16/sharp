<?php

use Code16\Sharp\EntityList\Commands\EntityCommand;
use Code16\Sharp\EntityList\Commands\Wizards\EntityWizardCommand;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Tests\Fixtures\Entities\PersonEntity;
use Code16\Sharp\Tests\Fixtures\Sharp\PersonList;
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
        // ->getListData()
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
