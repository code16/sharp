<?php

use Code16\Sharp\EntityList\Commands\InstanceCommand;
use Code16\Sharp\Exceptions\Form\SharpApplicativeException;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Tests\Fixtures\Entities\PersonEntity;
use Code16\Sharp\Tests\Fixtures\Sharp\PersonList;
use Code16\Sharp\Utils\Fields\FieldsContainer;
use Code16\Sharp\Utils\PageAlerts\PageAlert;
use Illuminate\Http\UploadedFile;

beforeEach(function () {
    sharp()->config()->addEntity('person', PersonEntity::class);
    login();
});

it('allows to call an info instance command', function () {
    fakeListFor('person', new class extends PersonList
    {
        protected function getInstanceCommands(): ?array
        {
            return [
                'instance_info' => new class extends InstanceCommand
                {
                    public function label(): ?string
                    {
                        return 'my command';
                    }

                    public function execute($instanceId, array $data = []): array
                    {
                        return $this->info('ok');
                    }
                },
            ];
        }
    });

    $this->postJson(route('code16.sharp.api.list.command.instance', ['person', 'instance_info', 1]))
        ->assertOk()
        ->assertJson([
            'action' => 'info',
            'message' => 'ok',
        ]);
});

it('allows to call a reload instance command', function () {
    fakeListFor('person', new class extends PersonList
    {
        protected function getInstanceCommands(): ?array
        {
            return [
                'instance_reload' => new class extends InstanceCommand
                {
                    public function label(): ?string
                    {
                        return 'my command';
                    }

                    public function execute($instanceId, array $data = []): array
                    {
                        return $this->reload();
                    }
                },
            ];
        }
    });

    $this->postJson(route('code16.sharp.api.list.command.instance', ['person', 'instance_reload', 1]))
        ->assertOk()
        ->assertJson([
            'action' => 'reload',
        ]);
});

it('allows to call a view instance command', function () {
    fakeListFor('person', new class extends PersonList
    {
        protected function getInstanceCommands(): ?array
        {
            return [
                'instance_view' => new class extends InstanceCommand
                {
                    public function label(): ?string
                    {
                        return 'my command';
                    }

                    public function execute($instanceId, array $data = []): array
                    {
                        return $this->view('welcome');
                    }
                },
            ];
        }
    });

    $this->postJson(route('code16.sharp.api.list.command.instance', ['person', 'instance_view', 1]))
        ->assertOk()
        ->assertJson([
            'action' => 'view',
        ]);
});

it('allows to call a refresh instance command', function () {
    fakeListFor('person', new class extends PersonList
    {
        protected function getInstanceCommands(): ?array
        {
            return [
                'instance_refresh' => new class extends InstanceCommand
                {
                    public function label(): ?string
                    {
                        return 'my command';
                    }

                    public function execute($instanceId, array $data = []): array
                    {
                        return $this->refresh([1, 3]);
                    }
                },
            ];
        }

        public function getListData(): array|\Illuminate\Contracts\Support\Arrayable
        {
            return collect([
                ['id' => 1, 'name' => 'Marie Curie'],
                ['id' => 2, 'name' => 'Albert Einstein'],
                ['id' => 3, 'name' => 'Niels Bohr'],
            ])->filter(function ($item) {
                return in_array($item['id'], $this->queryParams->specificIds());
            })->values()->all();
        }
    });

    $this->postJson(route('code16.sharp.api.list.command.instance', ['person', 'instance_refresh', 1]))
        ->assertOk()
        ->assertJson([
            'action' => 'refresh',
            'items' => [
                ['id' => 1, 'name' => 'Marie Curie'],
                ['id' => 3, 'name' => 'Niels Bohr'],
            ],
        ]);
});

it('allows to call a form instance command and it handles 422', function () {
    fakeListFor('person', new class extends PersonList
    {
        protected function getInstanceCommands(): ?array
        {
            return [
                'instance_form' => new class extends InstanceCommand
                {
                    public function label(): ?string
                    {
                        return 'my command';
                    }

                    public function buildFormFields(FieldsContainer $formFields): void
                    {
                        $formFields->addField(SharpFormTextField::make('name'));
                    }

                    public function execute($instanceId, array $data = []): array
                    {
                        $this->validate($data, ['name' => 'required']);

                        return $this->reload();
                    }
                },
            ];
        }
    });

    $this
        ->postJson(
            route('code16.sharp.api.list.command.instance', ['person', 'instance_form', 1]),
            ['data' => ['name' => 'Pierre']]
        )
        ->assertOk()
        ->assertJson([
            'action' => 'reload',
        ]);

    $this
        ->postJson(
            route('code16.sharp.api.list.command.instance', ['person', 'instance_form', 1]),
            ['data' => ['name' => '']]
        )
        ->assertJsonValidationErrors(['name']);
});

