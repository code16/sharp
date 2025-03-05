<?php

use Code16\Sharp\EntityList\Commands\InstanceCommand;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Tests\Fixtures\Entities\PersonEntity;
use Code16\Sharp\Tests\Fixtures\Entities\SinglePersonEntity;
use Code16\Sharp\Tests\Fixtures\Sharp\PersonShow;
use Code16\Sharp\Tests\Fixtures\Sharp\SinglePersonShow;
use Code16\Sharp\Utils\Fields\FieldsContainer;

beforeEach(function () {
    login();
});

it('allows to call an info instance command from a show', function () {
    sharp()->config()->declareEntity(PersonEntity::class);

    fakeShowFor(PersonEntity::class, new class() extends PersonShow
    {
        public function getInstanceCommands(): ?array
        {
            return [
                'cmd' => new class() extends InstanceCommand
                {
                    public function label(): ?string
                    {
                        return 'instance';
                    }

                    public function execute($instanceId, array $data = []): array
                    {
                        return $this->info('ok: '.$instanceId);
                    }
                },
            ];
        }
    });

    $this->postJson(route('code16.sharp.api.show.command.instance', ['person', 'cmd', 1]))
        ->assertOk()
        ->assertJson([
            'action' => 'info',
            'message' => 'ok: 1',
        ]);
});

it('allows to call an info instance command from a single show', function () {
    sharp()->config()->declareEntity(SinglePersonEntity::class);

    fakeShowFor(SinglePersonEntity::class, new class() extends SinglePersonShow
    {
        public function getInstanceCommands(): ?array
        {
            return [
                'cmd' => new class() extends InstanceCommand
                {
                    public function label(): ?string
                    {
                        return 'instance';
                    }

                    public function execute($instanceId, array $data = []): array
                    {
                        return $this->info('ok');
                    }
                },
            ];
        }
    });

    $this->postJson(route('code16.sharp.api.show.command.instance', ['single-person', 'cmd']))
        ->assertOk()
        ->assertJson([
            'action' => 'info',
            'message' => 'ok',
        ]);
});

it('gets form and initialize form data in an instance command of a show', function () {
    sharp()->config()->declareEntity(PersonEntity::class);

    fakeShowFor(PersonEntity::class, new class() extends PersonShow
    {
        public function getInstanceCommands(): ?array
        {
            return [
                'cmd' => new class() extends InstanceCommand
                {
                    public function label(): ?string
                    {
                        return 'instance';
                    }

                    public function buildCommandConfig(): void
                    {
                        $this->configureFormModalTitle(fn ($data) => "Edit {$data['name']}")
                            ->configureFormModalDescription('Custom description');
                    }

                    public function buildFormFields(FieldsContainer $formFields): void
                    {
                        $formFields->addField(SharpFormTextField::make('name'));
                    }

                    protected function initialData(mixed $instanceId): array
                    {
                        return [
                            'name' => 'Marie Curie',
                        ];
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
        ->getJson(route('code16.sharp.api.show.command.instance.form', ['person', 'cmd', 1]))
        ->assertOk()
        ->assertJsonFragment([
            'data' => [
                'name' => 'Marie Curie',
            ],
            'config' => [
                'title' => 'Edit Marie Curie',
                'description' => 'Custom description',
                'showSubmitAndReopenButton' => false,
            ],
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
                                        ['key' => 'name', 'size' => 12],
                                    ],
                                ],
                                'size' => 12,
                            ],
                        ],
                        'title' => '',
                    ],
                ],
            ],
        ]);

    $this
        ->postJson(
            route('code16.sharp.api.show.command.instance', ['person', 'cmd', 1]),
            ['data' => ['name' => '']]
        )
        ->assertJsonValidationErrors(['name']);
});

it('gets form and initialize form data in an instance command of a single show', function () {
    sharp()->config()->declareEntity(SinglePersonEntity::class);

    fakeShowFor(SinglePersonEntity::class, new class() extends SinglePersonShow
    {
        public function getInstanceCommands(): ?array
        {
            return [
                'single_cmd' => new class() extends InstanceCommand
                {
                    public function label(): ?string
                    {
                        return 'instance';
                    }

                    public function buildCommandConfig(): void
                    {
                        $this->configureFormModalTitle(fn ($data) => "Edit {$data['name']}")
                            ->configureFormModalDescription('Custom description');
                    }

                    public function buildFormFields(FieldsContainer $formFields): void
                    {
                        $formFields->addField(SharpFormTextField::make('name'));
                    }

                    protected function initialData(mixed $instanceId): array
                    {
                        return [
                            'name' => 'Marie Curie',
                        ];
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
        ->getJson(
            route('code16.sharp.api.show.command.singleInstance.form', [
                'entityKey' => 'single-person',
                'commandKey' => 'single_cmd',
            ])
        )
        ->assertOk()
        ->assertJsonFragment([
            'data' => [
                'name' => 'Marie Curie',
            ],
            'config' => [
                'title' => 'Edit Marie Curie',
                'description' => 'Custom description',
                'showSubmitAndReopenButton' => false,
            ],
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
                                        ['key' => 'name', 'size' => 12],
                                    ],
                                ],
                                'size' => 12,
                            ],
                        ],
                        'title' => '',
                    ],
                ],
            ],
        ]);

    $this
        ->postJson(
            route('code16.sharp.api.show.command.instance', ['single-person', 'single_cmd']),
            ['data' => ['name' => '']]
        )
        ->assertJsonValidationErrors(['name']);
});
