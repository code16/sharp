<?php

namespace Code16\Sharp\Tests\Unit\Show;

use Code16\Sharp\EntityList\Commands\InstanceCommand;
use Code16\Sharp\Tests\SharpTestCase;
use Code16\Sharp\Tests\Unit\Show\Utils\BaseSharpShowDefaultTest;

class SharpShowCommandTest extends SharpTestCase
{
    /** @test */
    public function we_can_configure_show_instance_command_in_sections()
    {
        $show = new class extends BaseSharpShowDefaultTest
        {
            public function getInstanceCommands(): ?array
            {
                return [
                    SharpShowCommandTestCommand::class,
                    'my-section' => [
                        SharpShowCommandTestCommand2::class,
                    ],
                ];
            }
        };

        $show->buildShowConfig();

        $this->assertArraySubset(
            [
                'commands' => [
                    'instance' => [
                        [
                            [
                                'key' => class_basename(SharpShowCommandTestCommand::class),
                                'label' => 'test',
                                'type' => 'instance',
                            ],
                        ],
                    ],
                    'my-section' => [
                        [
                            [
                                'key' => class_basename(SharpShowCommandTestCommand2::class),
                                'label' => 'test-2',
                                'type' => 'instance',
                            ],
                        ],
                    ],
                ],
            ],
            $show->showConfig(1),
        );
    }

    /** @test */
    public function we_can_configure_show_instance_command_in_sections_with_custom_keys()
    {
        $show = new class extends BaseSharpShowDefaultTest
        {
            public function getInstanceCommands(): ?array
            {
                return [
                    'my-command' => SharpShowCommandTestCommand::class,
                    'my-section' => [
                        'my-command-2' => SharpShowCommandTestCommand2::class,
                    ],
                ];
            }
        };

        $show->buildShowConfig();

        $this->assertArraySubset(
            [
                'commands' => [
                    'instance' => [
                        [
                            [
                                'key' => 'my-command',
                                'label' => 'test',
                                'type' => 'instance',
                            ],
                        ],
                    ],
                    'my-section' => [
                        [
                            [
                                'key' => 'my-command-2',
                                'label' => 'test-2',
                                'type' => 'instance',
                            ],
                        ],
                    ],
                ],
            ],
            $show->showConfig(1),
        );
    }
}

class SharpShowCommandTestCommand extends InstanceCommand
{
    public function label(): ?string
    {
        return 'test';
    }

    public function execute(mixed $instanceId, array $data = []): array
    {
        return [];
    }
}

class SharpShowCommandTestCommand2 extends InstanceCommand
{
    public function label(): ?string
    {
        return 'test-2';
    }

    public function execute(mixed $instanceId, array $data = []): array
    {
        return [];
    }
}
