<?php

namespace Code16\Sharp\Tests\Unit\Dashboard;

use Code16\Sharp\Dashboard\Commands\DashboardCommand;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Form\Layout\FormLayoutColumn;
use Code16\Sharp\Tests\SharpTestCase;
use Code16\Sharp\Tests\Unit\Dashboard\Fakes\FakeSharpDashboard;
use Code16\Sharp\Utils\Fields\FieldsContainer;

class DashboardCommandTest extends SharpTestCase
{
    /** @test */
    public function we_can_get_list_commands_config_of_a_dashboard()
    {
        $dashboard = new class() extends FakeSharpDashboard {
            public function getDashboardCommands(): ?array
            {
                return [
                    'dashboardCommand' => new class() extends DashboardCommand {
                        public function label(): string
                        {
                            return 'My Dashboard Command';
                        }

                        public function execute(array $data = []): array
                        {
                        }
                    },
                ];
            }
        };

        $dashboard->buildDashboardConfig();

        $this->assertArraySubset([
            'commands' => [
                'dashboard' => [
                    [
                        [
                            'key'           => 'dashboardCommand',
                            'label'         => 'My Dashboard Command',
                            'type'          => 'dashboard',
                            'authorization' => true,
                        ],
                    ],
                ],
            ],
        ], $dashboard->dashboardConfig());
    }

    /** @test */
    public function we_can_define_a_form_on_a_dashboard_command()
    {
        $list = new class() extends FakeSharpDashboard {
            public function getDashboardCommands(): ?array
            {
                return [
                    'dashboardCommand' => new class() extends DashboardCommand {
                        public function label(): string
                        {
                            return 'My Dashboard Command';
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

        $list->buildDashboardConfig();

        $this->assertArraySubset([
            'commands' => [
                'dashboard' => [
                    [
                        [
                            'key'   => 'dashboardCommand',
                            'label' => 'My Dashboard Command',
                            'type'  => 'dashboard',
                            'form'  => [
                                'fields' => [
                                    'message' => [
                                        'key'       => 'message',
                                        'type'      => 'text',
                                        'inputType' => 'text',
                                    ],
                                ],
                                'layout' => [
                                    [['key' => 'message', 'size' => 12, 'sizeXS' => 12]],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ], $list->dashboardConfig());
    }

    // Note: all other command test already are in SharpEntityListCommandTest
}
