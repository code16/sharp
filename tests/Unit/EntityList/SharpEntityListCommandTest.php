<?php

use Code16\Sharp\EntityList\Commands\EntityCommand;
use Code16\Sharp\EntityList\Commands\InstanceCommand;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Tests\Unit\EntityList\Fakes\FakeSharpEntityList;

it('returns commands config', function () {
    $list = new class extends FakeSharpEntityList
    {
        public function getEntityCommands(): ?array
        {
            return [
                'entityCommand' => new class extends EntityCommand
                {
                    public function label(): string
                    {
                        return 'My Entity Command';
                    }

                    public function execute(array $data = []): array
                    {
                    }
                },
            ];
        }

        public function getInstanceCommands(): ?array
        {
            return [
                'instanceCommand' => new class extends InstanceCommand
                {
                    public function label(): string
                    {
                        return 'My Instance Command';
                    }

                    public function execute($instanceId, array $data = []): array
                    {
                    }
                },
            ];
        }
    };

    $list->buildListConfig();

    expect($list->listConfig()['commands'])->toEqual([
        'entity' => [
            [
                [
                    'key' => 'entityCommand',
                    'label' => 'My Entity Command',
                    'type' => 'entity',
                    'authorization' => true,
                    'description' => null,
                    'instance_selection' => null,
                    'confirmation' => null,
                    'modal_title' => null,
                    'modal_confirm_label' => null,
                    'has_form' => false,
                ],
            ],
        ],
        'instance' => [
            [
                [
                    'key' => 'instanceCommand',
                    'label' => 'My Instance Command',
                    'type' => 'instance',
                    'authorization' => [],
                    'description' => null,
                    'confirmation' => null,
                    'modal_title' => null,
                    'modal_confirm_label' => null,
                    'has_form' => false,
                ],
            ],
        ],
    ]);
});

it('handles confirmation on a command', function () {
    $list = new class extends FakeSharpEntityList
    {
        public function getEntityCommands(): ?array
        {
            return [
                'entityCommand' => new class extends EntityCommand
                {
                    public function label(): string
                    {
                        return 'My Entity Command';
                    }

                    public function buildCommandConfig(): void
                    {
                        $this->configureConfirmationText('Sure?');
                    }

                    public function execute(array $data = []): array
                    {
                    }
                },
            ];
        }
    };

    $list->buildListConfig();

    expect($list->listConfig()['commands']['entity'][0][0]['confirmation'])->toEqual('Sure?');
});

it('allows to declare instance selection mode on a command', function () {
    $list = new class extends FakeSharpEntityList
    {
        public function getEntityCommands(): ?array
        {
            return [
                'command_required' => new class extends EntityCommand
                {
                    public function label(): string
                    {
                        return 'My Entity Command';
                    }

                    public function buildCommandConfig(): void
                    {
                        $this->configureInstanceSelectionRequired();
                    }

                    public function execute(array $data = []): array
                    {
                    }
                },
                'command_allowed' => new class extends EntityCommand
                {
                    public function label(): string
                    {
                        return 'My Entity Command';
                    }

                    public function buildCommandConfig(): void
                    {
                        $this->configureInstanceSelectionAllowed();
                    }

                    public function execute(array $data = []): array
                    {
                    }
                },
                'command_none' => new class extends EntityCommand
                {
                    public function label(): string
                    {
                        return 'My Entity Command';
                    }

                    public function buildCommandConfig(): void
                    {
                        $this->configureInstanceSelectionNone();
                    }

                    public function execute(array $data = []): array
                    {
                    }
                },
            ];
        }
    };

    $list->buildListConfig();

    expect($list->listConfig()['commands']['entity'][0][0]['instance_selection'])->toEqual('required')
        ->and($list->listConfig()['commands']['entity'][0][1]['instance_selection'])->toEqual('allowed')
        ->and($list->listConfig()['commands']['entity'][0][2]['instance_selection'])->toBeNull();
});

it('allows to define a form to a command', function () {
    $list = new class extends FakeSharpEntityList
    {
        public function getEntityCommands(): ?array
        {
            return [
                'entityCommand' => new class extends EntityCommand
                {
                    public function label(): string
                    {
                        return 'My Entity Command';
                    }

                    public function buildFormFields($formFields): void
                    {
                        $formFields->addField(SharpFormTextField::make('message'));
                    }

                    public function buildFormLayout(&$column): void
                    {
                        $column->withField('message');
                    }

                    public function execute(array $data = []): array
                    {
                    }
                },
            ];
        }
    };

    $list->buildListConfig();

    expect($list->listConfig()['commands']['entity'][0][0]['has_form'])->toBeTrue();
});

it('allows to define a form modal title on a command', function () {
    $list = new class extends FakeSharpEntityList
    {
        public function getEntityCommands(): ?array
        {
            return [
                'entityCommand' => new class extends EntityCommand
                {
                    public function label(): string
                    {
                        return 'My Entity Command';
                    }

                    public function buildCommandConfig(): void
                    {
                        $this->configureFormModalTitle('My title');
                    }

                    public function execute(array $data = []): array
                    {
                    }
                },
            ];
        }
    };

    $list->buildListConfig();

    expect($list->listConfig()['commands']['entity'][0][0]['modal_title'])->toEqual('My title');
});

