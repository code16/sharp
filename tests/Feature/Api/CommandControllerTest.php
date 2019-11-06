<?php

namespace Code16\Sharp\Tests\Feature\Api;

use Code16\Sharp\Dashboard\Commands\DashboardCommand;
use Code16\Sharp\Dashboard\DashboardQueryParams;
use Code16\Sharp\EntityList\Commands\EntityCommand;
use Code16\Sharp\EntityList\Commands\InstanceCommand;
use Code16\Sharp\EntityList\EntityListQueryParams;
use Code16\Sharp\Exceptions\Form\SharpApplicativeException;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Tests\Fixtures\PersonSharpEntityList;
use Code16\Sharp\Tests\Fixtures\SharpDashboard;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CommandControllerTest extends BaseApiTest
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->login();
    }

    /** @test */
    public function we_can_call_an_info_entity_command()
    {
        $this->buildTheWorld();
        $this->withoutExceptionHandling();

        $this->json('post', '/sharp/api/list/person/command/entity_info')
            ->assertStatus(200)
            ->assertJson([
                "action" => "info",
                "message" => "ok",
            ]);
    }

    /** @test */
    public function we_can_call_an_info_instance_command()
    {
        $this->buildTheWorld();
        $this->withoutExceptionHandling();

        $this->json('post', '/sharp/api/list/person/command/instance_info/1')
            ->assertStatus(200)
            ->assertJson([
                "action" => "info",
                "message" => "ok",
            ]);
    }

    /** @test */
    public function we_can_call_a_link_entity_command()
    {
        $this->buildTheWorld();
        $this->withoutExceptionHandling();

        $this->json('post', '/sharp/api/list/person/command/instance_link/1')
            ->assertStatus(200)
            ->assertJson([
                "action" => "link",
                "link"   => "/link/out"
            ]);
    }

    /** @test */
    public function we_can_call_a_reload_entity_command()
    {
        $this->buildTheWorld();
        $this->withoutExceptionHandling();

        $this->json('post', '/sharp/api/list/person/command/entity_reload')
            ->assertStatus(200)
            ->assertJson([
                "action" => "reload"
            ]);
    }

    /** @test */
    public function we_can_call_a_view_entity_command()
    {
        $this->buildTheWorld();
        $this->withoutExceptionHandling();

        $this->json('post', '/sharp/api/list/person/command/entity_view')
            ->assertStatus(200)
            ->assertJson([
                "action" => "view",
            ]);
    }

    /** @test */
    public function we_can_call_a_refresh_entity_command()
    {
        $this->buildTheWorld();
        $this->withoutExceptionHandling();

        $this->json('post', '/sharp/api/list/person/command/entity_refresh')
            ->assertStatus(200)
            ->assertJson([
                "action" => "refresh",
                "items" => [
                    [
                        "id" => 1,
                        "name" => "John <b>Wayne</b>",
                        "age" => 22
                    ], [
                        "id" => 2,
                        "name" => "Mary <b>Wayne</b>",
                        "age" => 26
                    ]
                ]
            ]);
    }

    /** @test */
    public function we_can_call_a_refresh_instance_command()
    {
        $this->buildTheWorld();
        $this->withoutExceptionHandling();

        $json = $this->json('post', '/sharp/api/list/person/command/instance_refresh/1')
            ->assertStatus(200)
            ->assertJson([
                "action" => "refresh",
                "items" => [
                    [
                        "id" => 1,
                        "name" => "John <b>Wayne</b>",
                        "age" => 22
                    ]
                ]
            ])->decodeResponseJson();

        $this->assertCount(1, $json["items"]);
    }

    /** @test */
    public function we_can_call_a_form_entity_command()
    {
        $this->buildTheWorld();
        $this->withoutExceptionHandling();

        $this->json('post', '/sharp/api/list/person/command/entity_form', [
            "data" => ["name" => "John"]
        ])->assertStatus(200);
    }

    /** @test */
    public function we_can_call_a_download_entity_command()
    {
        $this->buildTheWorld();
        $this->withoutExceptionHandling();

        $response = $this->json('post', '/sharp/api/list/person/command/entity_download')
            ->assertStatus(200)
            ->assertHeader("content-type", "application/pdf");

        $this->assertTrue(
            Str::contains(
                $response->headers->get("content-disposition"), "account.pdf"
            )
        );

        $this->json('post', '/sharp/api/list/person/command/entity_download_no_disk')
            ->assertStatus(200);
    }

    /** @test */
    public function applicative_exception_returns_a_417_as_always()
    {
        $this->buildTheWorld();

        $this->json('post', '/sharp/api/list/person/command/entity_exception')
            ->assertStatus(417)
            ->assertJson([
                "message" => "error"
            ]);
    }

    /** @test */
    public function we_get_a_422_when_posting_invalid_data_for_a_command_with_a_form()
    {
        $this->buildTheWorld();

        $this->json('post', '/sharp/api/list/person/command/entity_form')
            ->assertStatus(422)
            ->assertJson([
                "errors" => [
                    "name" => [
                        "The name field is required."
                    ]
                ]
            ]);
    }

    /** @test */
    public function we_get_the_full_query_in_an_entity_command()
    {
        $this->buildTheWorld();
        $this->withoutExceptionHandling();

        $this->json('post', '/sharp/api/list/person/command/entity_params', [
            "query" => ["sort" => "name", "dir" => "desc"]
        ])
            ->assertStatus(200)
            ->assertJson([
                "action" => "info",
                "message" => "namedesc"
            ]);
    }

    /** @test */
    public function we_cant_call_an_unauthorized_entity_command()
    {
        $this->buildTheWorld();

        $this->json('post', '/sharp/api/list/person/command/entity_unauthorized')
            ->assertStatus(403);
    }

    /** @test */
    public function we_cant_call_an_unauthorized_instance_command()
    {
        $this->buildTheWorld();

        $this->json('post', '/sharp/api/list/person/command/instance_unauthorized_odd_id/1')
            ->assertStatus(403);

        $this->json('post', '/sharp/api/list/person/command/instance_unauthorized_odd_id/2')
            ->assertStatus(200);
    }

    /** @test */
    public function we_can_initialize_form_data_in_an_entity_command()
    {
        $this->buildTheWorld();
        $this->withoutExceptionHandling();

        $response = $this->getJson('/sharp/api/list/person')
            ->assertStatus(200)
            ->json();

        $this->assertTrue(
            collect($response['config']['commands']['entity'][0])->where("key", "entity_with_init_data")->first()['fetch_initial_data']
        );

        $this->getJson('/sharp/api/list/person/command/entity_with_init_data/data')
            ->assertStatus(200)
            ->assertExactJson([
                "data" => [
                    "name" => "John Wayne"
                ]
            ]);
    }

    /** @test */
    public function we_can_initialize_form_data_in_an_instance_command()
    {
        $this->buildTheWorld();
        $this->withoutExceptionHandling();

        $response = $this->getJson('/sharp/api/list/person')
            ->assertStatus(200)
            ->json();

        $this->assertTrue(
            collect($response['config']['commands']['instance'][0])->where("key", "instance_with_init_data")->first()['fetch_initial_data']
        );

        $this->getJson('/sharp/api/list/person/command/instance_with_init_data/25/data')
            ->assertStatus(200)
            ->assertExactJson([
                "data" => [
                    "name" => "John Wayne [25]"
                ]
            ]);
    }

    /** @test */
    public function we_can_call_an_info_dashboard_command()
    {
        $this->buildTheWorldForDashboard();

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
        $this->buildTheWorldForDashboard();

        $this->getJson('/sharp/api/dashboard/my_dashboard/command/dashboard_form/data')
            ->assertStatus(200)
            ->assertJson([
                "data" => [
                    "name" => "John Wayne"
                ]
            ]);
    }

    protected function buildTheWorld()
    {
        parent::buildTheWorld();

        $this->app['config']->set(
            'sharp.entities.person.list',
            EntityCommandPersonSharpEntityList::class
        );
    }

    protected function buildTheWorldForDashboard()
    {
        parent::buildTheWorld();

        $this->app['config']->set(
            'sharp.dashboards.my_dashboard.view',
            CommandControllerTestDashboardView::class
        );
    }
}

