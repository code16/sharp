<?php

use Code16\Sharp\EntityList\Commands\Wizards\InstanceWizardCommand;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Tests\Fixtures\Entities\PersonEntity;
use Code16\Sharp\Tests\Fixtures\Sharp\PersonList;
use Code16\Sharp\Utils\Fields\FieldsContainer;

beforeEach(function () {
    sharp()->config()->addEntity('person', PersonEntity::class);
    login();
});

it('displays first step form on the first call of a wizard instance command', function () {
    fakeListFor('person', new class extends PersonList
    {
        protected function getInstanceCommands(): ?array
        {
            return [
                'wizard' => new class extends InstanceWizardCommand
                {
                    public function label(): ?string
                    {
                        return 'my command';
                    }

                    public function buildFormFieldsForFirstStep(FieldsContainer $formFields): void
                    {
                        $formFields->addField(SharpFormTextField::make('name'));
                    }

                    protected function executeFirstStep($instanceId, array $data): array
                    {
                        return $this->reload();
                    }
                },
            ];
        }
    });

    $this
        ->getJson(route('code16.sharp.api.list.command.instance.form', ['person', 'wizard', 1]))
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

it('allows to post first step of a wizard instance command', function () {
    fakeListFor('person', new class extends PersonList
    {
        protected function getInstanceCommands(): ?array
        {
            return [
                'wizard' => new class extends InstanceWizardCommand
                {
                    protected function getKey(): string
                    {
                        return 'test-key';
                    }

                    public function label(): ?string
                    {
                        return 'my command';
                    }

                    public function buildFormFieldsForFirstStep(FieldsContainer $formFields): void
                    {
                        $formFields->addField(SharpFormTextField::make('name'));
                    }

                    protected function executeFirstStep($instanceId, array $data): array
                    {
                        $this->validate($data, ['name' => 'required']);

                        return $this->toStep('next-step');
                    }
                },
            ];
        }
    });

    $this
        ->postJson(route('code16.sharp.api.list.command.instance', ['person', 'wizard', 1]))
        ->assertJsonValidationErrors('name');

    $this
        ->postJson(
            route('code16.sharp.api.list.command.instance', ['person', 'wizard', 1]),
            ['data' => ['name' => 'test']],
        )
        ->assertOk()
        ->assertJson([
            'action' => 'step',
            'step' => 'next-step:test-key',
        ]);
});

it('allows to check if context is valid', function () {
    fakeListFor('person', new class extends PersonList
    {
        protected function getInstanceCommands(): ?array
        {
            return [
                'wizard' => new class extends InstanceWizardCommand
                {
                    protected function getKey(): string
                    {
                        return 'test-key';
                    }

                    public function label(): ?string
                    {
                        return 'my command';
                    }

                    public function buildFormFieldsForFirstStep(FieldsContainer $formFields): void
                    {
                    }

                    protected function executeFirstStep($instanceId, array $data): array
                    {
                        $this->getWizardContext()->put('first-step-passed', true);

                        return $this->toStep('next-step');
                    }

                    public function initialDataForStepNextStep(): array
                    {
                        $this->getWizardContext()->validate(['first-step-passed' => 'required']);

                        return [
                            'age' => today()->day,
                        ];
                    }

                    public function buildFormFieldsForStepNextStep(FieldsContainer $formFields): void
                    {
                        $formFields->addField(SharpFormTextField::make('age'));
                    }

                    protected function executeStepNextStep($instanceId, array $data): array
                    {
                        return $this->reload();
                    }
                },
            ];
        }
    });

    // Try to get step 2 first: error (SharpInvalidStepException)
    $this
        ->getJson(
            route('code16.sharp.api.list.command.instance.form', [
                'entityKey' => 'person',
                'commandKey' => 'wizard',
                'instanceId' => 1,
                'command_step' => 'next-step:test-key',
            ])
        )
        ->assertStatus(500);

    // First post step 1...
    $this
        ->postJson(
            route('code16.sharp.api.list.command.instance', ['person', 'wizard', 1]),
        )
        ->assertOk();

    // Then get step 2
    $this
        ->getJson(
            route('code16.sharp.api.list.command.instance.form', [
                'entityKey' => 'person',
                'commandKey' => 'wizard',
                'instanceId' => 1,
                'command_step' => 'next-step:test-key',
            ])
        )
        ->assertOk()
        ->assertJsonFragment([
            'fields' => [
                'age' => [
                    'key' => 'age',
                    'type' => 'text',
                    'inputType' => 'text',
                ],
            ],
        ])
        ->assertJsonFragment([
            'data' => [
                'age' => today()->day,
            ],
        ]);
});

it('allows to post second step of a wizard instance command', function () {
    fakeListFor('person', new class extends PersonList
    {
        protected function getInstanceCommands(): ?array
        {
            return [
                'wizard' => new class extends InstanceWizardCommand
                {
                    protected function getKey(): string
                    {
                        return 'test-key';
                    }

                    public function label(): ?string
                    {
                        return 'my command';
                    }

                    public function buildFormFieldsForFirstStep(FieldsContainer $formFields): void
                    {
                        $formFields->addField(SharpFormTextField::make('name'));
                    }

                    protected function executeFirstStep($instanceId, array $data): array
                    {
                        $this->validate($data, ['name' => 'required']);

                        return $this->toStep('next-step');
                    }

                    public function buildFormFieldsForStepNextStep(FieldsContainer $formFields): void
                    {
                        $formFields->addField(SharpFormTextField::make('age'));
                    }

                    protected function executeStepNextStep($instanceId, array $data): array
                    {
                        return $this->reload();
                    }
                },
            ];
        }
    });

    // First post step 1...
    $this
        ->postJson(
            route('code16.sharp.api.list.command.instance', ['person', 'wizard', 1]),
            ['data' => ['name' => 'test']],
        )
        ->assertOk();

    // Then post step 2...
    $this
        ->postJson(
            route('code16.sharp.api.list.command.instance', [
                'entityKey' => 'person',
                'commandKey' => 'wizard',
                'instanceId' => 1,
                'command_step' => 'next-step:test-key',
            ]),
            ['data' => ['age' => '22']],
        )
        ->assertOk()
        ->assertJson([
            'action' => 'reload',
        ]);
});

it('allows to define a global method for step execution', function () {
    fakeListFor('person', new class extends PersonList
    {
        protected function getInstanceCommands(): ?array
        {
            return [
                'wizard' => new class extends InstanceWizardCommand
                {
                    protected function getKey(): string
                    {
                        return 'test-key';
                    }

                    public function label(): ?string
                    {
                        return 'my command';
                    }

                    public function buildFormFieldsForFirstStep(FieldsContainer $formFields): void
                    {
                        $formFields->addField(SharpFormTextField::make('name'));
                    }

                    public function executeFirstStep($instanceId, array $data): array
                    {
                        return $this->toStep('next-step');
                    }

                    public function buildFormFieldsForStep(string $step, FieldsContainer $formFields): void
                    {
                        if ($step === 'next-step') {
                            $formFields->addField(SharpFormTextField::make('age'));
                        }
                    }

                    public function executeStep(string $step, $instanceId, array $data = []): array
                    {
                        if ($step === 'next-step') {
                            return $this->reload();
                        }
                    }
                },
            ];
        }
    });

    // First post step 1...
    $this
        ->postJson(route('code16.sharp.api.list.command.instance', ['person', 'wizard', 1]))
        ->assertOk();

    // Then get step 2
    $this
        ->getJson(
            route('code16.sharp.api.list.command.instance.form', [
                'entityKey' => 'person',
                'commandKey' => 'wizard',
                'instanceId' => 1,
                'command_step' => 'next-step:test-key',
            ])
        )
        ->assertOk()
        ->assertJsonFragment([
            'fields' => [
                'age' => [
                    'key' => 'age',
                    'type' => 'text',
                    'inputType' => 'text',
                ],
            ],
        ]);

    // Then post step 2...
    $this
        ->postJson(
            route('code16.sharp.api.list.command.instance', [
                'entityKey' => 'person',
                'commandKey' => 'wizard',
                'instanceId' => 1,
                'command_step' => 'next-step:test-key',
            ]),
        )
        ->assertOk()
        ->assertJson([
            'action' => 'reload',
        ]);
});
