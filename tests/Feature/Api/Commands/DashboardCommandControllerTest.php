<?php

namespace Code16\Sharp\Tests\Feature\Api\Commands;

use Code16\Sharp\Dashboard\Commands\DashboardCommand;
use Code16\Sharp\Dashboard\DashboardQueryParams;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Tests\Feature\Api\BaseApiTest;
use Code16\Sharp\Tests\Fixtures\SharpDashboard;

class DashboardCommandControllerTest extends BaseApiTest
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->login();
    }

    /** @test */
    public function we_can_call_an_info_dashboard_command()
    {
        $this->buildTheWorld();

        $this->json('post', '/sharp/api/dashboard/my_dashboard/command/dashboard_info')
            ->assertStatus(200)
            ->assertJson([
                "action" => "info",
                "message" => "ok",
            ]);
    }

    /** @test */
    public function we_can_initialize_form_data_in_an_dashboard_command()
    {
        $this->buildTheWorld();

        $this->getJson('/sharp/api/dashboard/my_dashboard/command/dashboard_form/data')
            ->assertStatus(200)
            ->assertJson([
                "data" => [
                    "name" => "John Wayne"
                ]
            ]);
    }

    protected function buildTheWorld($singleShow = false)
    {
        parent::buildTheWorld(false);

        $this->app['config']->set(
            'sharp.dashboards.my_dashboard.view',
            EntityCommandTestSharpDashboard::class
        );
    }
}

class EntityCommandTestSharpDashboard extends SharpDashboard
{
    function buildDashboardConfig()
    {
        $this
            ->addDashboardCommand("dashboard_info", new class() extends DashboardCommand {
                public function label(): string { return "label"; }
                public function execute(DashboardQueryParams $params, array $data= []): array {
                    return $this->info("ok");
                }
            })
            ->addDashboardCommand("dashboard_form", new class() extends DashboardCommand {
                public function label(): string { return "label"; }
                public function buildFormFields() {
                    $this->addField(SharpFormTextField::make("name"));
                }
                protected function initialData(): array
                {
                    return [
                        "name" => "John Wayne",
                        "age" => 32
                    ];
                }
                public function execute(DashboardQueryParams $params, array $data = []): array {}
            });
    }
}