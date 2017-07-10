<?php

namespace Code16\Sharp\Tests\Feature\Api;

use Code16\Sharp\EntityList\Commands\EntityCommand;
use Code16\Sharp\EntityList\Commands\InstanceCommand;
use Code16\Sharp\Tests\Fixtures\PersonSharpEntityList;

class CommandControllerTest extends BaseApiTest
{
    protected function setUp()
    {
        parent::setUp();
        $this->login();
    }

    /** @test */
    public function we_can_call_an_info_entity_command()
    {
        $this->buildTheWorld();
        $this->disableExceptionHandling();

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
        $this->disableExceptionHandling();

        $this->json('post', '/sharp/api/list/person/command/instance_info/1')
            ->assertStatus(200)
            ->assertJson([
                "action" => "info",
                "message" => "ok",
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
}

class EntityCommandPersonSharpEntityList extends PersonSharpEntityList {

    function buildListConfig()
    {
        $this->addEntityCommand("entity_info", new class() extends EntityCommand {
            public function label(): string { return "label"; }
            public function execute() {
                return $this->info("ok");
            }

        })->addInstanceCommand("instance_info", new class() extends InstanceCommand {
            public function label(): string { return "label"; }
            public function execute($instanceId) {
                return $this->info("ok");
            }
        });
    }
}