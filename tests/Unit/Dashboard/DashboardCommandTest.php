<?php

namespace Code16\Sharp\Tests\Unit\Dashboard;

use Code16\Sharp\Dashboard\Commands\DashboardCommand;
use Code16\Sharp\Dashboard\DashboardQueryParams;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Form\Layout\FormLayoutColumn;
use Code16\Sharp\Tests\SharpTestCase;

class DashboardCommandTest extends SharpTestCase
{

    /** @test */
    function we_can_get_list_commands_config_of_a_dashboard()
    {
        $dashboard = new class extends SharpDashboardTestDashboard {
            function buildDashboardConfig()
            {
                $this->addDashboardCommand("dashboardCommand", new class extends DashboardCommand {
                    public function label(): string {
                        return "My Dashboard Command";
                    }
                    public function execute(DashboardQueryParams $params, array $data = []): array {}
                });
            }
        };

        $dashboard->buildDashboardConfig();

        $this->assertArrayContainsSubset([
            "commands" => [
                'dashboard' => [
                    [
                        [
                            "key" => "dashboardCommand",
                            "label" => "My Dashboard Command",
                            "type" => "dashboard",
                            "authorization" => true
                        ]
                    ]
                ]
            ]
        ], $dashboard->dashboardConfig());
    }

    /** @test */
    function we_can_define_a_form_on_a_dashboard_command()
    {
        $list = new class extends SharpDashboardTestDashboard {
            function buildDashboardConfig()
            {
                $this->addDashboardCommand("dashboardCommand", new class extends DashboardCommand {
                    public function label(): string {
                        return "My Dashboard Command";
                    }
                    public function buildFormFields() {
                        $this->addField(SharpFormTextField::make("message"));
                    }
                    public function buildFormLayout(FormLayoutColumn &$column) {
                        $column->withSingleField("message");
                    }
                    public function execute(DashboardQueryParams $params, array $data = []): array {}
                });
            }
        };

        $list->buildDashboardConfig();

        $this->assertArrayContainsSubset([
            "commands" => [
                "dashboard" => [
                    [
                        [
                            "key" => "dashboardCommand",
                            "label" => "My Dashboard Command",
                            "type" => "dashboard",
                            "form" => [
                                "fields" => [
                                    "message" => [
                                        "key" => "message",
                                        "type" => "text",
                                        "inputType" => "text"
                                    ]
                                ],
                                "layout" => [
                                    [["key" => "message", "size" => 12, "sizeXS" => 12]]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ], $list->dashboardConfig());
    }

    // Note: all other command test already are in SharpEntityListCommandTest
}