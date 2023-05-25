<?php

namespace Code16\Sharp\Tests\Unit\EntityList;

use Code16\Sharp\EntityList\Commands\EntityCommand;
use Code16\Sharp\EntityList\Commands\InstanceCommand;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Form\Layout\FormLayoutColumn;
use Code16\Sharp\Tests\SharpTestCase;
use Code16\Sharp\Tests\Unit\EntityList\Utils\SharpEntityDefaultTestList;
use Code16\Sharp\Utils\Fields\FieldsContainer;

class SharpEntityListCommandTest extends SharpTestCase
{
    /** @test */
    public function we_can_get_list_commands_config_with_an_instance()
    {
        $list = new class extends SharpEntityDefaultTestList
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

        $this->assertEquals(
            [
                'key' => 'entityCommand',
                'label' => 'My Entity Command',
                'type' => 'entity',
                'authorization' => true,
                'description' => null,
                'instance_selection' => 'none',
                'confirmation' => null,
                'modal_title' => null,
                'modal_confirm_label' => null,
                'has_form' => false,
            ],
            $list->listConfig()['commands']['entity'][0][0]
        );

        $this->assertEquals(
            [
                'key' => 'instanceCommand',
                'label' => 'My Instance Command',
                'type' => 'instance',
                'authorization' => [],
                'description' => null,
                'instance_selection' => null,
                'confirmation' => null,
                'modal_title' => null,
                'modal_confirm_label' => null,
                'has_form' => false,
            ],
            $list->listConfig()['commands']['instance'][0][0]
        );
    }

    /** @test */
    public function we_can_get_list_entity_command_config_with_a_class()
    {
        $list = new class extends SharpEntityDefaultTestList
        {
            public function getEntityCommands(): ?array
            {
                return [
                    'entityCommand' => SharpEntityListCommandTestCommand::class,
                ];
            }
        };

        $list->buildListConfig();

        $this->assertEquals('entityCommand', $list->listConfig()['commands']['entity'][0][0]['key']);
    }

    /** @test */
    public function we_can_ask_for_a_confirmation_on_a_command()
    {
        $list = new class extends SharpEntityDefaultTestList
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

        $this->assertEquals('Sure?', $list->listConfig()['commands']['entity'][0][0]['confirmation']);
    }

    /** @test */
    public function we_can_declare_instance_selection_mode_on_a_command()
    {
        $list = new class extends SharpEntityDefaultTestList
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

        $this->assertEquals('required', $list->listConfig()['commands']['entity'][0][0]['instance_selection']);
        $this->assertEquals('allowed', $list->listConfig()['commands']['entity'][0][1]['instance_selection']);
        $this->assertEquals('none', $list->listConfig()['commands']['entity'][0][2]['instance_selection']);
    }

    /** @test */
    public function we_can_define_that_a_command_has_a_form()
    {
        $list = new class extends SharpEntityDefaultTestList
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

                        public function buildFormFields(FieldsContainer $formFields): void
                        {
                            $formFields->addField(SharpFormTextField::make('message'));
                        }

                        public function buildFormLayout(FormLayoutColumn &$column): void
                        {
                            $column->withSingleField('message');
                        }

                        public function execute(array $data = []): array
                        {
                        }
                    },
                ];
            }
        };

        $list->buildListConfig();

        $this->assertTrue($list->listConfig()['commands']['entity'][0][0]['has_form']);
    }

    /** @test */
    public function we_can_define_a_form_modal_title_on_a_command()
    {
        $list = new class extends SharpEntityDefaultTestList
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

        $this->assertEquals('My title', $list->listConfig()['commands']['entity'][0][0]['modal_title']);
    }

    /** @test */
    public function we_can_handle_authorization_in_an_entity_command()
    {
        $list = new class extends SharpEntityDefaultTestList
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

        $this->assertFalse($list->listConfig()['commands']['entity'][0][0]['authorization']);
    }

    /** @test */
    public function we_can_handle_authorization_in_an_instance_command()
    {
        $list = new class extends SharpEntityDefaultTestList
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
        };

        $list->buildListConfig();
        $list->data([
            ['id' => 1], ['id' => 2], ['id' => 3],
            ['id' => 4], ['id' => 5], ['id' => 6],
        ]);

        $this->assertEquals([1, 2], $list->listConfig()['commands']['instance'][0][0]['authorization']);
    }

    /** @test */
    public function we_can_define_a_description_on_a_command()
    {
        $list = new class extends SharpEntityDefaultTestList
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

        $this->assertEquals('My Entity Command description', $list->listConfig()['commands']['entity'][0][0]['description']);
    }

    /** @test */
    public function we_can_define_separators_in_instance_commands()
    {
        $list = new class extends SharpEntityDefaultTestList
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

        $this->assertEquals('command-1', $list->listConfig()['commands']['instance'][0][0]['key']);
        $this->assertEquals('command-2', $list->listConfig()['commands']['instance'][0][1]['key']);
        $this->assertEquals('command-3', $list->listConfig()['commands']['instance'][1][0]['key']);
    }

    /** @test */
    public function we_can_define_separators_in_entity_commands()
    {
        $list = new class extends SharpEntityDefaultTestList
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

        $this->assertEquals('command-1', $list->listConfig()['commands']['entity'][0][0]['key']);
        $this->assertEquals('command-2', $list->listConfig()['commands']['entity'][1][0]['key']);
        $this->assertEquals('command-3', $list->listConfig()['commands']['entity'][1][1]['key']);
    }

    /** @test */
    public function we_can_declare_an_entity_command_as_primary()
    {
        $list = new class extends SharpEntityDefaultTestList
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

        $this->assertEquals('primary-entity', $list->listConfig()['commands']['entity'][0][1]['key']);
        $this->assertTrue($list->listConfig()['commands']['entity'][0][1]['primary']);
    }
}

class SharpEntityListCommandTestCommand extends EntityCommand
{
    public function label(): string
    {
        return 'My Entity Command';
    }

    public function execute(array $data = []): array
    {
    }
}
