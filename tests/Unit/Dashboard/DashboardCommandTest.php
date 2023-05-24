<?php

namespace Code16\Sharp\Tests\Unit\Dashboard;

use Code16\Sharp\Dashboard\Commands\DashboardCommand;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Tests\SharpTestCase;
use Code16\Sharp\Tests\Unit\Dashboard\Fakes\FakeSharpDashboard;
use Code16\Sharp\Utils\Fields\FieldsContainer;

class DashboardCommandTest extends SharpTestCase
{
    /** @test */
    public function we_can_get_list_commands_config_of_a_dashboard()
    {
        $dashboard = new class extends FakeSharpDashboard
        {
            public function getDashboardCommands(): ?array
            {
                return [
                    'dashboardCommand' => new class extends DashboardCommand
                    {
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
        $this->assertEquals(
            [
                'key' => 'dashboardCommand',
                'label' => 'My Dashboard Command',
                'type' => 'dashboard',
                'authorization' => true,
                'description' => null,
                'confirmation' => null,
                'modal_title' => null,
                'modal_confirm_label' => null,
                'has_form' => false,
            ],
            $dashboard->dashboardConfig()['commands']['dashboard'][0][0]
        );
    }

    /** @test */
    public function we_can_get_list_section_placed_commands_config_of_a_dashboard()
    {
        $dashboard = new class extends FakeSharpDashboard
        {
            public function getDashboardCommands(): ?array
            {
                return [
                    'section-1' => [
                        'dashboardSectionCommand' => new class extends DashboardCommand
                        {
                            public function label(): string
                            {
                                return 'My Dashboard Command';
                            }
    
                            public function execute(array $data = []): array
                            {
                            }
                        },
                    ],
                    'dashboardCommand' => new class extends DashboardCommand
                    {
                        public function label(): string
                        {
                            return 'Another Dashboard Command';
                        }

                        public function execute(array $data = []): array
                        {
                        }
                    },
                ];
            }
        };
        
        $dashboard->buildDashboardConfig();
        $this->assertEquals(
            'dashboardSectionCommand',
            $dashboard->dashboardConfig()['commands']['section-1'][0][0]['key']
        );
        $this->assertEquals(
            'dashboardCommand',
            $dashboard->dashboardConfig()['commands']['dashboard'][0][0]['key']
        );
    }

    /** @test */
    public function we_can_define_that_the_dashboard_command_has_a_form()
    {
        $dashboard = new class extends FakeSharpDashboard
        {
            public function getDashboardCommands(): ?array
            {
                return [
                    'dashboardCommand' => new class extends DashboardCommand
                    {
                        public function label(): string
                        {
                            return 'My Dashboard Command';
                        }

                        public function buildFormFields(FieldsContainer $formFields): void
                        {
                            $formFields->addField(SharpFormTextField::make('message'));
                        }

                        public function execute(array $data = []): array
                        {
                        }
                    },
                ];
            }
        };

        $dashboard->buildDashboardConfig();

        $this->assertArraySubset(
            [
                'commands' => [
                    'dashboard' => [
                        [
                            [
                                'key' => 'dashboardCommand',
                                'label' => 'My Dashboard Command',
                                'type' => 'dashboard',
                                'has_form' => true,
                            ],
                        ],
                    ],
                ],
            ],
            $dashboard->dashboardConfig(),
        );
    }

    // Note: all other command test already are in SharpEntityListCommandTest
}
