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
    config()->set(
        'sharp.entities.person',
        PersonEntity::class,
    );

    fakeShowFor('person', new class extends PersonShow {
        public function getInstanceCommands(): ?array
        {
            return [
                'instance_info' => new class extends InstanceCommand
                {
                    public function label(): ?string
                    {
                        return 'instance';
                    }
                    public function execute($instanceId, array $data = []): array
                    {
                        return $this->info('ok');
                    }
                }
            ];
        }
    });

    $this->postJson(route('code16.sharp.api.show.command.instance', ['person', 'instance_info', 1]))
        ->assertOk()
        ->assertJson([
            'action' => 'info',
            'message' => 'ok',
        ]);
});

it('allows to call an info instance command from a single show', function () {
    config()->set(
        'sharp.entities.person',
        SinglePersonEntity::class,
    );

    fakeShowFor('person', new class extends SinglePersonShow {
        public function getInstanceCommands(): ?array
        {
            return [
                'instance_info' => new class extends InstanceCommand
                {
                    public function label(): ?string
                    {
                        return 'instance';
                    }
                    public function execute($instanceId, array $data = []): array
                    {
                        return $this->info('ok');
                    }
                }
            ];
        }
    });

    $this->postJson(route('code16.sharp.api.show.command.instance', ['person', 'instance_info']))
        ->assertOk()
        ->assertJson([
            'action' => 'info',
            'message' => 'ok',
        ]);
});

it('gets form and initialize form data in an instance command of a show', function () {
    config()->set(
        'sharp.entities.person',
        PersonEntity::class,
    );

    fakeShowFor('person', new class extends PersonShow {
        public function getInstanceCommands(): ?array
        {
            return [
                'instance_with_init_data' => new class extends InstanceCommand {
                    public function label(): ?string
                    {
                        return 'instance';
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
                }
            ];
        }
    });

    $this
        ->getJson(route('code16.sharp.api.show.command.instance.form', ['person', 'instance_with_init_data', 1]))
        ->assertOk()
        ->assertJsonFragment([
            'data' => [
                'name' => 'Marie Curie',
            ],
            'fields' => [
                'name' => [
                    'key' => 'name',
                    'type' => 'text',
                    'inputType' => 'text',
                ],
            ],
            'layout' => [
                [['key' => 'name', 'size' => 12, 'sizeXS' => 12]],
            ],
        ]);

    $this
        ->postJson(
            route('code16.sharp.api.show.command.instance', ['person', 'instance_with_init_data', 1]),
            ['data' => ['name' => '']]
        )
        ->assertJsonValidationErrors(['name']);
});

it('gets form and initialize form data in an instance command of a single show', function () {
    config()->set(
        'sharp.entities.person',
        SinglePersonEntity::class,
    );

    fakeShowFor('person', new class extends SinglePersonShow {
        public function getInstanceCommands(): ?array
        {
            return [
                'single_instance_with_init_data' => new class extends InstanceCommand {
                    public function label(): ?string
                    {
                        return 'instance';
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
                }
            ];
        }
    });

    $this
        ->getJson(
            route('code16.sharp.api.show.command.singleInstance.form', [
                'entityKey' => 'person',
                'commandKey' => 'single_instance_with_init_data'
            ])
        )
        ->assertOk()
        ->assertJsonFragment([
            'data' => [
                'name' => 'Marie Curie',
            ],
            'fields' => [
                'name' => [
                    'key' => 'name',
                    'type' => 'text',
                    'inputType' => 'text',
                ],
            ],
            'layout' => [
                [['key' => 'name', 'size' => 12, 'sizeXS' => 12]],
            ],
        ]);

    $this
        ->postJson(
            route('code16.sharp.api.show.command.instance', ['person', 'single_instance_with_init_data']),
            ['data' => ['name' => '']]
        )
        ->assertJsonValidationErrors(['name']);
});