it('handles authorization in an entity command', function () {
    $list = new class extends FakeSharpEntityList
    {
        public function getEntityCommands(): ?array
        {
            return [
                'entityCommand' => new class extends EntityCommand
                {
                    public function label(): string
                    {
                        return 'My Entity Command';
                    }

                    public function authorize(): bool
                    {
                        return false;
                    }

                    public function execute(array $data = []): array
                    {
                    }
                },
            ];
        }
    };

    $list->buildListConfig();

    expect($list->listConfig()['commands']['entity'][0][0]['authorization'])->toBeFalse();
});

it('handles authorization in an instance command', function () {
    $list = new class extends FakeSharpEntityList
    {
        public function getInstanceCommands(): ?array
        {
            return [
                'command' => new class extends InstanceCommand
                {
                    public function label(): string
                    {
                        return 'My Instance Command';
                    }

                    public function authorizeFor($instanceId): bool
                    {
                        return $instanceId < 3;
                    }

                    public function execute($instanceId, array $data = []): array
                    {
                    }
                },
            ];
        }

        public function getListData(): array|\Illuminate\Contracts\Support\Arrayable
        {
            return [
                ['id' => 1], ['id' => 2], ['id' => 3],
                ['id' => 4], ['id' => 5], ['id' => 6],
            ];
        }
    };

    // We need to call data() to trigger the authorization check
    $list->data();

    expect($list->listConfig()['commands']['instance'][0][0]['authorization'])->toEqual([1, 2]);
});

it('allows to define a description on a command', function () {
    $list = new class extends FakeSharpEntityList
    {
        public function getEntityCommands(): ?array
        {
            return [
                'entityCommand' => new class extends EntityCommand
                {
                    public function label(): string
                    {
                        return 'My Entity Command';
                    }

                    public function buildCommandConfig(): void
                    {
                        $this->configureDescription('My Entity Command description');
                    }

                    public function execute(array $data = []): array
                    {
                    }
                },
            ];
        }
    };

    $list->buildListConfig();

    expect($list->listConfig()['commands']['entity'][0][0]['description'])->toEqual('My Entity Command description');
});

it('allows to define separators in instance commands', function () {
    $list = new class extends FakeSharpEntityList
    {
        public function getInstanceCommands(): ?array
        {
            return [
                'command-1' => new class extends InstanceCommand
                {
                    public function label(): string
                    {
                        return '';
                    }

                    public function execute($instanceId, array $data = []): array
                    {
                    }
                },
                'command-2' => new class extends InstanceCommand
                {
                    public function label(): string
                    {
                        return '';
                    }

                    public function execute($instanceId, array $data = []): array
                    {
                    }
                },
                '---',
                'command-3' => new class extends InstanceCommand
                {
                    public function label(): string
                    {
                        return '';
                    }

                    public function execute($instanceId, array $data = []): array
                    {
                    }
                },
            ];
        }
    };

    $list->buildListConfig();
    
    expect($list->listConfig()['commands']['instance'])->toHaveCount(2)
        ->and($list->listConfig()['commands']['instance'][0][0]['key'])->toEqual('command-1')
        ->and($list->listConfig()['commands']['instance'][0][1]['key'])->toEqual('command-2')
        ->and($list->listConfig()['commands']['instance'][1][0]['key'])->toEqual('command-3');
});

it('allows to define separators in entity commands', function () {
    $list = new class extends FakeSharpEntityList
    {
        public function getEntityCommands(): ?array
        {
            return [
                'command-1' => new class extends EntityCommand
                {
                    public function label(): string
                    {
                        return '';
                    }

                    public function execute(array $data = []): array
                    {
                    }
                },
                '---',
                'command-2' => new class extends EntityCommand
                {
                    public function label(): string
                    {
                        return '';
                    }

                    public function execute(array $data = []): array
                    {
                    }
                },
                'command-3' => new class extends EntityCommand
                {
                    public function label(): string
                    {
                        return '';
                    }

                    public function execute(array $data = []): array
                    {
                    }
                },
            ];
        }
    };

    $list->buildListConfig();
    
    expect($list->listConfig()['commands']['entity'])->toHaveCount(2)
        ->and($list->listConfig()['commands']['entity'][0][0]['key'])->toEqual('command-1')
        ->and($list->listConfig()['commands']['entity'][1][0]['key'])->toEqual('command-2')
        ->and($list->listConfig()['commands']['entity'][1][1]['key'])->toEqual('command-3');
});

it('allows to declare an entity command as primary', function () {
    $list = new class extends FakeSharpEntityList
    {
        public function getEntityCommands(): ?array
        {
            return [
                'entity' => new class extends EntityCommand
                {
                    public function label(): string
                    {
                        return 'My Entity Command';
                    }

                    public function execute(array $data = []): array
                    {
                    }
                },
                'primary-entity' => new class extends EntityCommand
                {
                    public function label(): string
                    {
                        return 'My Primary Entity Command';
                    }

                    public function execute(array $data = []): array
                    {
                    }
                },
            ];
        }

        public function buildListConfig(): void
        {
            $this->configurePrimaryEntityCommand('primary-entity');
        }
    };

    $list->buildListConfig();

    expect($list->listConfig()['commands']['entity'][0][1]['key'])->toEqual('primary-entity')
        ->and($list->listConfig()['commands']['entity'][0][1]['primary'])->toBeTrue();
});
