<?php

namespace Code16\Sharp\Tests\Unit\Dashboard;

use Code16\Sharp\Dashboard\Commands\DashboardCommand;
use Code16\Sharp\Dashboard\DashboardQueryParams;
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

        $this->assertArraySubset([
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

    // Note: all other command test already are in SharpEntityListCommandTest
}