it('allows to call a download instance command', function () {
    fakeListFor('person', new class extends PersonList
    {
        protected function getInstanceCommands(): ?array
        {
            return [
                'instance_download' => new class extends InstanceCommand
                {
                    public function label(): ?string
                    {
                        return 'my command';
                    }

                    public function execute($instanceId, array $data = []): array
                    {
                        Storage::fake('files');
                        UploadedFile::fake()
                            ->create('account.pdf', 100, 'application/pdf')
                            ->storeAs('pdf', 'account.pdf', ['disk' => 'files']);

                        return $this->download('pdf/account.pdf', 'account.pdf', 'files');
                    }
                },
            ];
        }
    });

    $response = $this
        ->postJson(route('code16.sharp.api.list.command.instance', ['person', 'instance_download', 1]))
        ->assertOk();

    expect($response->headers->get('content-disposition'))
        ->toContain('account.pdf');
});

it('allows to call a streamDownload instance command', function () {
    fakeListFor('person', new class extends PersonList
    {
        protected function getInstanceCommands(): ?array
        {
            return [
                'instance_streamDownload' => new class extends InstanceCommand
                {
                    public function label(): ?string
                    {
                        return 'my command';
                    }

                    public function execute($instanceId, array $data = []): array
                    {
                        return $this->streamDownload('content', 'stream.txt');
                    }
                },
            ];
        }
    });

    $response = $this
        ->postJson(route('code16.sharp.api.list.command.instance', ['person', 'instance_streamDownload', 1]))
        ->assertOk()
        ->assertHeader('content-type', 'text/html; charset=UTF-8');

    expect($response->headers->get('content-disposition'))
        ->toContain('stream.txt');
});

it('returns an applicative exception as a 417 as always', function () {
    fakeListFor('person', new class extends PersonList
    {
        protected function getInstanceCommands(): ?array
        {
            return [
                'instance_417' => new class extends InstanceCommand
                {
                    public function label(): ?string
                    {
                        return 'my command';
                    }

                    public function execute($instanceId, array $data = []): array
                    {
                        throw new SharpApplicativeException('error');
                    }
                },
            ];
        }
    });

    $this
        ->postJson(route('code16.sharp.api.list.command.instance', ['person', 'instance_417', 1]))
        ->assertStatus(417)
        ->assertJson([
            'message' => 'error',
        ]);
});

it('disallows to call an unauthorized instance command', function () {
    fakeListFor('person', new class extends PersonList
    {
        protected function getInstanceCommands(): ?array
        {
            return [
                'instance_unauthorized' => new class extends InstanceCommand
                {
                    public function label(): ?string
                    {
                        return 'my command';
                    }

                    public function authorizeFor($instanceId): bool
                    {
                        return $instanceId != 1;
                    }

                    public function execute($instanceId, array $data = []): array
                    {
                        return $this->reload();
                    }
                },
            ];
        }
    });

    $this
        ->postJson(route('code16.sharp.api.list.command.instance', ['person', 'instance_unauthorized', 1]))
        ->assertForbidden();

    $this
        ->postJson(route('code16.sharp.api.list.command.instance', ['person', 'instance_unauthorized', 2]))
        ->assertOk();
});

