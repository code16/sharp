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
        $this
            ->addInstanceCommand("instance_info", new class() extends SingleInstanceCommand {
                public function label(): string { return "label"; }
                public function executeSingle(array $params = []): array {
                    return $this->info("ok");
                }
            });
    }
}