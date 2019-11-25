<?php

namespace Code16\Sharp\Tests\Feature\Api\Commands;

use Code16\Sharp\EntityList\Commands\InstanceCommand;
use Code16\Sharp\EntityList\Commands\SingleInstanceCommand;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Tests\Feature\Api\BaseApiTest;
use Code16\Sharp\Tests\Fixtures\PersonSharpEntityList;
use Code16\Sharp\Tests\Fixtures\PersonSharpShow;
use Code16\Sharp\Tests\Fixtures\PersonSharpSingleShow;

class ShowInstanceCommandControllerTest extends BaseApiTest
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->login();
    }

    /** @test */
    public function we_can_call_an_info_instance_command_from_a_show()
    {
        $this->buildTheWorld();

        $this->postJson('/sharp/api/show/person/command/instance_info/1')
            ->assertStatus(200)
            ->assertJson([
                "action" => "info",
                "message" => "ok",
            ]);

        $this->postJson('/sharp/api/show/person/command/instance_info')
            ->assertStatus(404);
    }

    /** @test */
    public function we_can_call_an_info_instance_command_from_a_single_show()
    {
        $this->buildTheWorld(true);

        $this->postJson('/sharp/api/show/person/command/instance_info')
            ->assertStatus(200)
            ->assertJson([
                "action" => "info",
                "message" => "ok",
            ]);

        $this->postJson('/sharp/api/show/person/command/instance_info/1')
            ->assertStatus(404);
    }
//
//    /** @test */
//    public function we_can_call_a_refresh_instance_command_from_a_show()
//    {
//        $this->buildTheWorld();
//        $this->withoutExceptionHandling();
//
//        $json = $this->json('post', '/sharp/api/list/person/command/instance_refresh/1')
//            ->assertStatus(200)
//            ->assertJson([
//                "action" => "refresh",
//                "items" => [
//                    [
//                        "id" => 1,
//                        "name" => "John <b>Wayne</b>",
//                        "age" => 22
//                    ]
//                ]
//            ])->decodeResponseJson();
//
//        $this->assertCount(1, $json["items"]);
//    }
//
//    /** @test */
//    public function we_cant_call_an_unauthorized_instance_command()
//    {
//        $this->buildTheWorld();
//
//        $this->json('post', '/sharp/api/list/person/command/instance_unauthorized_odd_id/1')
//            ->assertStatus(403);
//
//        $this->json('post', '/sharp/api/list/person/command/instance_unauthorized_odd_id/2')
//            ->assertStatus(200);
//    }
//
//    /** @test */
//    public function we_can_initialize_form_data_in_an_instance_command()
//    {
//        $this->buildTheWorld();
//        $this->withoutExceptionHandling();
//
//        $response = $this->getJson('/sharp/api/list/person')
//            ->assertStatus(200)
//            ->json();
//
//        $this->assertTrue(
//            collect($response['config']['commands']['instance'][0])->where("key", "instance_with_init_data")->first()['fetch_initial_data']
//        );
//
//        $this->getJson('/sharp/api/list/person/command/instance_with_init_data/25/data')
//            ->assertStatus(200)
//            ->assertExactJson([
//                "data" => [
//                    "name" => "John Wayne [25]"
//                ]
//            ]);
//    }

    protected function buildTheWorld($singleShow = false)
    {
        parent::buildTheWorld($singleShow);

        $this->app['config']->set(
            'sharp.entities.person.show',
            $singleShow
                ? ShowInstanceCommandPersonSharpSingleShow::class
                : ShowInstanceCommandPersonSharpShow::class
        );
    }
}

class ShowInstanceCommandPersonSharpShow extends PersonSharpShow {

    function buildShowConfig()
    {
        $this->addInstanceCommand("instance_info", new class() extends InstanceCommand {
            public function label(): string { return "label"; }
            public function execute($instanceId, array $params = []): array {
                return $this->info("ok");
            }
        });
    }
}

class ShowInstanceCommandPersonSharpSingleShow extends PersonSharpSingleShow {

    function buildShowConfig()
    {
        $this->addInstanceCommand("instance_info", new class() extends SingleInstanceCommand {
            public function label(): string { return "label"; }
            public function executeSingle(array $params = []): array {
                return $this->info("ok");
            }
        });
    }
}