it('returns the form of the instance command', function () {
    fakeListFor('person', new class extends PersonList
    {
        protected function getInstanceCommands(): ?array
        {
            return [
                'instance_form' => new class extends InstanceCommand
                {
                    public function label(): ?string
                    {
                        return 'my command';
                    }

                    public function buildFormFields(FieldsContainer $formFields): void
                    {
                        $formFields->addField(SharpFormTextField::make('name'));
                    }

                    public function execute($instanceId, array $data = []): array
                    {
                        return $this->reload();
                    }
                },
            ];
        }
    });

    $this
        ->getJson(route('code16.sharp.api.list.command.instance.form', ['person', 'instance_form', 1]))
        ->assertOk()
        ->assertJsonFragment([
            'fields' => [
                'name' => [
                    'key' => 'name',
                    'type' => 'text',
                    'inputType' => 'text',
                ],
            ],
            'layout' => [
                'tabbed' => false,
                'tabs' => [
                    [
                        'columns' => [
                            [
                                'fields' => [
                                    [
                                        ['key' => 'name', 'size' => 12, 'sizeXS' => 12],
                                    ],
                                ],
                                'size' => 12,
                            ],
                        ],
                        'title' => 'one',
                    ],
                ],
            ],
        ]);
});

it('allows to configure a page alert on an instance command', function () {
    fakeListFor('person', new class extends PersonList
    {
        protected function getInstanceCommands(): ?array
        {
            return [
                'cmd' => new class extends InstanceCommand
                {
                    public function label(): ?string
                    {
                        return 'my command';
                    }

                    public function buildPageAlert(PageAlert $pageAlert): void
                    {
                        $pageAlert
                            ->setLevelInfo()
                            ->setMessage('My page alert');
                    }
                    
                    public function buildFormFields(FieldsContainer $formFields): void
                    {
                        $formFields->addField(SharpFormTextField::make('name'));
                    }

                    public function execute($instanceId, array $data = []): array
                    {
                        return $this->reload();
                    }
                },
            ];
        }
    });

    $this
        ->getJson(route('code16.sharp.api.list.command.instance.form', ['person', 'cmd', 1]))
        ->assertOk()
        ->assertJsonFragment([
            'pageAlert' => [
                'text' => 'My page alert',
                'level' => \Code16\Sharp\Enums\PageAlertLevel::Info->value,
            ],
        ]);
});

it('handles localized form of the instance command', function () {
    fakeListFor('person', new class extends PersonList
    {
        protected function getInstanceCommands(): ?array
        {
            return [
                'instance_form_localized' => new class extends InstanceCommand
                {
                    public function label(): ?string
                    {
                        return 'my command';
                    }

                    public function buildFormFields(FieldsContainer $formFields): void
                    {
                        $formFields->addField(SharpFormTextField::make('name')->setLocalized());
                    }

                    public function execute($instanceId, array $data = []): array
                    {
                        return $this->reload();
                    }

                    public function getDataLocalizations(): array
                    {
                        return ['fr', 'en', 'it'];
                    }
                },
            ];
        }
    });

    $this
        ->getJson(route('code16.sharp.api.list.command.instance.form', ['person', 'instance_form_localized', 1]))
        ->assertOk()
        ->assertJsonFragment([
            'fields' => [
                'name' => [
                    'key' => 'name',
                    'type' => 'text',
                    'inputType' => 'text',
                    'localized' => true,
                ],
            ],
            'locales' => ['fr', 'en', 'it'],
        ]);
});

it('allows to initialize form data in an instance command', function () {
    fakeListFor('person', new class extends PersonList
    {
        protected function getInstanceCommands(): ?array
        {
            return [
                'instance_with_init_data' => new class extends InstanceCommand
                {
                    public function label(): ?string
                    {
                        return 'my command';
                    }
                    
                    public function buildCommandConfig(): void
                    {
                        $this->configureFormModalTitle(fn ($data) => "Edit {$data['name']}")
                            ->configureFormModalDescription('Custom description');
                    }

                    public function buildFormFields(FieldsContainer $formFields): void
                    {
                        $formFields->addField(SharpFormTextField::make('name')->setLocalized());
                    }

                    public function execute($instanceId, array $data = []): array
                    {
                        return $this->reload();
                    }

                    public function initialData($instanceId): array
                    {
                        return [
                            'name' => $instanceId == 1 ? 'Marie Curie' : '',
                        ];
                    }
                },
            ];
        }
    });

    $this
        ->getJson(route('code16.sharp.api.list.command.instance.form', ['person', 'instance_with_init_data', 1]))
        ->assertOk()
        ->assertJsonFragment([
            'data' => [
                'name' => 'Marie Curie',
            ],
            'config' => [
                'title' => 'Edit Marie Curie',
                'description' => 'Custom description',
            ],
        ]);
});