class EntityCommandPersonSharpEntityList extends PersonSharpEntityList {

    function buildListConfig()
    {
        $this->addEntityCommand("entity_info", new class() extends EntityCommand {
            public function label(): string { return "label"; }
            public function execute(EntityListQueryParams $params, array $data = []): array {
                return $this->info("ok");
            }

        })->addInstanceCommand("instance_info", new class() extends InstanceCommand {
            public function label(): string { return "label"; }
            public function execute($instanceId, array $params = []): array {
                return $this->info("ok");
            }

        })->addEntityCommand("entity_reload", new class() extends EntityCommand {
            public function label(): string { return "label"; }
            public function execute(EntityListQueryParams $params, array $data = []): array {
                return $this->reload();
            }

        })->addEntityCommand("entity_view", new class() extends EntityCommand {
            public function label(): string { return "label"; }
            public function execute(EntityListQueryParams $params, array $data = []): array {
                return $this->view("welcome");
            }

        })->addEntityCommand("entity_refresh", new class() extends EntityCommand {
            public function label(): string { return "label"; }
            public function execute(EntityListQueryParams $params, array $data = []): array {
                return $this->refresh([1, 2]);
            }

        })->addInstanceCommand("instance_refresh", new class() extends InstanceCommand {
            public function label(): string { return "label"; }
            public function execute($instanceId, array $params = []): array {
                return $this->refresh(1);
            }

        })->addInstanceCommand("instance_link", new class() extends InstanceCommand {
            public function label(): string { return "label"; }
            public function execute($instanceId, array $params = []): array {
                return $this->link('/link/out');
            }

        })->addEntityCommand("entity_exception", new class() extends EntityCommand {
            public function label(): string { return "label"; }
            public function execute(EntityListQueryParams $params, array $data = []): array {
                throw new SharpApplicativeException("error");
            }

        })->addEntityCommand("entity_form", new class() extends EntityCommand {
            public function label(): string { return "label"; }
            public function buildFormFields() {
                $this->addField(SharpFormTextField::make("name"));
            }
            public function execute(EntityListQueryParams $params, array $data = []): array {
                $this->validate($data, ["name"=>"required"]);
                return $this->reload();
            }

        })->addEntityCommand("entity_download", new class() extends EntityCommand {
            public function label(): string { return "label"; }
            public function execute(EntityListQueryParams $params, array $data = []): array {
                Storage::fake('files');
                UploadedFile::fake()->create('account.pdf', 100)->storeAs('pdf', 'account.pdf', ['disk'=>'files']);
                return $this->download("pdf/account.pdf", "account.pdf", "files");
            }

        })->addEntityCommand("entity_download_no_disk", new class() extends EntityCommand {
            public function label(): string { return "label"; }
            public function execute(EntityListQueryParams $params, array $data = []): array {
                Storage::fake('local');
                UploadedFile::fake()->create('account.pdf', 100)->storeAs('pdf', 'account.pdf');
                return $this->download("pdf/account.pdf");
            }

        })->addEntityCommand("entity_unauthorized", new class() extends EntityCommand {
            public function label(): string { return "label"; }
            public function authorize(): bool { return false; }
            public function execute(EntityListQueryParams $params, array $data = []): array {
                return $this->reload();
            }

        })->addInstanceCommand("instance_unauthorized_odd_id", new class() extends InstanceCommand {
            public function label(): string { return "label"; }
            public function authorizeFor($instanceId): bool { return $instanceId%2==0; }
            public function execute($instanceId, array $params = []): array {
                return $this->reload();
            }

        })->addEntityCommand("entity_params", new class() extends EntityCommand
        {
            public function label(): string { return "label"; }
            public function execute(EntityListQueryParams $params, array $data = []): array
            {
                return $this->info($params->sortedBy() . $params->sortedDir());
            }
        })->addEntityCommand("entity_with_init_data", new class() extends EntityCommand {
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
            public function execute(EntityListQueryParams $params, array $data = []): array {}

        })->addInstanceCommand("instance_with_init_data", new class() extends InstanceCommand {
            public function label(): string { return "label"; }
            public function buildFormFields() {
                $this->addField(SharpFormTextField::make("name"));
            }
            protected function initialData($instanceId): array
            {
                return [
                    "name" => "John Wayne [$instanceId]",
                    "age" => 32
                ];
            }
            public function execute($instanceId, array $data = []): array {}
        });
    }
}

class CommandControllerTestDashboardView extends SharpDashboard
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